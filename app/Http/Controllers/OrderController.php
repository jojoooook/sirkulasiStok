<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Supplier;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class OrderController extends Controller
{
    public function getItemsBySupplier($supplierId)
    {
        $items = Item::where('supplier_id', $supplierId)->get(['id', 'nama_barang']);
        return response()->json($items);
    }
    public function index()
    {
        $orders = Order::with(['supplier', 'item'])
            ->orderBy('tanggal_order', 'desc')
            ->paginate(10);  // Menggunakan paginate untuk daftar order

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



    // Menampilkan form untuk mengedit order
    public function edit($id)
    {
        $order = Order::findOrFail($id);
        $suppliers = Supplier::all();
        return view('pages.order.edit', compact('order', 'suppliers'));
    }

    // Menyimpan perubahan data order
    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'item_id' => 'required|exists:items,id',
            'jumlah_order' => 'required|integer|min:1',
            'catatan' => 'nullable|string|max:255',
        ]);

        $order->update([
            'supplier_id' => $validated['supplier_id'],
            'item_id' => $validated['item_id'],
            'jumlah_order' => $validated['jumlah_order'],
            'status_order' => 'pending',  // Bisa diubah menjadi 'selesai' jika perlu
            'catatan' => $validated['catatan'],
        ]);

        return redirect()->route('order.index')->with('success', 'Order berhasil diupdate.');
    }

    public function selesai(Order $order)
    {
        // Cek apakah order benar-benar ada
        if ($order->status_order == 'selesai') {
            return response()->json(['error' => 'Pesanan sudah selesai.'], 400);
        }

        Log::debug('Pesanan yang diterima untuk diselesaikan:', ['order_id' => $order->id, 'status_awal' => $order->status_order]);

        // Mengubah status pesanan
        $order->status_order = 'selesai';
        $order->tanggal_selesai = now(); // Menambahkan tanggal selesai
        $order->save();

        Log::debug('Pesanan setelah diupdate:', ['order_id' => $order->id, 'status_baru' => $order->status_order]);

        return response()->json(['success' => 'Order selesai']);
    }

}