<?php

namespace App\Http\Controllers;

use App\Actions\Booking\CancelBookingAction;
use App\Actions\Booking\CreateBookingAction;
use App\Actions\Booking\CreateOrGetSnapTokenAction;
use App\Actions\Booking\GetBookingCreateDataAction;
use App\Models\Booking as ModelsBooking;
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
        $request->validate([
            'start_time' => ['required', 'date', 'after_or_equal:now'],
            'end_time' => ['required', 'date', 'after:start_time'],
            'discount_code' => ['nullable', 'string'],
        ]);

        $booking = CreateBookingAction::run($request->only('start_time', 'end_time', 'discount_code'), $unitId, $request->user());

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

        return Inertia::render('Booking/payment', compact('snapToken', 'booking'));
    }
}
