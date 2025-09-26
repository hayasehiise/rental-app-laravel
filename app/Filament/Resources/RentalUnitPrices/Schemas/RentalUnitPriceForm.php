<?php

namespace App\Filament\Resources\RentalUnitPrices\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;

class RentalUnitPriceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('type')
                    ->label('Tipe Sewa')
                    ->required()
                    ->options([
                        'hourly' => 'Per Jam',
                        'daily' => 'Per Hari',
                        'monthly' => 'Per Bulan',
                    ])
                    ->searchable(),
                TextInput::make('price')
                    ->label('Harga')
                    ->required()
                    ->prefix('Rp')
                    ->numeric(),
            ]);
    }
}
