<?php

namespace App\Actions\Booking;

use App\Models\Booking;
use App\Models\RentalUnit;
use Lorisleiva\Actions\Concerns\AsAction;

class GetBookingCreateDataAction
{
    use AsAction;

    public function handle(int $unitId): array
    {
        $unit = RentalUnit::with([
            'image',
            'rental.category',
            'lapanganPrice',
            'gedungPrice',
            'kendaraanPrice'
        ])->findOrFail($unitId);

        $bookings = Booking::where('rental_unit_id', $unitId)
            ->whereDate('start_time', '>=', now()->toDateString())
            ->get();

        dd($unit);

        return compact('unit', 'bookings');
    }
}
