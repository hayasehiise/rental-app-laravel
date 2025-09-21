<?php

namespace App\Actions\Booking;

use Midtrans\Snap;
use Midtrans\Config;
use App\Models\Booking;
use App\Events\PaymentCreated;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateOrGetSnapTokenAction
{
    use AsAction;

    public function handle(Booking $booking): string
    {
        $booking->load(['unit', 'payment', 'user', 'discounts']);
        $payment = $booking->payment;

        // Setup Midtrans
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');

        // Hitung Biaya Transaksi
        $transactionFee = (int) round($booking->final_price * 0.04);
        $grossAmount = (int) $booking->final_price + $transactionFee;

        if (!$payment->snap_token) {
            $orderId = $payment->order_id;

            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => $grossAmount,
                ],
                'customer_details' => [
                    'first_name' => $booking->user->name,
                    'email' => $booking->user->email,
                ],
                'expiry' => [
                    'start_time' => now()->format('Y-m-d H:i:s O'),
                    'unit' => 'minutes',
                    'duration' => 15,
                ],
                'item_details' => [
                    [
                        'id' => 'Booking_' . $booking->id,
                        'price' => (int) $booking->final_price,
                        'quantity' => 1,
                        'name' => 'Booking Unit ' . $booking->unit->name,
                    ],
                    [
                        'id' => 'fee_admin',
                        'price' => $transactionFee,
                        'quantity' => 1,
                        'name' => 'Biaya Transaksi (4%)',
                    ],
                ],
            ];

            $snapToken = Snap::getSnapToken($params);

            $payment->update([
                'snap_token' => $snapToken,
                'transaction_status' => 'pending'
            ]);

            event(new PaymentCreated($payment));
        } else {
            $snapToken = $payment->snap_token;
        }

        return $snapToken;
    }
}
