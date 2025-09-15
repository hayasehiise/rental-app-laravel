<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\RentalCategory;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BookingStatWidget extends StatsOverviewWidget
{
    protected ?string $pollingInterval = '10s';
    protected function getStats(): array
    {
        $stats = [];
        $icons = [
            'Lapangan' => 'tabler-building-stadium',
            'Gedung' => 'tabler-building',
            'Kendaraan' => 'tabler-car'
        ];

        // Preload relasi untuk semua kategori
        RentalCategory::with(['rentals.units.bookings'])->get()
            ->each(function ($category) use (&$stats, $icons) {
                // Hitung total booking per kategori
                $total = $category->rentals->sum(function ($rental) {
                    return $rental->units->sum(fn($unit) => $unit->bookings->filter->isPaid()->count());
                });

                $stats[] = Stat::make('Booking ' . $category->name, $total)
                    ->icon($icons[$category->name]);
            });

        $stats[] = Stat::make('Total Booking', Booking::where('status', 'paid')->count());

        return $stats;
    }

    public static function canView(): bool
    {
        $user = auth()->user();
        return $user->can('viewAny', Booking::class);
    }
}
