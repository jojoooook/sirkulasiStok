<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_barang', 'like', "%{$search}%")
                ->orWhere('nama_barang', 'like', "%{$search}%")
                ->orWhere('stok', 'like', "%{$search}%")
                ->orWhere('harga', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'kode_barang');
        $sortOrder = $request->get('sort_order', 'asc');
        $allowedSorts = ['kode_barang', 'nama_barang', 'stok', 'harga'];

        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        $items = $query->paginate(10)->appends($request->all());

        if ($request->ajax()) {
            return view('pages.items._table', compact('items'))->render();
        }

        return view('pages.items.index', compact('items', 'sortBy', 'sortOrder'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        return view('pages.items.create', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $messages = [
            'kode_barang.required' => 'Kode barang wajib diisi.',
            'kode_barang.string' => 'Kode barang harus berupa teks.',
            'kode_barang.max' => 'Kode barang maksimal 255 karakter.',
            'kode_barang.unique' => 'Kode barang sudah digunakan.',
            'nama_barang.required' => 'Nama barang wajib diisi.',
            'nama_barang.string' => 'Nama barang harus berupa teks.',
            'nama_barang.max' => 'Nama barang maksimal 255 karakter.',
            'supplier_id.exists' => 'Supplier tidak valid.',
            'stok.required' => 'Stok wajib diisi.',
            'stok.integer' => 'Stok harus berupa angka bulat.',
            'stok.min' => 'Stok minimal 0.',
            'harga.required' => 'Harga wajib diisi.',
            'harga.numeric' => 'Harga harus berupa angka.',
            'harga.min' => 'Harga minimal 0.',
            'gambar.image' => 'File gambar harus berupa gambar.',
            'gambar.mimes' => 'Format gambar harus jpeg, png, jpg, atau gif.',
            'gambar.max' => 'Ukuran gambar maksimal 2MB.',
        ];

        $validated = $request->validate([
            'kode_barang' => 'required|string|max:255|unique:items,kode_barang',
            'nama_barang' => 'required|string|max:255',
            'supplier_id' => 'nullable|exists:suppliers,kode_supplier',
            'stok' => 'required|integer|min:0',
            'harga' => 'required|numeric|min:0',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], $messages);

        if ($request->hasFile('gambar')) {
            try {
                $file = $request->file('gambar');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('images'), $filename);
                $validated['gambar'] = 'images/' . $filename;
            } catch (\Exception $e) {
                return redirect()->back()
                    ->withErrors(['gambar' => 'Gagal mengunggah gambar: ' . $e->getMessage()])
                    ->withInput();
            }
        }

        Item::create($validated);

        return redirect()->route('item.index')->with('success', 'Barang berhasil ditambahkan.');
    }

    public function edit($kode_barang)
    {
        $item = Item::findOrFail($kode_barang);
        $suppliers = Supplier::all();
        return view('pages.items.edit', compact('item', 'suppliers'));
    }

    public function update(Request $request, $kode_barang)
    {
        $messages = [
            'kode_barang.required' => 'Kode barang wajib diisi.',
            'kode_barang.string' => 'Kode barang harus berupa teks.',
            'kode_barang.max' => 'Kode barang maksimal 255 karakter.',
            'kode_barang.unique' => 'Kode barang sudah digunakan.',
            'nama_barang.required' => 'Nama barang wajib diisi.',
            'nama_barang.string' => 'Nama barang harus berupa teks.',
            'nama_barang.max' => 'Nama barang maksimal 255 karakter.',
            'supplier_id.exists' => 'Supplier tidak valid.',
            'stok.required' => 'Stok wajib diisi.',
            'stok.integer' => 'Stok harus berupa angka bulat.',
            'stok.min' => 'Stok minimal 0.',
            'harga.required' => 'Harga wajib diisi.',
            'harga.numeric' => 'Harga harus berupa angka.',
            'harga.min' => 'Harga minimal 0.',
            'gambar.image' => 'File gambar harus berupa gambar.',
            'gambar.mimes' => 'Format gambar harus jpeg, png, jpg, atau gif.',
            'gambar.max' => 'Ukuran gambar maksimal 2MB.',
        ];

        $validated = $request->validate([
            'kode_barang' => 'required|string|max:255|unique:items,kode_barang,' . $kode_barang . ',kode_barang',
            'nama_barang' => 'required|string|max:255',
            'supplier_id' => 'nullable|exists:suppliers,kode_supplier',
            'stok' => 'required|integer|min:0',
            'harga' => 'required|numeric|min:0',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], $messages);

        $item = Item::findOrFail($kode_barang);

        if ($request->hasFile('gambar')) {
            if ($item->gambar && file_exists(public_path($item->gambar))) {
                unlink(public_path($item->gambar));
            }

            try {
                $file = $request->file('gambar');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('images'), $filename);
                $validated['gambar'] = 'images/' . $filename;
            } catch (\Exception $e) {
                return redirect()->back()
                    ->withErrors(['gambar' => 'Gagal mengunggah gambar: ' . $e->getMessage()])
                    ->withInput();
            }
        } else {
            // Remove 'gambar' from validated data to avoid overwriting with old value
            unset($validated['gambar']);
        }

        $item->update($validated);

        return redirect()->route('item.index')->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy($kode_barang)
    {
        $item = Item::findOrFail($kode_barang);

        // Prevent deletion if item has a supplier
        if ($item->supplier_id) {
            return redirect()->route('item.index')->with('error', 'Barang tidak dapat dihapus karena memiliki supplier terkait.');
        }

        if ($item->gambar && file_exists(public_path($item->gambar))) {
            unlink(public_path($item->gambar));
        }

        $item->delete();

        return redirect()->route('item.index')->with('success', 'Barang berhasil dihapus.');
    }
}
