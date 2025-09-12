<?php

namespace App\Filament\Resources\Bookings\Tables;

use App\Mail\BookingInvoice;
use App\Models\Booking;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;

class BookingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Nama User')
                    ->searchable(),
                TextColumn::make('unit.name')
                    ->label('Unit Rental'),
                TextColumn::make('start_time')
                    ->label('Waktu Mulai')
                    ->dateTime('d-m-Y, H:i')
                    ->sortable(),
                TextColumn::make('end_time')
                    ->label('Waktu Selesai')
                    ->dateTime('d-m-Y, H:i')
                    ->sortable(),
                TextColumn::make('price')
                    ->numeric()
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->sortable(),
                TextColumn::make('discount')
                    ->suffix('%')
                    ->sortable(),
                TextColumn::make('final_price')
                    ->numeric()
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->sortable(),
                // TextColumn::make('status')
                //     ->badge()
                //     ->formatStateUsing(fn(string $state) => strtoupper($state))
                //     ->color(fn($state) => match ($state) {
                //         'paid' => 'success',
                //         'pending' => 'warning',
                //         'cancelled' => 'danger',
                //         default => 'gray'
                //     })
                //     ->icon(fn($state) => match ($state) {
                //         'paid' => 'fas-check-circle',
                //         'pending' => 'fas-pause-circle',
                //         'cancelled' => 'heroicon-s-x-circle',
                //         default => 'heroicon-s-question-mark-circle'
                //     }),
                IconColumn::make('status')
                    ->color(fn($state) => match ($state) {
                        'paid' => 'success',
                        'pending' => 'warning',
                        'cancelled' => 'danger',
                        default => 'gray'
                    })
                    ->icon(fn($state) => match ($state) {
                        'paid' => 'heroicon-s-check-circle',
                        'pending' => 'heroicon-s-pause-circle',
                        'cancelled' => 'heroicon-s-x-circle',
                        default => 'heroicon-s-question-mark-circle'
                    })
                    ->tooltip(fn(string $state) => match ($state) {
                        'paid' => 'Paid',
                        'pending' => 'On Hold',
                        'cancelled' => 'Cancelled',
                    })
                    ->size('2xl'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('sendInvoice')
                    ->label('')
                    ->tooltip('Send Invoice')
                    ->icon('heroicon-o-paper-airplane')
                    ->iconSize('lg')
                    ->requiresConfirmation()
                    ->modalHeading('Kirim Invoice')
                    ->modalDescription('Apakah anda yakin ingin mengirim email invoice?')
                    ->color('info')
                    ->action(function (Booking $record) {
                        Mail::to($record->user->email)
                            ->send(new BookingInvoice($record));
                    })
                    ->successNotificationTitle('Invoice Email Send')
                    ->authorize(fn(Booking $record) => Gate::allows('sendInvoice', $record)),
                EditAction::make()
                    ->label('')
                    ->tooltip('Edit Record')
                    ->iconSize('lg'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
