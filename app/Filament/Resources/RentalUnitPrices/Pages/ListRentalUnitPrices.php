<?php

namespace App\Filament\Resources\RentalUnitPrices\Pages;

use App\Filament\Resources\RentalUnitPrices\RentalUnitPriceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRentalUnitPrices extends ListRecords
{
    protected static string $resource = RentalUnitPriceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
