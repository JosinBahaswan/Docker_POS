@extends('layouts.app')

@section('title', 'Products')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-3xl font-bold text-gray-800">Products</h1>
    <div class="flex gap-2">
        <a href="{{ route('products.export-pdf') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
            📄 Export PDF
        </a>
        <a href="{{ route('products.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Add Product
        </a>
    </div>
</div>

<div class="mb-4">
    <form action="{{ route('products.index') }}" method="GET" class="flex gap-2" id="searchForm">
        <input type="text" name="search" id="searchInput" value="{{ request('search') }}" 
            placeholder="Search products or scan barcode..." 
            class="shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline flex-1">
        <button type="button" id="scanBtn" class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
            📷 Scan
        </button>
        <button type="submit" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            Search
        </button>
        @if(request('search'))
        <a href="{{ route('products.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
            Clear
        </a>
        @endif
    </form>
</div>

<!-- Barcode Scanner Modal -->
<div id="scannerModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold">Scan Barcode</h3>
            <button id="closeScanner" class="text-gray-600 hover:text-gray-800 text-2xl">&times;</button>
        </div>
        <div id="reader" class="w-full"></div>
        <div id="scanResult" class="mt-4 text-center text-sm text-gray-600"></div>
    </div>
</div>

<div class="bg-white shadow-md rounded-lg overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barcode</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($products as $product)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-16 h-16 object-cover rounded">
                    @else
                        <div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center">
                            <span class="text-gray-400 text-xs">No Image</span>
                        </div>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $product->code }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <img src="{{ route('products.barcode', $product) }}" alt="Barcode" class="h-12">
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $product->name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $product->category->name ?? '-' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $product->stock }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <a href="{{ route('products.print-label', $product) }}" class="text-purple-600 hover:text-purple-900 mr-3" title="Print Label" target="_blank">🏷️ Label</a>
                    <a href="{{ route('products.edit', $product) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                    <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">No products found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $products->links() }}
</div>

<!-- Include html5-qrcode library -->
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    const scanBtn = document.getElementById('scanBtn');
    const scannerModal = document.getElementById('scannerModal');
    const closeScanner = document.getElementById('closeScanner');
    const searchInput = document.getElementById('searchInput');
    const searchForm = document.getElementById('searchForm');
    let html5QrCode = null;

    scanBtn.addEventListener('click', function() {
        scannerModal.classList.remove('hidden');
        startScanner();
    });

    closeScanner.addEventListener('click', function() {
        stopScanner();
        scannerModal.classList.add('hidden');
    });

    function startScanner() {
        html5QrCode = new Html5Qrcode("reader");
        const config = { fps: 10, qrbox: { width: 250, height: 250 } };
        
        html5QrCode.start(
            { facingMode: "environment" },
            config,
            (decodedText, decodedResult) => {
                document.getElementById('scanResult').innerHTML = `<p class="text-green-600">✓ Scanned: ${decodedText}</p>`;
                searchInput.value = decodedText;
                setTimeout(() => {
                    stopScanner();
                    scannerModal.classList.add('hidden');
                    searchForm.submit();
                }, 500);
            },
            (errorMessage) => {
                // Ignore scan errors
            }
        ).catch(err => {
            console.error('Camera error:', err);
            document.getElementById('scanResult').innerHTML = '<p class="text-red-600">Error: Cannot access camera</p>';
        });
    }

    function stopScanner() {
        if (html5QrCode) {
            html5QrCode.stop().then(() => {
                html5QrCode.clear();
            }).catch(err => {
                console.error('Stop scanner error:', err);
            });
        }
    }

    // Close modal when clicking outside
    scannerModal.addEventListener('click', function(e) {
        if (e.target === scannerModal) {
            stopScanner();
            scannerModal.classList.add('hidden');
        }
    });
</script>
@endsection
