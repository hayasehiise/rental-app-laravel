<?php

namespace App\Filament\Resources\RentalUnits\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class RentalUnitForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('rental_id')
                    ->default(fn() => request()->query('rental_id'))
                    ->required(),
                TextInput::make('name')
                    ->label('Nama Unit')
                    ->required(),
                TextInput::make('hourly_price')
                    ->label('Harga Perjam')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),
                TextInput::make('member_price')
                    ->label('Harga Perjam')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),
                Toggle::make('is_available')
                    ->label('Tersedia')
                    ->default(true),
            ]);
    }
}
