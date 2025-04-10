@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="h3">Edit Kategori</h1>

        <form action="{{ route('category.update', $category->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="nama" class="form-label">Nama Kategori</label>
                <input type="text" class="form-control" id="nama" name="nama" value="{{ $category->nama }}" required>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('category.index') }}" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
@endsection