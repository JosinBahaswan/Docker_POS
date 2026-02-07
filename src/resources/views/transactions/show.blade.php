@extends('layouts.app')

@section('title', 'Transaction Detail')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-3xl font-bold text-gray-800">Transaction Detail</h1>
    <div class="space-x-2">
        <a href="{{ route('transactions.print', $transaction->invoice_code) }}" 
            class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded" target="_blank">
            Print Receipt
        </a>
        <a href="{{ route('transactions.index') }}" 
            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            Back to History
        </a>
    </div>
</div>

<div class="bg-white shadow-md rounded-lg p-6">
    <!-- Transaction Info -->
    <div class="grid grid-cols-2 gap-6 mb-6 pb-6 border-b">
        <div>
            <h2 class="text-xl font-bold text-gray-800 mb-4">Transaction Information</h2>
            <div class="space-y-2">
                <div class="flex">
                    <span class="text-gray-600 w-32">Invoice Code:</span>
                    <span class="font-semibold">{{ $transaction->invoice_code }}</span>
                </div>
                <div class="flex">
                    <span class="text-gray-600 w-32">Date:</span>
                    <span class="font-semibold">{{ $transaction->created_at->format('d M Y H:i') }}</span>
                </div>
                <div class="flex">
                    <span class="text-gray-600 w-32">Payment Method:</span>
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                        {{ $transaction->payment_method == 'cash' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                        {{ ucfirst($transaction->payment_method) }}
                    </span>
                </div>
            </div>
        </div>
        <div class="text-right">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Total</h2>
            <div class="text-4xl font-bold text-blue-600">
                Rp {{ number_format($transaction->total_price, 0, ',', '.') }}
            </div>
        </div>
    </div>

    <!-- Transaction Details -->
    <div>
        <h2 class="text-xl font-bold text-gray-800 mb-4">Items</h2>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product Code</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product Name</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($transaction->details as $detail)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $detail->product_code }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $detail->product->name ?? 'Unknown Product' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                        {{ $detail->quantity }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                        Rp {{ number_format($detail->subtotal / $detail->quantity, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 text-right">
                        Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                    </td>
                </tr>
                @endforeach
                <tr class="bg-gray-50">
                    <td colspan="4" class="px-6 py-4 text-right font-bold text-gray-900">
                        Grand Total:
                    </td>
                    <td class="px-6 py-4 text-right font-bold text-blue-600 text-lg">
                        Rp {{ number_format($transaction->total_price, 0, ',', '.') }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
