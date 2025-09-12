<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use Filament\Widgets\Widget;

class DateTimeWidget extends Widget
{
    protected string $view = 'filament.widgets.date-time-widget';

    protected static bool $isLazy = false;

    public string $currentDate;

    public function mount(): void
    {
        $now = Carbon::now('Asia/Makassar');
        $this->currentDate = $now->translatedFormat('l, d F Y');
    }
}
