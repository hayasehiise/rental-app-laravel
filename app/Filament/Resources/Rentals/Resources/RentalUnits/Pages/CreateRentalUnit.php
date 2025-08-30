<?php

namespace App\Filament\Resources\Rentals\Resources\RentalUnits\Pages;

use App\Filament\Resources\Rentals\RentalResource;
use App\Filament\Resources\Rentals\Resources\RentalUnits\RentalUnitResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRentalUnit extends CreateRecord
{
    protected static string $resource = RentalUnitResource::class;
}
