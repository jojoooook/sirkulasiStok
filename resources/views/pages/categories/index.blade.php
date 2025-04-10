@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Daftar Kategori</h1>

        <form action="{{ route('category.store') }}" method="POST" class="mb-4">
            @csrf
            <div class="mb-3">
                <label for="nama" class="form-label">Nama Kategori</label>
                <input type="text" class="form-control" id="nama" name="nama" required>
            </div>
            <button type="submit" class="btn btn-primary">Tambah Kategori</button>
        </form>

        <a href="{{ route('item.index') }}" class="btn btn-secondary mb-3">Kembali ke Daftar Barang</a>

        <div class="card">
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Kategori</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $category)
                            <tr>
                                <td>{{ $category->id }}</td>
                                <td>{{ $category->nama }}</td>
                                <td>
                                    <a href="{{ route('category.edit', $category->id) }}" class="btn btn-warning">Edit</a>
                                    <form id="delete-form-{{ $category->id }}"
                                        action="{{ route('category.destroy', $category->id) }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-danger delete-btn" data-id="{{ $category->id }}"
                                            data-name="{{ $category->nama }}">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function () {
                const formId = 'delete-form-' + this.getAttribute('data-id');
                const categoryName = this.getAttribute('data-name');

                Swal.fire({
                    title: 'Yakin ingin menghapus?',
                    text: `Kategori ${categoryName} akan dihapus permanen!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById(formId).submit();
                    }
                });
            });
        });

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: '{{ session('error') }}',
            });
        @endif

        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '{{ session('success') }}',
            });
        @endif
    </script>
@endpush