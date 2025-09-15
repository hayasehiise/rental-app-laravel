<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use App\Models\RentalCategory;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TransactionStatWidget extends StatsOverviewWidget
{
    protected ?string $pollingInterval = '10s';
    protected function getStats(): array
    {
        $stats = [];

        RentalCategory::with([
            'rentals.units.bookings.payment' => fn($q) => $q->paid()
        ])->get()
            ->each(function ($category) use (&$stats) {
                $total = $category->rentals->sum(fn($rental) => $rental->units->sum(fn($unit) => $unit->bookings->filter(fn($booking) => $booking->payment?->transaction_status === 'capture')->count()));

                $stats[] = Stat::make('Transaksi ' . $category->name, $total);
            });

        return $stats;
    }

    public static function canView(): bool
    {
        $user = auth()->user();
        return $user->can('viewAny', Payment::class);
    }
}
