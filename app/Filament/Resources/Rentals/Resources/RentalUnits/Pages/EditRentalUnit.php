<?php

namespace App\Filament\Resources\Rentals\Resources\RentalUnits\Pages;

use App\Filament\Resources\Rentals\Resources\RentalUnits\RentalUnitResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRentalUnit extends EditRecord
{
    protected static string $resource = RentalUnitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            Action::make('back')
                ->label('Kembali')
                ->icon('heroicon-m-arrow-left')
                ->url(fn() => route('filament.admin.resources.rentals.edit', [
                    'record' => $this->record->rental_id
                ])),
        ];
    }
}
