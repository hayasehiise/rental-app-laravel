<?php

namespace App\Filament\Resources\RentalUnits\Pages;

use App\Filament\Resources\RentalUnits\RentalUnitResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRentalUnit extends EditRecord
{
    protected static string $resource = RentalUnitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): ?string
    {
        return $this->getResource()::getUrl('index', [
            'rental_id' => $this->record->rental_id
        ]);
    }
}
