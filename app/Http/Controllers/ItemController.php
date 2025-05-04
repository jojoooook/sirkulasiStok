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

        // Pencarian
        if ($request->has('search') && $request->search != '') {
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
        $sortBy = $request->get('sort_by', 'id'); // default sort
        $sortOrder = $request->get('sort_order', 'asc');

        // Daftar kolom yang diperbolehkan
        $allowedSorts = ['nama_barang', 'stok', 'harga', 'category.nama'];

        if (in_array($sortBy, $allowedSorts)) {
            if ($sortBy === 'category.nama') {
                // Join ke tabel kategori untuk sorting
                $query->join('categories', 'items.category_id', '=', 'categories.id')
                    ->orderBy('categories.nama', $sortOrder)
                    ->select('items.*'); // Harus select items.* agar tidak bentrok
            } else {
                $query->orderBy($sortBy, $sortOrder);
            }
        }

        // Pagination + keep query string
        $items = $query->paginate(10)->appends($request->all());

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
            'nama_barang' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'stok' => 'required|integer|min:0',
            'harga' => 'required|numeric|min:0',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle file upload first
        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('images', 'public');
            $validated['gambar'] = $path;
        }

        $item = Item::create($validated);

        return redirect()->route('item.index')->with('success', 'Barang berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $item = Item::findOrFail($id);
        $categories = Category::all();
        $suppliers = Supplier::all();
        return view('pages.items.edit', compact('item', 'categories', 'suppliers'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'stok' => 'required|integer|min:0',
            'harga' => 'required|numeric|min:0',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $item = Item::findOrFail($id);
        
        // Handle file upload
        if ($request->hasFile('gambar')) {
            // Delete old image if exists
            if ($item->gambar && Storage::disk('public')->exists($item->gambar)) {
                Storage::disk('public')->delete($item->gambar);
            }
            $path = $request->file('gambar')->store('images', 'public');
            $validated['gambar'] = $path;
        } else {
            // Keep existing image if no new file uploaded
            $validated['gambar'] = $item->gambar;
        }

        $item->update($validated);

        return redirect()->route('item.index')->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $item = Item::findOrFail($id);
        $item->delete();

        return redirect()->route('item.index')->with('success', 'Barang berhasil dihapus.');
    }
}
