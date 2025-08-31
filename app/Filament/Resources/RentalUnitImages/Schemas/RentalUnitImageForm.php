<?php

namespace App\Filament\Resources\RentalUnitImages\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Schema;

class RentalUnitImageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('rental_unit_id')
                    ->default(fn() => request()->query('rental_unit_id'))
                    ->required(),
                FileUpload::make('path')
                    ->label('Foto Unit')
                    ->disk('public')
                    ->directory('unit-image')
                    ->visibility('public')
                    ->image()
                    ->imagePreviewHeight('200')
                    ->openable()
                    ->downloadable(),
            ]);
    }
}
