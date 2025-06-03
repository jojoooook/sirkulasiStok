@extends('layouts.app')

@section('title', 'Edit Supplier')

@section('content')
    <div class="container">
        <h1 class="mb-4 text-center">Edit Supplier</h1>

        <form action="{{ route('supplier.update', $supplier->kode_supplier) }}" method="POST" id="supplier-form">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="kode_supplier">Kode Supplier</label>
                <input type="text" id="kode_supplier" name="kode_supplier" class="form-control" readonly
                    value="{{ old('kode_supplier', $supplier->kode_supplier) }}" required
                    oninput="this.value = this.value.toUpperCase()">
            </div>
            @error('kode_supplier')
                <small class="text-danger">{{ $message }}</small>
            @enderror

            <div class="form-group">
                <label for="nama">Nama Supplier</label>
                <input type="text" name="nama" class="form-control" value="{{ old('nama', $supplier->nama) }}" required>
                @error('nama')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label for="alamat">Alamat</label>
                <textarea name="alamat" class="form-control" required>{{ old('alamat', $supplier->alamat) }}</textarea>
                @error('alamat')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label for="telepon">No. Telepon</label>
                <input type="text" name="telepon" class="form-control" value="{{ old('telepon', $supplier->telepon) }}"
                    required minlength="10" maxlength="14" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                @error('telepon')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>


            <button type="submit" class="btn btn-primary mt-3" id="submit-button">Update Supplier</button>
        </form>
    </div>

    @push('scripts')
        <script>
            document.getElementById('supplier-form').addEventListener('submit', function (event) {
                document.getElementById('submit-button').disabled = true;
                document.getElementById('submit-button').innerText = "Sedang Memproses...";
            });
        </script>
    @endpush
@endsection