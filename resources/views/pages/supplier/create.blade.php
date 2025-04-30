@extends('layouts.app')

@section('title', 'Tambah Supplier')

@section('content')
    <div class="container">
        <h1 class="mb-4 text-center">Tambah Supplier</h1>

        <form action="{{ route('supplier.store') }}" method="POST" id="supplier-form">
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

            <button type="submit" class="btn btn-success mt-3" id="submit-button">Simpan Supplier</button>
        </form>
    </div>
@endsection
@push('scripts')
    @if(session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                confirmButtonText: 'OK'
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: '{{ session('error') }}',
                confirmButtonText: 'OK'
            });
        </script>
    @endif

    <script>
        document.getElementById('supplier-form').addEventListener('submit', function (event) {
            document.getElementById('submit-button').disabled = true;
            document.getElementById('submit-button').innerText = "Sedang Memproses...";
        });
    </script>
@endpush