<?php

namespace App\Filament\Resources\Rentals\Pages;

use App\Filament\Resources\Rentals\RentalResource;
use App\Filament\Resources\RentalUnits\RentalUnitResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRental extends CreateRecord
{
    protected static string $resource = RentalResource::class;

    protected function getRedirectUrl(): string
    {
        return RentalUnitResource::getUrl('index', [
            'rental_id' => $this->record->id,
        ]);
    }
}
