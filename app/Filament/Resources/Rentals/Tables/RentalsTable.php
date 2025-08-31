<?php

namespace App\Filament\Resources\Rentals\Tables;

use App\Filament\Resources\RentalUnits\RentalUnitResource;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RentalsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Rental')
                    ->searchable(),
                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color('info')
                    ->icon(fn(string $state) => match ($state) {
                        'lapangan' => 'tabler-building-stadium',
                        'gedung' => 'tabler-building',
                        'kendaraan' => 'tabler-car',
                        default     => 'heroicon-o-question-mark-circle',
                    })
                    ->formatStateUsing(fn(string $state) => $state ? ucwords($state) : '-'),
                TextColumn::make('created_at')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('manageUnits')
                    ->label('Manage Units')
                    ->icon('heroicon-o-building-office-2')
                    ->url(fn($record) => RentalUnitResource::getUrl('index', [
                        'rental_id' => $record->id,
                    ])),
                EditAction::make()
                    ->label('')
                    ->button(),
                DeleteAction::make()
                    ->label('')
                    ->button(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
