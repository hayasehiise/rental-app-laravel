<?php

namespace App\Actions\Booking;

use App\Models\Booking;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class CancelBookingAction
{
    use AsAction;

    public function handle(Booking $booking): Booking
    {
        $booking->loadMissing(['payment', 'unit.rental.category']);
        $category = $booking->unit->rental->category->slug ?? null;

        // if ($booking->isPending()) {
        //     $booking->update(['status' => 'cancelled']);

        //     if ($booking->payment) {
        //         $booking->payment->update(['transaction_status' => 'cancelled']);
        //     }
        // }

        // return $booking;

        DB::transaction(function () use ($booking, $category) {
            if (!$booking->isPending()) return;

            $booking->update([
                'status' => 'cancelled',
            ]);

            if ($booking->payment) {
                $booking->payment->update([
                    'transaction_status' => 'cancelled',
                ]);
            }

            if ($category === 'lapangan') {
                Booking::where('parent_booking_id', $booking->id)
                    ->where('status', 'pending')
                    ->update(['status' => 'cancelled']);
            }
        });

        return $booking->fresh(['payment']);
    }
}
