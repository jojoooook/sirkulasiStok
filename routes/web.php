<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomepageController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\StockEntryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\StockExitController;

Route::get('/', [HomepageController::class, 'index']);
// ItemController
Route::get('/itemlist', [ItemController::class, 'index'])->name('items.index');
Route::get('/itemlist/create', [ItemController::class, 'create'])->name('item.create');
Route::post('/itemlist', [ItemController::class, 'store'])->name('item.store');
Route::get('/itemlist/{id}/edit', [ItemController::class, 'edit'])->name('item.edit');
Route::put('/itemlist/{id}', [ItemController::class, 'update'])->name('item.update');
Route::delete('/itemlist/{id}', [ItemController::class, 'destroy'])->name('item.destroy');
Route::resource('item', ItemController::class);
Route::resource('category', CategoryController::class)->except(['show']);

// StockEntryController(Barang Masuk)
Route::get('/stock-entry', [StockEntryController::class, 'index'])->name('stock-entry.index');
Route::get('/stock-entry/create', [StockEntryController::class, 'create'])->name('stock-entry.create');
Route::post('/stock-entry', [StockEntryController::class, 'store'])->name('stock-entry.store');

// SupplierController
Route::get('/supplier', [SupplierController::class, 'index'])->name('supplier.index');
Route::get('/supplier/create', [SupplierController::class, 'create'])->name('supplier.create');
Route::post('/supplier', [SupplierController::class, 'store'])->name('supplier.store');

// Tambahkan ini:
Route::get('/supplier/{id}/edit', [SupplierController::class, 'edit'])->name('supplier.edit');
Route::put('/supplier/{id}', [SupplierController::class, 'update'])->name('supplier.update');
Route::delete('/supplier/{id}', [SupplierController::class, 'destroy'])->name('supplier.destroy');


// StockExitController (Barang Keluar)
Route::get('/stock-exit', [StockExitController::class, 'index'])->name('stock-exit.index');
Route::get('/stock-exit/create', [StockExitController::class, 'create'])->name('stock-exit.create');
Route::post('/stock-exit', [StockExitController::class, 'store'])->name('stock-exit.store');