<?php

namespace App\Actions\Admin;

use Carbon\Carbon;
use App\Models\Payment;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Lorisleiva\Actions\Concerns\AsAction;

class PrintTransactionAction
{
    use AsAction;

    public function handle(string $startDate, string $endDate)
    {
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        $buildQuery = function () use ($start, $end) {
            return Payment::with(['booking.unit', 'booking.user'])
                ->whereBetween('created_at', [$start, $end]);
        };

        $successStatues = ['capture', 'settlement'];
        $failedStatues = ['deny', 'cancelled', 'expire'];
        $pendingStatues = ['pending'];

        $success = $buildQuery()->whereIn('transaction_status', $successStatues)->orderBy('created_at', 'desc')->get();
        $failed = $buildQuery()->whereIn('transaction_status', $failedStatues)->orderBy('created_at', 'desc')->get();
        $pending = $buildQuery()->whereIn('transaction_status', $pendingStatues)->orderBy('created_at', 'desc')->get();

        $totals = [
            'success' => $success->sum(fn($p) => $p->booking->final_price),
            'failed' => $failed->sum('gross_amount'),
            'pending' => $pending->sum('gross_amount'),
        ];

        return Pdf::loadView('pdf.transactions', [
            'start' => $start,
            'end' => $end,
            'success' => $success,
            'failed' => $failed,
            'pending' => $pending,
            'totals' => $totals,
        ]);
    }

    public function asController(Request $request)
    {
        $data = $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date'],
        ]);

        $pdf = $this->handle($data['start_date'], $data['end_date']);

        return $pdf->stream('Laporan Transaksi ' . now()->format('Y-m-d_H-i-s') . '.pdf');
    }
}
