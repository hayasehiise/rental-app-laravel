<?php

namespace App\Filament\Resources\RentalUnitImages\Pages;

use App\Filament\Resources\RentalUnitImages\RentalUnitImageResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRentalUnitImage extends EditRecord
{
    protected static string $resource = RentalUnitImageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
