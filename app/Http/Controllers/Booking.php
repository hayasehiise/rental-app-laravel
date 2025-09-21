<?php

namespace App\Http\Controllers;

use App\Actions\Booking\CancelBookingAction;
use App\Actions\Booking\CreateBookingAction;
use App\Actions\Booking\CreateOrGetSnapTokenAction;
use App\Actions\Booking\GetBookingCreateDataAction;
use App\Models\Booking as ModelsBooking;
// use App\Models\BookingType;
// use App\Models\Payment;
// use App\Models\RentalUnit;
// use App\Services\BookingService;
// use Carbon\Carbon;
// use Exception;
// use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
// use Midtrans\Config;
// use Midtrans\Snap;

class Booking extends Controller
{
    // protected BookingService $bookingService;

    // public function __construct(BookingService $bookingService)
    // {
    //     $this->bookingService = $bookingService;
    // }

    public function create(int $unitId)
    {
        // ini kalau tidak pakai laravel action
        // $unit = RentalUnit::with(['image', 'rental.category'])->findOrFail($unitId);

        // $bookings = ModelsBooking::where('rental_unit_id', $unitId)
        //     ->whereDate('start_time', '>=', now()->toDateString())
        //     ->get();

        // $user = auth()->user();
        // $isMember = $user->hasRole('member');

        // return Inertia::render('Booking/create', [
        //     'unit' => $unit,
        //     'bookings' => $bookings,
        //     'isMember' => $isMember
        // ]);
        // =====================================================

        $data = GetBookingCreateDataAction::run($unitId, auth()->user());

        return Inertia::render('Booking/create', $data);
    }

    public function store(Request $request, $unitId)
    {
        // tanpa laravel actions
        // $request->validate([
        //     'start_time' => ['required', 'date', 'after_or_equal:now'],
        //     'end_time' => ['required', 'date', 'after:start_time'],
        //     'discount_code' => ['nullable', 'string'],
        // ]);

        // $user = auth()->user();
        // $isMember = $user->hasRole('member');

        // $booking = $this->bookingService->create($request->only('start_time', 'end_time', 'discount_code'), $unitId, $isMember);

        // return redirect()->route('booking.payment', $booking);
        // ================================================================

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
        // Sebelum pakai Laravel Actions
        // $booking->load(['payment']);
        // if ($booking->isPending()) {
        //     $booking->update(['status' => 'cancelled']);
        //     $booking->payment->update([
        //         'transaction_status' => 'cancelled'
        //     ]);
        // }

        // return redirect()->route('transaction.index')->with('success', 'Booking Dibatalkan');
        // =====================================

        CancelBookingAction::run($booking);

        return redirect()->route('transaction.index')->with('success', 'Booking Dibatalkan');
    }

    public function payment(ModelsBooking $booking)
    {
        // Sebelum laravel actions
        // $booking->load(['unit', 'payment', 'user', 'discounts']);
        // $snapToken = $this->bookingService->createOrGetSnapToken($booking);
        // return Inertia::render('Booking/payment', compact('snapToken', 'booking'));
        // =======================================

        $snapToken = CreateOrGetSnapTokenAction::run($booking);

        return Inertia::render('Booking/payment', compact('snapToken', 'booking'));
    }
}
