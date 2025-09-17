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
        $booking = $this->reminder->booking;

        if ($this->reminder->sent_at) {
            return;
        }

        $msg = <<<HTML
        <b>Reminder Booking ⏰</b>
        ID: {$booking->payment->order_id}
        Customer: {$booking->user->name}
        Start: {$booking->start_time->format('d M Y H:i')}
        Status: {$booking->status}
        HTML;

        $telegram->send($msg);

        $this->reminder->update([
            'sent_at' => now(),
        ]);
    }
}
