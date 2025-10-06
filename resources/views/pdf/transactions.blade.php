<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Transaksi</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #111
        }

        .header {
            text-align: center;
            margin-bottom: 8px;
        }

        .meta {
            margin-bottom: 18px;
            text-align: center;
            font-size: 11px;
            color: #444;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }

        th,
        td {
            border: 1px solid #444;
            padding: 6px 8px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background: #f3f3f3;
        }

        tfoot td {
            font-weight: bold;
            background: #fafafa;
        }

        .right {
            text-align: right;
        }

        h3 {
            margin: 12px 0 6px 0;
            font-size: 14px;
        }

        .summary {
            width: 40%;
            margin-top: 6px;
            float: right;
        }

        .small {
            font-size: 11px;
            color: #666
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>Laporan Transaksi</h2>
    </div>
    <div class="meta">
        Periode: <strong>{{ $start->format('d M Y') }}</strong> — <strong>{{ $end->format('d M Y') }}</strong>
        <br>
        Dibuat: {{ now()->format('d M Y H:i') }}
    </div>

    {{-- SUCCESS --}}
    <h3>Transaksi Sukses (capture / settlement)</h3>
    @if ($success->count())
        <table>
            <thead>
                <tr>
                    <th style="width:4%">No</th>
                    <th style="width:12%">Tanggal</th>
                    <th>Order ID</th>
                    <th>Nama Pelanggan</th>
                    <th>Unit</th>
                    <th>Tipe Pembayaran</th>
                    <th class="right" style="width:12%">Nominal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($success as $i => $tx)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ optional($tx->created_at)->format('d/m/Y H:i') }}</td>
                        <td>{{ $tx->order_id }}</td>
                        <td>{{ $tx->booking->user->name ?? '-' }}</td>
                        <td>{{ $tx->booking->unit->name ?? '-' }}</td>
                        <td>{{ $tx->payment_type }}</td>
                        <td class="right">Rp {{ number_format($tx->booking->final_price, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6" class="right">Total</td>
                    <td class="right">Rp {{ number_format($totals['success'], 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    @else
        <p class="small">Tidak ada transaksi sukses pada periode ini.</p>
    @endif

    {{-- FAILED --}}
    <h3>Transaksi Gagal / Dibatalkan (cancelled / deny / expire)</h3>
    @if ($failed->count())
        <table>
            <thead>
                <tr>
                    <th style="width:4%">No</th>
                    <th style="width:12%">Tanggal</th>
                    <th>Order ID</th>
                    <th>Nama Pelanggan</th>
                    <th>Unit</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($failed as $i => $tx)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ optional($tx->created_at)->format('d/m/Y H:i') }}</td>
                        <td>{{ $tx->order_id }}</td>
                        <td>{{ $tx->booking->user->name ?? '-' }}</td>
                        <td>{{ $tx->booking->unit->name ?? '-' }}</td>
                        <td>{{ ucfirst($tx->transaction_status) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="small">Tidak ada transaksi gagal pada periode ini.</p>
    @endif

    {{-- PENDING --}}
    <h3>Transaksi Pending</h3>
    @if ($pending->count())
        <table>
            <thead>
                <tr>
                    <th style="width:4%">No</th>
                    <th style="width:12%">Tanggal</th>
                    <th>Order ID</th>
                    <th>Nama Pelanggan</th>
                    <th>Unit</th>
                    <th class="right" style="width:12%">Nominal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pending as $i => $tx)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ optional($tx->created_at)->format('d/m/Y H:i') }}</td>
                        <td>{{ $tx->order_id }}</td>
                        <td>{{ $tx->booking->user->name ?? '-' }}</td>
                        <td>{{ $tx->booking->unit->name ?? '-' }}</td>
                        <td class="right">Rp {{ number_format($tx->booking->final_price, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" class="right">Total</td>
                    <td class="right">Rp {{ number_format($totals['pending'], 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    @else
        <p class="small">Tidak ada transaksi pending pada periode ini.</p>
    @endif

    {{-- RINGKASAN --}}
    <div style="clear:both;"></div>
    <h3>Ringkasan</h3>
    <table style="width:50%">
        <tbody>
            <tr>
                <td>Total Sukses</td>
                <td class="right">Rp {{ number_format($totals['success'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Gagal</td>
                <td class="right">Rp {{ number_format($totals['failed'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Pending</td>
                <td class="right">Rp {{ number_format($totals['pending'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>Grand Total</strong></td>
                <td class="right"><strong>Rp
                        {{ number_format($totals['success'] + $totals['failed'] + $totals['pending'], 0, ',', '.') }}</strong>
                </td>
            </tr>
        </tbody>
    </table>

</body>

</html>
