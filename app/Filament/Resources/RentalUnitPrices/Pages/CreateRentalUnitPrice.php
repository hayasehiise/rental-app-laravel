<?php

namespace App\Filament\Resources\RentalUnitPrices\Pages;

use App\Filament\Resources\RentalUnitPrices\RentalUnitPriceResource;
use App\Filament\Resources\RentalUnits\RentalUnitResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRentalUnitPrice extends CreateRecord
{
    protected static string $resource = RentalUnitPriceResource::class;

    protected function getRedirectUrl(): string
    {
        $unit = $this->record->unit;

        return RentalUnitResource::getUrl('edit', ['record' => $unit]);
    }
}
