<?php

use App\Http\Controllers\Auth\FrontendAuth;
use App\Http\Controllers\Auth\PasswordReset;
use App\Http\Controllers\Booking;
use App\Http\Controllers\Homepage;
use App\Http\Controllers\Invoice;
use App\Http\Controllers\Payment;
use App\Http\Controllers\Rental;
use App\Http\Controllers\Transaction;
use Illuminate\Support\Facades\Route;

Route::get('/', [Homepage::class, 'index'])->name('home');
Route::get('/rental', [Rental::class, 'index'])->name('rental.index');
Route::get('/rental/{id}', [Rental::class, 'list'])->name('rental.list');

Route::middleware('guest')->group(function () {
    Route::get('/login', [FrontendAuth::class, 'showLogin'])->name('login.user');
    Route::post('/login', [FrontendAuth::class, 'login']);

    Route::get('/register', [FrontendAuth::class, 'showRegister'])->name('register.user');
    Route::post('/register', [FrontendAuth::class, 'register'])->name('register.store');

    Route::get('/forgot-password', [PasswordReset::class, 'requestForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordReset::class, 'sendResetLink'])->name('password.email');

    Route::get('/reset-password', [PasswordReset::class, 'resetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordReset::class, 'resetPassword'])->name('password.update');
});

Route::middleware(['isUser'])->group(function () {
    Route::post('/logout', [FrontendAuth::class, 'logout'])->name('logout.user');

    Route::get('/booking/{unitId}', [Booking::class, 'create'])->name('booking.index');
    Route::get('/booking/{booking}/payment', [Booking::class, 'payment'])->name('booking.payment');
    Route::post('/booking/{unitId}', [Booking::class, 'store'])->name('booking.store');
    Route::post('/booking/{booking}/cancel', [Booking::class, 'cancel'])->name('booking.cancel');

    Route::get('/transaction', [Transaction::class, 'index'])->name('transaction.index');
});

Route::get('/invoice/{booking}', [Invoice::class, 'download'])->middleware(['auth'])->name('invoice.download');

Route::post('/payment/midtrans/callback', [Payment::class, 'callback'])->name('payment.callback');

Route::get('/register/verify/{token}', [FrontendAuth::class, 'verify'])->name('register.verify');
