<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Barryvdh\DomPDF\Facade\Pdf;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $products = Product::latest();
        
        if ($request->has('search')) {
            $search = $request->input('search');
            $products->where('name', 'like', '%' . $search . '%')
                    ->orWhere('code', 'like', '%' . $search . '%');
        }
        $products = $products->paginate(50);
        return view('products.index', [
            'products' => $products
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('products.create', [
            'categories' => Category::latest()->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //validasi
        $validated = $request->validate([
            'code' => 'required|string|max:100|unique:products,code',
            'name' => 'required|string|max:255',
            'category_slug' => 'required|string|exists:categories,slug',
            'price' => 'required|integer|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpg,svg,png|max:2048',
            'description' => 'nullable|string',
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $validated['image'] = $imagePath;
        }
        //lakuin logika insert
        Product::create($validated);
        //redirect
        return redirect()->route('products.index')->with('success', 'Product created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        return view('products.edit', [
            'product' => $product,
            'categories' => Category::latest()->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        //validasi
        $validated = $request->validate([
            'code' => 'required|string|max:100|unique:products,code,' . $product->id,
            'name' => 'required|string|max:255',
            'category_slug' => 'required|string|exists:categories,slug',
            'price' => 'required|integer|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpg,svg,png|max:2048',
            'description' => 'nullable|string',
        ]);

        if ($request->hasFile('image')) {
            //hapus foto lama
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $imagePath = $request->file('image')->store('products', 'public');
            $validated['image'] = $imagePath;
        }
        //lakuin logika update
        $product->update($validated);
        //redirect
        return redirect()->route('products.index')->with('success', 'Product updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //hapus foto lama
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted successfully');
    }

    /**
     * Generate barcode for product
     */
    public function barcode(Product $product)
    {
        $generator = new BarcodeGeneratorPNG();
        $barcode = $generator->getBarcode($product->code, $generator::TYPE_CODE_128, 3, 50);
        
        return response($barcode)
            ->header('Content-Type', 'image/png');
    }

    /**
     * Export all products to PDF
     */
    public function exportPdf()
    {
        $products = Product::with('category')->latest()->get();
        
        $pdf = Pdf::loadView('products.pdf', [
            'products' => $products
        ]);
        
        return $pdf->download('products-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Print single product label with barcode
     */
    public function printLabel(Product $product)
    {
        $generator = new BarcodeGeneratorPNG();
        $barcode = base64_encode($generator->getBarcode($product->code, $generator::TYPE_CODE_128, 3, 50));
        
        $pdf = Pdf::loadView('products.label', [
            'product' => $product,
            'barcode' => $barcode
        ])->setPaper([0, 0, 226.77, 141.73]); // 80mm x 50mm
        
        return $pdf->stream('label-' . $product->code . '.pdf');
    }
}
