<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Supplier;
use App\Models\Item;
use App\Models\StockEntry;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class OrderController extends Controller
{
    public function getItemsBySupplier($supplierId)
    {
        $items = Item::where('supplier_id', $supplierId)->get(['kode_barang', 'nama_barang']);
        return response()->json($items);
    }
    public function index(Request $request)
    {
        // Mulai dengan query untuk mengambil data order dengan relasi supplier dan item
        $query = Order::with(['supplier', 'item']);  

        // Pencarian berdasarkan supplier atau nama barang
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->whereHas('supplier', function ($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%');
            })->orWhereHas('item', function ($q) use ($search) {
                $q->where('nama_barang', 'like', '%' . $search . '%');
            });
        }

        // Sorting berdasarkan parameter yang diterima
        $sortBy = $request->get('sort_by', 'tanggal_order'); // default: tanggal_order
        $sortOrder = $request->get('sort_order', 'desc');    // default: desc

        $allowedSorts = [
            'tanggal_order',
            'jumlah_order',
            'status_order',
            'supplier.nama',
            'item.nama_barang',
            'nomor_nota',
        ];

        // Cek apakah pengurutan berdasarkan nama supplier atau item
        if (in_array($sortBy, $allowedSorts)) {
            if ($sortBy === 'supplier.nama') {
                $query->whereHas('supplier', function ($q) use ($sortOrder) {
                    $q->orderBy('nama', $sortOrder);
                });
            } elseif ($sortBy === 'item.nama_barang') {
                $query->whereHas('item', function ($q) use ($sortOrder) {
                    $q->orderBy('nama_barang', $sortOrder);
                });
            } else {
                $query->orderBy($sortBy, $sortOrder);
            }
        }

        // Ambil data dengan pagination
        $orders = $query->paginate(10)->appends($request->query());

        return view('pages.order.index', compact('orders'));
    }



    // Menampilkan form untuk membuat order
    public function create()
    {
        $suppliers = Supplier::all();  // Menampilkan daftar supplier
        return view('pages.order.create', compact('suppliers'));
    }

    // Menyimpan data order baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_nota' => 'required|string|max:50',
            'supplier_id' => 'required|exists:suppliers,kode_supplier',
            'items' => 'required|array',
            'items.*.item_id' => 'required|exists:items,kode_barang',
            'items.*.jumlah_order' => 'required|integer|min:1',
            'items.*.catatan' => 'nullable|string|max:255',
        ]);

        // Insert or ignore transaction record for nomor_nota
        \App\Models\Transaction::firstOrCreate([
            'nomor_nota' => $validated['nomor_nota'],
        ]);

        foreach ($validated['items'] as $item) {
            $itemData = Item::find($item['item_id']);

            if (!$itemData) {
                return redirect()->route('order.create')->with('error', 'Item tidak ditemukan.');
            }

            Order::create([
                'nomor_nota' => $validated['nomor_nota'],  // Menyimpan nomor nota
                'supplier_id' => $validated['supplier_id'],
                'kode_barang' => $item['item_id'],
                'jumlah_order' => $item['jumlah_order'],
                'tanggal_order' => now(),
                'status_order' => 'pending',
                'catatan' => $item['catatan'] ?? null,
            ]);
        }

        return redirect()->route('order.index')->with('success', 'Order berhasil dibuat.');
    }

    
    public function complete(Request $request, Order $order)
    {
        $request->validate([
            'jumlah_masuk' => 'required|integer|min:1',  // Validasi jumlah barang masuk
            'catatan' => 'nullable|string|max:255',
        ]);

        // Validasi apakah order tersebut masih berstatus 'pending'
        if ($order->status_order !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak dapat diselesaikan, status tidak sesuai.'
            ]);
        }

        // Mengupdate status order menjadi 'selesai'
        $order->status_order = 'selesai';
        $order->tanggal_selesai = now();  // Set tanggal selesai
        $order->catatan = $request->catatan;  // Menambahkan catatan jika ada
        $order->save();  // Simpan perubahan pada order

        // Update stok barang yang terkait dengan order ini
        $item = $order->item;  // Ambil item terkait dengan order
        $item->stok += $request->jumlah_masuk;  // Tambah stok barang
        $item->save();  // Simpan perubahan stok barang

        // Menambahkan riwayat barang masuk di StockEntry
        StockEntry::create([
            'kode_barang' => $order->kode_barang,
            'nomor_nota' => $order->nomor_nota,  // Menambahkan nomor_nota dari order
            'stok_masuk' => $request->jumlah_masuk,  // Jumlah barang masuk
            'tanggal_masuk' => now(),  // Tanggal masuk barang
            'keterangan' => $request->catatan,  // Menambahkan keterangan jika ada
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pesanan berhasil diselesaikan dan stok diperbarui.'
        ]);
    }

    public function cancel(Request $request, $id)
    {
        // Temukan order berdasarkan ID yang diberikan
        $order = Order::findOrFail($id);

        // Pastikan status order adalah 'pending' sebelum bisa dibatalkan
        if ($order->status_order !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak dapat dibatalkan, status tidak sesuai.'
            ]);
        }

        // Update status order menjadi 'dibatalkan'
        $order->status_order = 'dibatalkan';
        $order->catatan = $request->catatan;  // Menambahkan catatan pembatalan jika ada
        $order->save();  // Simpan perubahan status

        // Kembalikan response sukses
        return response()->json([
            'success' => true,
            'message' => 'Pesanan berhasil dibatalkan'
        ]);
    }




}