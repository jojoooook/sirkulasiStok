@extends('layouts.app')

@section('title', content: 'Riwayat Order Barang')

@section('content')
    <div class="container mt-4">
        <h1 class="mb-4 text-center">Riwayat Order Barang</h1>

        <a href="{{ route('order.create') }}" class="btn btn-primary mb-3 shadow-sm">
            <i class="fas fa-plus"></i> Tambah Order
        </a>

        <div class="table-responsive">
            <table class="table table-hover table-bordered shadow-sm">
                <thead class="thead-light">
                    <tr>
                        <th>Supplier</th>
                        <th>Nama Barang</th>
                        <th>Jumlah Order</th>
                        <th>Tanggal Order</th>
                        <th>Status</th>
                        <th>Catatan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr>
                            <td>{{ $order->supplier->nama }}</td>
                            <td>{{ $order->item->nama_barang }}</td>
                            <td>{{ $order->jumlah_order }}</td>
                            <td>{{ $order->tanggal_order }}</td>
                            <td>{{ $order->status_order }}</td>
                            <td>{{ $order->catatan }}</td>
                            <td>
                                @if($order->status_order === 'pending')
                                    <button class="btn btn-success selesai-btn" data-id="{{ $order->id }}">Selesaikan
                                        Pesanan</button>
                                    <button class="btn btn-danger batal-btn" data-id="{{ $order->id }}">Batalkan Pesanan</button>
                                @else
                                    <button class="btn btn-secondary" disabled>Selesai</button>
                                    <button class="btn btn-secondary" disabled>Batalkan</button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="d-flex justify-content-center">
                {!! $orders->links('pagination::bootstrap-4') !!}
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalKonfirmasiSelesai" tabindex="-1" aria-labelledby="modalKonfirmasiSelesaiLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form id="formSelesaiOrder">
                @csrf
                <input type="hidden" name="order_id" id="order_id">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalKonfirmasiSelesaiLabel">Konfirmasi Penyelesaian Pesanan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="jumlah_masuk" class="form-label">Jumlah Barang Masuk</label>
                            <input type="number" min="1" class="form-control" id="jumlah_masuk" name="jumlah_masuk"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="catatan" class="form-label">Catatan</label>
                            <textarea class="form-control" id="catatan" name="catatan" rows="3"
                                placeholder="Opsional"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Konfirmasi Selesai</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="modalKonfirmasiBatal" tabindex="-1" aria-labelledby="modalKonfirmasiBatalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form id="formBatalOrder">
                @csrf
                <input type="hidden" name="order_id" id="order_id_batal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalKonfirmasiBatalLabel">Konfirmasi Pembatalan Pesanan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="catatan_batal" class="form-label">Alasan Pembatalan</label>
                            <textarea class="form-control" id="catatan_batal" name="catatan" rows="3" required
                                placeholder="Opsional"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Batalkan Pesanan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            let selectedOrderId = null;

            $('.selesai-btn').on('click', function () {
                selectedOrderId = $(this).data('id');
                $('#order_id').val(selectedOrderId);
                $('#modalKonfirmasiSelesai').modal('show');
            });

            $('.batal-btn').on('click', function () {
                selectedOrderId = $(this).data('id');
                $('#order_id_batal').val(selectedOrderId);
                $('#modalKonfirmasiBatal').modal('show');
            });

            // Menangani form submit untuk penyelesaian pesanan
            $('#formSelesaiOrder').submit(function (e) {
                e.preventDefault();

                const jumlahMasuk = $('#jumlah_masuk').val();
                const catatan = $('#catatan').val();

                Swal.fire({
                    title: 'Konfirmasi Akhir',
                    text: 'Apakah data yang dimasukkan sudah benar?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Selesaikan',
                    cancelButtonText: 'Periksa Lagi'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/order/' + selectedOrderId,
                            type: 'PATCH',
                            data: {
                                _token: '{{ csrf_token() }}',
                                jumlah_masuk: jumlahMasuk,
                                catatan: catatan
                            },
                            success: function (response) {
                                if (response.success) {
                                    Swal.fire('Sukses!', response.message, 'success').then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire('Gagal!', response.message, 'error');
                                }
                            },
                            error: function (xhr) {
                                Swal.fire('Gagal!', 'Terjadi kesalahan.', 'error');
                            }
                        });
                    }
                });
            });

            // Menangani form submit untuk pembatalan pesanan
            $('#formBatalOrder').submit(function (e) {
                e.preventDefault();

                const catatanBatal = $('#catatan_batal').val();

                Swal.fire({
                    title: 'Konfirmasi Akhir',
                    text: 'Apakah Anda yakin ingin membatalkan pesanan?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Batalkan',
                    cancelButtonText: 'Periksa Lagi'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/order/' + selectedOrderId + '/cancel',
                            type: 'PATCH',
                            data: {
                                _token: '{{ csrf_token() }}',
                                catatan: catatanBatal
                            },
                            success: function (response) {
                                if (response.success) {
                                    Swal.fire('Sukses!', response.message, 'success').then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire('Gagal!', response.message, 'error');
                                }
                            },
                            error: function (xhr) {
                                Swal.fire('Gagal!', 'Terjadi kesalahan.', 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush