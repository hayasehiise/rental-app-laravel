<?php

namespace App\Http\Controllers;

use App\Actions\Payment\HandleCallback;
use Illuminate\Http\Request;

class Payment extends Controller
{
    // midtrans server-to-server callback
    public function callback(Request $request)
    {
        HandleCallback::run($request);

        return response()->json(['status' => 'ok']);
    }
}
