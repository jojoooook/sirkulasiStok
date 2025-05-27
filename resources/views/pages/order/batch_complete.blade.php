@extends('layouts.app')

@section('title', 'Selesaikan Pesanan Batch')

@section('content')
    <div class="container mt-4">
        <h1 class="mb-4 text-center">Selesaikan Pesanan Batch</h1>

        <form id="batchCompleteForm">
            @csrf
            <div class="mb-3">
                <label for="nomor_invoice" class="form-label">Nomor Invoice</label>
                <input type="text" id="nomor_invoice" name="nomor_invoice" class="form-control"
                    placeholder="Masukkan nomor invoice" required oninput="this.value = this.value.toUpperCase()">
            </div>
            <div class="mb-3">
                <label for="tanggal_invoice" class="form-label">Tanggal Invoice</label>
                <input type="date" id="tanggal_invoice" name="tanggal_invoice" class="form-control" required>
            </div>
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Supplier</th>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Jumlah Order</th>
                        <th>Jumlah Masuk</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr>
                            <td>{{ $order->supplier->nama }}</td>
                            <td>{{ $order->item->kode_barang }}</td>
                            <td>{{ $order->item->nama_barang }}</td>
                            <td>{{ $order->jumlah_order }}</td>
                            <td>
                                <input type="number" name="orders[{{ $loop->index }}][jumlah_masuk]" min="0"
                                    max="{{ $order->jumlah_order }}" class="form-control" value="{{ $order->jumlah_order }}"
                                    required>
                                <input type="hidden" name="orders[{{ $loop->index }}][nomor_order]"
                                    value="{{ $order->nomor_order }}">
                                <input type="hidden" name="orders[{{ $loop->index }}][kode_barang]"
                                    value="{{ $order->kode_barang }}">
                            </td>
                            <td>
                                <input type="text" name="orders[{{ $loop->index }}][catatan]" class="form-control"
                                    placeholder="Opsional">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="d-flex justify-content-between">
                <a href="{{ route('order.index') }}" class="btn btn-secondary">Kembali</a>
                <button type="submit" class="btn btn-success">Selesaikan Pesanan</button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('#batchCompleteForm').submit(function (e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Konfirmasi Penyelesaian',
                    text: 'Apakah Anda yakin ingin menyelesaikan pesanan ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Selesaikan',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route("order.batchComplete", ["nomor_order" => $nomor_order]) }}',
                            type: 'PATCH',
                            data: $(this).serialize(),
                            success: function (response) {
                                if (response.success) {
                                    Swal.fire('Berhasil', response.message, 'success').then(() => {
                                        window.location.href = '{{ route("order.index") }}';
                                    });
                                } else {
                                    Swal.fire('Gagal', response.message, 'error');
                                }
                            },
                            error: function () {
                                Swal.fire('Gagal', 'Terjadi kesalahan saat menyelesaikan pesanan.', 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush