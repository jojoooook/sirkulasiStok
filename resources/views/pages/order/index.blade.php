@extends('layouts.app')

@section('title', 'Riwayat Order Barang')

@section('content')
    <div class="container mt-4">
        <h1 class="mb-4 text-center">Riwayat Order Barang</h1>

        <!-- Search Form & Sorting -->
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <form action="{{ route('order.index') }}" method="GET" class="d-flex gap-2 flex-wrap align-items-center mb-3">
                <input type="text" name="nomor_order" class="form-control" placeholder="Cari Nomor Order"
                    value="{{ request('nomor_order') }}" style="min-width: 150px;">
                <input type="text" name="supplier" class="form-control" placeholder="Cari Supplier"
                    value="{{ request('supplier') }}" style="min-width: 150px;">
                <input type="date" name="tanggal_order" class="form-control" value="{{ request('tanggal_order') }}"
                    style="min-width: 150px;">
                <input type="text" name="nama_barang" class="form-control" placeholder="Cari Nama Barang"
                    value="{{ request('nama_barang') }}" style="min-width: 200px;">
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-search"></i> Cari
                </button>
            </form>
            <div class="d-flex gap-2">
                <a href="{{ route('order.create') }}" class="btn btn-primary shadow-sm">
                    <i class="fas fa-plus"></i> Tambah Order
                </a>
            </div>
        </div>

        @php
            $groupedOrders = $orders->groupBy('nomor_order');
        @endphp

        <div class="accordion" id="ordersAccordion">
            @foreach($groupedOrders as $nomorOrder => $orderGroup)
                @php
                    $firstOrder = $orderGroup->first();
                @endphp
                <div class="accordion-item mb-3 shadow-sm">
                    <h2 class="accordion-header" id="heading{{ $nomorOrder }}">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapse{{ $nomorOrder }}" aria-expanded="false"
                            aria-controls="collapse{{ $nomorOrder }}">
                            <strong>Nomor Order:</strong> {{ $nomorOrder }} &nbsp;&nbsp;
                            <strong>Supplier:</strong> {{ $firstOrder->supplier->nama }} &nbsp;&nbsp;
                            <strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($firstOrder->tanggal_order)->format('d-m-Y') }}
                            &nbsp;&nbsp;
                            <strong>Status:</strong> {{ ucfirst($firstOrder->status_order) }}
                        </button>
                    </h2>
                    <div id="collapse{{ $nomorOrder }}" class="accordion-collapse collapse"
                        aria-labelledby="heading{{ $nomorOrder }}" data-bs-parent="#ordersAccordion">
                        <div class="accordion-body p-0">
                            <table class="table table-bordered mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Kode Barang</th>
                                        <th>Nama Barang</th>
                                        <th>Jumlah</th>
                                        <th>Catatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orderGroup as $order)
                                        <tr>
                                            <td>{{ $order->item->kode_barang }}</td>
                                            <td>{{ $order->item->nama_barang }}</td>
                                            <td>{{ $order->jumlah_order }}</td>
                                            <td>{{ $order->catatan }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @if($firstOrder->status_order === 'pending')
                                <div class="p-3 d-flex justify-content-end gap-2">
                                    <a href="{{ route('order.showBatchComplete', ['nomor_order' => $nomorOrder]) }}"
                                        class="btn btn-success">
                                        Selesaikan Pesanan
                                    </a>
                                    <form action="{{ route('order.cancel', ['id' => $nomorOrder]) }}" method="POST"
                                        class="d-inline cancel-order-form">
                                        @csrf
                                        @method('PATCH')
                                        <button type="button" class="btn btn-danger cancel-order-btn">
                                            Batalkan Pesanan
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="d-flex justify-content-center">
            {!! $orders->links('pagination::bootstrap-4') !!}
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
                                <textarea class="form-control" id="catatan_batal" name="catatan" rows="3"
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
    </div>
@endsection