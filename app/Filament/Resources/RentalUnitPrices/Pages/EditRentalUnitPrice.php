<?php

namespace App\Filament\Resources\RentalUnitPrices\Pages;

use App\Filament\Resources\RentalUnitPrices\RentalUnitPriceResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRentalUnitPrice extends EditRecord
{
    protected static string $resource = RentalUnitPriceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
