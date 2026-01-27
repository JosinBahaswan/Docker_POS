<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Products List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .text-right {
            text-align: right;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <h1>Products List</h1>
    <p>Generated on: {{ date('d F Y H:i:s') }}</p>
    
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Code</th>
                <th>Product Name</th>
                <th>Category</th>
                <th class="text-right">Price</th>
                <th class="text-right">Stock</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $index => $product)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $product->code }}</td>
                <td>{{ $product->name }}</td>
                <td>{{ $product->category->name ?? '-' }}</td>
                <td class="text-right">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                <td class="text-right">{{ $product->stock }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="footer">
        <p>POS Inventory System - © {{ date('Y') }}</p>
    </div>
</body>
</html>
