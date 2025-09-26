<?php

namespace App\Filament\Resources\RentalUnitPrices;

use App\Filament\Resources\RentalUnitPrices\Pages\CreateRentalUnitPrice;
use App\Filament\Resources\RentalUnitPrices\Pages\EditRentalUnitPrice;
use App\Filament\Resources\RentalUnitPrices\Pages\ListRentalUnitPrices;
use App\Filament\Resources\RentalUnitPrices\Schemas\RentalUnitPriceForm;
use App\Filament\Resources\RentalUnitPrices\Tables\RentalUnitPricesTable;
use App\Filament\Resources\RentalUnits\RentalUnitResource;
use App\Models\RentalUnitPrice;
use BackedEnum;
use Filament\Resources\ParentResourceRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class RentalUnitPriceResource extends Resource
{
    protected static ?string $model = RentalUnitPrice::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'type';

    public static function form(Schema $schema): Schema
    {
        return RentalUnitPriceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RentalUnitPricesTable::configure($table);
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
            'index' => ListRentalUnitPrices::route('/'),
            'create' => CreateRentalUnitPrice::route('/create'),
            'edit' => EditRentalUnitPrice::route('/{record}/edit'),
        ];
    }

    public static function getParentResourceRegistration(): ?ParentResourceRegistration
    {
        return RentalUnitResource::asParent()
            ->relationship('prices')
            ->inverseRelationship('unit');
    }
}
