@extends('layouts.app')

@section('title', 'Riwayat Order Barang')

@section('content')
    <div class="container mt-4">
        <h1 class="mb-4 text-center">Riwayat Order Barang</h1>

        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <button class="btn btn-outline-secondary" type="button" data-bs-toggle="collapse"
                data-bs-target="#searchFormCollapseOrder" aria-expanded="false" aria-controls="searchFormCollapseOrder">
                <i class="fas fa-filter"></i> Filter Pencarian
            </button>

            <a href="{{ route('order.create') }}" class="btn btn-primary shadow-sm ms-auto me-2">
                <i class="fas fa-plus"></i> Tambah Order
            </a>
        </div>

        <div class="collapse mb-3" id="searchFormCollapseOrder">
            <div class="card card-body">
                <form action="{{ route('order.index') }}" method="GET" class="d-flex gap-2 flex-wrap align-items-center">
                    <input type="text" name="nomor_order" class="form-control" placeholder="Cari Nomor Order"
                        value="{{ request('nomor_order') }}" style="min-width: 150px;">
                    <input type="text" name="supplier" class="form-control" placeholder="Cari Supplier"
                        value="{{ request('supplier') }}" style="min-width: 150px;">
                    <input type="date" name="tanggal_order" class="form-control" value="{{ request('tanggal_order') }}"
                        style="min-width: 150px;">
                    <input type="text" name="nama_barang" class="form-control" placeholder="Cari Nama Barang"
                        value="{{ request('nama_barang') }}" style="min-width: 200px;">
                    <select name="status_order" class="form-select" style="min-width: 150px;">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status_order') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="selesai" {{ request('status_order') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="dibatalkan" {{ request('status_order') == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan
                        </option>
                    </select>
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search"></i> Cari
                    </button>
                </form>
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
                        <button class="accordion-button collapsed d-flex align-items-center" type="button"
                            data-bs-toggle="collapse" data-bs-target="#collapse{{ $nomorOrder }}" aria-expanded="false"
                            aria-controls="collapse{{ $nomorOrder }}">
                            <div class="me-auto d-flex flex-wrap align-items-center gap-2">
                                <strong>Nomor Order:</strong> {{ $nomorOrder }}
                                <strong>Supplier:</strong> {{ $firstOrder->supplier->nama }}
                                <strong>Tanggal:</strong>
                                {{ \Carbon\Carbon::parse($firstOrder->tanggal_order)->format('d-m-Y') }}
                                <strong>Status:</strong> {{ ucfirst($firstOrder->status_order) }}
                            </div>
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
                            <div class="p-3 d-flex justify-content-end align-items-center gap-2 flex-wrap">
                                <a href="{{ route('order.show', $nomorOrder) }}" class="btn btn-secondary">
                                    Detail Order <i class="fas fa-external-link-alt ms-1"></i>
                                </a>
                                @if($firstOrder->status_order === 'pending')
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
                                @endif
                            </div>
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
                <form id="formBatalOrder" method="POST" action="">
                    @csrf
                    @method('PATCH')
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

@endsection

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const searchFormCollapseElement = document.getElementById('searchFormCollapseOrder');
                const triggerButton = document.querySelector(`button[data-bs-target="#searchFormCollapseOrder"]`);

                if (searchFormCollapseElement && triggerButton) {
                    const urlParams = new URLSearchParams(window.location.search);
                    let hasActiveFilter = false;
                    const filterParams = ['nomor_order', 'supplier', 'tanggal_order', 'nama_barang', 'status_order'];

                    for (const param of filterParams) {
                        if (urlParams.has(param) && urlParams.get(param).trim() !== '') {
                            hasActiveFilter = true;
                            break;
                        }
                    }

                    if (hasActiveFilter) {
                        searchFormCollapseElement.classList.add('show');
                        triggerButton.setAttribute('aria-expanded', 'true');
                    }
                }

                const cancelButtons = document.querySelectorAll('.cancel-order-btn');
                cancelButtons.forEach(button => {
                    button.addEventListener('click', function (event) {
                        event.preventDefault(); // Prevent default button behavior
                        const form = this.closest('form');
                        const actionUrl = form.getAttribute('action');
                        const parts = actionUrl.split('/');
                        const orderId = parts[parts.length - 2];

                        // Populate hidden input with order ID
                        document.getElementById('order_id_batal').value = orderId;
                        // Show the modal
                        const modalElement = document.getElementById('modalKonfirmasiBatal');
                        const modal = new bootstrap.Modal(modalElement);
                        modal.show();
                        // Set the form action dynamically to the correct cancel route
                        const formBatalOrder = document.getElementById('formBatalOrder');
                        formBatalOrder.action = `/order/${orderId}/cancel`; // Corrected action URL
                        // Handle form submission with confirmation
                        formBatalOrder.onsubmit = function (e) {
                            e.preventDefault();
                            const catatan = document.getElementById('catatan_batal').value;
                            Swal.fire({
                                title: 'Yakin membatalkan pesanan?',
                                html: `<p><strong>Alasan:</strong> ${catatan || 'Tidak ada alasan diberikan'}</p>`,
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Ya, Batalkan',
                                cancelButtonText: 'Batal',
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    formBatalOrder.submit();
                                }
                            });
                        };
                    });
                });
            });
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#28a745'
                });
            @endif
        </script>
    @endpush