<?php

namespace App\Filament\Resources\RentalUnits;

use BackedEnum;
use App\Models\RentalUnit;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\RentalUnits\Pages\EditRentalUnit;
use App\Filament\Resources\RentalUnits\Pages\ListRentalUnits;
use App\Filament\Resources\RentalUnits\Pages\CreateRentalUnit;
use App\Filament\Resources\RentalUnits\Schemas\RentalUnitForm;
use App\Filament\Resources\RentalUnits\Tables\RentalUnitsTable;
use App\Filament\Resources\RentalUnits\RelationManagers\PricesRelationManager;

class RentalUnitResource extends Resource
{
    protected static ?string $model = RentalUnit::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if ($rentalId = request()->query('rental_id')) {
            return $query->where('rental_id', $rentalId);
        }

        return $query;
    }

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
            PricesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRentalUnits::route('/'),
            'create' => CreateRentalUnit::route('/create'),
            'edit' => EditRentalUnit::route('/{record}/edit'),
        ];
    }
}
