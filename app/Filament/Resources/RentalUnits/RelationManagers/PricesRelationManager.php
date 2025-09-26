<?php

namespace App\Filament\Resources\RentalUnits\RelationManagers;

use App\Filament\Resources\RentalUnitPrices\RentalUnitPriceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class PricesRelationManager extends RelationManager
{
    protected static string $relationship = 'prices';

    protected static ?string $relatedResource = RentalUnitPriceResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
