<?php

namespace App\Filament\Resources\RentalUnits\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class GedungPriceRelationManager extends RelationManager
{
    protected static string $relationship = 'gedungPrice';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return $ownerRecord->rental->category->slug === 'gedung';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('type')
                    ->label('Tipe')
                    ->options([
                        'pax' => 'PAX',
                        'day' => 'DAY',
                    ])
                    ->reactive()
                    ->required(),
                TextInput::make('pax')
                    ->label('PAX')
                    ->numeric()
                    ->suffix('Person')
                    ->visible(fn($get) => $get('type') === 'pax'),
                TextInput::make('per_day')
                    ->label('Hari')
                    ->numeric()
                    ->suffix('Hari')
                    ->required(),
                TextInput::make('price')
                    ->label('Harga')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            // ->recordTitleAttribute('price')
            ->columns([
                TextColumn::make('type')
                    ->label('Tipe')
                    ->getStateUsing(fn($record) => $record->type === 'pax' ? 'PAX' : 'DAY'),
                TextColumn::make('pax')
                    ->label('PAX')
                    ->getStateUsing(fn($record) => $record->type === 'pax' ? $record->pax . ' Person' : '-'),
                TextColumn::make('per_days')
                    ->label('Hari')
                    ->getStateUsing(fn($record) => $record->per_day . ' Hari'),
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
