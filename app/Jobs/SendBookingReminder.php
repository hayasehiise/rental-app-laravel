<?php

namespace App\Jobs;

use App\Models\BookingReminder;
use App\Services\TelegramService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Tiptap\Utils\HTML;

class SendBookingReminder implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public BookingReminder $reminder)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(TelegramService $telegram): void
    {
        $reminder = BookingReminder::with(['booking.unit', 'booking.user', 'booking.payment'])->findOrFail($this->reminder->id);
        $booking = $reminder->booking;

        if ($reminder->sent_at) {
            return;
        }

        $msg = <<<HTML
        <b>Reminder Booking ⏰</b>
        ID: {$booking->payment->order_id}
        Rental Unit: {$booking->unit->name}
        Customer: {$booking->user->name}
        Start: {$booking->start_time->format('d M Y H:i')}
        End: {$booking->end_time->format('d M Y H:i')}
        HTML;

        $telegram->send($msg);

        $this->reminder->update([
            'sent_at' => now(),
        ]);
    }
}
