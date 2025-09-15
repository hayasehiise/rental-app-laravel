<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BookingStatusStatWidget extends StatsOverviewWidget
{
    protected ?string $pollingInterval = '10s';
    protected function getStats(): array
    {
        $now = Carbon::now();

        $paidActiveBookintCount = Booking::whereHas('payment', function ($query) {
            $query->where('transaction_status', 'capture');
        })
            ->where('status', 'paid')
            ->where('end_time', '>', $now)
            ->count();

        $cancelBookingCount = Booking::where('status', 'cancelled')->count();

        $pendingBookingCount = Booking::where('status', 'pending')->count();

        return [
            Stat::make('Booking Active', $paidActiveBookintCount)
                ->description('Booking yang aktif saat ini'),
            Stat::make('Booking Pending', $pendingBookingCount)
                ->description('Booking yang masih menunggu pembayaran'),
            Stat::make('Booking Cancelled', $cancelBookingCount)
                ->description('Booking yang sudah tercancel'),
        ];
    }
}
