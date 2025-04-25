<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomepageController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\StockEntryController;

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