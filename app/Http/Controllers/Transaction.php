<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Inertia\Inertia;

class Transaction extends Controller
{
    public function index(Request $request)
    {
        // ambil user login
        $user = auth()->user();

        // ambil booking dengan relasi unit dan payment, terbaru diatas, paginate 10
        $bookings = Booking::with(['unit', 'payment'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Kirim Props Ke inertia
        return Inertia::render('Transaction/index', [
            'bookings' => [
                'data' => $bookings->items(),
                'current_page' => $bookings->currentPage(),
                'last_page' => $bookings->lastPage(),
            ],
        ]);
    }
}
