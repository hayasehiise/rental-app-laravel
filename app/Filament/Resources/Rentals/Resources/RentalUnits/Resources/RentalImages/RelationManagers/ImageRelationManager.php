<?php

namespace App\Filament\Resources\Rentals\Resources\RentalUnits\Resources\RentalImages\RelationManagers;

use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use App\Filament\Resources\Rentals\Resources\RentalUnits\Resources\RentalImages\Schemas\RentalImageForm;
use App\Filament\Resources\Rentals\Resources\RentalUnits\Resources\RentalImages\Tables\RentalImagesTable;

class ImageRelationManager extends RelationManager
{
    protected static string $relationship = 'image';

    public function form(Schema $schema): Schema
    {
        return RentalImageForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return RentalImagesTable::configure($table);
    }
}
