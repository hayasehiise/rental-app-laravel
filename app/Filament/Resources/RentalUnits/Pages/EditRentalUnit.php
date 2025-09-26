<?php

namespace App\Filament\Resources\RentalUnits\Pages;

use App\Filament\Resources\RentalUnits\RentalUnitResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
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

    protected function getFormActions(): array
    {
        return [
            EditAction::make(),
            Action::make('cancel')
                ->label('Kembali')
                ->color('gray')
                ->url(fn() => static::getResource()::getUrl('index', [
                    'rental_id' => $this->record->rental_id
                ])),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index', [
            'rental_id' => $this->record->rental_id
        ]);
    }
}
