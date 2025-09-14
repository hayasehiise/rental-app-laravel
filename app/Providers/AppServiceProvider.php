<?php

namespace App\Providers;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Rental;
use App\Models\RentalCategory;
use App\Models\RentalImage;
use App\Models\RentalUnit;
use App\Policies\Admin\BookingPolicy;
use App\Policies\Admin\RentalCategoryPolicy;
use App\Policies\Admin\RentalImagePolicy;
use App\Policies\Admin\RentalPolicy;
use App\Policies\Admin\RentalUnitPolicy;
use App\Policies\Admin\TransactionPolicy;
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

        // Policy Register
        Gate::policy(Booking::class, BookingPolicy::class);
        Gate::policy(Rental::class, RentalPolicy::class);
        Gate::policy(Payment::class, TransactionPolicy::class);
        Gate::policy(RentalUnit::class, RentalUnitPolicy::class);
        Gate::policy(RentalImage::class, RentalImagePolicy::class);
        Gate::policy(RentalCategory::class, RentalCategoryPolicy::class);
    }
}
