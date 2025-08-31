<?php

namespace App\Filament\Resources\RentalUnits\Pages;

use App\Filament\Resources\Rentals\RentalResource;
use App\Filament\Resources\RentalUnits\RentalUnitResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRentalUnits extends ListRecords
{
    protected static string $resource = RentalUnitResource::class;

    public function mount(): void
    {
        parent::mount();

        if (!request()->has('rental_id')) {
            $this->redirect(RentalResource::getUrl('index'));
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make('createUnit')
                ->hidden(fn() => !request()->has('rental_id'))
                ->url(function () {
                    $rentalId = request()->query('rental_id');
                    return $this->getResource()::getUrl('create', ['rental_id' => $rentalId]);
                }),
            Action::make('back')
                ->label('Kembali')
                ->icon('tabler-arrow-bar-left')
                ->url(fn() => RentalResource::getUrl('index')),
        ];
    }
}
