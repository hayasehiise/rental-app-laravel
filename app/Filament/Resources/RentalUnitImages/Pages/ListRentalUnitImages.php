<?php

namespace App\Filament\Resources\RentalUnitImages\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Rentals\RentalResource;
use App\Filament\Resources\RentalUnitImages\RentalUnitImageResource;
use App\Filament\Resources\RentalUnits\RentalUnitResource;
use App\Models\RentalUnit;
use Filament\Actions\Action;

class ListRentalUnitImages extends ListRecords
{
    protected static string $resource = RentalUnitImageResource::class;

    public function mount(): void
    {
        parent::mount();

        if (!request()->has('rental_unit_id')) {
            $this->redirect(RentalResource::getUrl('index'));
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->url(function () {
                    $rentalUnitId = request()->query('rental_unit_id');
                    return $this->getResource()::getUrl('create', [
                        'rental_unit_id' => $rentalUnitId
                    ]);
                }),
            Action::make('back')
                ->label('Kembali')
                ->icon('tabler-arrow-bar-left')
                ->url(fn() => RentalUnitResource::getUrl('index', [
                    'rental_id' => RentalUnit::find(request()->query('rental_unit_id'))?->rental_id,
                ]))
        ];
    }
}
