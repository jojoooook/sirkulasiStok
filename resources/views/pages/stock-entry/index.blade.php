@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <h1 class="mb-4 text-center">Riwayat Barang Masuk</h1>
        <!-- Form Pencarian -->
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <form action="{{ route('stock-entry.index') }}" method="GET" class="d-flex gap-2 flex-wrap align-items-center">
                <input type="text" name="search" class="form-control me-2" placeholder="Cari Nama Barang"
                    value="{{ request('search') }}" style="min-width: 200px;">
                <input type="text" name="nomor_invoice" class="form-control me-2" placeholder="Cari Nomor Invoice"
                    value="{{ request('nomor_invoice') }}" style="min-width: 150px;">
                <input type="text" name="supplier_nama" class="form-control me-2" placeholder="Cari Supplier"
                    value="{{ request('supplier_nama') }}" style="min-width: 150px;">
                <input type="date" name="tanggal_masuk" class="form-control me-2" value="{{ request('tanggal_masuk') }}"
                    style="min-width: 150px;">
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-search"></i> Cari
                </button>
            </form>
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
                                    href="{{ route('stock-entry.index', array_merge(request()->except(['sort_by', 'sort_order']), $nextSort)) }}">
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
                                    href="{{ route('stock-entry.index', array_merge(request()->except(['sort_by', 'sort_order']), $nextSort)) }}">
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
                                <span>Jumlah Stok Masuk</span>
                                @php $nextSort = next_sort_state('stok_masuk'); @endphp
                                <a
                                    href="{{ route('stock-entry.index', array_merge(request()->except(['sort_by', 'sort_order']), $nextSort)) }}">
                                    @if(request('sort_by') === 'stok_masuk')
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
                                    href="{{ route('stock-entry.index', array_merge(request()->except(['sort_by', 'sort_order']), $nextSort)) }}">
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
                    @foreach($groupedEntries as $groupKey => $entries)
                        @php
                            list($nomorInvoice, $supplierNama) = explode('|', $groupKey);
                        @endphp
                        <tr class="table-primary">
                            <td colspan="6">
                                <strong>Nomor Invoice:</strong> {{ $nomorInvoice }} &nbsp;&nbsp;
                                <strong>Supplier:</strong> {{ $supplierNama }} &nbsp;&nbsp;
                                <strong>Tanggal Masuk:</strong>
                                {{ \Carbon\Carbon::parse($entries->first()->tanggal_masuk)->format('d-m-Y') }}
                            </td>
                        </tr>
                        @foreach($entries as $entry)
                            <tr>
                                <td>{{ $entry->nama_barang ?? '-' }}</td>
                                <td>{{ $entry->kode_barang }}</td>
                                <td>{{ $entry->stok_masuk }}</td>
                                <td>{{ $entry->keterangan ?? '-' }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>

            </table>

            <div class="d-flex justify-content-center">
                {!! $stockEntries->links('pagination::bootstrap-4') !!}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- SweetAlert untuk pesan sukses atau error -->
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