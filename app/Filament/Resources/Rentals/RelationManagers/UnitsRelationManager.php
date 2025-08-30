<?php

namespace App\Filament\Resources\Rentals\RelationManagers;

use App\Filament\Resources\Rentals\Resources\RentalUnits\RentalUnitResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class UnitsRelationManager extends RelationManager
{
    protected static string $relationship = 'units';

    protected static ?string $relatedResource = RentalUnitResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
