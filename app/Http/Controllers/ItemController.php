<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

class ItemController extends Controller
{
    public function index()
    {
        $items = Item::paginate(10);
        return view('pages.items.index', compact('items')); 
    }

    public function create()
    {
        return view('pages.items.create'); 
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'stok' => 'required|integer',
            'harga' => 'required|numeric',
            'gambar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only(['nama_barang', 'kategori', 'stok', 'harga']);

        // Handle upload gambar
        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $destinationPath = public_path('images');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $fileName);
            $data['gambar'] = 'images/' . $fileName;
        }

        Item::create($data);

        return redirect()->route('items.index')->with('success', 'Item berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $item = Item::findOrFail($id);
        return view('pages.items.edit', compact('item'));
    }

    public function update(Request $request, $id)
{
    $request->validate([
        'nama_barang' => 'required|string|max:255',
        'kategori' => 'required|string|max:255',
        'stok' => 'required|integer',
        'harga' => 'required|numeric',
        'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    $item = Item::findOrFail($id);
    $data = $request->except('gambar');

    if ($request->hasFile('gambar')) {
        $storagePath = public_path('images');
        if (!file_exists($storagePath)) {
            mkdir($storagePath, 0755, true);
        }

        $fileName = time() . '_' . $request->file('gambar')->getClientOriginalName();
        $filePath = $storagePath . '/' . $fileName;

        $request->file('gambar')->move($storagePath, $fileName);

        if (!empty($item->gambar) && file_exists(public_path($item->gambar))) {
            unlink(public_path($item->gambar));
        }

        $data['gambar'] = 'images/' . $fileName;
    } else {
        $data['gambar'] = $item->gambar;
    }

    $item->update($data);

    return redirect()->route('items.index')->with('success', 'Item updated successfully.');
}


    public function destroy($id)
    {
        $item = Item::findOrFail($id);

        if ($item->gambar && file_exists(public_path($item->gambar))) {
            unlink(public_path($item->gambar));
        }

        $item->delete();

        return redirect()->route('items.index')->with('success', 'Item berhasil dihapus.');
    }
}
