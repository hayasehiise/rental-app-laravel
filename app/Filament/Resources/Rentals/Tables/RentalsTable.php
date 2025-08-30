<?php

namespace App\Filament\Resources\Rentals\Tables;

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
                    ->label('Tipe'),
                TextColumn::make('units_count')
                    ->label('Total Item')
                    ->counts('units'),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->sortable()
                    ->date('d/m/Y'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Manage & Edit'),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
