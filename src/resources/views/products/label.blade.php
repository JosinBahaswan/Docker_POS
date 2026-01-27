<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Product Label</title>
    <style>
        body {
            margin: 0;
            padding: 5px;
            font-family: Arial, sans-serif;
        }
        .label {
            width: 100%;
            height: 100%;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .product-name {
            font-size: 14px;
            font-weight: bold;
            margin: 5px 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            width: 90%;
        }
        .product-code {
            font-size: 10px;
            margin: 3px 0;
        }
        .barcode {
            margin: 5px 0;
        }
        .price {
            font-size: 16px;
            font-weight: bold;
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="label">
        <div class="product-name">{{ $product->name }}</div>
        <div class="product-code">{{ $product->code }}</div>
        <div class="barcode">
            <img src="data:image/png;base64,{{ $barcode }}" alt="Barcode">
        </div>
        <div class="price">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
    </div>
</body>
</html>
