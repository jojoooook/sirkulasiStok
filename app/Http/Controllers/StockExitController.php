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

        // Pencarian
        if ($request->has('search') && $request->search != '') {
            $query->whereHas('item', function ($q) use ($request) {
                $q->where('nama_barang', 'like', '%' . $request->search . '%');
            });
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'tanggal_keluar'); // default to tanggal_keluar
        $sortOrder = $request->input('sort_order', 'desc');

        $sortableColumns = [
            'nomor_nota',
            'tanggal_keluar',
        ];

        $sortColumn = in_array($sortBy, $sortableColumns) ? $sortBy : 'tanggal_keluar';

        $query->orderBy($sortColumn, $sortOrder);

        // Paginate the query
        $stockExitsPaginated = $query->with('item')->paginate(10)->appends($request->except('page'));

        // Group the current page's items by nomor_nota and tanggal_keluar
        $stockExitsGrouped = $stockExitsPaginated->getCollection()->groupBy(function ($item) {
            return $item->nomor_nota . '|' . $item->tanggal_keluar;
        });

        // Convert grouped collection to array of objects with nomor_nota, tanggal_keluar, and items
        $stockExits = $stockExitsGrouped->map(function ($group, $key) {
            [$nomorNota, $tanggalKeluar] = explode('|', $key);
            return (object)[
                'nomor_nota' => $nomorNota,
                'tanggal_keluar' => $tanggalKeluar,
                'items' => $group,
            ];
        });

        return view('pages.stock-exit.index', [
            'stockExits' => $stockExits,
            'stockExitsPaginated' => $stockExitsPaginated,
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder,
        ]);
    }

    // New method to get all items with stock info for AJAX
    public function getItems()
    {
        $items = Item::select('kode_barang', 'nama_barang', 'stok')->get();
        return response()->json($items);
    }


    // Menampilkan form untuk mencatat barang keluar
    public function create()
    {
        $items = Item::all(); // Menampilkan daftar barang yang ada

        // Generate next nomor_nota as zero-padded string starting from 00001
        $lastNota = StockExit::orderBy('nomor_nota', 'desc')->first();
        if ($lastNota && preg_match('/^\d+$/', $lastNota->nomor_nota)) {
            $nextNumber = intval($lastNota->nomor_nota) + 1;
        } else {
            $nextNumber = 1;
        }
        $nomorNota = str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

        return view('pages.stock-exit.create', compact('items', 'nomorNota'));
    }

    // Menyimpan data barang keluar
    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.item_id' => 'required|exists:items,kode_barang',
            'items.*.jumlah_keluar' => 'required|integer|min:1',
            'items.*.catatan' => 'nullable|string|max:255',
            'tanggal_keluar' => 'required|date',
        ]);

        $tanggalKeluar = $validated['tanggal_keluar'];
        $items = $validated['items'];

        // Generate nomor_nota as zero-padded string starting from 00001
        $lastNota = StockExit::orderBy('nomor_nota', 'desc')->first();
        if ($lastNota && preg_match('/^\d+$/', $lastNota->nomor_nota)) {
            $nextNumber = intval($lastNota->nomor_nota) + 1;
        } else {
            $nextNumber = 1;
        }
        $nomorNota = str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

        // Check stock availability for all items first
        $errors = [];
        foreach ($items as $itemData) {
            $item = Item::where('kode_barang', $itemData['item_id'])->first();
            if (!$item) {
                $errors[] = "Barang dengan kode {$itemData['item_id']} tidak ditemukan.";
                continue;
            }
            if ($item->stok < $itemData['jumlah_keluar']) {
                $errors[] = "Stok tidak mencukupi untuk barang $item->nama_barang.";
            }
        }
        if (!empty($errors)) {
            return redirect()->back()->withErrors($errors)->withInput();
        }

        // Process each item
        foreach ($items as $itemData) {
            $item = Item::where('kode_barang', $itemData['item_id'])->first();
            $item->stok -= $itemData['jumlah_keluar'];
            $item->save();

            StockExit::create([
                'nomor_nota' => $nomorNota,
                'kode_barang' => $itemData['item_id'],
                'stok_keluar' => $itemData['jumlah_keluar'],
                'tanggal_keluar' => $tanggalKeluar,
                'keterangan' => $itemData['catatan'] ?? null,
            ]);
        }

        return redirect()->route('stock-exit.index')->with('success', 'Barang keluar berhasil dicatat.');
    }
}
