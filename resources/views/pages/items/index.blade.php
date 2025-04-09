@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">Daftar Barang</h1>
            <a href="{{ route('item.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Barang
            </a>
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
                                    <td>{{ $item->kategori }}</td>
                                    <td>{{ $item->stok }}</td>
                                    <td>Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                    <td>
                                        @if($item->gambar && file_exists(public_path($item->gambar)))
                                            <img src="{{ asset($item->gambar) }}" class="img-thumbnail zoomable-image"
                                                style="width: 200px; height: 200px; object-fit: cover; cursor: pointer;"
                                                data-bs-toggle="modal" data-bs-target="#imageModal"
                                                data-image="{{ asset($item->gambar) }}" alt="{{ $item->nama_barang }}">
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
                                            <button class="btn btn-danger btn-sm d-flex align-items-center"
                                                data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="{{ $item->id }}"
                                                data-nama="{{ $item->nama_barang }}">
                                                <i class="fas fa-trash me-1"></i>Hapus
                                            </button>
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

    <!-- Modal Delete -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus <strong id="modalItemName">item ini</strong>?</p>
                </div>
                <div class="modal-footer">
                    <form id="deleteForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </form>
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
        const deleteModal = document.getElementById('deleteModal');
        deleteModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const itemId = button.getAttribute('data-id');
            const itemName = button.getAttribute('data-nama');
            const modalItemName = deleteModal.querySelector('#modalItemName');
            const deleteForm = deleteModal.querySelector('#deleteForm');

            modalItemName.textContent = itemName;
            deleteForm.action = `/itemlist/${itemId}`;
        });

        const imageModal = document.getElementById('imageModal');
        imageModal.addEventListener('show.bs.modal', function (event) {
            const triggerImage = event.relatedTarget;
            const imageUrl = triggerImage.getAttribute('data-image');
            const modalImage = imageModal.querySelector('#modalImage');
            modalImage.src = imageUrl;
        });
    </script>
@endpush