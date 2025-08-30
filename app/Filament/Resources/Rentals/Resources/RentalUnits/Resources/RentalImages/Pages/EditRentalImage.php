<?php

namespace App\Filament\Resources\Rentals\Resources\RentalUnits\Resources\RentalImages\Pages;

use App\Filament\Resources\Rentals\Resources\RentalUnits\Resources\RentalImages\RentalImageResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRentalImage extends EditRecord
{
    protected static string $resource = RentalImageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
