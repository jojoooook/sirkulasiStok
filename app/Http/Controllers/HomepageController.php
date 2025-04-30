<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\StockEntry;
use App\Models\StockExit;
use Illuminate\Http\Request;

class HomepageController extends Controller
{
    public function index()
    {
        return view('pages.homepage', [
            'totalBarang' => Item::count(),
            'barangKeluarHariIni' => StockExit::whereDate('created_at', today())->sum('stok_keluar'),
            'barangMasukHariIni' => StockEntry::whereDate('created_at', today())->sum('stok_masuk'),
            'barangKeluarBulanIni' => StockExit::whereMonth('created_at', now()->month)->sum('stok_keluar'),
            'barangMasukBulanIni' => StockEntry::whereMonth('created_at', now()->month)->sum('stok_masuk'),
            'barangHampirHabis' => Item::where('stok', '<=', 10)->get(), 
        ]);
        
    }
    
}
