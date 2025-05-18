<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Http\Request;

class NotaController extends Controller
{
    public function index()
    {
        // Get all nomor_nota from transactions with pagination
        $page = request()->get('page', 1);
        $perPage = 10;

        $query = Transaction::orderBy('nomor_nota', 'desc');

        $total = $query->count();

        $transactions = $query->skip(($page - 1) * $perPage)->take($perPage)->get();

        // Load related orders for each nomor_nota
        $notas = $transactions->mapWithKeys(function ($transaction) {
            $orders = Order::with(['supplier', 'item'])
                ->where('nomor_nota', $transaction->nomor_nota)
                ->get()
                ->map(function($order) {
                    // Mengambil stok masuk yang relevan untuk nomor_nota dan kode_barang
                    $stockEntries = \App\Models\StockEntry::where('nomor_nota', $order->nomor_nota)
                        ->where('kode_barang', $order->kode_barang)
                        ->get();

                    // Total stok masuk untuk barang tersebut
                    $totalStockIn = $stockEntries->sum('stok_masuk');

                    // Menentukan status dan jumlah barang masuk berdasarkan status order
                    if ($order->status_order === 'pending' || $order->status_order === 'dibatalkan') {
                        $order->stok_masuk_display = '-'; // Tampilkan "-" jika status pending atau dibatalkan
                    } elseif ($order->status_order === 'selesai') {
                        $order->stok_masuk_display = $totalStockIn; // Tampilkan jumlah stok masuk sesuai data
                    } else {
                        $order->stok_masuk_display = '-'; // Default tampilkan "-"
                    }

                    return $order;
                });

            return [$transaction->nomor_nota => $orders];
        });

        $paginatedNotas = new \Illuminate\Pagination\LengthAwarePaginator(
            $notas,
            $total,
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('pages.nota.index', ['notas' => $paginatedNotas]);
    }

    public function store(Request $request)
    {
        // Validasi nomor nota, pastikan unik di tabel transactions
        $validated = $request->validate([
            'nomor_nota' => 'required|string|max:50|unique:transactions,nomor_nota',
        ]);

        try {
            // Create transaction record for nomor_nota
            Transaction::create([
                'nomor_nota' => $validated['nomor_nota'],
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            // Error handling jika terjadi duplikasi
            if ($e->errorInfo[1] == 1062) { // MySQL duplicate entry error code
                // Kirim pesan error ke halaman utama
                return redirect()->route('nota.index')->with('error', 'Nomor nota sudah ada, silakan gunakan nomor lain.');
            }
            // Handle other errors
            return redirect()->route('nota.index')->with('error', 'Terjadi kesalahan saat menambahkan nomor nota.');
        }

        // Jika berhasil, redirect dengan pesan sukses
        return redirect()->route('nota.index')->with('success', 'Nomor nota berhasil ditambahkan.');
    }


    public function destroy($nomor_nota)
    {
        // Check if there are orders related to this nomor_nota
        $orderCount = Order::where('nomor_nota', $nomor_nota)->count();

        if ($orderCount > 0) {
            return redirect()->route('nota.index')->with('error', 'Nota tidak dapat dihapus karena masih memiliki relasi order.');
        }

        // Delete the transaction record
        Transaction::where('nomor_nota', $nomor_nota)->delete();

        return redirect()->route('nota.index')->with('success', 'Nota berhasil dihapus.');
    }
}
