<?php

namespace App\Http\Controllers;

use App\Models\Booking as ModelsBooking;
use App\Models\Payment;
use App\Models\RentalUnit;
use App\Services\BookingService;
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
    protected BookingService $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

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

        $booking = $this->bookingService->create($request->only('start_time', 'end_time'), $unitId);

        return redirect()->route('booking.payment', $booking);
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
        $booking->load(['unit', 'payment', 'user']);
        $snapToken = $this->bookingService->createOrGetSnapToken($booking);
        return Inertia::render('Booking/payment', compact('snapToken', 'booking'));
    }
}
