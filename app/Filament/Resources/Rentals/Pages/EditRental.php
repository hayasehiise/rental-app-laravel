<?php

namespace App\Filament\Resources\Rentals\Pages;

use App\Filament\Resources\Rentals\RentalResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRental extends EditRecord
{
    protected static string $resource = RentalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            Action::make('back')
                ->label('Kembali')
                ->icon('heroicon-m-arrow-left')
                ->url(fn() => route('filament.admin.resources.rentals.index'))
            // ->url($this->getResource()::getUrl('index'))
        ];
    }
}
