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
            'telepon' => 'required|digits_between:10,14',
        ], [
            'kode_supplier.required' => 'Kode supplier wajib diisi.',
            'kode_supplier.string' => 'Kode supplier harus berupa teks.',
            'kode_supplier.max' => 'Kode supplier maksimal 255 karakter.',
            'kode_supplier.unique' => 'Kode supplier sudah digunakan.',
            'nama.required' => 'Nama supplier wajib diisi.',
            'nama.string' => 'Nama supplier harus berupa teks.',
            'nama.max' => 'Nama supplier maksimal 255 karakter.',
            'alamat.required' => 'Alamat supplier wajib diisi.',
            'alamat.string' => 'Alamat supplier harus berupa teks.',
            'telepon.required' => 'Nomor telepon wajib diisi.',
            'telepon.digits_between' => 'Nomor telepon harus antara 10 sampai 14 digit.',
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
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string',
            'telepon' => 'required|digits_between:10,14',
        ], [
            'nama.required' => 'Nama supplier wajib diisi.',
            'nama.string' => 'Nama supplier harus berupa teks.',
            'nama.max' => 'Nama supplier maksimal 255 karakter.',
            'alamat.required' => 'Alamat supplier wajib diisi.',
            'alamat.string' => 'Alamat supplier harus berupa teks.',
            'telepon.required' => 'Nomor telepon wajib diisi.',
            'telepon.digits_between' => 'Nomor telepon harus antara 10 sampai 14 digit.',
        ]);

        $supplier = Supplier::findOrFail($kode_supplier);
        $supplier->update([
            'nama' => $request->nama,
            'alamat' => $request->alamat,
            'telepon' => $request->telepon,
        ]);

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
