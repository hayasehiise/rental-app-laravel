<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TransactionStatusWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $paidCount = Payment::whereIn('transaction_status', ['capture', 'settlement', 'success'])->count();
        $pendingCount = Payment::where('transaction_status', 'pending')->count();
        $failedCount = Payment::whereIn('transaction_status', ['expire', 'deny', 'cancelled'])->count();
        return [
            Stat::make('Paid Transaction', $paidCount)
            ->description('Transaksi yang lunas/terbayarkan'),
            Stat::make('Pending Transaction', $pendingCount)
            ->description('Transaksi yang on-hold'),
            Stat::make('Failed Transaction', $failedCount)
            ->description('Transaksi yang terbatalkan'),
        ];
    }
}
