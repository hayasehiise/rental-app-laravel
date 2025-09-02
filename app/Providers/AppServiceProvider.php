<?php

namespace App\Providers;

use App\Models\Booking;
use App\Models\Rental;
use App\Policies\BookingPolicy;
use App\Policies\RentalPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policies(Rental::class, RentalPolicy::class);
        Gate::policies(Booking::class, BookingPolicy::class);
    }
}
