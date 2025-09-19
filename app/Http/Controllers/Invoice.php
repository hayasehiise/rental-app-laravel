<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Inertia\Inertia;

class Invoice extends Controller
{
    public function download(Booking $booking)
    {
        $booking->load(['unit', 'unit.rental', 'unit.rental.category', 'payment', 'user', 'discounts']);

        return Inertia::render('invoice', compact('booking'));
    }
}
