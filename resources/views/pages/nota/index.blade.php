@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Daftar Nota</h1>

        <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalTambahNota">
            Tambah Nomor Nota
        </button>

        <div class="card">
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
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
                                    <button class="btn btn-info btn-sm toggle-collapse-btn" data-bs-toggle="collapse"
                                        data-bs-target="#items-{{ $loop->index }}" aria-expanded="false"
                                        aria-controls="items-{{ $loop->index }}" onclick="toggleButtonText(this)">
                                        Lihat Barang
                                    </button>
                                    <form action="{{ route('nota.destroy', $nomor_nota) }}" method="POST"
                                        style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus nota ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Hapus Nota</button>
                                    </form>
                                </td>
                            </tr>
                            <tr class="collapse" id="items-{{ $loop->index }}">
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
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($orders as $order)
                                                <tr>
                                                    <td>{{ $order->supplier->nama ?? '-' }}</td>
                                                    <td>{{ $order->kode_barang }}</td>
                                                    <td>{{ $order->item->nama_barang ?? '-' }}</td>
                                                    <td>{{ $order->jumlah_order }}</td>
                                                    <td>
                                                        @php
                                                            $stockEntry = \App\Models\StockEntry::where('nomor_nota', $order->nomor_nota)
                                                                ->where('kode_barang', $order->kode_barang)
                                                                ->first();
                                                        @endphp
                                                        {{ $stockEntry->stok_masuk ?? '-' }}
                                                    </td>
                                                    <td>{{ \Carbon\Carbon::parse($order->tanggal_order)->format('Y-m-d') }}</td>
                                                    <td>{{ $order->catatan ?? '-' }}</td>
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
                        <h5 class="modal-title id= " modalTambahNotaLabel">Tambah Nomor Nota Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nomor_nota" class="form-label">Nomor Nota</label>
                            <input type="text" class="form-control" id="nomor_nota" name="nomor_nota" required>
                            @error('nomor_nota')
                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                            @enderror
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

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Ambil semua tombol dengan class 'toggle-collapse-btn'
            var toggleButtons = document.querySelectorAll('.toggle-collapse-btn');

            toggleButtons.forEach(function (button) {
                // Target collapse dari tombol ini
                var targetSelector = button.getAttribute('data-bs-target');
                var collapseElement = document.querySelector(targetSelector);

                // Tambahkan event listener saat collapse akan ditampilkan (show)
                collapseElement.addEventListener('show.bs.collapse', function () {
                    button.textContent = 'Tutup Barang';
                });

                // Tambahkan event listener saat collapse akan disembunyikan (hide)
                collapseElement.addEventListener('hide.bs.collapse', function () {
                    button.textContent = 'Lihat Barang';
                });
            });
        });
    </script>
@endsection