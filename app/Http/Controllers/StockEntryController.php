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
    $stockEntries = StockEntry::with('item')
        ->orderBy('tanggal_masuk', 'desc')
        ->paginate(10);  
    return view('pages.stock-entry.index', compact('stockEntries'));
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
            'item_id' => 'required|exists:items,id',
            'stok_masuk' => 'required|integer|min:1',
            'keterangan' => 'nullable|string|max:255',
        ]);

        // Menambah stok barang
        $item = Item::findOrFail($validated['item_id']);
        $item->stok += $validated['stok_masuk'];

        // Simpan riwayat barang masuk
        StockEntry::create([
            'item_id' => $validated['item_id'],
            'stok_masuk' => $validated['stok_masuk'],
            'tanggal_masuk' => now(),
            'keterangan' => $validated['keterangan'],
        ]);

        // Update stok barang di database
        $item->save();

        // Mengirimkan pesan sukses
        return redirect()->route('stock-entry.index')->with('success', 'Barang masuk berhasil dicatat.');
    }
}
