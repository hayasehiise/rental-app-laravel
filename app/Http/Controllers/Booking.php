<?php

namespace App\Http\Controllers;

use App\Models\Booking as ModelsBooking;
use App\Models\Payment;
use App\Models\RentalUnit;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Midtrans\Config;

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
        DB::transaction();
        try {

            // double check di booking untuk mengatasi race condition
            $exists = ModelsBooking::where('rental_unit_id', $unitId)->where('booking_date', $request->booking_date)->where('booking_time', $request->booking_time)->exists();
            if ($exist) {
                return back()->withErrors(['booking_time' => 'Slot Sudah Dipesan, Silahkan memilih waktu lain']);
            }

            // Create booking (pending)
            $booking = ModelsBooking::create([
                'user_id' => auth()->user()->id,
                'rental_unit_id' => $unit->id,
                'booking_date' => $request->booking_date,
                'booking_time' => $request->booking_time,
                'status' => 'pending',
            ]);

            // preparing payment gateway
            Config::$serverKey = config('midtrans.server_key');
            Config::$isProduction = config('midtrans.is_production');
            Config::$isSanitized = config('midtrans.is_sanitized');
            COnfig::$is3ds = config('midtrans.is_3ds');

            // buat order ID unik
            $orderId = 'RENT-' . $booking->id . '-' . time();

            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amout' => (float) $unit->price,
                ],
                'customer_details' => [
                    'first_name' => auth()->user()->name,
                    'email' => auth()->user()->email,
                ],
            ];

            $snapToken = Snap::getSnapToken($params);

            // buat payment record (pending)
            Payment::create([
                'booking_id' => $booking->id,
                'order_id' => $orderId,
                'transaction_status' => 'pending',
                'payload' => null,
            ]);

            DB::commit();

            // render page payment via inertia
            return Inertia::render('Booking/payment', compact('snapToken', 'booking', 'unit'));
        } catch (QueryException $e) {
            DB::rollBack();
            return back()->withErrors(['booking' => 'Slot sudah dipesan (Konkurensi). Silahkan pilih waktu lain']);
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            return back()->withErrors(['booking' => 'Terjadi kesalahan pada pemesanan. Silahkan coba lagi']);
        }
    }
}
