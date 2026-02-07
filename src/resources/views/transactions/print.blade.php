<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - {{ $transaction->invoice_code }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Courier New', monospace;
            width: 80mm;
            margin: 0 auto;
            padding: 10mm;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px dashed #000;
            padding-bottom: 10px;
        }
        .header h1 {
            font-size: 18px;
            margin-bottom: 5px;
        }
        .header p {
            font-size: 10px;
            margin: 2px 0;
        }
        .info {
            margin-bottom: 15px;
            font-size: 11px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th {
            border-bottom: 1px solid #000;
            padding: 5px 0;
            text-align: left;
            font-size: 11px;
        }
        td {
            padding: 5px 0;
            font-size: 11px;
        }
        .item-name {
            max-width: 120px;
            word-wrap: break-word;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .total-section {
            border-top: 1px solid #000;
            border-bottom: 2px dashed #000;
            padding: 10px 0;
            margin-bottom: 15px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
            font-size: 12px;
        }
        .grand-total {
            font-weight: bold;
            font-size: 14px;
            margin-top: 5px;
        }
        .footer {
            text-align: center;
            font-size: 11px;
            margin-top: 15px;
        }
        .footer p {
            margin: 3px 0;
        }
        @media print {
            body {
                width: 80mm;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>POS INVENTORY</h1>
        <p>Jl. Contoh No. 123, Jakarta</p>
        <p>Telp: (021) 12345678</p>
    </div>

    <div class="info">
        <div class="info-row">
            <span>Invoice:</span>
            <strong>{{ $transaction->invoice_code }}</strong>
        </div>
        <div class="info-row">
            <span>Date:</span>
            <span>{{ $transaction->created_at->format('d/m/Y H:i') }}</span>
        </div>
        <div class="info-row">
            <span>Payment:</span>
            <span>{{ strtoupper($transaction->payment_method) }}</span>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th class="text-center">Qty</th>
                <th class="text-right">Price</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaction->details as $detail)
            <tr>
                <td class="item-name">
                    <div>{{ $detail->product->name ?? 'Unknown' }}</div>
                    <div style="font-size: 9px; color: #666;">{{ $detail->product_code }}</div>
                </td>
                <td class="text-center">{{ $detail->quantity }}</td>
                <td class="text-right">{{ number_format($detail->subtotal / $detail->quantity, 0) }}</td>
                <td class="text-right">{{ number_format($detail->subtotal, 0) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-section">
        <div class="total-row">
            <span>Subtotal:</span>
            <span>Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</span>
        </div>
        <div class="total-row grand-total">
            <span>TOTAL:</span>
            <span>Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</span>
        </div>
    </div>

    <div class="footer">
        <p>Thank you for your purchase!</p>
        <p>Please come again</p>
        <p style="margin-top: 10px; font-size: 10px;">
            {{ $transaction->created_at->format('d/m/Y H:i:s') }}
        </p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
