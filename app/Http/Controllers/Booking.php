<?php

namespace App\Http\Controllers;

use App\Models\Booking as ModelsBooking;
use App\Models\Payment;
use App\Models\RentalUnit;
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
        $unit = RentalUnit::with('image')->findOrFail($unitId);

        $bookings = ModelsBooking::where('rental_unit_id', $unitId)
            ->whereDate('booking_date', '>=', now()->toDateString())
            ->get();

        return Inertia::render('Booking/create', compact('unit', 'bookings'));
    }

    public function store(Request $request, $unitId)
    {
        $request->validate([
            'booking_date' => ['required', 'date', 'after_or_equal:today'],
            'booking_time' => ['required', 'string'],
        ]);

        $unit = RentalUnit::findOrFail($unitId);

        // Gunakan transaction atomicity
        DB::beginTransaction();
        try {

            // double check di booking untuk mengatasi race condition
            $exists = ModelsBooking::where('rental_unit_id', $unitId)
                ->where('booking_date', $request->booking_date)
                ->where('booking_time', $request->booking_time)
                ->whereIn('status', ['pending', 'paid'])
                ->exists();
            if ($exists) {
                throw ValidationException::withMessages([
                    'booking' => 'Slot Sudah Dipesan',
                ]);
            }

            // Create booking (pending)
            $booking = ModelsBooking::create([
                'user_id' => auth()->user()->id,
                'rental_unit_id' => $unit->id,
                'booking_date' => $request->booking_date,
                'booking_time' => $request->booking_time,
                'status' => 'pending',
            ]);

            // buat order ID unik
            $orderId = 'RENT-' . $booking->id . '-' . time();

            // buat payment record (pending)
            Payment::create([
                'booking_id' => $booking->id,
                'order_id' => $orderId,
                'transaction_status' => 'pending',
                'payload' => null,
            ]);

            DB::commit();

            // render page payment via inertia
            return redirect()->route('booking.payment', [
                'booking' => $booking->id,
            ]);
        } catch (QueryException $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'booking' => 'Slot Sudah Dipesan (Konkurensi). Silahkan Pilih Jam Lain'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            throw ValidationException::withMessages([
                'booking' => 'Terjadi Kesalahan Silahkan coba lagi'
            ]);
        }
    }
    public function cancel(ModelsBooking $booking)
    {
        $booking->load(['unit', 'payment']);
        if ($booking->status === 'pending') {
            $booking->update(['status' => 'cancelled']);
            $booking->payment->update([
                'transaction_status' => 'cancelled'
            ]);
        }

        return redirect()->route('rental.index')->with('success', 'Booking Dibatalkan');
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

        if (!$payment->order_id) {
            // buat order ID unik
            $orderId = 'RENT-' . $booking->id . '-' . time();

            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => (int) $unit->price,
                ],
                'customer_details' => [
                    'first_name' => auth()->user()->name,
                    'email' => auth()->user()->email,
                ],
            ];

            $snapToken = Snap::getSnapToken($params);

            $payment->update([
                'order_id' => $orderId,
                'payload' => null,
            ]);
        } else {
            $params = [
                'transaction_details' => [
                    'order_id' => $payment->order_id,
                    'gross_amount' => (int) $unit->price,
                ],
                'customer_details' => [
                    'first_name' => auth()->user()->name,
                    'email' => auth()->user()->email,
                ],
            ];

            $snapToken = Snap::getSnapToken($params);
        }

        return Inertia::render('Booking/payment', compact('snapToken', 'booking', 'unit'));
    }
}
