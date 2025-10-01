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

class LapanganPriceRelationManager extends RelationManager
{
    protected static string $relationship = 'lapanganPrice';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return  $ownerRecord->rental->category->slug === 'lapangan';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // TextInput::make('guest_price')
                //     ->required()
                //     ->maxLength(255),
                TextInput::make('guest_price')
                    ->label('Harga Perjam')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),
                TextInput::make('member_price')
                    ->label('Harga Member')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),
                TextInput::make('member_quota')
                    ->label('Quota')
                    ->numeric()
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            // ->recordTitleAttribute('guest_price')
            ->columns([
                TextColumn::make('guest_price')
                    ->label('Harga Perjam')
                    ->getStateUsing(fn($record) =>  'Rp' . number_format($record->guest_price, 0, ',', '.')),
                TextColumn::make('member_price')
                    ->label('Harga Member')
                    ->getStateUsing(fn($record) =>  'Rp' . number_format($record->member_price, 0, ',', '.')),
                TextColumn::make('member_quota')
                    ->label('Quota Member'),
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
