<?php

namespace App\Filament\Resources\Rentals\Resources\RentalUnits\Resources\RentalImages\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;

class RentalImageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('path')
                    ->label('Gambar Item')
                    ->image()
                    ->directory('rental-image')
                    ->required(),
            ]);
    }
}
