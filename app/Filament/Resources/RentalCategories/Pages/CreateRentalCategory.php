<?php

namespace App\Filament\Resources\RentalCategories\Pages;

use App\Filament\Resources\RentalCategories\RentalCategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRentalCategory extends CreateRecord
{
    protected static string $resource = RentalCategoryResource::class;

    protected function getRedirectUrl(): string
    {
        return RentalCategoryResource::getUrl('index');
    }
}
