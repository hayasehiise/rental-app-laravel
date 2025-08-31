<?php

namespace App\Filament\Resources\RentalUnitImages;

use BackedEnum;
use Filament\Tables\Table;
use App\Models\RentalImage;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use App\Filament\Resources\RentalUnitImages\Pages\EditRentalUnitImage;
use App\Filament\Resources\RentalUnitImages\Pages\ListRentalUnitImages;
use App\Filament\Resources\RentalUnitImages\Pages\CreateRentalUnitImage;
use App\Filament\Resources\RentalUnitImages\Schemas\RentalUnitImageForm;
use App\Filament\Resources\RentalUnitImages\Tables\RentalUnitImagesTable;
use Illuminate\Database\Eloquent\Builder;

class RentalUnitImageResource extends Resource
{
    protected static ?string $model = RentalImage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static bool $shouldRegisterNavigation = false;

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if ($rentalUnitId = request()->query('rental_unit_id')) {
            return $query->where('rental_unit_id', $rentalUnitId);
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return RentalUnitImageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RentalUnitImagesTable::configure($table);
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
            'index' => ListRentalUnitImages::route('/'),
            'create' => CreateRentalUnitImage::route('/create'),
            'edit' => EditRentalUnitImage::route('/{record}/edit'),
        ];
    }
}
