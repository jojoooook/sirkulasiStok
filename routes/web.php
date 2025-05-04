<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomepageController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\StockEntryController;
use App\Http\Controllers\StockExitController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SettingController;

Route::get('/', [HomepageController::class, 'index'])->name('pages.homepage');
// ItemController
Route::get('/item', [ItemController::class, 'index'])->name('item.index');
Route::get('/item/create', [ItemController::class, 'create'])->name('item.create');
Route::post('/item', [ItemController::class, 'store'])->name('item.store');
Route::get('/item/{id}/edit', [ItemController::class, 'edit'])->name('item.edit');
Route::put('/item/{id}', [ItemController::class, 'update'])->name('item.update');
Route::delete('/item/{id}', [ItemController::class, 'destroy'])->name('item.destroy');
Route::resource('item', ItemController::class);
Route::resource('category', CategoryController::class)->except(['show']);

// StockEntryController(Barang Masuk)
Route::get('/stock-entry', [StockEntryController::class, 'index'])->name('stock-entry.index');
Route::get('/stock-entry/create', [StockEntryController::class, 'create'])->name('stock-entry.create');
Route::post('/stock-entry', [StockEntryController::class, 'store'])->name('stock-entry.store');

// StockExitController (Barang Keluar)
Route::get('/stock-exit', [StockExitController::class, 'index'])->name('stock-exit.index');
Route::get('/stock-exit/create', [StockExitController::class, 'create'])->name('stock-exit.create');
Route::post('/stock-exit', [StockExitController::class, 'store'])->name('stock-exit.store');

// SupplierController
Route::get('/supplier', [SupplierController::class, 'index'])->name('supplier.index');
Route::get('/supplier/create', [SupplierController::class, 'create'])->name('supplier.create');
Route::post('/supplier', [SupplierController::class, 'store'])->name('supplier.store');
Route::get('/supplier/{id}/edit', [SupplierController::class, 'edit'])->name('supplier.edit');
Route::put('/supplier/{id}', [SupplierController::class, 'update'])->name('supplier.update');
Route::delete('/supplier/{id}', [SupplierController::class, 'destroy'])->name('supplier.destroy');

// OrderController (Order Barang)
Route::get('/get-items/{supplierId}', [OrderController::class, 'getItemsBySupplier']);

Route::get('/order', [OrderController::class, 'index'])->name('order.index');  
Route::get('/order/create', [OrderController::class, 'create'])->name('order.create');  
Route::post('/order', [OrderController::class, 'store'])->name('order.store');  

Route::patch('/order/{order}', [OrderController::class, 'complete'])->name('order.complete');
Route::patch('/order/{id}/cancel', [OrderController::class, 'cancel'])->name('order.cancel');

// SettingController
Route::get('/setting', [SettingController::class, 'index'])->name('setting.index'); 
Route::get('/setting/create', [SettingController::class, 'create'])->name('setting.create');
Route::post('/setting', [SettingController::class, 'store'])->name('setting.store');
Route::get('/setting/{id}/edit', [SettingController::class, 'edit'])->name('setting.edit');
Route::put('/setting/{id}', [SettingController::class, 'update'])->name('setting.update');
Route::delete('/setting/{id}', [SettingController::class, 'destroy'])->name('setting.destroy');

