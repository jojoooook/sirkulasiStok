<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\StockExit;
use Illuminate\Http\Request;

class StockExitController extends Controller
{
    // Menampilkan daftar riwayat barang keluar
    public function index(Request $request)
    {
        $query = StockExit::query();

        // Join dengan tabel items agar bisa sort berdasarkan nama_barang
        $query->join('items', 'stock_exits.kode_barang', '=', 'items.kode_barang')
            ->select('stock_exits.*'); // penting agar pagination tetap berjalan

        // Pencarian
        if ($request->has('search') && $request->search != '') {
            $query->where('items.nama_barang', 'like', '%' . $request->search . '%');
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'tanggal_keluar'); // default diubah ke tanggal_keluar
        $sortOrder = $request->input('sort_order', 'desc');

        // Validasi kolom sort agar aman
        $sortableColumns = [
            'items.nama_barang',
            'stok_keluar',
            'tanggal_keluar',
            'keterangan',
        ];

        $sortColumn = in_array($sortBy, $sortableColumns) ? $sortBy : 'items.nama_barang';

        $query->orderBy($sortColumn, $sortOrder);

        // Pagination dan appends untuk menjaga parameter query di URL
        $stockExits = $query->with('item')->paginate(10)->appends($request->except('page'));

        return view('pages.stock-exit.index', compact('stockExits', 'sortBy', 'sortOrder'));
    }


    // Menampilkan form untuk mencatat barang keluar
    public function create()
    {
        $items = Item::all(); // Menampilkan daftar barang yang ada
        return view('pages.stock-exit.create', compact('items'));
    }

    // Menyimpan data barang keluar
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_barang' => 'required|exists:items,kode_barang',
            'stok_keluar' => 'required|integer|min:1',
            'keterangan' => 'nullable|string|max:255',
        ]);

        // Mengurangi stok barang
        $item = Item::findOrFail($validated['kode_barang']);
        if ($item->stok < $validated['stok_keluar']) {
            return redirect()->back()->with('error', 'Stok tidak mencukupi.');
        }
        $item->stok -= $validated['stok_keluar'];

        // Simpan riwayat barang keluar
        StockExit::create([
            'kode_barang' => $validated['kode_barang'],
            'stok_keluar' => $validated['stok_keluar'],
            'tanggal_keluar' => now(),
            'keterangan' => $validated['keterangan'],
        ]);

        // Update stok barang di database
        $item->save();

        // Mengirimkan pesan sukses
        return redirect()->route('stock-exit.index')->with('success', 'Barang keluar berhasil dicatat.');
    }
}
