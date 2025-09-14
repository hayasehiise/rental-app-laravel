<?php

namespace App\Filament\Resources\RentalCategories\Pages;

use App\Filament\Resources\RentalCategories\RentalCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRentalCategories extends ListRecords
{
    protected static string $resource = RentalCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
