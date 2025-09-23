<?php

namespace App\Observers;

use App\Jobs\SendBookingReminder;
use App\Models\Booking;
use App\Models\BookingReminder;

class BookingObserver
{
    /**
     * Handle the Booking "created" event.
     */
    public function created(Booking $booking): void
    {
        //
    }

    /**
     * Handle the Booking "updated" event.
     */
    public function updated(Booking $booking): void
    {
        if ($booking->wasChanged(['start_time', 'end_time'])) {
            // Hapus Reminder Lama
            $booking->reminders()->delete();

            // Buat Ulang Reminder Baru
            foreach ([30, 15, 5] as $minutes) {
                $runAt = $booking->start_time->copy()->subMinutes($minutes);

                // Buat reminder baru di database
                $reminder = $booking->reminders()->create([
                    'minutes_before' => $minutes,
                    'schedule_at' => $runAt,
                ]);

                // Masukan job ke queue
                SendBookingReminder::dispatch($reminder)->delay($runAt);
            }
        }
    }

    /**
     * Handle the Booking "deleted" event.
     */
    public function deleted(Booking $booking): void
    {
        //
    }

    /**
     * Handle the Booking "restored" event.
     */
    public function restored(Booking $booking): void
    {
        //
    }

    /**
     * Handle the Booking "force deleted" event.
     */
    public function forceDeleted(Booking $booking): void
    {
        //
    }
}
