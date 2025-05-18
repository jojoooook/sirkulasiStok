@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Daftar Nota</h1>

    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalTambahNota">
        Tambah Nomor Nota
    </button>

    <div class="mb-3">
        <div class="d-flex gap-2">
            <!-- Search Nota -->
            <form action="{{ route('nota.index') }}" method="GET" class="flex-grow-1">
                <div class="input-group">
                    <input type="text" class="form-control" name="search" placeholder="Cari Nomor Nota" value="{{ request('search') }}">
                    <button class="btn btn-outline-secondary" type="submit">Cari</button>
                </div>
            </form>

            <!-- Sort Nota -->
            <form action="{{ route('nota.index') }}" method="GET" class="flex-shrink-0" style="width: 200px;">
                <select class="form-select" name="sort" onchange="this.form.submit()">
                    <option value="" disabled {{ request('sort') ? '' : 'selected' }}>Sort By</option>
                    <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>Nomor Nota Asc</option>
                    <option value="desc" {{ request('sort') == 'desc' ? 'selected' : '' }}>Nomor Nota Desc</option>
                </select>
            </form>
        </div>
    </div>


    <div class="card">
        <div class="card-body">
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nomor Nota</th>
                        <th>Jumlah Item</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($notas as $nomor_nota => $orders)
                        <tr>
                            <td>{{ $nomor_nota }}</td>
                            <td>{{ $orders->count() }}</td>
                            <td>
                                <button class="btn btn-info btn-sm" id="lihatBarangButton-{{ $loop->index }}"
                                    onclick="toggleBarang({{ $loop->index }})">
                                    Lihat Barang
                                </button>
                                <button class="btn btn-info btn-sm" id="tutupBarangButton-{{ $loop->index }}"
                                    style="display:none;" onclick="toggleBarang({{ $loop->index }})">
                                    Tutup Barang
                                </button>

                                <form action="{{ route('nota.destroy', $nomor_nota) }}" method="POST" style="display:inline;" id="form-delete-{{ $loop->index }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-danger btn-sm" onclick="deleteNota({{ $loop->index }})">
                                        Hapus Nota
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <!-- Detail Barang per Nota -->
                        <tr id="items-{{ $loop->index }}" style="display:none;">
                            <td colspan="3">
                                <!-- Search and Sort Items per Nota -->
                                <form method="GET" class="mb-3">
                                    <input type="hidden" name="search" value="{{ request('search') }}">
                                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input type="text" name="search_item_{{ $loop->index }}" class="form-control" placeholder="Cari Nama Barang atau Supplier..." value="{{ request("search_item_$loop->index") }}">
                                        </div>
                                        <div class="col-md-3">
                                            <select name="sort_item_{{ $loop->index }}" class="form-select" onchange="this.form.submit()">
                                                <option value="">Urutkan</option>
                                                <option value="nama_barang_asc" {{ request("sort_item_$loop->index") == 'nama_barang_asc' ? 'selected' : '' }}>Nama Barang A-Z</option>
                                                <option value="nama_barang_desc" {{ request("sort_item_$loop->index") == 'nama_barang_desc' ? 'selected' : '' }}>Nama Barang Z-A</option>
                                            </select>
                                        </div>
                                    </div>
                                </form>

                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Nama Supplier</th>
                                            <th>Kode Barang</th>
                                            <th>Nama Barang</th>
                                            <th>Jumlah Order</th>
                                            <th>Jumlah Barang Masuk</th>
                                            <th>Tanggal Order</th>
                                            <th>Catatan</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $filteredOrders = $orders;

                                            $searchItem = request("search_item_$loop->index");
                                            if ($searchItem) {
                                                $filteredOrders = $filteredOrders->filter(function ($order) use ($searchItem) {
                                                    return str_contains(strtolower($order->item->nama_barang ?? ''), strtolower($searchItem))
                                                        || str_contains(strtolower($order->supplier->nama ?? ''), strtolower($searchItem));
                                                });
                                            }

                                            $sortItem = request("sort_item_$loop->index");
                                            if ($sortItem === 'nama_barang_asc') {
                                                $filteredOrders = $filteredOrders->sortBy(function($order) {
                                                    return $order->item->nama_barang ?? '';
                                                });
                                            } elseif ($sortItem === 'nama_barang_desc') {
                                                $filteredOrders = $filteredOrders->sortByDesc(function($order) {
                                                    return $order->item->nama_barang ?? '';
                                                });
                                            }
                                        @endphp

                                        @forelse($filteredOrders as $order)
                                            <tr>
                                                <td>{{ $order->supplier->nama ?? '-' }}</td>
                                                <td>{{ $order->kode_barang }}</td>
                                                <td>{{ $order->item->nama_barang ?? '-' }}</td>
                                                <td>{{ $order->jumlah_order }}</td>
                                                <td>{{ $order->stok_masuk_display }}</td>
                                                <td>{{ \Carbon\Carbon::parse($order->tanggal_order)->format('Y-m-d') }}</td>
                                                <td>{{ $order->catatan ?? '-' }}</td>
                                                <td>{{ $order->status_order ?? 'Pending' }}</td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="8">Tidak ada barang yang cocok.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $notas->links() }}
        </div>
    </div>
</div>

<!-- Modal Tambah Nomor Nota -->
<div class="modal fade" id="modalTambahNota" tabindex="-1" aria-labelledby="modalTambahNotaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('nota.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahNotaLabel">Tambah Nomor Nota Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="mb-3">
                        <label for="nomor_nota" class="form-label">Nomor Nota</label>
                        <input type="text" class="form-control" id="nomor_nota" name="nomor_nota" required value="{{ old('nomor_nota') }}" oninput="this.value = this.value.toUpperCase()">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="submit-button" class="btn btn-primary">Tambah</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function toggleBarang(index) {
        const itemRow = document.getElementById('items-' + index);
        const lihatButton = document.getElementById('lihatBarangButton-' + index);
        const tutupButton = document.getElementById('tutupBarangButton-' + index);
        const isHidden = itemRow.style.display === "none";

        itemRow.style.display = isHidden ? "table-row" : "none";
        lihatButton.style.display = isHidden ? "none" : "inline-block";
        tutupButton.style.display = isHidden ? "inline-block" : "none";
    }

    function deleteNota(index) {
        Swal.fire({
            title: 'Yakin ingin menghapus nota ini?',
            text: "Data yang terhapus tidak bisa dikembalikan",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('form-delete-' + index).submit();
            }
        });
    }
</script>
@endpush
