<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::query();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('nama', 'like', "%{$search}%")
                ->orWhere('alamat', 'like', "%{$search}%")
                ->orWhere('telepon', 'like', "%{$search}%");
        }

        
        // Sorting
        $sortBy = $request->get('sort_by', 'nama'); // default sort by nama
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $suppliers = $query->paginate(10)->appends($request->all());

        return view('pages.supplier.index', compact('suppliers', 'sortBy', 'sortOrder'));
    }

    public function create()
    {
        return view('pages.supplier.create');
    }

    public function store(Request $request)
    {
        // Validasi data
        $request->validate([
            'kode_supplier' => 'required|string|max:255|unique:suppliers,kode_supplier',
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string',
            'telepon' => 'required|string|max:20',
        ]);

        // Simpan data
        Supplier::create([
            'kode_supplier' => $request->kode_supplier,
            'nama' => $request->nama,
            'alamat' => $request->alamat,
            'telepon' => $request->telepon,
        ]);

        // Redirect dengan pesan sukses
        return redirect()->route('supplier.index')->with('success', 'Supplier berhasil ditambahkan.');
    }

    public function edit($kode_supplier)
    {
        $supplier = Supplier::findOrFail($kode_supplier);
        return view('pages.supplier.edit', compact('supplier'));
    }

    public function update(Request $request, $kode_supplier)
    {
        $request->validate([
            'kode_supplier' => 'required|string|max:255|unique:suppliers,kode_supplier,' . $kode_supplier . ',kode_supplier',
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string',
            'telepon' => 'required|string|max:20',
        ]);

        $supplier = Supplier::findOrFail($kode_supplier);
        $supplier->update($request->all());

        return redirect()->route('supplier.index')->with('success', 'Supplier berhasil diperbarui.');
    }

    public function destroy($kode_supplier)
    {
        $supplier = Supplier::findOrFail($kode_supplier);

        if ($supplier->items()->count() > 0) {
            return redirect()->route('supplier.index')
                ->with('error', 'Supplier tidak dapat dihapus karena masih ada barang yang terkait.');
        }

        $supplier->delete();

        return redirect()->route('supplier.index')->with('success', 'Supplier berhasil dihapus.');
    }
}
