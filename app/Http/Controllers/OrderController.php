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
        $items = Item::where('supplier_id', $supplierId)->get(['id', 'nama_barang']);
        return response()->json($items);
    }
    public function index(Request $request)
    {
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

        // Sorting
        $sortBy = $request->get('sort_by', 'tanggal_order'); // default: tanggal_order
        $sortOrder = $request->get('sort_order', 'desc');    // default: desc

        // Validasi kolom sort yang diizinkan
        $allowedSorts = [
            'tanggal_order',
            'jumlah_order',
            'status_order',
            'supplier.nama',
            'item.nama_barang',
        ];

        if (in_array($sortBy, $allowedSorts)) {
            // Jika sortBy adalah relasi, gunakan join
            if ($sortBy === 'supplier.nama') {
                $query->join('suppliers', 'orders.supplier_id', '=', 'suppliers.id')
                    ->orderBy('suppliers.nama', $sortOrder)
                    ->select('orders.*');
            } elseif ($sortBy === 'item.nama_barang') {
                $query->join('items', 'orders.item_id', '=', 'items.id')
                    ->orderBy('items.nama_barang', $sortOrder)
                    ->select('orders.*');
            } else {
                $query->orderBy($sortBy, $sortOrder);
            }
        }

        // Pagination
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

    // dd($request->all());

    $validated = $request->validate([
        'supplier_id' => 'required|exists:suppliers,id',
        'items' => 'required|array',
        'items.*.item_id' => 'required|exists:items,id',
        'items.*.jumlah_order' => 'required|integer|min:1',
        'items.*.catatan' => 'nullable|string|max:255',
    ]);

    // Proses penyimpanan order untuk setiap item
    foreach ($validated['items'] as $item) {
        // Cek apakah item_id valid dan tersedia
        $itemData = Item::find($item['item_id']);
        
        if (!$itemData) {
            return redirect()->route('order.create')->with('error', 'Item tidak ditemukan.');
        }

        // Simpan order
        Order::create([
            'supplier_id' => $validated['supplier_id'],
            'item_id' => $item['item_id'],
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
            'jumlah_masuk' => 'required|integer|min:1',  
            'catatan' => 'nullable|string|max:255',
        ]);

        $order->status_order = 'selesai';
        $order->tanggal_selesai = now();  
        $order->catatan = $request->catatan;  
        $order->save();  

        $item = $order->item;  
        $item->stok += $request->jumlah_masuk;  
        $item->save();  

        $stockEntry = new StockEntry([
            'item_id' => $order->item_id,  
            'stok_masuk' => $request->jumlah_masuk,  
            'tanggal_masuk' => now(),  
            'keterangan' => $request->catatan,  
        ]);
        $stockEntry->save();  

        return response()->json([
            'success' => true,
            'message' => 'Pesanan berhasil diselesaikan dan stok diperbarui.',
        ]);
    }

    public function cancel(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        if ($order->status_order !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak dapat dibatalkan, status tidak sesuai.'
            ]);
        }

        $order->status_order = 'dibatalkan'; 
        $order->catatan = $request->catatan; 
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Pesanan berhasil dibatalkan'
        ]);
    }


}