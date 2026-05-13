<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Transaksi - Fundlink</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #eee;
            padding-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #111;
        }
        .header p {
            margin: 5px 0 0;
            color: #666;
        }
        .filters {
            margin-bottom: 20px;
            padding: 10px;
            background: #f9f9f9;
            border-radius: 5px;
        }
        .filters table {
            width: 100%;
        }
        .filters td {
            padding: 5px;
        }
        .filters .label {
            font-weight: bold;
            color: #999;
            text-transform: uppercase;
            font-size: 10px;
        }
        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table.data th {
            text-align: left;
            background: #f5f5f5;
            padding: 10px;
            border-bottom: 1px solid #ddd;
            font-size: 10px;
            text-transform: uppercase;
        }
        table.data td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .amount {
            text-align: right;
            font-weight: bold;
        }
        .pemasukan {
            color: #2563eb;
        }
        .pengeluaran {
            color: #e11d48;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            color: #999;
        }
        .summary {
            margin-top: 20px;
            text-align: right;
        }
        .summary table {
            float: right;
            width: 250px;
        }
        .summary td {
            padding: 5px 10px;
        }
        .summary .total-label {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>RIWAYAT TRANSAKSI</h1>
        <p>Fundlink Financial Management System</p>
    </div>

    <div class="filters">
        <table>
            <tr>
                <td>
                    <span class="label">Rentang:</span><br>
                    <strong>{{ $range }}</strong>
                </td>
                <td>
                    <span class="label">Frekuensi:</span><br>
                    <strong>{{ $frequency }}</strong>
                </td>
                <td>
                    <span class="label">Unit/Cabang:</span><br>
                    <strong>{{ $unit_name ?? 'Semua Cabang' }}</strong>
                </td>
            </tr>
        </table>
    </div>

    <table class="data">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Kategori</th>
                <th>Unit</th>
                <th>Tipe</th>
                <th class="amount">Nominal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->transaction_date->format('d M Y') }}</td>
                    <td>{{ $transaction->category }}</td>
                    <td>{{ $transaction->unit->name }}</td>
                    <td>{{ ucfirst($transaction->type) }}</td>
                    <td class="amount {{ $transaction->type === 'pemasukan' ? 'pemasukan' : 'pengeluaran' }}">
                        {{ $transaction->type === 'pemasukan' ? '+' : '-' }} Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <table>
            <tr>
                <td>Total Pemasukan:</td>
                <td class="amount pemasukan">Rp {{ number_format($total_pemasukan, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Pengeluaran:</td>
                <td class="amount pengeluaran">Rp {{ number_format($total_pengeluaran, 0, ',', '.') }}</td>
            </tr>
            <tr class="total-label">
                <td style="border-top: 1px solid #ddd;">Saldo Akhir:</td>
                <td class="amount" style="border-top: 1px solid #ddd;">Rp {{ number_format($total_pemasukan - $total_pengeluaran, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Dicetak pada: {{ now()->format('d M Y H:i') }}
    </div>
</body>
</html>
