<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sales Report {{ $start_date }} - {{ $end_date }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h1 { text-align: center; margin-bottom: 10px; }
        p { margin: 4px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background: #f5f5f5; }
        .text-right { text-align: right; }
        .summary { margin-top: 12px; }
    </style>
</head>
<body>
    <h1>Sales Report</h1>
    <p>Period: {{ $finalStartDate }} - {{ $finalEndDate }}</p>

    <div class="summary">
        <p><strong>Total Revenue:</strong> Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
        <p><strong>Total Transactions:</strong> {{ $transactions->count() }}</p>
        <p><strong>Total Items Sold:</strong> {{ $totalItems }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Invoice</th>
                <th>Date</th>
                <th>Payment</th>
                <th class="text-right">Items</th>
                <th class="text-right">Total (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $trx)
            <tr>
                <td>{{ $trx->invoice_code }}</td>
                <td>{{ $trx->created_at->format('d M Y H:i') }}</td>
                <td>{{ strtoupper($trx->payment_method ?? '-') }}</td>
                <td class="text-right">{{ $trx->details->sum('quantity') }}</td>
                <td class="text-right">{{ number_format($trx->total_price, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
