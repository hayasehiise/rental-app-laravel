<?php

namespace App\Actions\Payment;

use Midtrans\Config;
use App\Models\Payment;
use Midtrans\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Events\PaymentStatusUpdated;
use Lorisleiva\Actions\Concerns\AsAction;

class HandleCallback
{
    use AsAction;

    public function handle(Request $request): void
    {
        Log::info('Midtrans callback received: ', $request->all());

        // config midtrans
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');

        // Notification SDK Midtrans
        $notif = new Notification();

        $orderId = $notif->order_id;
        $transactionStatus = $notif->transaction_status ?? $request->input('transaction_status');
        $transactionId = $notif->transaction_id ?? $request->input('transaction_id');
        $paymentType = $notif->payment_type ?? $request->input('payment_type');

        $payment = Payment::where('order_id', $orderId)->first();
        if (!$payment) {
            Log::error('Payment not found for order', ['order_id' => $orderId]);
            return;
        }

        $payment->update([
            'transaction_id' => $transactionId,
            'payment_type' => $paymentType,
            'transaction_status' => $transactionStatus,
            'payload' => $request->all(),
        ]);

        event(new PaymentStatusUpdated($payment));

        Log::info('Payment Updated', [
            'order_id' => $orderId,
            'transaction_status' => $transactionStatus,
        ]);
    }
}
