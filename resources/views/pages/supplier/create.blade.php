@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4 text-center">Tambah Supplier</h1>

    <form action="{{ route('supplier.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="nama">Nama Supplier</label>
            <input type="text" name="nama" class="form-control" value="{{ old('nama') }}" required>
        </div>

        <div class="form-group">
            <label for="alamat">Alamat</label>
            <textarea name="alamat" class="form-control" required>{{ old('alamat') }}</textarea>
        </div>

        <div class="form-group">
            <label for="telepon">No. Telepon</label>
            <input type="text" name="telepon" class="form-control" value="{{ old('telepon') }}" required>
        </div>

        <button type="submit" class="btn btn-success mt-3">Simpan Supplier</button>
    </form>
</div>
@endsection
