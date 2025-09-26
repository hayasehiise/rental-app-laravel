<?php

namespace App\Filament\Resources\RentalUnits\Pages;

use App\Filament\Resources\RentalUnits\RentalUnitResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRentalUnit extends CreateRecord
{
    protected static string $resource = RentalUnitResource::class;

    // protected function getRedirectUrl(): string
    // {
    //     return $this->getResource()::getUrl('index', [
    //         'rental_id' => $this->record->rental_id,
    //     ]);
    // }
}
