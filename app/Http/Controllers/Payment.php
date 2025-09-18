<?php

namespace App\Http\Controllers;

// use App\Jobs\SendBookingReminder;
// use App\Models\BookingReminder;
// use App\Models\Payment as ModelsPayment;
use App\Services\PaymentService;
use Illuminate\Http\Request;
// use Midtrans\Config;
// use Midtrans\Notification;

class Payment extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }
    // midtrans server-to-server callback
    public function callback(Request $request)
    {
        $this->paymentService->handleCallback($request);

        return response()->json(['status' => 'ok']);
    }
}
