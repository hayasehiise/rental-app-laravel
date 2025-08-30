<?php

namespace App\Filament\Resources\Rentals\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RentalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Rental')
                    ->required(),
                Select::make('type')
                    ->label('Tipe')
                    ->options([
                        'lapangan' => 'Lapangan',
                        'gedung' => 'Gedung',
                        'kendaraan' => 'Kendaraan'
                    ])
                    ->required(),
                Textarea::make('description')
                    ->label('Deskripsi')
                    ->rows(3),
            ]);
    }
}
