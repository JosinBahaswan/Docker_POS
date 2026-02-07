@extends('layouts.app')

@section('title', 'New Transaction')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">New Transaction</h1>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Product List -->
    <div class="lg:col-span-2">
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Products</h2>
            
            <!-- Search Product -->
            <div class="mb-4 flex gap-2">
                <input type="text" id="searchProduct" 
                    placeholder="Search product by name or scan barcode..." 
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <button type="button" id="scanBtn" class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                    📷 Scan
                </button>
            </div>

            <!-- Product Grid -->
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 max-h-[600px] overflow-y-auto" id="productList">
                @foreach($products as $product)
                <div class="border rounded-lg p-3 hover:shadow-lg cursor-pointer transition product-item" 
                    data-id="{{ $product->id }}"
                    data-code="{{ $product->code }}"
                    data-name="{{ $product->name }}"
                    data-price="{{ $product->price }}"
                    data-stock="{{ $product->stock }}">
                    <div class="text-center">
                        <div class="text-xs text-gray-500 mb-1">{{ $product->code }}</div>
                        <div class="font-semibold text-sm mb-2">{{ $product->name }}</div>
                        <div class="text-blue-600 font-bold text-lg mb-1">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                        <div class="text-xs text-gray-500">Stock: {{ $product->stock }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Cart -->
    <div class="lg:col-span-1">
        <div class="bg-white shadow-md rounded-lg p-6 sticky top-4">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Cart</h2>
            
            <div id="cartItems" class="mb-4 max-h-[300px] overflow-y-auto">
                <p class="text-gray-500 text-center py-4">Cart is empty</p>
            </div>

            <div class="border-t pt-4 mb-4">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-gray-700 font-semibold">Total Items:</span>
                    <span class="font-bold" id="totalItems">0</span>
                </div>
                <div class="flex justify-between items-center mb-4">
                    <span class="text-gray-700 font-semibold">Total Price:</span>
                    <span class="text-xl font-bold text-blue-600" id="totalPrice">Rp 0</span>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Payment Method</label>
                <select id="paymentMethod" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="cash">Cash</option>
                    <option value="debit">Debit Card</option>
                    <option value="credit">Credit Card</option>
                    <option value="qris">QRIS</option>
                </select>
            </div>

            <button id="checkoutBtn" class="w-full bg-green-500 hover:bg-green-700 text-white font-bold py-3 px-4 rounded disabled:bg-gray-400 disabled:cursor-not-allowed" disabled>
                Checkout
            </button>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    let cart = [];
    let html5QrCode = null;

    // Search Product
    document.getElementById('searchProduct').addEventListener('input', function(e) {
        const search = e.target.value.toLowerCase();
        document.querySelectorAll('.product-item').forEach(item => {
            const name = item.dataset.name.toLowerCase();
            const code = item.dataset.code.toLowerCase();
            if (name.includes(search) || code.includes(search)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });

    // Add to Cart
    function addProductToCart(product) {
        const existingIndex = cart.findIndex(item => item.product_id === product.product_id);

        if (existingIndex !== -1) {
            if (cart[existingIndex].quantity < product.stock) {
                cart[existingIndex].quantity++;
            } else {
                alert('Stock not available!');
                return;
            }
        } else {
            cart.push(product);
        }

        updateCart();
    }

    document.querySelectorAll('.product-item').forEach(item => {
        item.addEventListener('click', function() {
            const product = {
                product_id: this.dataset.id,
                code: this.dataset.code,
                name: this.dataset.name,
                price: parseFloat(this.dataset.price),
                stock: parseInt(this.dataset.stock),
                quantity: 1
            };

            addProductToCart(product);
        });
    });

    // Update Cart Display
    function updateCart() {
        const cartItems = document.getElementById('cartItems');
        const totalItems = document.getElementById('totalItems');
        const totalPrice = document.getElementById('totalPrice');
        const checkoutBtn = document.getElementById('checkoutBtn');

        if (cart.length === 0) {
            cartItems.innerHTML = '<p class="text-gray-500 text-center py-4">Cart is empty</p>';
            totalItems.textContent = '0';
            totalPrice.textContent = 'Rp 0';
            checkoutBtn.disabled = true;
            return;
        }

        let html = '';
        let total = 0;
        let items = 0;

        cart.forEach((item, index) => {
            const subtotal = item.price * item.quantity;
            total += subtotal;
            items += item.quantity;

            html += `
                <div class="border-b pb-2 mb-2">
                    <div class="flex justify-between items-start mb-1">
                        <div class="flex-1">
                            <div class="font-semibold text-sm">${item.name}</div>
                            <div class="text-xs text-gray-500">${item.code}</div>
                        </div>
                        <button class="text-red-500 hover:text-red-700 text-xs" onclick="removeFromCart(${index})">×</button>
                    </div>
                    <div class="flex justify-between items-center">
                        <div class="flex items-center space-x-2">
                            <button class="bg-gray-200 hover:bg-gray-300 px-2 py-1 rounded text-xs" onclick="decreaseQty(${index})">-</button>
                            <span class="text-sm">${item.quantity}</span>
                            <button class="bg-gray-200 hover:bg-gray-300 px-2 py-1 rounded text-xs" onclick="increaseQty(${index})">+</button>
                        </div>
                        <div class="text-sm font-bold">Rp ${subtotal.toLocaleString('id-ID')}</div>
                    </div>
                </div>
            `;
        });

        cartItems.innerHTML = html;
        totalItems.textContent = items;
        totalPrice.textContent = 'Rp ' + total.toLocaleString('id-ID');
        checkoutBtn.disabled = false;
    }

    // Cart Functions
    function removeFromCart(index) {
        cart.splice(index, 1);
        updateCart();
    }

    function increaseQty(index) {
        if (cart[index].quantity < cart[index].stock) {
            cart[index].quantity++;
            updateCart();
        } else {
            alert('Stock not available!');
        }
    }

    function decreaseQty(index) {
        if (cart[index].quantity > 1) {
            cart[index].quantity--;
            updateCart();
        }
    }

    // Checkout
    document.getElementById('checkoutBtn').addEventListener('click', function() {
        if (cart.length === 0) {
            alert('Cart is empty!');
            return;
        }

        const paymentMethod = document.getElementById('paymentMethod').value;
        
        if (!confirm('Process this transaction?')) {
            return;
        }

        this.disabled = true;
        this.textContent = 'Processing...';

        fetch('{{ route("transactions.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                payment_method: paymentMethod,
                cart: cart
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert('Transaction successful!');
                window.location.href = data.redirect_url;
            } else {
                alert('Transaction failed: ' + (data.message || 'Unknown error'));
                this.disabled = false;
                this.textContent = 'Checkout';
            }
        })
        .catch(error => {
            alert('Transaction error: ' + error.message);
            this.disabled = false;
            this.textContent = 'Checkout';
        });
    });

    // Barcode scanner
    const scannerModal = document.createElement('div');
    scannerModal.id = 'scannerModal';
    scannerModal.className = 'hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
    scannerModal.innerHTML = `
        <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold">Scan Barcode</h3>
                <button id="closeScanner" class="text-gray-600 hover:text-gray-800 text-2xl">&times;</button>
            </div>
            <div id="reader" class="w-full"></div>
            <div id="scanResult" class="mt-4 text-center text-sm text-gray-600"></div>
        </div>
    `;
    document.body.appendChild(scannerModal);

    const scanBtn = document.getElementById('scanBtn');
    const closeScannerBtn = scannerModal.querySelector('#closeScanner');

    scanBtn.addEventListener('click', () => {
        scannerModal.classList.remove('hidden');
        startScanner();
    });

    closeScannerBtn.addEventListener('click', () => {
        stopScanner();
        scannerModal.classList.add('hidden');
    });

    scannerModal.addEventListener('click', function(e) {
        if (e.target === scannerModal) {
            stopScanner();
            scannerModal.classList.add('hidden');
        }
    });

    function startScanner() {
        html5QrCode = new Html5Qrcode('reader');
        const config = { fps: 10, qrbox: { width: 250, height: 250 } };

        html5QrCode.start(
            { facingMode: 'environment' },
            config,
            (decodedText) => {
                document.getElementById('scanResult').innerHTML = `<p class="text-green-600">✓ Scanned: ${decodedText}</p>`;
                fetchProductByCode(decodedText.trim());
            },
            () => {}
        ).catch(err => {
            document.getElementById('scanResult').innerHTML = '<p class="text-red-600">Error: Cannot access camera</p>';
            console.error(err);
        });
    }

    function stopScanner() {
        if (html5QrCode) {
            html5QrCode.stop().then(() => {
                html5QrCode.clear();
            }).catch(err => console.error('Stop scanner error:', err));
        }
    }

    function fetchProductByCode(code) {
        fetch(`{{ url('/transactions/product') }}/${code}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const p = data.product;
                    addProductToCart({
                        product_id: p.id,
                        code: p.code,
                        name: p.name,
                        price: parseFloat(p.price),
                        stock: parseInt(p.stock),
                        quantity: 1
                    });
                    stopScanner();
                    scannerModal.classList.add('hidden');
                } else {
                    alert(data.message || 'Product not found');
                }
            })
            .catch(() => alert('Failed to fetch product'));
    }
</script>
@endsection
