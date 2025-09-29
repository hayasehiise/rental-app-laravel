<?php

namespace App\Filament\Resources\RentalUnits\Tables;

use App\Filament\Resources\RentalUnitImages\RentalUnitImageResource;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RentalUnitsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Unit')
                    ->searchable(),
                IconColumn::make('is_available')
                    ->label('Tersedia')
                    ->icon(fn(bool $state) => match ($state) {
                        true => 'tabler-circle-check-f',
                        false => 'tabler-circle-x-f'
                    })
                    ->color(fn(bool $state) => $state ? 'success' : 'danger'),
            ])
            ->filters([
                //
            ])
            ->recordUrl(fn($record) => RentalUnitImageResource::getUrl('index', [
                'rental_unit_id' => $record->id
            ]))
            ->recordActions([
                Action::make('manageImages')
                    ->label('Manage Images')
                    ->icon('tabler-picture-in-picture-f')
                    ->url(fn($record) => RentalUnitImageResource::getUrl('index', [
                        'rental_unit_id' => $record->id
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
