<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return view('pages.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('pages.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:categories,nama',
        ]);

        Category::create($request->all());

        return redirect()->route('category.index')
            ->with('success', 'Kategori berhasil ditambahkan');
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        return view('pages.categories.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:categories,nama,'.$id,
        ]);

        $category = Category::findOrFail($id);
        $category->update($request->all());

        return redirect()->route('category.index')
            ->with('success', 'Kategori berhasil diperbarui');
    }

    public function destroy($id)
{
    $category = Category::findOrFail($id);
    
    if ($category->items()->count() > 0) {
        return redirect()->route('category.index')
            ->with('error', 'Kategori tidak dapat dihapus karena ada barang yang terkait.');
    }

    $category->delete();

    return redirect()->route('category.index')
        ->with('success', 'Kategori berhasil dihapus');
}
}
