<?php

namespace App\Filament\Resources\RentalUnits\RelationManagers;

use App\Models\Prices\GedungPrice;
use App\Models\Prices\KendaraanPrice;
use App\Models\Prices\LapanganPrice;
use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PricesRelationManager extends RelationManager
{
    protected static string $relationship = 'prices';

    public function form(Schema $schema): Schema
    {
        $unitType = $this->getOwnerRecord()->rental->category->slug;
        return $schema
            ->components([
                //Lapangan
                TextInput::make('guest_price')
                    ->label('Harga Normal')
                    ->numeric()
                    ->prefix('Rp')
                    ->required()
                    ->visible(fn() => $unitType === 'lapangan'),
                TextInput::make('member_price')
                    ->label('Harga Member')
                    ->numeric()
                    ->prefix('Rp')
                    ->required()
                    ->visible(fn() => $unitType === 'lapangan'),
                TextInput::make('membership_quota')
                    ->label('Quota')
                    ->numeric()
                    ->required()
                    ->visible(fn() => $unitType === 'lapangan'),

                //Gedung
                Select::make('type')
                    ->label('Tipe')
                    ->options([
                        'pax' => 'PAX',
                        'Per Hari' => 'DAY'
                    ])
                    ->reactive()
                    ->visible(fn() => $unitType === 'gedung'),
                TextInput::make('pax')
                    ->label('PAX')
                    ->numeric()
                    ->suffix('Person')
                    ->visible(fn(callable $get) => $unitType === 'gedung' && $get('type') === 'pax'),
                TextInput::make('per_day')
                    ->label('Per Hari')
                    ->numeric()
                    ->suffix('Hari')
                    ->visible(fn() => $unitType === 'gedung'),
                TextInput::make('price')
                    ->label('Harga')
                    ->numeric()
                    ->prefix('Rp')
                    ->required()
                    ->visible(fn() => $unitType === 'gedung'),

                //Kendaraan
                TextInput::make('price')
                    ->label('Harga')
                    ->prefix('Rp')
                    ->suffix('/ Hari')
                    ->numeric()
                    ->required()
                    ->visible(fn() => $unitType === 'kendaraan'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('priceable_type')
            ->columns([
                TextColumn::make('priceable_type')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->using(function (array $data, RelationManager $livewire) {
                        $unit = $livewire->getOwnerRecord();
                        $category = $unit->rental->category->slug;

                        // buat record child dulu
                        $priceable = match ($category) {
                            'lapangan' => LapanganPrice::create([
                                'guest_price' => $data['guest_price'],
                                'member_price' => $data['member_price'],
                                'membership_quota' => $data['membership_quota'],
                            ]),

                            'gedung' => GedungPrice::create([
                                'type' => $data['type'],
                                'pax' => $data['pax'] ?? null,
                                'per_day' => $data['per_day'] ?? null,
                                'price' => $data['price'],
                            ]),

                            'kendaraan' => KendaraanPrice::create([
                                'price' => $data['price'],
                            ]),
                        };

                        return $unit->prices()->create([
                            'priceable_type' => $priceable::class,
                            'priceable_id' => $priceable->id,
                        ]);
                    }),
                // AssociateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                // DissociateAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
