@extends('layouts.app')

@section('title', 'Tambah Barang Baru')

@section('content')
    <div class="container">
        <h1>Tambah Barang Baru</h1>
        <form action="{{ route('item.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="kode_barang" class="form-label">Kode Barang</label>
                <input type="text" class="form-control" id="kode_barang" name="kode_barang" required>
            </div>
            <div class="mb-3">
                <label for="nama_barang" class="form-label">Nama Barang</label>
                <input type="text" class="form-control" id="nama_barang" name="nama_barang" required>
            </div>

            <div class="mb-3">
                <label for="category_id" class="form-label">Kategori</label>
                <select class="form-control" id="category_id" name="category_id" required>
                    <option value="">Pilih Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="supplier_id" class="form-label">Pilih Supplier</label>
                <select class="form-control" id="supplier_id" name="supplier_id">
                    <option value="">Pilih Supplier</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->kode_supplier }}">{{ $supplier->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="stok" class="form-label">Stok</label>
                <input type="number" class="form-control" id="stok" name="stok" min="0" required>
            </div>

            <div class="mb-3">
                <label for="harga" class="form-label">Harga</label>
                <input type="number" class="form-control" id="harga" name="harga" min="0" required>
            </div>

            <div class="mb-3">
                <label for="gambar" class="form-label">Gambar</label>
                <input type="file" class="form-control" id="gambar" name="gambar">
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('#supplier_id').select2({
                placeholder: "Pilih Supplier",
                allowClear: true
            });

            $('#category_id').select2({
                placeholder: "Pilih Kategori",
                allowClear: true
            });
        });
    </script>
@endpush