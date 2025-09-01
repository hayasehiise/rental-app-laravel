<?php

use App\Http\Controllers\Homepage;
use App\Http\Controllers\Rental;
use Illuminate\Support\Facades\Route;

Route::get('/', [Homepage::class, 'index'])->name('home');
Route::get('/rental', [Rental::class, 'index'])->name('rental.index');
