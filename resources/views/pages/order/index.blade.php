@extends('layouts.app')

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
                                @else
                                    <button class="btn btn-secondary" disabled>Selesai</button>
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
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('.selesai-btn').on('click', function () {
                var orderId = $(this).data('id');

                // Konfirmasi dengan SweetAlert
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Pesanan ini akan ditandai sebagai selesai.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Selesai!',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Mengirim request AJAX untuk mengupdate status
                        $.ajax({
                            url: '/order/' + orderId + '/selesai', // Pastikan URL sesuai
                            type: 'PATCH',
                            dataType: 'json', // Pastikan format JSON
                            success: function (response) {
                                // Jika berhasil
                                if (response.success) {
                                    Swal.fire(
                                        'Selesai!',
                                        'Pesanan telah ditandai sebagai selesai.',
                                        'success'
                                    ).then(() => {
                                        location.reload(); // Refresh halaman setelah status berubah
                                    });
                                }
                            },
                            error: function (xhr, status, error) {
                                // Jika gagal
                                var errorMessage = xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : 'Terdapat kesalahan saat menyelesaikan pesanan.';
                                Swal.fire(
                                    'Gagal!',
                                    errorMessage,
                                    'error'
                                );
                            }
                        });
                    }
                });
            });
        });

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