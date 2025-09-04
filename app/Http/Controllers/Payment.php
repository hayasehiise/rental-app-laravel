<?php

namespace App\Http\Controllers;

use App\Models\Payment as ModelsPayment;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Notification;

class Payment extends Controller
{
    // midtrans server-to-server callback
    public function callback(Request $request)
    {
        \Log::info('Midtrans callback received', $request->all());
        // supaya mana, re-fetch lagi confignya
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');

        // gunakan notification helper dari SDK - ini mem-parse request body dan signature
        $notif = new Notification();

        $orderId = $notif->order_id;
        $transactionStatus = $notif->transaction_status ?? $request->input('transaction_status');
        $transactionId = $notif->transaction_id ?? $request->input('transaction_id');
        $paymentType = $notif->payment_type ?? $request->input('payment_type');

        $payment = ModelsPayment::where('order_id', $orderId)->first();

        if (!$payment) {
            \Log::error('Payment not found for order', ['order_id' => $orderId]);
            return response()->json(['message' => 'Payment not found'], 400);
        }

        // update based on Status
        $payment->update([
            'transaction_id' => $transactionId,
            'payment_type' => $paymentType,
            'transaction_status' => $transactionStatus,
            'payload' => $request->all(),
        ]);

        // update booking status
        $booking = $payment->booking;
        if ($transactionStatus === 'capture' || $transactionStatus === 'settlement') {
            $booking->update(['status' => 'paid']);
        } elseif (in_array($transactionStatus, ['deny', 'cancel', 'expire'])) {
            $booking->update(['status' => 'cancelled']);
        }

        \Log::info('Payment updated', [
            'order_id' => $orderId,
            'transaction_status' => $transactionStatus,
            'booking_status' => $booking->status,
        ]);

        return response()->json(['status' => 'ok']);
    }
}
