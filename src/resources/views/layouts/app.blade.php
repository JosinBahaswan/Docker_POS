<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'POS Inventory App')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex space-x-7">
                    <div>
                        <a href="/" class="flex items-center py-2 px-2">
                            <span class="font-semibold text-gray-800 text-lg">POS Inventory</span>
                        </a>
                    </div>
                    <div class="flex items-center space-x-1">
                        <a href="{{ route('categories.index') }}" class="py-2 px-4 text-gray-700 hover:text-blue-500 transition duration-300 {{ request()->routeIs('categories.*') ? 'text-blue-500 font-semibold' : '' }}">Categories</a>
                        <a href="{{ route('products.index') }}" class="py-2 px-4 text-gray-700 hover:text-blue-500 transition duration-300 {{ request()->routeIs('products.*') ? 'text-blue-500 font-semibold' : '' }}">Products</a>
                        <a href="{{ route('transactions.index') }}" class="py-2 px-4 text-gray-700 hover:text-blue-500 transition duration-300 {{ request()->routeIs('transactions.*') ? 'text-blue-500 font-semibold' : '' }}">Transactions</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 py-8">
        @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>
