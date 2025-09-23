<?php

namespace App\Filament\Resources\Bookings\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
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
                    ->required()
                    ->visible(fn() => auth()->user()->hasRole('admin')),
                Hidden::make('rental_unit_id')
                    ->required(),
                DateTimePicker::make('start_time')
                    ->required(),
                DateTimePicker::make('end_time')
                    ->required(),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('Rp.')
                    ->visible(fn() => auth()->user()->hasRole('admin')),
                TextInput::make('final_price')
                    ->required()
                    ->numeric()
                    ->prefix('Rp.')
                    ->visible(fn() => auth()->user()->hasRole('admin')),
                Select::make('status')
                    ->options(['pending' => 'Pending', 'paid' => 'Paid', 'cancelled' => 'Cancelled'])
                    ->default('pending')
                    ->required()
                    ->visible(fn() => auth()->user()->hasRole('admin')),
            ]);
    }
}
