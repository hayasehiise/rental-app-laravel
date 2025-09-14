<?php

namespace App\Filament\Resources\RentalCategories\Pages;

use App\Filament\Resources\RentalCategories\RentalCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRentalCategory extends EditRecord
{
    protected static string $resource = RentalCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
