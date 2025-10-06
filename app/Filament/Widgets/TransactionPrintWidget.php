<?php

namespace App\Filament\Widgets;

use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Widgets\Widget;

class TransactionPrintWidget extends Widget implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.widgets.transaction-print-widget';

    public ?array $data = [];

    // protected function getFormSchema(): array
    // {
    //     return [
    //         Grid::make()
    //             ->schema([
    //                 DatePicker::make('start_date')
    //                     ->label('Dari Tanggal')
    //                     ->required(),
    //                 DatePicker::make('end_date')
    //                     ->label('Sampai Tanggal')
    //                     ->required(),
    //                 Action::make('print')
    //                     ->label('Cetak')
    //                     ->button()
    //                     ->color('info')
    //                     ->action('printTransactions'),
    //             ])
    //             ->statePath('data')
    //             ->columns(2)
    //             ->columnSpanFull(),
    //     ];
    // }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Grid::make()
                    ->schema([
                        DatePicker::make('start_date')
                            ->label('Dari Tanggal')
                            ->required(),
                        DatePicker::make('end_date')
                            ->label('Sampai Tanggal')
                            ->required(),
                        Action::make('print')
                            ->label('Cetak')
                            ->button()
                            ->color('info')
                            ->action('printTransactions'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function printTransactions()
    {
        $data = $this->form->getState();

        if (!$data['start_date'] || !$data['end_date']) {
            Notification::make()
                ->title('Tanggal Belum Dipilih')
                ->danger()
                ->send();

            return;
        }

        return redirect()->route('transaction.print', [
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
        ]);
    }
}
