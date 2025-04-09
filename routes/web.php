<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomepageController;
use App\Http\Controllers\ItemController;

Route::get('/', [HomepageController::class, 'index']);
Route::get('/itemlist', [ItemController::class, 'index'])->name('items.index');
Route::get('/itemlist/create', [ItemController::class, 'create'])->name('item.create');
Route::post('/itemlist', [ItemController::class, 'store'])->name('item.store');
Route::get('/itemlist/{id}/edit', [ItemController::class, 'edit'])->name('item.edit');
Route::put('/itemlist/{id}', [ItemController::class, 'update'])->name('item.update');
Route::delete('/itemlist/{id}', [ItemController::class, 'destroy'])->name('item.destroy');
