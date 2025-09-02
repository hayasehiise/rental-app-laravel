<?php

namespace App\Policies;

use App\Models\Rental;
use App\Models\User;

class RentalPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'staff_admin']);
    }

    public function view(User $user, Rental $model): bool
    {
        return $user->hasAnyRole(['admin', 'staff_admin']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'staff_admin']);
    }

    public function update(User $user, Rental $model): bool
    {
        return $user->hasAnyRole(['admin', 'staff_admin']);
    }

    public function delete(User $user, Rental $model): bool
    {
        return $user->hasAnyRole(['admin', 'staff_admin']);
    }
}
