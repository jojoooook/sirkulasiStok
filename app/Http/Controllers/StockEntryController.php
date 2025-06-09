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

        // Join dengan tabel items, suppliers, dan orders agar bisa sort dan tampil order_id
        $query->join('items', 'stock_entries.kode_barang', '=', 'items.kode_barang')
            ->join('suppliers', 'stock_entries.supplier_id', '=', 'suppliers.kode_supplier')
            ->leftJoin('orders', 'stock_entries.order_id', '=', 'orders.id')
            ->select('stock_entries.*', 'items.nama_barang', 'suppliers.nama as supplier_nama', 'orders.id as order_id'); // penting agar pagination tetap berjalan

        // Pencarian
        if (request('search')) {
            $query->where('items.nama_barang', 'like', '%' . request('search') . '%');
        }

        if (request('nomor_invoice')) {
            $query->where('nomor_invoice', 'like', '%' . request('nomor_invoice') . '%');
        }

        if (request('supplier_nama')) {
            $query->where('suppliers.nama', 'like', '%' . request('supplier_nama') . '%');
        }

        if (request('tanggal_masuk')) {
            $query->whereDate('tanggal_masuk', request('tanggal_masuk'));
        }

        // Sorting
        $sortBy = request('sort_by', 'created_at'); // default diubah ke created_at
        $sortOrder = request('sort_order', 'desc');

        // Validasi kolom sort agar aman
        $sortableColumns = [
            'items.nama_barang',
            'suppliers.nama',
            'stok_masuk',
            'tanggal_masuk',
            'keterangan',
            'created_at',
        ];

        $sortColumn = in_array($sortBy, $sortableColumns) ? $sortBy : 'created_at';

        $query->orderBy($sortColumn, $sortOrder);

        $stockEntries = $query->paginate(10)->appends(request()->except('page'));

        // Group stock entries by nomor_invoice and supplier_nama for current page items
        $groupedEntries = $stockEntries->getCollection()->groupBy(function ($item) {
            return $item->nomor_invoice . '|' . $item->supplier_nama;
        });

        return view('pages.stock-entry.index', [
            'groupedEntries' => $groupedEntries,
            'stockEntries' => $stockEntries,
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder,
        ]);
    }

    // Menyimpan data barang masuk
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_barang' => 'required|exists:items,kode_barang',  // Validasi kode barang
            'stok_masuk' => 'required|integer|min:1',  // Validasi stok masuk
            'keterangan' => 'nullable|string|max:255',
            'order_id' => 'required|exists:orders,id', // Validate order_id exists
        ]);

        // Menambah stok barang
        $item = Item::findOrFail($validated['kode_barang']);
        $item->stok += $validated['stok_masuk'];  // Tambahkan stok barang sesuai jumlah masuk

        // Simpan riwayat barang masuk
        StockEntry::create([
            'kode_barang' => $validated['kode_barang'],
            'stok_masuk' => $validated['stok_masuk'],
            'tanggal_masuk' => now(),
            'keterangan' => $validated['keterangan'],
            'order_id' => $validated['order_id'], // Save order_id
        ]);

        // Update stok barang di database
        $item->save();

        return redirect()->route('stock-entry.index')->with('success', 'Barang masuk berhasil dicatat.');
    }

}
