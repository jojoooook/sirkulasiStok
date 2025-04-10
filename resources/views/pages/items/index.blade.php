@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">Daftar Barang</h1>
            <div>
                <a href="{{ route('item.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Barang
                </a>
                <a href="{{ route('category.index') }}" class="btn btn-success ms-2">
                    <i class="fas fa-tags"></i> Tambah Kategori
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle text-center">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Nama Barang</th>
                                <th>Kategori</th>
                                <th>Stok</th>
                                <th>Harga</th>
                                <th>Gambar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->nama_barang }}</td>
                                    <td>
                                        <span class="badge bg-primary">
                                            {{ $item->category->nama }}
                                        </span>
                                    </td>
                                    <td>{{ $item->stok }}</td>
                                    <td>Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                    <td>
                                        @if($item->gambar)
                                            <img src="{{ Storage::url($item->gambar) }}" class="img-thumbnail zoomable-image"
                                                style="width: 200px; height: 200px; object-fit: cover; cursor: pointer;"
                                                data-bs-toggle="modal" data-bs-target="#imageModal"
                                                data-image="{{ Storage::url($item->gambar) }}" alt="{{ $item->nama_barang }}">
                                        @else
                                            <span class="text-danger">Gambar tidak ditemukan</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2 flex-wrap">
                                            <a href="{{ route('item.edit', $item->id) }}"
                                                class="btn btn-warning btn-sm d-flex align-items-center">
                                                <i class="fas fa-edit me-1"></i>Edit
                                            </a>
                                            <form id="delete-item-form-{{ $item->id }}"
                                                action="{{ route('item.destroy', $item->id) }}" method="POST"
                                                style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                    class="btn btn-danger btn-sm d-flex align-items-center delete-item-btn"
                                                    data-id="{{ $item->id }}" data-name="{{ $item->nama_barang }}">
                                                    <i class="fas fa-trash me-1"></i>Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $items->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Image Preview -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content bg-dark border-0">
                <div class="modal-body text-center p-0">
                    <img src="" id="modalImage" class="img-fluid rounded" style="max-height: 500px;" alt="Preview Gambar">
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // SweetAlert for delete confirmation
        document.querySelectorAll('.delete-item-btn').forEach(button => {
            button.addEventListener('click', function () {
                const formId = 'delete-item-form-' + this.getAttribute('data-id');
                const itemName = this.getAttribute('data-name');

                Swal.fire({
                    title: 'Yakin ingin menghapus?',
                    text: `Barang ${itemName} akan dihapus permanen!`,
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

        // Image preview modal
        const imageModal = document.getElementById('imageModal');
        imageModal.addEventListener('show.bs.modal', function (event) {
            const triggerImage = event.relatedTarget;
            const imageUrl = triggerImage.getAttribute('data-image');
            const modalImage = imageModal.querySelector('#modalImage');
            modalImage.src = imageUrl;
        });

        // SweetAlert notifications
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '{{ session('success') }}',
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: '{{ session('error') }}',
            });
        @endif
    </script>
@endpush