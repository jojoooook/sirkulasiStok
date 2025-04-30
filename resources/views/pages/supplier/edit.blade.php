@extends('layouts.app')

@section('title', 'Edit Supplier')

@section('content')
    <div class="container">
        <h1 class="mb-4 text-center">Edit Supplier</h1>

        <form action="{{ route('supplier.update', $supplier->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="nama">Nama Supplier</label>
                <input type="text" name="nama" class="form-control" value="{{ old('nama', $supplier->nama) }}" required>
            </div>

            <div class="form-group">
                <label for="alamat">Alamat</label>
                <textarea name="alamat" class="form-control" required>{{ old('alamat', $supplier->alamat) }}</textarea>
            </div>

            <div class="form-group">
                <label for="telepon">No. Telepon</label>
                <input type="text" name="telepon" class="form-control" value="{{ old('telepon', $supplier->telepon) }}"
                    required>
            </div>


            <button type="submit" class="btn btn-primary mt-3">Update Supplier</button>
        </form>
    </div>
@endsection