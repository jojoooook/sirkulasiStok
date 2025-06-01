<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomepageController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\StockEntryController;
use App\Http\Controllers\StockExitController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\LoginController;
use App\Http\Middleware\RoleMiddleware;

// Route untuk login
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Route yang membutuhkan autentikasi dan middleware role
Route::middleware('auth')->group(function () {

    // Route Dashboard - Admin dan Karyawan bisa akses
    Route::get('/', [HomepageController::class, 'index'])->name('dashboard'); // Dashboard

    // Routes untuk Admin - Admin bisa mengakses semua route
    Route::middleware(RoleMiddleware::class . ':admin')->group(function () {
        // Admin bisa mengakses semua route
        Route::resource('item', ItemController::class);

        // StockEntryController (Barang Masuk)
        Route::get('/stock-entry', [StockEntryController::class, 'index'])->name('stock-entry.index');
        Route::post('/stock-entry', [StockEntryController::class, 'store'])->name('stock-entry.store');

        // StockExitController (Barang Keluar)
        Route::get('/stock-exit', [StockExitController::class, 'index'])->name('stock-exit.index');
        Route::get('/stock-exit/create', [StockExitController::class, 'create'])->name('stock-exit.create');
        Route::post('/stock-exit', [StockExitController::class, 'store'])->name('stock-exit.store');
        Route::get('/stock-exit/get-items', [StockExitController::class, 'getItems'])->name('stock-exit.getItems');

        // SupplierController
        Route::get('/supplier', [SupplierController::class, 'index'])->name('supplier.index');
        Route::get('/supplier/create', [SupplierController::class, 'create'])->name('supplier.create');
        Route::post('/supplier', [SupplierController::class, 'store'])->name('supplier.store');
        Route::get('/supplier/{id}/edit', [SupplierController::class, 'edit'])->name('supplier.edit');
        Route::put('/supplier/{id}', [SupplierController::class, 'update'])->name('supplier.update');
        Route::delete('/supplier/{id}', [SupplierController::class, 'destroy'])->name('supplier.destroy');

        // OrderController (Order Barang)
        Route::get('/order', [OrderController::class, 'index'])->name('order.index');
        Route::get('/order/create', [OrderController::class, 'create'])->name('order.create');
        Route::post('/order', [OrderController::class, 'store'])->name('order.store');
        Route::patch('/order/{order}', [OrderController::class, 'complete'])->name('order.complete');
        Route::patch('/order/{id}/cancel', [OrderController::class, 'cancel'])->name('order.cancel');
        Route::get('/order/{nomor_order}/batch-complete', [OrderController::class, 'showBatchComplete'])->name('order.showBatchComplete');
        Route::patch('/order/{nomor_order}/batch-complete', [OrderController::class, 'batchComplete'])->name('order.batchComplete');
        Route::get('/get-items/{supplierId}', [OrderController::class, 'getItemsBySupplier'])->name('get-items');
        Route::get('/order/{nomor_order}/show', [OrderController::class, 'show'])->name('order.show');

        // SettingController
        Route::get('/setting', [SettingController::class, 'index'])->name('setting.index');
        Route::get('/setting/create', [SettingController::class, 'create'])->name('setting.create');
        Route::post('/setting', [SettingController::class, 'store'])->name('setting.store');
        Route::get('/setting/{id}/edit', [SettingController::class, 'edit'])->name('setting.edit');
        Route::put('/setting/{id}', [SettingController::class, 'update'])->name('setting.update');
        Route::patch('/setting/{id}/toggle-active', [SettingController::class, 'toggleActive'])->name('setting.toggleActive');
        Route::post('/setting/reset-password/{id}', [SettingController::class, 'resetPassword'])->name('setting.resetPassword');
    });

    // Karyawan hanya bisa mengakses Dashboard, Daftar Barang, dan Barang Keluar
    Route::middleware(RoleMiddleware::class . ':karyawan')->group(function () {
        Route::get('/item', [ItemController::class, 'index'])->name('item.index'); // Daftar Barang
        Route::get('/stock-exit', [StockExitController::class, 'index'])->name('stock-exit.index'); // Barang Keluar - Index
        Route::get('/stock-exit/create', [StockExitController::class, 'create'])->name('stock-exit.create'); // Barang Keluar - Input
        Route::post('/stock-exit', [StockExitController::class, 'store'])->name('stock-exit.store'); // Barang Keluar - Store
    });

});
