@extends('layouts.app')

@section('title', 'Sales Report')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <h1 class="text-3xl font-bold text-gray-800">Sales Report</h1>
    <div class="flex gap-2">
        <form action="{{ route('reports.print') }}" method="GET" target="_blank">
            <input type="hidden" name="start_date" value="{{ $start_date }}">
            <input type="hidden" name="end_date" value="{{ $end_date }}">
            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded flex items-center gap-2">
                📄 Print / PDF
            </button>
        </form>
    </div>
</div>

<div class="bg-white shadow-md rounded-lg p-6 mb-6">
    <form action="{{ route('reports.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
        <div>
            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
            <input type="date" id="start_date" name="start_date" value="{{ $start_date }}" class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
            <input type="date" id="end_date" name="end_date" value="{{ $end_date }}" class="w-full border rounded px-3 py-2">
        </div>
        <div class="md:col-span-2 flex gap-2">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Filter</button>
            <a href="{{ route('reports.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded">Reset</a>
        </div>
    </form>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white shadow rounded-lg p-4">
        <p class="text-sm text-gray-500">Total Revenue</p>
        <p class="text-2xl font-bold text-blue-600">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
    </div>
    <div class="bg-white shadow rounded-lg p-4">
        <p class="text-sm text-gray-500">Transactions</p>
        <p class="text-2xl font-bold text-green-600">{{ $totalTransactions }}</p>
    </div>
    <div class="bg-white shadow rounded-lg p-4">
        <p class="text-sm text-gray-500">Items Sold</p>
        <p class="text-2xl font-bold text-purple-600">{{ $totalItems }}</p>
    </div>
</div>

<div class="bg-white shadow-md rounded-lg overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($transactions as $trx)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">{{ $trx->invoice_code }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $trx->created_at->format('d M Y H:i') }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ strtoupper($trx->payment_method ?? '-') }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">Rp {{ number_format($trx->total_price, 0, ',', '.') }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ $trx->details->sum('quantity') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-4 text-center text-gray-500">No transactions in this range.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
