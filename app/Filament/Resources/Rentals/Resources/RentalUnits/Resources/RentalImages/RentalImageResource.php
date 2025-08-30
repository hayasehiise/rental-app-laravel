<?php

namespace App\Filament\Resources\Rentals\Resources\RentalUnits\Resources\RentalImages;

use App\Filament\Resources\Rentals\Resources\RentalUnits\RentalUnitResource;
use App\Filament\Resources\Rentals\Resources\RentalUnits\Resources\RentalImages\Pages\CreateRentalImage;
use App\Filament\Resources\Rentals\Resources\RentalUnits\Resources\RentalImages\Pages\EditRentalImage;
use App\Filament\Resources\Rentals\Resources\RentalUnits\Resources\RentalImages\Schemas\RentalImageForm;
use App\Filament\Resources\Rentals\Resources\RentalUnits\Resources\RentalImages\Tables\RentalImagesTable;
use App\Models\RentalImage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class RentalImageResource extends Resource
{
    protected static ?string $model = RentalImage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $parentResource = RentalUnitResource::class;

    public static function form(Schema $schema): Schema
    {
        return RentalImageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RentalImagesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'create' => CreateRentalImage::route('/create'),
            'edit' => EditRentalImage::route('/{record}/edit'),
        ];
    }
}
