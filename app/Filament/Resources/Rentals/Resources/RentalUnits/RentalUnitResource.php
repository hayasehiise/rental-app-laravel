<?php

namespace App\Filament\Resources\Rentals\Resources\RentalUnits;

use App\Filament\Resources\Rentals\RentalResource;
use App\Filament\Resources\Rentals\Resources\RentalUnits\Pages\CreateRentalUnit;
use App\Filament\Resources\Rentals\Resources\RentalUnits\Pages\EditRentalUnit;
use App\Filament\Resources\Rentals\Resources\RentalUnits\Resources\RentalImages\RelationManagers\ImageRelationManager;
use App\Filament\Resources\Rentals\Resources\RentalUnits\Resources\RentalImages\RentalImageResource;
use App\Filament\Resources\Rentals\Resources\RentalUnits\Schemas\RentalUnitForm;
use App\Filament\Resources\Rentals\Resources\RentalUnits\Tables\RentalUnitsTable;
use App\Models\RentalUnit;
use BackedEnum;
use Filament\Resources\ParentResourceRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class RentalUnitResource extends Resource
{

    protected static ?string $model = RentalUnit::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function getParentResourceRegistration(): ?ParentResourceRegistration
    {
        return RentalResource::asParent()
            ->relationship('units')
            ->inverseRelationship('rental');
    }

    protected static ?string $relatedResource = RentalImageResource::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return RentalUnitForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RentalUnitsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            'image' => ImageRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'create' => CreateRentalUnit::route('/create'),
            'edit' => EditRentalUnit::route('/{record}/edit'),
        ];
    }
}
