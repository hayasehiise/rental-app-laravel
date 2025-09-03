<?php

use App\Http\Controllers\Auth\FrontendAuth;
use App\Http\Controllers\Booking;
use App\Http\Controllers\Homepage;
use App\Http\Controllers\Payment;
use App\Http\Controllers\Rental;
use Illuminate\Support\Facades\Route;

Route::get('/', [Homepage::class, 'index'])->name('home');
Route::get('/rental', [Rental::class, 'index'])->name('rental.index');
Route::get('/rental/{id}', [Rental::class, 'list'])->name('rental.list');

Route::middleware('guest')->group(function () {
    Route::get('/login', [FrontendAuth::class, 'showLogin'])->name('login');
    Route::post('/login', [FrontendAuth::class, 'login']);
});

Route::middleware(['auth', 'user'])->group(function () {
    Route::post('/logout', [FrontendAuth::class, 'logout'])->name('logout');

    Route::get('/booking/{unitId}', [Booking::class, 'create'])->name('booking.index');
    Route::post('/booking/{unitId}', [Booking::class, 'store'])->name('booking.store');
});

Route::post('/payment/callback', [Payment::class, 'callback'])->name('payment.callback');
