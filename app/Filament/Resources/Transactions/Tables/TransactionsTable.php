<?php

namespace App\Filament\Resources\Transactions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\IconSize;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('booking.user.name')
                    ->label('Nama Pelanggan')
                    ->searchable(),
                TextColumn::make('booking.unit.name')
                    ->label('Unit Name'),
                TextColumn::make('order_id')
                    ->label('Order ID'),
                TextColumn::make('payment_type')
                    ->label('Tipe Pembayaran'),
                IconColumn::make('transaction_status')
                    ->label('Status Transaksi')
                    ->icon(fn(string $state) => match ($state) {
                        'capture' => 'heroicon-s-check-circle',
                        'pending' => 'heroicon-s-pause-circle',
                        'cancelled' => 'heroicon-s-x-circle'
                    })
                    ->color(fn(string $state) => match ($state) {
                        'capture' => 'success',
                        'pending' => 'warning',
                        'cancelled' => 'danger',
                        default => 'primary'
                    })
                    ->size('2xl')
                    ->tooltip(fn(string $state) => match ($state) {
                        'capture' => 'Paid',
                        'pending' => 'Pending',
                        'cancelled' => 'Cancelled'
                    }),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
