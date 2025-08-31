<?php

namespace App\Filament\Resources\RentalUnitImages\Pages;

use App\Filament\Resources\RentalUnitImages\RentalUnitImageResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRentalUnitImage extends CreateRecord
{
    protected static string $resource = RentalUnitImageResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index', [
            'rental_unit_id' => $this->record->rental_unit_id
        ]);
    }
}
