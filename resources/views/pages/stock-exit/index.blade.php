@extends('layouts.app')

@section('title', 'Riwayat Barang Keluar')

@section('content')
    <div class="container mt-4">
        <h1 class="mb-4 text-center">Riwayat Barang Keluar</h1>

        <!-- Form Pencarian -->
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <form action="{{ route('stock-exit.index') }}" method="GET" class="d-flex gap-2 flex-wrap align-items-center">
                <input type="text" name="search" class="form-control me-2" placeholder="Cari Nama Barang"
                    value="{{ request('search') }}" style="min-width: 200px;">
                <input type="text" name="nomor_nota" class="form-control me-2" placeholder="Cari Nomor Nota"
                    value="{{ request('nomor_nota') }}" style="min-width: 150px;">
                <input type="date" name="tanggal_keluar" class="form-control me-2" value="{{ request('tanggal_keluar') }}"
                    style="min-width: 150px;">
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-search"></i> Cari
                </button>
            </form>
            <div class="d-flex gap-2">
                <a href="{{ route('stock-exit.create') }}" class="btn btn-primary">
                    + Tambah Barang Keluar
                </a>
            </div>
        </div>

        @php
            function next_sort_state($column)
            {
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
                                <span>Nama Barang</span>
                                @php $nextSort = next_sort_state('items.nama_barang'); @endphp
                                <a
                                    href="{{ route('stock-exit.index', array_merge(request()->except(['sort_by', 'sort_order']), $nextSort)) }}">
                                    @if(request('sort_by') === 'items.nama_barang')
                                        <i
                                            class="fas fa-sort-{{ request('sort_order') === 'asc' ? 'up' : (request('sort_order') === 'desc' ? 'down' : '') }}"></i>
                                    @else
                                        <i class="fas fa-sort"></i>
                                    @endif
                                </a>
                            </div>
                        </th>
                        <th>
                            <div class="d-flex justify-content-between align-items-center">
                                <span>Kode Barang</span>
                                @php $nextSort = next_sort_state('kode_barang'); @endphp
                                <a
                                    href="{{ route('stock-exit.index', array_merge(request()->except(['sort_by', 'sort_order']), $nextSort)) }}">
                                    @if(request('sort_by') === 'kode_barang')
                                        <i
                                            class="fas fa-sort-{{ request('sort_order') === 'asc' ? 'up' : (request('sort_order') === 'desc' ? 'down' : '') }}"></i>
                                    @else
                                        <i class="fas fa-sort"></i>
                                    @endif
                                </a>
                            </div>
                        </th>
                        <th>
                            <div class="d-flex justify-content-between align-items-center">
                                <span>Jumlah Stok Keluar</span>
                                @php $nextSort = next_sort_state('stok_keluar'); @endphp
                                <a
                                    href="{{ route('stock-exit.index', array_merge(request()->except(['sort_by', 'sort_order']), $nextSort)) }}">
                                    @if(request('sort_by') === 'stok_keluar')
                                        <i
                                            class="fas fa-sort-{{ request('sort_order') === 'asc' ? 'up' : (request('sort_order') === 'desc' ? 'down' : '') }}"></i>
                                    @else
                                        <i class="fas fa-sort"></i>
                                    @endif
                                </a>
                            </div>
                        </th>
                        <th>
                            <div class="d-flex justify-content-between align-items-center">
                                <span>Keterangan</span>
                                @php $nextSort = next_sort_state('keterangan'); @endphp
                                <a
                                    href="{{ route('stock-exit.index', array_merge(request()->except(['sort_by', 'sort_order']), $nextSort)) }}">
                                    @if(request('sort_by') === 'keterangan')
                                        <i
                                            class="fas fa-sort-{{ request('sort_order') === 'asc' ? 'up' : (request('sort_order') === 'desc' ? 'down' : '') }}"></i>
                                    @else
                                        <i class="fas fa-sort"></i>
                                    @endif
                                </a>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stockExits as $group)
                        <tr class="table-primary">
                            <td colspan="5">
                                <strong>Nomor Nota:</strong> {{ $group->nomor_nota }} &nbsp;&nbsp;
                                <strong>Tanggal Keluar:</strong>
                                {{ \Carbon\Carbon::parse($group->tanggal_keluar)->format('Y-m-d') }}
                            </td>
                        </tr>
                        @foreach($group->items as $exit)
                            <tr>
                                <td>{{ $exit->item->nama_barang }}</td>
                                <td>{{ $exit->kode_barang }}</td>
                                <td>{{ $exit->stok_keluar }}</td>
                                <td>{{ $exit->keterangan }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>

            <div class="d-flex justify-content-center">
                {!! $stockExitsPaginated->links('pagination::bootstrap-4') !!}
            </div>
        </div>
    </div>


@endsection

@push('scripts')
    @if(session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                confirmButtonText: 'OK',
                confirmButtonColor: '#28a745'
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: '{{ session('error') }}',
                confirmButtonText: 'OK',
                confirmButtonColor: '#dc3545'
            });
        </script>
    @endif
@endpush