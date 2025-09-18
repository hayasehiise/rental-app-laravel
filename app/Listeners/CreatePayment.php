<?php

namespace App\Listeners;

use App\Events\BookingCreated;
use App\Models\Payment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreatePayment
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(BookingCreated $event): void
    {
        // Create Payment after booking
        $booking = $event->booking;

        $orderId = 'RENT - ' . $booking->id . now()->timestamp;

        Payment::create([
            'booking_id' => $booking->id,
            'order_id' => $orderId,
            'transaction_status' => 'pending'
        ]);
    }
}
