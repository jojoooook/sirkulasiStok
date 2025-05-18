<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\StockEntry;

class NotaController extends Controller
{
    public function index()
    {
        $page = request()->get('page', 1);
        $perPage = 10;

        $query = Transaction::query();

        // Filter by search
        if ($search = request('search')) {
            $query->where('nomor_nota', 'like', "%$search%");
        }

        // Sorting
        if ($sort = request('sort')) {
            $query->orderBy('nomor_nota', $sort);
        } else {
            $query->orderBy('nomor_nota', 'desc');
        }

        // Get paginated transactions
        $total = $query->count();
        $transactions = $query->skip(($page - 1) * $perPage)->take($perPage)->get();

        // Map each nomor_nota to its orders
        $notas = $transactions->mapWithKeys(function ($transaction, $index) {
            $orders = Order::with(['supplier', 'item'])
                ->where('nomor_nota', $transaction->nomor_nota)
                ->get()
                ->map(function ($order) {
                    // Hitung stok masuk
                    $stockEntries = StockEntry::where('nomor_nota', $order->nomor_nota)
                        ->where('kode_barang', $order->kode_barang)
                        ->get();

                    $totalStockIn = $stockEntries->sum('stok_masuk');

                    // Format stok_masuk_display
                    if ($order->status_order === 'pending' || $order->status_order === 'dibatalkan') {
                        $order->stok_masuk_display = '-';
                    } elseif ($order->status_order === 'selesai') {
                        $order->stok_masuk_display = $totalStockIn;
                    } else {
                        $order->stok_masuk_display = '-';
                    }

                    return $order;
                });

            return [$transaction->nomor_nota => $orders];
        });

        // Custom paginator untuk hasil map
        $paginatedNotas = new LengthAwarePaginator(
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
        $validated = $request->validate([
            'nomor_nota' => 'required|string|max:50|unique:transactions,nomor_nota',
        ]);

        try {
            Transaction::create([
                'nomor_nota' => $validated['nomor_nota'],
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                return redirect()->route('nota.index')->with('error', 'Nomor nota sudah ada, silakan gunakan nomor lain.');
            }

            return redirect()->route('nota.index')->with('error', 'Terjadi kesalahan saat menambahkan nomor nota.');
        }

        return redirect()->route('nota.index')->with('success', 'Nomor nota berhasil ditambahkan.');
    }

    public function destroy($nomor_nota)
    {
        $orderCount = Order::where('nomor_nota', $nomor_nota)->count();

        if ($orderCount > 0) {
            return redirect()->route('nota.index')->with('error', 'Nota tidak dapat dihapus karena masih memiliki relasi order.');
        }

        Transaction::where('nomor_nota', $nomor_nota)->delete();

        return redirect()->route('nota.index')->with('success', 'Nota berhasil dihapus.');
    }
    
}
