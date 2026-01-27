<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transactions = Transaction::latest()->get();
        return view('transactions.index', ['transactions' => $transactions]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $query = Product::where('stock', '>', 0);
        //pengecekan kalau ada pencarian
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('barcode', 'like', "%$search%");
            });
        }
        $products = $query->latest()->get();
        if ($request->has('search') && $products->isEmpty()) {
            return redirect()->route('transactions.index')->with('error', 'Product not found or out of stock.');
        }
        return view('transactions.create', ['products' => $products]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //validasi
        $request->validate ([
            'customer_name' => 'nullable|string|max:255',
            'cart' => 'required|array|min:1',
            'cart.*.product_id' => 'required|exists:products,id',
            'cart.*.quantity' => 'required|integer|min:1',
        ]);
        try {
            // beresin / tdk sama sekali
            DB::beginTransaction();

            //bikin transaksi dulu
            $transaction = Transaction::create([
                'customer_name' => $request->customer_name,
                'invoice_code' => 'PI=' . date('Ymd') . '-' . mt_rand(1000, 9999),

            ]);
           
            //bikin detail transaksinya
            foreach ($request->cart as $item) {
                //tarik data prduk dan tahan dulu stok sementara
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);
                //cek stok produk sesuai qty yg dimau apa tdk
                if ($product->stock < $item['quantity']) {
                   throw new Exception('Stok Produk: ' . $product->name);
                }else{
                    //kurangi stok produk
                    $product->decrement('stock', $item['quantity']);
                    //hitung total harga
                    $subtotal = $product->price * $item['quantity'];
                    //buat detail transaksinya
                    $transaction->details()->create([
                        'trasaction_invoice_code' => $transaction->invoice_code,
                        'prduct_code' => $product->code,
                        'quantity' => $item['quantity'],
                        'subtotal' => $subtotal
                    ]);
                    $totalPrice += $subtotal;
                }
                //update total harga di transaksi

                $transaction->update(['total_price' => $totalPrice]);
            }
            DB::commit();
            return response()->json([
                'status' => 'success',
                'redirect_url' => route('transactions.show', $transaction->id)
            ]);

        } catch(Exception $e){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Transaction failed to process. ' . $e->getMessage()
                ], 500);
            }
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction)
    {
        $transaction->load('details');
        return view('transactions.show', ['transaction' => $transaction]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaction $transaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        //
    }

    public function print(Request $request, Transaction $transaction)
    {
        $transaction->load('details.product');
        return view('transactions.print', ['transaction' => $transaction]);
    }

    //menanani pencarian barocde
    public function getProduct($code)
    {
        $product = Product::where('barcode', $code)->where('stock', '>', 0)->first();
        if ($product) {
            return response()->json([
                'status' => 'success',
                'product' => $product
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found or out of stock.'
            ], 404);    
        }
    }
}
