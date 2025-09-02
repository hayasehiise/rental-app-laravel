<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function view(User $user): bool
    {
        return $user->hasAnyRole(['member', 'guest']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['member', 'guest']);
    }

    public function checkout(User $user, Booking $model): bool
    {
        if ($user->hasAnyRole(['member', 'guest'])) {
            if ($user->hasAnyPermission(['get_discount', 'normal_checkout'])) {
                return true;
            }
        }

        return false;
    }
}
