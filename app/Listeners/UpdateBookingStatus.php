<?php

namespace App\Listeners;

use App\Events\PaymentStatusUpdated;
use App\Jobs\SendBookingReminder;
use App\Models\Booking;
use App\Models\BookingReminder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateBookingStatus
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
    public function handle(PaymentStatusUpdated $event): void
    {
        $payment = $event->payment;
        $booking = $payment->booking;

        if (!$booking) return;

        $status = $payment->transaction_status;

        // if (in_array($status, ['capture', 'settlement'])) {
        //     $booking->update(['status' => 'paid']);

        //     foreach ([30, 15, 5] as $minutes) {
        //         $scheduleAt = $booking->start_time->copy()->subMinutes($minutes);

        //         $reminder = BookingReminder::create([
        //             'booking_id' => $booking->id,
        //             'minutes_before' => $minutes,
        //             'schedule_at' => $scheduleAt,
        //         ]);

        //         SendBookingReminder::dispatch($reminder)->delay($scheduleAt);
        //     }
        // } elseif (in_array($status, ['deny', 'cancel', 'expire'])) {
        //     $booking->update(['status' => 'cancelled']);
        // }
        if (in_array($status, ['capture', 'settlement'])) {
            $bookings = Booking::where('id', $booking->id)
                ->orWhere('parent_booking_id', $booking->id)
                ->get();

            foreach ($bookings as $b) {
                $b->update(['status' => 'paid']);

                // buat reminder
                foreach ([30, 15, 5] as $minutes) {
                    $scheduleAt = $b->start_time->copy()->subMinutes($minutes);

                    $reminder = BookingReminder::create([
                        'booking_id' => $booking->id,
                        'minutes_before' => $minutes,
                        'schedule_at' => $scheduleAt,
                    ]);

                    SendBookingReminder::dispatch($reminder)->delay($scheduleAt);
                }
            }
        } elseif (in_array($status, ['deny', 'cancel', 'expire'])) {
            // $booking->update(['status' => 'cancelled']);
            Booking::where('id', $booking->id)
                ->orWhere('parent_booking_id', $booking->id)
                ->update(['status' => 'cancelled']);

            BookingReminder::whereIn('booking_id', function ($q) use ($booking) {
                $q->select('id')
                    ->from('bookings')
                    ->where('id', $booking->id)
                    ->orWhere('parent_booking_id', $booking->id);
            })->delete();
        }
    }
}
