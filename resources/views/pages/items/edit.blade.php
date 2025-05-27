@extends('layouts.app')

@section('title', 'Edit Barang')

@section('content')
    <div class="container">
        <h1>Edit Barang</h1>
        <form action="{{ route('item.update', $item->kode_barang) }}" method="POST" enctype="multipart/form-data"
            id="edit-item-form">
            @csrf
            @method('PUT')

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mb-3">
                <label for="kode_barang" class="form-label">Kode Barang</label>
                <input type="text" class="form-control bg-light text-muted" id="kode_barang" name="kode_barang"
                    value="{{ $item->kode_barang }}" required readonly>
            </div>

            <div class="mb-3">
                <label for="nama_barang" class="form-label">Nama Barang</label>
                <input type="text" class="form-control" id="nama_barang" name="nama_barang" value="{{ $item->nama_barang }}"
                    required>
            </div>

            <div class="mb-3">
                <label for="supplier_id" class="form-label">Pilih Supplier</label>
                <select class="form-control" id="supplier_id" name="supplier_id">
                    <option value="">Pilih Supplier</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->kode_supplier }}" {{ $item->supplier_id == $supplier->kode_supplier ? 'selected' : '' }}>
                            {{ $supplier->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="stok" class="form-label">Stok</label>
                <input type="number" class="form-control" id="stok" name="stok" min="0" value="{{ $item->stok }}" required>
            </div>

            <div class="mb-3">
                <label for="harga" class="form-label">Harga</label>
                <input type="number" class="form-control" id="harga" name="harga" min="0" value="{{ $item->harga }}"
                    required>
            </div>

            <div class="mb-3">
                <label for="gambar" class="form-label">Gambar</label>
                <input type="file" class="form-control" id="gambar" name="gambar">
            </div>

            <button type="submit" class="btn btn-primary" id="submit-button">Update</button>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            // Initialize select2 for supplier dropdown
            $('#supplier_id').select2({
                placeholder: "Pilih Supplier",
                allowClear: true
            });

            // Disable submit button and show processing text on form submit
            $('#edit-item-form').on('submit', function () {
                $('#submit-button').prop('disabled', true).text('Sedang Memproses...');
            });
        });
    </script>
@endpush