<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\StockEntry;
use App\Models\StockExit;
use App\Models\Supplier;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

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

        // Ambil ambang batas stok rendah dari file settings.json
        $settingsPath = storage_path('app/settings.json');
        $settings = File::exists($settingsPath) ? json_decode(File::get($settingsPath), true) : [];
        $lowStockThreshold = $settings['low_stock_threshold'] ?? 10;

        // Ambil daftar semua item dengan stok <= ambang batas dan urutkan dari stok paling sedikit
        $barangHampirHabis = Item::where('stok', '<=', $lowStockThreshold)
            ->orderBy('stok', 'asc')
            ->paginate(10);

        // Ambil semua item (jika diperlukan di bagian lain dari homepage)
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
            'barangHampirHabis' => $barangHampirHabis,
            'jumlahBarangKeluarHariIni' => $jumlahBarangKeluarHariIni,
            'items' => $items,
            'error' => $error, // Mengirimkan pesan error ke view
            'lowStockThreshold' => $lowStockThreshold, // Mengirimkan ambang batas ke view
        ]);
    }
}
