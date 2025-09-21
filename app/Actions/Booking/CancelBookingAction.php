<?php

namespace App\Actions\Booking;

use App\Models\Booking;
use Lorisleiva\Actions\Concerns\AsAction;

class CancelBookingAction
{
    use AsAction;

    public function handle(Booking $booking): Booking
    {
        $booking->loadMissing(['payment']);

        if ($booking->isPending()) {
            $booking->update(['status' => 'cancelled']);

            if ($booking->payment) {
                $booking->payment->update(['transaction_status' => 'cancelled']);
            }
        }

        return $booking;
    }
}
