<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController; 

// Route::get('/', function () {
//     return view('welcome');
// });
Route::redirect('/', '/categories');
Route::resource('/categories', CategoryController::class);
Route::resource('/products', ProductController::class);

Route::get('/transactions/create', [TransactionController::class, 'create'])->name('transactions.create');
Route::get('/transactions/history', [TransactionController::class, 'index'])->name('transactions.index');
Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
Route::get('/transactions/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');
Route::get('/transactions/{transaction}/print', [TransactionController::class, 'print'])->name('transactions.print');
Route::get('/transactions/product/{product}', [TransactionController::class, 'getProduct'])->name('transactions.get-product');

Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
Route::get('/reports/print', [ReportController::class, 'print'])->name('reports.print');