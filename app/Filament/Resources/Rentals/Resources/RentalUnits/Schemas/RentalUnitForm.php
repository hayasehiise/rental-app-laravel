<?php

namespace App\Filament\Resources\Rentals\Resources\RentalUnits\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class RentalUnitForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Unit')
                    ->required(),
                TextInput::make('price')
                    ->label('Harga')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),
                Toggle::make('is_available')
                    ->label('Tersedia')
                    ->default(true),
            ]);
    }
}
