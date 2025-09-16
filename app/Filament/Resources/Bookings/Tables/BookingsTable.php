<?php

namespace App\Filament\Resources\Bookings\Tables;

use App\Mail\BookingInvoice;
use App\Models\Booking;
use Carbon\Carbon;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Midtrans\ApiRequestor;
use Midtrans\Config;
use Midtrans\CoreApi;

class BookingsTable
{
    protected static function canRefund($record): bool
    {
        $now = Carbon::now();
        $endTime = Carbon::parse($record->end_time);
        $createdAt = Carbon::parse($record->start_time);

        // Jika end time sudah terlewati
        if ($now->greaterThan($endTime)) {
            return false;
        }

        // jika sudah lebih dari 2 hari setelah booking mulai
        if ($now->diffInDays($createdAt) > 2) {
            return false;
        }

        // Jika payment status bukan capture/settlement 
        if (!in_array($record->payment->transaction_status, ['capture', 'settlement'])) {
            return false;
        }

        // Hanya support refund untuk metode tertentu
        $refundableTypes = ['credit_card', 'gopay', 'shopeepay', 'qris'];
        if (!in_array($record->payment->payment_type, $refundableTypes)) {
            return false;
        }

        return true;
    }
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
                    ->authorize(fn(Booking $record) => Gate::allows('sendInvoice', $record))
                    ->visible(fn(Booking $record): bool => !in_array($record->status, ['pending', 'cancelled'])),
                Action::make('refund')
                    ->label('')
                    ->icon('heroicon-s-receipt-refund')
                    ->iconSize('lg')
                    ->tooltip(fn($record) => $record->payment->transaction_status === 'capture' ? 'Cancel' : 'Refund')
                    ->requiresConfirmation()
                    ->modalHeading(fn($record) => $record->payment->transaction_status === 'capture' ? 'Cancel Booking' : 'Refund Booking')
                    ->modalDescription('Apakah Anda Yakin?')
                    ->color('danger')
                    ->visible(fn($record) => self::canRefund($record))
                    ->action(function ($record) {
                        $payment = $record->payment;
                        if (!$payment) throw new Exception('Payment Tidak Ditemukan');

                        $refundAmount = (int) round($record->final_price * 0.4);

                        $params = [
                            'refund_key' => 'refund-' . $payment->transaction_id,
                            'amount' => $refundAmount,
                            'reason' => 'Approved by admin with 40% Policy Refund',
                        ];

                        Config::$serverKey = config('midtrans.server_key');
                        Config::$isProduction = config('midtrans.is_production');
                        Config::$isSanitized = config('midtrans.is_sanitized');
                        Config::$is3ds = config('midtrans.is_sanitized');

                        try {
                            if ($payment->transaction_status === 'capture') {
                                // pakai Cancel API
                                $endPoint = Config::getBaseUrl() . '/v2/' . $payment->transaction_id . '/cancel';
                                $response = ApiRequestor::post(
                                    $endPoint,
                                    Config::$serverKey,
                                    []
                                );
                                $record->update([
                                    'status' => 'cancelled'
                                ]);

                                Notification::make()
                                    ->title('Cancel Berhasil')
                                    ->body('Booking Telah Dicancel')
                                    ->success()
                                    ->send();
                            } elseif ($payment->transaction_status === 'settlement') {
                                // pakai Refund API
                                $params = [
                                    'refund_key' => 'refund-' . $payment->transaction_id,
                                    'amount' => (int) round($record->final_price * 0.4),
                                    'reason' => 'Approved by admin with 40% Policy Refund',
                                ];
                                $endPoint = Config::getBaseUrl() . '/v2/' . $payment->transaction_id . '/refund';
                                $response = ApiRequestor::post(
                                    $endPoint,
                                    Config::$serverKey,
                                    $params
                                );
                                $record->update([
                                    'status' => 'cancelled'
                                ]);

                                Notification::make()
                                    ->title('Refund Berhasil')
                                    ->body('Booking Telah Direfund')
                                    ->success()
                                    ->send();
                            }
                        } catch (Exception $e) {
                            Notification::make()
                                ->title('Refund Gagal')
                                ->body('Midtrans Error : ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                EditAction::make()
                    ->label('')
                    ->tooltip('Edit Record')
                    ->iconSize('lg'),
                DeleteAction::make()
                    ->label('')
                    ->tooltip('Delete Record')
                    ->iconSize('lg'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
