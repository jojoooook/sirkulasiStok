<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::with('category');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                ->orWhere('stok', 'like', "%{$search}%")
                ->orWhere('harga', 'like', "%{$search}%")
                ->orWhereHas('category', function ($q2) use ($search) {
                    $q2->where('nama', 'like', "%{$search}%");
                });
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'kode_barang');
        $sortOrder = $request->get('sort_order', 'asc');
        $allowedSorts = ['kode_barang', 'nama_barang', 'stok', 'harga', 'category.nama'];

        if (in_array($sortBy, $allowedSorts)) {
            if ($sortBy === 'category.nama') {
                $query->join('categories', 'items.category_id', '=', 'categories.id')
                    ->orderBy('categories.nama', $sortOrder)
                    ->select('items.*');
            } else {
                $query->orderBy($sortBy, $sortOrder);
            }
        }

        $items = $query->paginate(10)->appends($request->all());

        if ($request->ajax()) {
            return view('pages.items._table', compact('items'))->render();
        }

        return view('pages.items.index', compact('items', 'sortBy', 'sortOrder'));
    }



    public function create()
    {
        $categories = Category::all();
        $suppliers = Supplier::all();
        return view('pages.items.create', compact('categories', 'suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_barang' => 'required|string|max:255|unique:items,kode_barang',
            'nama_barang' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'nullable|exists:suppliers,kode_supplier',
            'stok' => 'required|integer|min:0',
            'harga' => 'required|numeric|min:0',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('gambar')) {
            $validated['gambar'] = $request->file('gambar')->store('images', 'public');
        }

        Item::create($validated);

        return redirect()->route('item.index')->with('success', 'Barang berhasil ditambahkan.');
    }

    public function edit($kode_barang)
    {
        $item = Item::findOrFail($kode_barang);
        $categories = Category::all();
        $suppliers = Supplier::all();
        return view('pages.items.edit', compact('item', 'categories', 'suppliers'));
    }


    public function update(Request $request, $kode_barang)
    {
        $validated = $request->validate([
            'kode_barang' => 'required|string|max:255|unique:items,kode_barang,' . $kode_barang . ',kode_barang',
            'nama_barang' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'nullable|exists:suppliers,kode_supplier',
            'stok' => 'required|integer|min:0',
            'harga' => 'required|numeric|min:0',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $item = Item::findOrFail($kode_barang);

        if ($request->hasFile('gambar')) {
            if ($item->gambar && Storage::disk('public')->exists($item->gambar)) {
                Storage::disk('public')->delete($item->gambar);
            }

            $validated['gambar'] = $request->file('gambar')->store('images', 'public');
        } else {
            $validated['gambar'] = $item->gambar;
        }

        $item->update($validated);

        return redirect()->route('item.index')->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy($kode_barang)
    {
        $item = Item::findOrFail($kode_barang);

        if ($item->gambar && Storage::disk('public')->exists($item->gambar)) {
            Storage::disk('public')->delete($item->gambar);
        }

        $item->delete();

        return redirect()->route('item.index')->with('success', 'Barang berhasil dihapus.');
    }
}
