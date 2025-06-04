<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Supplier;
use App\Models\Item;
use App\Models\StockEntry;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function getItemsBySupplier($supplierId)
    {
        $items = Item::where('supplier_id', $supplierId)->get(['kode_barang', 'nama_barang', 'stok']);
        return response()->json($items);
    }

    public function index(Request $request)
    {
        try {
            $query = Order::with(['supplier', 'item']);

            if ($request->has('search') && $request->search != '') {
                $search = $request->search;
                $query->whereHas('supplier', function ($q) use ($search) {
                    $q->where('nama', 'like', '%' . $search . '%');
                })->orWhereHas('item', function ($q) use ($search) {
                    $q->where('nama_barang', 'like', '%' . $search . '%');
                });
            }

            if ($request->has('nomor_order') && $request->nomor_order != '') {
                $query->where('nomor_order', 'like', '%' . $request->nomor_order . '%');
            }

            if ($request->has('supplier') && $request->supplier != '') {
                $supplier = $request->supplier;
                $query->whereHas('supplier', function ($q) use ($supplier) {
                    $q->where('nama', 'like', '%' . $supplier . '%');
                });
            }

            if ($request->has('tanggal_order') && $request->tanggal_order != '') {
                $query->whereDate('tanggal_order', $request->tanggal_order);
            }

            if ($request->has('nama_barang') && $request->nama_barang != '') {
                $namaBarang = $request->nama_barang;
                $query->whereHas('item', function ($q) use ($namaBarang) {
                    $q->where('nama_barang', 'like', '%' . $namaBarang . '%');
                });
            }

            if ($request->has('status_order') && $request->status_order != '') {
                $query->where('status_order', $request->status_order);
            }

            // Sorting logic
            $sort = $request->get('sort', 'latest'); // default to latest


            if ($sort === 'latest') {
                $query->orderBy('created_at', 'desc');
            } elseif ($sort === 'oldest') {
                $query->orderBy('created_at', 'asc');
            } elseif ($sort === 'nomor_order_asc') {
                $query->orderBy('nomor_order', 'asc');
            } elseif ($sort === 'nomor_order_desc') {
                $query->orderBy('nomor_order', 'desc');
            } else {
                // fallback default
                $query->orderBy('created_at', 'desc');
            }

            $orders = $query->paginate(10)->appends(request()->query());

            return view('pages.order.index', compact('orders'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat data order: ' . $e->getMessage());
        }
    }
    
    public function show($nomor_order)
    {
        $orders = Order::with(['supplier', 'item'])
            ->where('nomor_order', $nomor_order)
            ->get()
            ->map(function ($order) {
                // Get the stock entry for this order
                $stockEntry = StockEntry::where('nomor_invoice', $order->nomor_invoice)
                    ->where('kode_barang', $order->kode_barang)
                    ->first();
                
                // Add the stock entry quantity to the order object
                $order->jumlah_barang_masuk = $stockEntry ? $stockEntry->stok_masuk : null;
                
                return $order;
            });

        if ($orders->isEmpty()) {
            return redirect()->route('order.index')->with('error', 'Order tidak ditemukan.');
        }

        return view('pages.order.show', compact('orders', 'nomor_order'));
    }

    public function create()
    {
        $suppliers = Supplier::all();

        // Ambil nomor order terakhir
        $lastOrder = Order::orderBy('nomor_order', 'desc')->first();
        $lastNomorOrder = $lastOrder ? intval(substr($lastOrder->nomor_order, -4)) : 0; // Ambil 4 digit terakhir
        $nextNomorOrder = 'ORD-' . str_pad($lastNomorOrder + 1, 4, '0', STR_PAD_LEFT); // Menambah 1 pada nomor order terakhir

        return view('pages.order.create', compact('suppliers', 'nextNomorOrder'));
    }


    public function store(Request $request)
    {
        try {
            $messages = [
                'supplier_id.required' => 'Supplier harus diisi.',
                'supplier_id.exists' => 'Supplier tidak ditemukan.',
                'tanggal_order.required' => 'Tanggal order harus diisi.',
                'tanggal_order.date' => 'Tanggal order harus berupa tanggal yang valid.',
                'tanggal_order.before_or_equal' => 'Tanggal order tidak boleh melewati tanggal hari ini.',
                'items.required' => 'Daftar barang harus diisi.',
                'items.array' => 'Daftar barang harus berupa array.',
                'items.*.item_id.required' => 'Kode barang harus diisi.',
                'items.*.item_id.exists' => 'Kode barang tidak ditemukan.',
                'items.*.jumlah_order.required' => 'Jumlah order harus diisi.',
                'items.*.jumlah_order.integer' => 'Jumlah order harus berupa angka.',
                'items.*.jumlah_order.min' => 'Jumlah order minimal 1.',
                'items.*.catatan.string' => 'Catatan harus berupa teks.',
                'items.*.catatan.max' => 'Catatan maksimal 255 karakter.',
            ];
            $validated = $request->validate([
                'supplier_id' => 'required|exists:suppliers,kode_supplier',
                'tanggal_order' => 'required|date|before_or_equal:today',
                'items' => 'required|array',
                'items.*.item_id' => 'required|exists:items,kode_barang',
                'items.*.jumlah_order' => 'required|integer|min:1',
                'items.*.catatan' => 'nullable|string|max:255',
            ], $messages);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        }

        // Generate nomor_order yang unik
        $lastNomorOrder = Order::orderBy('nomor_order', 'desc')->first();
        $lastNomorOrder = $lastNomorOrder ? intval(substr($lastNomorOrder->nomor_order, -4)) : 0;
        $newNomorOrder = 'ORD-' . str_pad($lastNomorOrder + 1, 4, '0', STR_PAD_LEFT);

        try {
            // Periksa apakah nomor order dengan supplier yang sama dan item yang sama sudah ada
            foreach ($validated['items'] as $item) {
                $existingOrder = Order::where('nomor_order', $newNomorOrder)
                    ->where('supplier_id', $validated['supplier_id'])
                    ->where('kode_barang', $item['item_id'])
                    ->exists();
                
                if ($existingOrder) {
                    return redirect()->route('order.create')->with('error', 'Barang dengan nomor order ini sudah ada.');
                }
            }

            // Loop untuk setiap item yang dipilih dan buat order
            foreach ($validated['items'] as $item) {
                $itemData = Item::find($item['item_id']);

                // Cek apakah item ada di database
                if (!$itemData) {
                    return redirect()->route('order.create')->with('error', 'Item tidak ditemukan.');
                }

                // Simpan order baru untuk setiap item
                Order::create([
                    'nomor_order' => $newNomorOrder,
                    'supplier_id' => $validated['supplier_id'],
                    'kode_barang' => $item['item_id'],
                    'jumlah_order' => $item['jumlah_order'],
                    'tanggal_order' => $validated['tanggal_order'],
                    'status_order' => 'pending',
                    'catatan' => $item['catatan'] ?? null,
                ]);
            }
        } catch (\Exception $e) {
            return redirect()->route('order.create')->with('error', 'Terjadi kesalahan saat menambahkan barang: ' . $e->getMessage());
        }

        return redirect()->route('order.index')->with('success', 'Order berhasil dibuat.');
    }

    public function cancel(Request $request, $nomor_order)
    {
        // Ambil semua order berdasarkan nomor_order
        $orders = Order::where('nomor_order', $nomor_order)->get();

        // Periksa apakah ada pesanan dengan status 'pending'
        $orders->each(function ($order) {
            if ($order->status_order !== 'pending') {
                return redirect()->route('order.index')->with('error', 'Pesanan tidak dapat dibatalkan, status tidak sesuai.');
            }
        });

        // Pembatalan semua order dalam pesanan
        foreach ($orders as $order) {
            $order->status_order = 'dibatalkan';
            $order->catatan = $request->catatan; // Menyimpan catatan alasan pembatalan
            $order->save();
        }

        return redirect()->route('order.index')->with('success', 'Semua item dalam pesanan telah dibatalkan.');
    }



    public function showBatchComplete($nomor_order)
    {
        $orders = Order::with(['supplier', 'item'])
            ->where('nomor_order', $nomor_order)
            ->where('status_order', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pages.order.batch_complete', compact('orders', 'nomor_order'));
    }

    public function batchComplete(Request $request, $nomor_order)
    {
        $errors = [];
        $invoiceErrors = [];
        $dateErrors = [];
        $otherErrors = [];

        $validator = Validator::make($request->all(), [
            'nomor_invoice' => 'required|string|max:255',
            'tanggal_invoice' => 'required|date|before_or_equal:today',
            'orders' => 'required|array',
            'orders.*.nomor_order' => 'required|exists:orders,nomor_order',
            'orders.*.jumlah_masuk' => 'required|integer|min:0',
            'orders.*.catatan' => 'nullable|string|max:255',
            'orders.*.kode_barang' => 'required|string',
        ]);

        if ($validator->fails()) {
            $errors = array_merge($errors, $validator->errors()->all());
        }

        if ($request->filled('nomor_invoice')) {
            $existingInvoice = StockEntry::where('nomor_invoice', $request->input('nomor_invoice'))->exists();
            if ($existingInvoice) {
                $invoiceErrors[] = 'Nomor invoice sudah ada.';
            }
        }

        // Sort errors from validator: separate date errors from others
        foreach ($errors as $error) {
            if (stripos($error, 'tanggal') !== false) {
                $dateErrors[] = 'Tanggal invoice tidak valid atau melewati tanggal hari ini.';
            } else {
                $otherErrors[] = $error;
            }
        }

        $errors = array_merge($invoiceErrors, $dateErrors, $otherErrors);

        if (count($errors) > 0) {
            return response()->json([
                'success' => false,
                'errors' => $errors,
            ]);
        }

        DB::beginTransaction();

        try {
            foreach ($request->input('orders') as $orderData) {
                $order = Order::where('nomor_order', $orderData['nomor_order'])
                    ->where('kode_barang', $orderData['kode_barang'])
                    ->first();

                if (!$order || $order->status_order !== 'pending') {
                    $otherErrors[] = "Order nomor {$orderData['nomor_order']} dengan kode barang {$orderData['kode_barang']} tidak dapat diselesaikan atau sudah selesai.";
                    continue;
                }

                $jumlahMasuk = $orderData['jumlah_masuk'];

                // if ($jumlahMasuk > $order->jumlah_order) {
                //     $otherErrors[] = "Jumlah masuk untuk order {$orderData['nomor_order']} ({$order->item->nama_barang}) tidak boleh melebihi jumlah order ({$order->jumlah_order}).";
                // }

                if (count($otherErrors) > 0) {
                    // Continue to collect all errors before rollback
                }

                $order->status_order = 'selesai';
                $order->tanggal_selesai = $request->input('tanggal_invoice');
                $order->catatan = $orderData['catatan'] ?? null;
                $order->nomor_invoice = $request->input('nomor_invoice');
                $order->save();

                $item = $order->item;
                $item->stok += $jumlahMasuk;
                $item->save();

                $stockEntry = StockEntry::where('nomor_invoice', $request->input('nomor_invoice'))
                                    ->where('kode_barang', $order->kode_barang)
                                    ->first();

                if ($stockEntry) {
                    $stockEntry->stok_masuk += $jumlahMasuk;
                    $stockEntry->save();
                } else {
                    StockEntry::create([
                        'supplier_id' => $order->supplier_id,
                        'kode_barang' => $order->kode_barang,
                        'nomor_invoice' => $request->input('nomor_invoice'),
                        'stok_masuk' => $jumlahMasuk,
                        'tanggal_masuk' => $request->input('tanggal_invoice'),
                        'keterangan' => $orderData['catatan'] ?? null,
                    ]);
                }
            }

            if (count($otherErrors) > 0) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'errors' => array_merge($invoiceErrors, $dateErrors, $otherErrors),
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Semua order berhasil diselesaikan.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
                'errors' => array_merge($invoiceErrors, $dateErrors, $otherErrors),
            ]);
        }
    }

}
