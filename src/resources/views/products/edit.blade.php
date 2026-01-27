@extends('layouts.app')

@section('title', 'Edit Product')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Edit Product</h1>
</div>

<div class="bg-white shadow-md rounded-lg p-6">
    <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="mb-4">
            <label for="code" class="block text-gray-700 text-sm font-bold mb-2">Product Code</label>
            <input type="text" name="code" id="code" value="{{ old('code', $product->code) }}" 
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('code') border-red-500 @enderror" 
                required>
            @error('code')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
            <!-- Display Barcode -->
            <div class="mt-2">
                <img src="{{ route('products.barcode', $product) }}" alt="Barcode" class="border p-2 bg-white">
            </div>
        </div>

        <div class="mb-4">
            <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Product Name</label>
            <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" 
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('name') border-red-500 @enderror" 
                required>
            @error('name')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="category_slug" class="block text-gray-700 text-sm font-bold mb-2">Category</label>
            <select name="category_slug" id="category_slug" 
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('category_slug') border-red-500 @enderror" 
                required>
                <option value="">Select Category</option>
                @foreach($categories as $category)
                    <option value="{{ $category->slug }}" {{ old('category_slug', $product->category_slug) == $category->slug ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('category_slug')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="price" class="block text-gray-700 text-sm font-bold mb-2">Price (Rp)</label>
            <input type="number" name="price" id="price" value="{{ old('price', $product->price) }}" min="0" 
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('price') border-red-500 @enderror" 
                required>
            @error('price')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="stock" class="block text-gray-700 text-sm font-bold mb-2">Stock</label>
            <input type="number" name="stock" id="stock" value="{{ old('stock', $product->stock) }}" min="0" 
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('stock') border-red-500 @enderror" 
                required>
            @error('stock')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="image" class="block text-gray-700 text-sm font-bold mb-2">Product Image</label>
            @if($product->image)
                <div class="mb-2">
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-32 h-32 object-cover rounded">
                    <p class="text-xs text-gray-500 mt-1">Current image</p>
                </div>
            @endif
            <input type="file" name="image" id="image" accept="image/*" 
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('image') border-red-500 @enderror">
            <p class="text-xs text-gray-500 mt-1">Leave empty to keep current image</p>
            @error('image')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Description</label>
            <textarea name="description" id="description" rows="4" 
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('description') border-red-500 @enderror">{{ old('description', $product->description) }}</textarea>
            @error('description')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Update Product
            </button>
            <a href="{{ route('products.index') }}" class="text-gray-600 hover:text-gray-800">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
