<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\StockEntry;
use App\Models\StockExit;
use App\Models\Supplier;
use App\Models\Order;
use Illuminate\Http\Request;

class HomepageController extends Controller
{
    public function index()
    {
        // Cek jika ada pesan error di session dan kirimkan ke view
        $error = session('error'); 

        // Get jumlah barang keluar hari ini grouped by item
        $jumlahBarangKeluarHariIni = StockExit::selectRaw('kode_barang, sum(stok_keluar) as total_keluar')
            ->whereDate('created_at', today())
            ->groupBy('kode_barang')
            ->with('item')
            ->get();

        $items = Item::paginate(10);

        return view('pages.homepage', [
            'totalBarang' => Item::count(),
            'totalSupplier' => Supplier::count(),
            'pendingOrders' => Order::where('status_order', 'pending')->distinct('nomor_order')->count('nomor_order'),
            'totalUser' => \App\Models\User::count(),
            'barangKeluarHariIni' => StockExit::whereDate('created_at', today())->sum('stok_keluar'),
            'barangMasukHariIni' => StockEntry::whereDate('created_at', today())->sum('stok_masuk'),
            'barangKeluarBulanIni' => StockExit::whereMonth('created_at', now()->month)->sum('stok_keluar'),
            'barangMasukBulanIni' => StockEntry::whereMonth('created_at', now()->month)->sum('stok_masuk'),
            'barangHampirHabis' => Item::where('stok', '<=', 10)->paginate(10),
            'jumlahBarangKeluarHariIni' => $jumlahBarangKeluarHariIni,
            'items' => $items,
            'error' => $error, // Mengirimkan pesan error ke view
        ]);
    }
}
