<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\StockExit;
use Illuminate\Http\Request;

class StockExitController extends Controller
{
    // Menampilkan daftar riwayat barang keluar
    public function index()
    {
        $stockExits = StockExit::with('item')
            ->orderBy('tanggal_keluar', 'desc')
            ->paginate(10);  
        return view('pages.stock-exit.index', compact('stockExits'));
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
            'item_id' => 'required|exists:items,id',
            'stok_keluar' => 'required|integer|min:1',
            'keterangan' => 'nullable|string|max:255',
        ]);

        // Mengurangi stok barang
        $item = Item::findOrFail($validated['item_id']);
        if ($item->stok < $validated['stok_keluar']) {
            return redirect()->back()->with('error', 'Stok tidak mencukupi.');
        }
        $item->stok -= $validated['stok_keluar'];

        // Simpan riwayat barang keluar
        StockExit::create([
            'item_id' => $validated['item_id'],
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
