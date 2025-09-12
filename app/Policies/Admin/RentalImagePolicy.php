<?php

namespace App\Policies\Admin;

use App\Models\RentalImage;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class RentalImagePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // return false;
        return $user->hasAnyRole(['staff_admin', 'admin']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, RentalImage $rentalImage): bool
    {
        // return false;
        return $user->hasAnyRole(['staff_admin', 'admin']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // return false;
        return $user->hasAnyRole(['staff_admin', 'admin']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, RentalImage $rentalImage): bool
    {
        // return false;
        return $user->hasAnyRole(['staff_admin', 'admin']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, RentalImage $rentalImage): bool
    {
        // return false;
        return $user->hasAnyRole(['staff_admin', 'admin']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, RentalImage $rentalImage): bool
    {
        // return false;
        return $user->hasAnyRole(['staff_admin', 'admin']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, RentalImage $rentalImage): bool
    {
        // return false;
        return $user->hasAnyRole(['staff_admin', 'admin']);
    }
}
