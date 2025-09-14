<?php

namespace App\Http\Controllers;

use App\Models\Booking as ModelsBooking;
use App\Models\Payment;
use App\Models\RentalUnit;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Midtrans\Config;
use Midtrans\Snap;

class Booking extends Controller
{
    public function create($unitId)
    {
        $unit = RentalUnit::with(['image', 'rental.category'])->findOrFail($unitId);

        $bookings = ModelsBooking::where('rental_unit_id', $unitId)
            ->whereDate('start_time', '>=', now()->toDateString())
            ->get();

        return Inertia::render('Booking/create', compact('unit', 'bookings'));
    }

    public function store(Request $request, $unitId)
    {
        $request->validate([
            'start_time' => ['required', 'date', 'after_or_equal:now'],
            'end_time' => ['required', 'date', 'after:start_time'],
        ]);

        $unit = RentalUnit::with(['image', 'rental.category'])->findOrFail($unitId);

        // cek apakah tanggal mulai atau selesai jatuh di hari sabtu (khusus lapangan/gedung)
        $start = Carbon::parse($request->start_time);
        $end = Carbon::parse($request->end_time);

        $category = $unit->rental->category->slug;

        if (in_array($category, ['lapangan', 'gedung'])) {
            if ($start->isSaturday() || $end->isSaturday()) {
                throw ValidationException::withMessages([
                    'booking' => 'Tidak Bisa Memesan Dihari Sabtu'
                ]);
            }
        }

        // Gunakan transaction atomicity
        DB::beginTransaction();
        try {
            // double check di booking untuk mengatasi race condition
            $exists = ModelsBooking::where('rental_unit_id', $unitId)
                ->whereIn('status', ['pending', 'paid'])
                ->where(function ($q) use ($start, $end) {
                    $q->whereBetween('start_time', [$start, $end])
                        ->orWhereBetween('end_time', [$start, $end])
                        ->orWhere(function ($q2) use ($start, $end) {
                            $q2->where('start_time', '<=', $start)
                                ->where('end_time', '>=', $end);
                        });
                })
                ->exists();
            if ($exists) {
                throw ValidationException::withMessages([
                    'booking' => 'Slot Sudah Dipesan',
                ]);
            }

            // Hitung harga
            $hours = $start->diffInHours($end);
            $days = (int) ceil($hours / 24);
            $price = $unit->price * $days;
            $discount = 0;
            $finalPrice = $price;

            // Create booking (pending)
            $booking = ModelsBooking::create([
                'user_id' => auth()->user()->id,
                'rental_unit_id' => $unit->id,
                'start_time' => $start,
                'end_time' => $end,
                'price' => $unit->price,
                'discount' => $discount,
                'final_price' => $finalPrice,
                'status' => 'pending',
            ]);

            // buat order ID unik
            $orderId = 'RENT-' . $booking->id . '-' . now()->timestamp;

            // buat payment record (pending)
            Payment::create([
                'booking_id' => $booking->id,
                'order_id' => $orderId,
                'transaction_status' => 'pending',
                'payload' => null,
            ]);

            DB::commit();

            // render page payment via inertia
            return redirect()->route('booking.payment', $booking);
        } catch (QueryException $e) {
            DB::rollBack();
            // report($e);
            throw ValidationException::withMessages([
                'booking' => 'Booking sudah diisi (Konkurensi). silahkan pilih tanggal/jam lain'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            throw ValidationException::withMessages([
                'booking' => 'Cek ulang tanggal dan waktu yang diinput'
            ]);
        }
    }
    public function cancel(ModelsBooking $booking)
    {
        $booking->load(['payment']);
        if ($booking->isPending()) {
            $booking->update(['status' => 'cancelled']);
            $booking->payment->update([
                'transaction_status' => 'cancelled'
            ]);
        }

        return redirect()->route('transaction.index')->with('success', 'Booking Dibatalkan');
    }

    public function payment(ModelsBooking $booking)
    {
        $booking->load(['unit', 'payment']);
        $unit = $booking->unit;
        $payment = $booking->payment;
        // preparing payment gateway
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');

        if (!$payment->snap_token) {
            // Buat snap token baru hanya jika belum ada
            $orderId = $payment->order_id; // tetap pakai order_id yang sudah dibuat saat store

            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => (int) $booking->final_price,
                ],
                'customer_details' => [
                    'first_name' => $booking->user->name,
                    'email' => $booking->user->email,
                ],
                'expiry' => [
                    'start_time' => now()->format('Y-m-d H:i:s O'),
                    'unit'       => 'minutes',
                    'duration'   => 15,
                ],
            ];

            $snapToken = Snap::getSnapToken($params);

            // Update payment dengan snap_token baru
            $payment->update([
                'snap_token' => $snapToken,
                'transaction_status' => 'pending',
            ]);
        } else {
            // Pakai snap_token yang sudah ada
            $snapToken = $payment->snap_token;
        }

        return Inertia::render('Booking/payment', compact('snapToken', 'booking'));
    }
}
