@extends('layouts.app')

@section('title', 'Daftar Barang')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4 text-center">Daftar Barang</h1>

    <!-- Form Pencarian dan Tombol -->
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <form action="{{ route('item.index') }}" method="GET" class="d-flex">
            <div class="input-group">
                <input type="text" name="search" class="form-control me-2" placeholder="Cari Barang" value="{{ request('search') }}">
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-search"></i> Cari
                </button>
            </div>
        </form>
        <div class="d-flex gap-2">
            <a href="{{ route('item.create') }}" class="btn btn-primary">
                + Tambah Barang
            </a>
            <a href="{{ route('category.index') }}" class="btn btn-success">
                <i class="fas fa-tags"></i> Tambah Kategori
            </a>
        </div>
    </div>

    <!-- Flash Message -->
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @php
        function next_sort_state($column) {
            $currentSortBy = request('sort_by');
            $currentSortOrder = request('sort_order');

            if ($currentSortBy !== $column) return ['sort_by' => $column, 'sort_order' => 'asc'];
            if ($currentSortOrder === 'asc') return ['sort_by' => $column, 'sort_order' => 'desc'];
            return [];
        }
    @endphp

    <!-- Table -->
    <div class="table-wrapper">
        <div class="table-responsive">
            <table class="table table-bordered table-hover text-center align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="mx-auto">Nama Barang</span>
                                @php $nextSort = next_sort_state('nama_barang'); @endphp
                                <a href="{{ route('item.index', array_merge(request()->except(['sort_by', 'sort_order']), $nextSort)) }}">
                                    @if(request('sort_by') === 'nama_barang')
                                        <i class="fas fa-sort-{{ request('sort_order') === 'asc' ? 'up' : 'down' }}"></i>
                                    @else
                                        <i class="fas fa-sort"></i>
                                    @endif
                                </a>
                            </div>
                        </th>
                        <th>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="mx-auto">Kategori</span>
                                @php $nextSort = next_sort_state('category.nama'); @endphp
                                <a href="{{ route('item.index', array_merge(request()->except(['sort_by', 'sort_order']), $nextSort)) }}">
                                    @if(request('sort_by') === 'category.nama')
                                        <i class="fas fa-sort-{{ request('sort_order') === 'asc' ? 'up' : 'down' }}"></i>
                                    @else
                                        <i class="fas fa-sort"></i>
                                    @endif
                                </a>
                            </div>
                        </th>
                        <th>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="mx-auto">Stok</span>
                                @php $nextSort = next_sort_state('stok'); @endphp
                                <a href="{{ route('item.index', array_merge(request()->except(['sort_by', 'sort_order']), $nextSort)) }}">
                                    @if(request('sort_by') === 'stok')
                                        <i class="fas fa-sort-{{ request('sort_order') === 'asc' ? 'up' : 'down' }}"></i>
                                    @else
                                        <i class="fas fa-sort"></i>
                                    @endif
                                </a>
                            </div>
                        </th>
                        <th>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="mx-auto">Harga</span>
                                @php $nextSort = next_sort_state('harga'); @endphp
                                <a href="{{ route('item.index', array_merge(request()->except(['sort_by', 'sort_order']), $nextSort)) }}">
                                    @if(request('sort_by') === 'harga')
                                        <i class="fas fa-sort-{{ request('sort_order') === 'asc' ? 'up' : 'down' }}"></i>
                                    @else
                                        <i class="fas fa-sort"></i>
                                    @endif
                                </a>
                            </div>
                        </th>
                        <th>Gambar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                        <tr>
                            <td>{{ $item->kode_barang }}</td>
                            <td>{{ $item->nama_barang }}</td>
                            <td><span class="badge bg-primary">{{ $item->category->nama ?? 'Tidak ada' }}</span></td>
                            <td>{{ $item->stok }}</td>
                            <td>Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                            <td>
                                @if($item->gambar)
                                    <img src="{{ Storage::url($item->gambar) }}" class="img-thumbnail zoomable-image"
                                         style="width: 80px; height: 80px; object-fit: cover; cursor: pointer;"
                                         data-bs-toggle="modal" data-bs-target="#imageModal"
                                         data-image="{{ Storage::url($item->gambar) }}" alt="{{ $item->nama_barang }}">
                                @else
                                    <span class="text-danger">Tidak ada gambar</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-2 flex-wrap">
                                    <a href="{{ route('item.edit', $item->kode_barang) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit me-1"></i>Edit
                                    </a>
                                    <form id="delete-item-form-{{ $item->kode_barang }}" action="{{ route('item.destroy', $item->kode_barang) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-danger btn-sm delete-item-btn"
                                                data-id="{{ $item->kode_barang }}" data-name="{{ $item->nama_barang }}">
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

        <!-- Pagination -->
        <div class="mt-3 d-flex justify-content-center">
            {{ $items->links('pagination::bootstrap-4') }}
        </div>
    </div>

    <!-- Modal Preview Gambar -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content bg-dark border-0">
                <div class="modal-body text-center p-0">
                    <img src="" id="modalImage" class="img-fluid rounded" style="max-height: 500px;" alt="Preview Gambar">
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // SweetAlert untuk konfirmasi hapus
    document.querySelectorAll('.delete-item-btn').forEach(button => {
        button.addEventListener('click', function () {
            const formId = 'delete-item-form-' + this.getAttribute('data-id');
            const itemName = this.getAttribute('data-name');

            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: Barang "${itemName}" akan dihapus permanen!,
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

    // Modal gambar
    const imageModal = document.getElementById('imageModal');
    imageModal.addEventListener('show.bs.modal', function (event) {
        const triggerImage = event.relatedTarget;
        const imageUrl = triggerImage.getAttribute('data-image');
        const modalImage = imageModal.querySelector('#modalImage');
        modalImage.src = imageUrl;
    });

    @if(session('success'))
        Swal.fire({ icon: 'success', title: 'Berhasil!', text: '{{ session('success') }}' });
    @endif

    @if(session('error'))
        Swal.fire({ icon: 'error', title: 'Gagal!', text: '{{ session('error') }}' });
    @endif
</script>
@endpush