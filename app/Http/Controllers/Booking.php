<?php

namespace App\Http\Controllers;

use App\Actions\Booking\CancelBookingAction;
use App\Actions\Booking\CreateBookingAction;
use App\Actions\Booking\CreateOrGetSnapTokenAction;
use App\Actions\Booking\GetBookingCreateDataAction;
use App\Models\Booking as ModelsBooking;
use App\Models\RentalUnit;
use Illuminate\Http\Request;
use Inertia\Inertia;

class Booking extends Controller
{
    public function create(int $unitId)
    {
        $data = GetBookingCreateDataAction::run($unitId);

        return Inertia::render('Booking/create', $data);
    }

    public function store(Request $request, $unitId)
    {
        $unit = RentalUnit::with('rental.category')->findOrFail($unitId);
        $category = $unit->rental->category->slug;

        $rules = [
            'start_time' => ['required', 'date', 'after_or_equal:now'],
        ];

        if ($category === 'lapangan') {
            $rules['member'] = ['boolean'];

            if (!$request->boolean('member')) {
                $rules['end_time'] = ['required', 'date', 'after:start_time'];
            }
        }

        if ($category === 'gedung') {
            $rules['gedung_price_id'] = ['required', 'exists:gedung_prices,id'];
        }

        if ($category === 'kendaraan') {
            $rules['end_time'] = ['required', 'date', 'after:start_time'];
        }

        $rulesMessage = [
            'start_time.required' => 'Waktu Mulai Wajib Diisi',
            'start_time.date' => 'Waktu Mulai Tidak Valid',
            'start_time.after_or_equal' => 'Waktu Mulai Minimal Adalah Sekarang',
            'end_time.required' => 'Waktu Selesai Wajib Diisi',
            'end_time.date' => 'Waktu Selesai Tidak Valid',
            'end_time.after' => 'Waktu Selesai Harus Setelah Waktu Mulai',
            'gedung_price_id.required' => 'Paket Wajib Dipilih',
            'gedung_price_id.exists' => 'Paket Tidak Valid',
        ];

        $validated = $request->validate($rules, $rulesMessage);

        $booking = CreateBookingAction::run($validated, $unitId, $request->user());

        return redirect()->route('booking.payment', $booking);
    }

    public function cancel(ModelsBooking $booking)
    {
        CancelBookingAction::run($booking);

        return redirect()->route('transaction.index')->with('success', 'Booking Dibatalkan');
    }

    public function payment(ModelsBooking $booking)
    {
        $snapToken = CreateOrGetSnapTokenAction::run($booking);

        return Inertia::render('Booking/payment', [
            'booking' => $booking->load([
                'unit.rental.category',     // load rental di dalam unit
                'discounts',       // kalau mau diskon juga muncul
                'payment',         // kalau mau payment juga
            ]),
            'snapToken' => $snapToken,
        ]);
    }
}
