<?php

namespace App\Filament\Resources\RentalCategories;

use App\Filament\Resources\RentalCategories\Pages\CreateRentalCategory;
use App\Filament\Resources\RentalCategories\Pages\EditRentalCategory;
use App\Filament\Resources\RentalCategories\Pages\ListRentalCategories;
use App\Filament\Resources\RentalCategories\Schemas\RentalCategoryForm;
use App\Filament\Resources\RentalCategories\Tables\RentalCategoriesTable;
use App\Models\RentalCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class RentalCategoryResource extends Resource
{
    protected static ?string $model = RentalCategory::class;

    protected static string|BackedEnum|null $navigationIcon = 'tabler-category-plus';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return RentalCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RentalCategoriesTable::configure($table);
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
            'index' => ListRentalCategories::route('/'),
            'create' => CreateRentalCategory::route('/create'),
            'edit' => EditRentalCategory::route('/{record}/edit'),
        ];
    }
}
