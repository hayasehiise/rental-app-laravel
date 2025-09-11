<?php

namespace App\Providers;

use App\Models\Booking;
use App\Models\Rental;
use App\Policies\Admin\BookingPolicy;
use App\Policies\Admin\RentalPolicy;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

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
        Inertia::share([
            'auth' => fn() => [
                'user' => Auth::user(),
            ],
        ]);

        Gate::policy(Booking::class, BookingPolicy::class);
        Gate::policy(Rental::class, RentalPolicy::class);
    }
}
