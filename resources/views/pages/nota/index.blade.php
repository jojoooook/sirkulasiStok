@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Daftar Nota</h1>

        <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalTambahNota">
            Tambah Nomor Nota
        </button>

        <div class="card">
            <div class="card-body">
                <!-- Pesan Error dan Sukses -->
                @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
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
                                    <!-- Tombol untuk Lihat Barang -->
                                    <button class="btn btn-info btn-sm" id="lihatBarangButton-{{ $loop->index }}"
                                        onclick="toggleBarang({{ $loop->index }})">
                                        Lihat Barang
                                    </button>
                                    <!-- Tombol untuk Tutup Barang -->
                                    <button class="btn btn-info btn-sm" id="tutupBarangButton-{{ $loop->index }}"
                                        style="display:none;" onclick="toggleBarang({{ $loop->index }})">
                                        Tutup Barang
                                    </button>

                                    <form action="{{ route('nota.destroy', $nomor_nota) }}" method="POST"
                                        style="display:inline;" id="form-delete-{{ $loop->index }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-danger btn-sm"
                                            onclick="deleteNota({{ $loop->index }})">
                                            Hapus Nota
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <tr id="items-{{ $loop->index }}" style="display:none;">
                                <td colspan="3">
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
                                            @foreach($orders as $order)
                                                <tr>
                                                    <td>{{ $order->supplier->nama ?? '-' }}</td>
                                                    <td>{{ $order->kode_barang }}</td>
                                                    <td>{{ $order->item->nama_barang ?? '-' }}</td>
                                                    <td>{{ $order->jumlah_order }}</td>
                                                    <td>{{ $order->stok_masuk_display }}</td>
                                                    <!-- Menampilkan stok_masuk_display -->
                                                    <td>{{ \Carbon\Carbon::parse($order->tanggal_order)->format('Y-m-d') }}</td>
                                                    <td>{{ $order->catatan ?? '-' }}</td>
                                                    <td>{{ $order->status_order ?? 'Pending' }}</td> <!-- Status barang -->
                                                </tr>
                                            @endforeach
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
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label for="nomor_nota" class="form-label">Nomor Nota</label>
                            <input type="text" class="form-control" id="nomor_nota" name="nomor_nota" required
                                value="{{ old('nomor_nota') }}">
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Tambah</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Tombol lihat barang dan tutup barang toggle function
            window.toggleBarang = function (index) {
                let itemRow = document.getElementById('items-' + index);
                let lihatButton = document.getElementById('lihatBarangButton-' + index);
                let tutupButton = document.getElementById('tutupBarangButton-' + index);

                if (itemRow.style.display === "none") {
                    itemRow.style.display = "table-row";
                    tutupButton.style.display = "inline-block"; // tampilkan tutup barang
                    lihatButton.style.display = "none"; // sembunyikan lihat barang
                } else {
                    itemRow.style.display = "none";
                    tutupButton.style.display = "none"; // sembunyikan tutup barang
                    lihatButton.style.display = "inline-block"; // tampilkan lihat barang
                }
            };

            // SweetAlert untuk Hapus Nota
            window.deleteNota = function (index) {
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
            };
        });
    </script>
@endpush