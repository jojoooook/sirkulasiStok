<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\StockEntry;
use Illuminate\Http\Request;

class StockEntryController extends Controller
{
    // Menampilkan daftar riwayat barang masuk
    public function index()
    {
        $query = StockEntry::query();

        // Join dengan tabel items agar bisa sort berdasarkan nama_barang
        $query->join('items', 'stock_entries.kode_barang', '=', 'items.kode_barang')
            ->select('stock_entries.*'); // penting agar pagination tetap berjalan

        // Pencarian
        if (request('search')) {
            $query->where('items.nama_barang', 'like', '%' . request('search') . '%');
        }

        // Sorting
        $sortBy = request('sort_by', 'tanggal_masuk'); // default diubah ke tanggal_masuk
        $sortOrder = request('sort_order', 'desc');

        // Validasi kolom sort agar aman
        $sortableColumns = [
            'items.nama_barang',
            'stok_masuk',
            'tanggal_masuk',
            'keterangan',
        ];

        $sortColumn = in_array($sortBy, $sortableColumns) ? $sortBy : 'items.nama_barang';

        $query->orderBy($sortColumn, $sortOrder);

        $stockEntries = $query->with('item')->paginate(10)->appends(request()->except('page'));

        return view('pages.stock-entry.index', compact('stockEntries', 'sortBy', 'sortOrder'));
    }


    // Menampilkan form untuk mencatat barang masuk
    public function create()
    {
        $items = Item::all(); // Menampilkan daftar barang yang ada
        return view('pages.stock-entry.create', compact('items'));
    }

    // Menyimpan data barang masuk
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_barang' => 'required|exists:items,kode_barang',  // Validasi kode barang
            'nomor_nota' => 'nullable|string|max:255',  // Nomor nota opsional, bisa kosong
            'stok_masuk' => 'required|integer|min:1',  // Validasi stok masuk
            'keterangan' => 'nullable|string|max:255',
        ]);

        // Menambah stok barang
        $item = Item::findOrFail($validated['kode_barang']);
        $item->stok += $validated['stok_masuk'];  // Tambahkan stok barang sesuai jumlah masuk

        // Simpan riwayat barang masuk
        StockEntry::create([
            'kode_barang' => $validated['kode_barang'],
            'nomor_nota' => $validated['nomor_nota'],  // Nomor nota yang diterima
            'stok_masuk' => $validated['stok_masuk'],
            'tanggal_masuk' => now(),
            'keterangan' => $validated['keterangan'],
        ]);

        // Update stok barang di database
        $item->save();

        return redirect()->route('stock-entry.index')->with('success', 'Barang masuk berhasil dicatat.');
    }

}
