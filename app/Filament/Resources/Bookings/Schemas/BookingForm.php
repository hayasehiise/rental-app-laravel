<?php

namespace App\Filament\Resources\Bookings\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BookingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                TextInput::make('rental_unit_id')
                    ->required()
                    ->numeric(),
                DateTimePicker::make('start_time')
                    ->required(),
                DateTimePicker::make('end_time')
                    ->required(),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                TextInput::make('discount')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('final_price')
                    ->required()
                    ->numeric(),
                Select::make('status')
                    ->options(['pending' => 'Pending', 'paid' => 'Paid', 'cancelled' => 'Cancelled'])
                    ->default('pending')
                    ->required(),
            ]);
    }
}
