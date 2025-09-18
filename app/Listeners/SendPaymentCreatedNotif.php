<?php

namespace App\Listeners;

use App\Events\PaymentCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendPaymentCreatedNotif
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
    public function handle(PaymentCreated $event): void
    {
        $payment = $event->payment;

        Log::info("Snap token dibuat untuk order {$payment->order_id}", [
            'snap_token' => $payment->snap_token,
        ]);
    }
}
