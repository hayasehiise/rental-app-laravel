<?php

namespace App\Actions\Booking;

use App\Models\Booking;
use App\Models\RentalUnit;
use App\Models\User;
use Lorisleiva\Actions\Concerns\AsAction;

class GetBookingCreateDataAction
{
    use AsAction;

    public function handle(int $unitId, User $user): array
    {
        $unit = RentalUnit::with(['image', 'rental.category'])->findOrFail($unitId);

        $bookings = Booking::where('rental_unit_id', $unitId)
            ->whereDate('start_time', '>=', now()->toDateString())
            ->get();

        $isMember = $user->hasRole('member');

        return compact('unit', 'bookings', 'isMember');
    }
}
