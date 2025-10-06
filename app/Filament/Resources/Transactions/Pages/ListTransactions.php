<?php

namespace App\Filament\Resources\Transactions\Pages;

use Filament\Actions\CreateAction;
use App\Http\Controllers\Transaction;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Widgets\TransactionPrintWidget;
use App\Filament\Widgets\TransactionStatusWidget;
use App\Filament\Resources\Transactions\TransactionResource;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            TransactionStatusWidget::class,
            TransactionPrintWidget::class,
        ];
    }
}
