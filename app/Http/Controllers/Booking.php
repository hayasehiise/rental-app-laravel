<?php

namespace App\Http\Controllers;

use App\Models\Booking as ModelsBooking;
use App\Models\BookingType;
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

        $user = auth()->user();
        $bookingTypeCode = $user->hasRole('member') ? 'member' : 'hourly';

        $bookingType = BookingType::where('code', $bookingTypeCode)->firstOrFail();

        $hasReachLimit = false;
        if ($bookingType->monthly_limit) {
            $count = ModelsBooking::where('user_id', $user->id)
                ->where('booking_type_id', $bookingType->id)
                ->whereBetween('start_time', [
                    Carbon::now()->startOfMonth(),
                    Carbon::now()->endOfMonth(),
                ])
                ->count();

            $hasReachLimit = $count >= $bookingType->monthly_limit;
        }

        return Inertia::render('Booking/create', [
            'unit' => $unit,
            'bookings' => $bookings,
            'bookingType' => $bookingType->code,
            'monthlyLimit' => $bookingType->monthly_limit,
            'hasReachedLimit' => $hasReachLimit,
        ]);
    }

    public function store(Request $request, $unitId)
    {
        $request->validate([
            'start_time' => ['required', 'date', 'after_or_equal:now'],
            'end_time' => ['required', 'date', 'after:start_time'],
        ]);

        $user = auth()->user();
        $bookingTypeCode = $user->hasRole('member') ? 'member' : 'hourly';
        $bookingType = BookingType::where('code', $bookingTypeCode)->firstOrFail();

        $hasReachLimit = false;
        // cek limit member
        if ($bookingType->monthly_limit) {
            $count = Booking::where('user_id', $user->id)
                ->where('booking_type_id', $bookingType->id)
                ->whereBetween('start_time', [
                    Carbon::now()->startOfMonth(),
                    Carbon::now()->endOfMonth(),
                ])
                ->count();

            $hasReachLimit = $count >= $bookingType->monthly_limit;
        }

        $booking = $this->bookingService->create($request->only('start_time', 'end_time'), $unitId, $bookingType, $hasReachLimit);

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
