@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4 text-center">Daftar Supplier</h1>

    <!-- Form Pencarian -->
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <form action="{{ route('supplier.index') }}" method="GET" class="d-flex">
            <div class="input-group">
                <input type="text" name="search" class="form-control me-2" placeholder="Cari Supplier" value="{{ request('search') }}">
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-search"></i> Cari
                </button>
            </div>
        </form>

        <div class="d-flex gap-2">
            <a href="{{ route('supplier.create') }}" class="btn btn-primary">
                + Tambah Supplier
            </a>
        </div>
    </div>

    @php
        function next_sort_state($column) {
            $currentSortBy = request('sort_by');
            $currentSortOrder = request('sort_order');

            if ($currentSortBy !== $column) {
                return ['sort_by' => $column, 'sort_order' => 'asc'];
            }

            if ($currentSortOrder === 'asc') {
                return ['sort_by' => $column, 'sort_order' => 'desc'];
            }

            // Third click: remove sorting
            return [];
        }
    @endphp

    <div class="table-responsive">
        <table class="table table-hover table-bordered shadow-sm">
            <thead class="thead-light">
                <tr>
                <th>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Nama Supplier</span>
                        @php $nextSort = next_sort_state('nama'); @endphp
                        <a href="{{ route('supplier.index', array_merge(request()->except(['sort_by', 'sort_order']), $nextSort)) }}">
                            @if(request('sort_by') === 'nama')
                                <i class="fas fa-sort-{{ request('sort_order') === 'asc' ? 'up' : (request('sort_order') === 'desc' ? 'down' : '') }}"></i>
                            @else
                                <i class="fas fa-sort"></i>
                            @endif
                        </a>
                    </div>
                </th>
                <th>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Alamat</span>
                        @php $nextSort = next_sort_state('alamat'); @endphp
                        <a href="{{ route('supplier.index', array_merge(request()->except(['sort_by', 'sort_order']), $nextSort)) }}">
                            @if(request('sort_by') === 'alamat')
                                <i class="fas fa-sort-{{ request('sort_order') === 'asc' ? 'up' : (request('sort_order') === 'desc' ? 'down' : '') }}"></i>
                            @else
                                <i class="fas fa-sort"></i>
                            @endif
                        </a>
                    </div>
                </th>
                <th>Telepon</th>
                <th>Aksi</th>
            </tr>
            </thead>
            <tbody>
                @foreach($suppliers as $supplier)
                    <tr>
                        <td>{{ $supplier->nama }}</td>
                        <td>{{ $supplier->alamat }}</td>
                        <td>{{ $supplier->telepon }}</td>
                        <td>
                            <a href="{{ route('supplier.edit', $supplier->id) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>

                            <form id="delete-form-{{ $supplier->id }}" action="{{ route('supplier.destroy', $supplier->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete({{ $supplier->id }})">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="d-flex justify-content-center">
            {!! $suppliers->links('pagination::bootstrap-4') !!}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function confirmDelete(id) {
    Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: "Supplier ini akan dihapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + id).submit();
        }
    });
}

@if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '{{ session('success') }}',
        confirmButtonText: 'OK',
        confirmButtonColor: '#28a745'
    });
@endif

@if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        text: '{{ session('error') }}',
        confirmButtonText: 'OK',
        confirmButtonColor: '#dc3545'
    });
@endif
</script>
@endpush
