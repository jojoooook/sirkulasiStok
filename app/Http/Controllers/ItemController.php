<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    public function index()
    {
        $items = Item::with('category')->paginate(10);
        return view('pages.items.index', compact('items'));
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
