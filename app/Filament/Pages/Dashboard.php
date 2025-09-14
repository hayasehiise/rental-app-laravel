<?php

namespace App\Filament\Pages;

// use Filament\Pages\Page;

use App\Filament\Widgets\DateTimeWidget;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets\AccountWidget;

class Dashboard extends BaseDashboard
{
    // protected string $view = 'filament.pages.dashboard';
    protected static ?string $title = 'Dashboard';

    protected ?string $heading = 'Dashboard Page';

    protected static ?string $navigationLabel = 'Dashboard';

    public function getWidgets(): array
    {
        return [
            //
        ];
    }

    public function getColumns(): int|array
    {
        return [
            //
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            DateTimeWidget::class,
            AccountWidget::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return [
            'lg' => 2,
            'md' => 2,
            '' => 1,
        ];
    }
}
