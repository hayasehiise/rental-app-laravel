<?php

namespace App\Filament\Resources\RentalUnits\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class KendaraanPriceRelationManager extends RelationManager
{
    protected static string $relationship = 'kendaraanPrice';

    public static function  canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return $ownerRecord->rental->category->slug === 'kendaraan';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('price')
                    ->label('Harga')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->suffix('/ Hari'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            // ->recordTitleAttribute('price')
            ->columns([
                TextColumn::make('price')
                    ->label('Harga')
                    ->getStateUsing(fn($record) => 'Rp.' . number_format($record->price, 0, ',', '.')),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Input Harga'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
