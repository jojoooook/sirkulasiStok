@extends('layouts.app')

@section('title', 'Tambah Order Barang')

@section('content')
    <div class="container">
        <h1 class="text-center mb-4">Tambah Order Barang</h1>

        <form action="{{ route('order.store') }}" method="POST">
            @csrf

            <!-- Nomor Nota -->
            <div class="mb-3">
                <label for="nomor_nota" class="form-label">Nomor Nota</label>
                <input type="text" name="nomor_nota" id="nomor_nota" class="form-control" value="{{ old('nomor_nota') }}"
                    required>
            </div>

            <!-- Supplier -->
            <div class="mb-3">
                <label for="supplier_id" class="form-label">Pilih Supplier</label>
                <select name="supplier_id" id="supplier_id" class="form-control select2" required>
                    <option value="">Pilih Supplier</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->kode_supplier }}" {{ old('supplier_id') == $supplier->kode_supplier ? 'selected' : '' }}>
                            {{ $supplier->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Order Items (Barang) dan Pembatas untuk tiap pesanan -->
            <div id="order-items" class="mb-3">
                <div class="order-item-container card p-3 mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="item_id_0" class="form-label">Pilih Barang</label>
                            <select name="items[0][item_id]" id="item_id_0" class="form-control select2" required>
                                <option value="">Pilih Barang</option>
                                <!-- Barang akan dimuat berdasarkan supplier -->
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="jumlah_order" class="form-label">Jumlah Order</label>
                            <input type="number" name="items[0][jumlah_order]" class="form-control" min="1"
                                value="{{ old('items.0.jumlah_order') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label for="catatan_0" class="form-label">Catatan (Opsional)</label>
                            <input type="text" name="items[0][catatan]" id="catatan_0" class="form-control"
                                value="{{ old('items.0.catatan') }}">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-primary" id="add-item">Tambah Pesanan</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-success">Simpan Order</button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            // Inisialisasi select2
            $('.select2').select2({
                placeholder: "Pilih Barang",
                allowClear: true
            });

            // Menampilkan SweetAlert jika ada session sukses atau error
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

            // Memuat barang berdasarkan supplier yang dipilih
            $('#supplier_id').on('change', function () {
                var supplierId = $(this).val();
                $('#item_id_0').empty().append('<option value="">Pilih Barang</option>').trigger('change');

                if (supplierId) {
                    $.ajax({
                        url: '/get-items/' + supplierId, // Endpoint untuk mengambil barang berdasarkan supplier
                        type: 'GET',
                        dataType: 'json',
                        success: function (data) {
                            // Menambahkan barang yang tersedia ke select2
                            $.each(data, function (key, item) {
                                $('#item_id_0').append('<option value="' + item.kode_barang + '">' + item.nama_barang + '</option>');
                            });
                            $('#item_id_0').trigger('change'); // Refresh select2
                        },
                        error: function () {
                            alert('Gagal mengambil data barang.');
                        }
                    });
                }
            });

            // Menambahkan item ke order
            let itemCount = 1;  // Menjaga count item
            $('#add-item').on('click', function () {
                itemCount++;
                let newItem = `
                                            <div class="order-item-container card p-3 mb-3">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label for="item_id_${itemCount}" class="form-label">Pilih Barang</label>
                                                        <select name="items[${itemCount}][item_id]" id="item_id_${itemCount}" class="form-control select2" required>
                                                            <option value="">Pilih Barang</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="jumlah_order" class="form-label">Jumlah Order</label>
                                                        <input type="number" name="items[${itemCount}][jumlah_order]" class="form-control" min="1" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="catatan_${itemCount}" class="form-label">Catatan (Opsional)</label>
                                                        <input type="text" name="items[${itemCount}][catatan]" id="catatan_${itemCount}" class="form-control">
                                                    </div>
                                                    <div class="col-md-2 d-flex align-items-end">
                                                        <button type="button" class="btn btn-danger remove-item">Hapus Pesanan</button>
                                                    </div>
                                                </div>
                                            </div>
                                        `;
                $('#order-items').append(newItem);

                // Reinitialize select2 pada item baru
                $('.select2').select2({
                    placeholder: "Pilih Barang",
                    allowClear: true
                });

                // Mengambil barang berdasarkan supplier yang dipilih untuk item baru
                var supplierId = $('#supplier_id').val();
                if (supplierId) {
                    $.ajax({
                        url: '/get-items/' + supplierId, // Endpoint untuk mengambil barang berdasarkan supplier
                        type: 'GET',
                        dataType: 'json',
                        success: function (data) {
                            $.each(data, function (key, item) {
                                $('#item_id_' + itemCount).append('<option value="' + item.kode_barang + '">' + item.nama_barang + '</option>');
                            });
                            $('#item_id_' + itemCount).trigger('change');
                        },
                        error: function () {
                            alert('Gagal mengambil data barang.');
                        }
                    });
                }

                // Menambahkan event untuk menghapus item
                $('.remove-item').on('click', function () {
                    $(this).closest('.order-item-container').remove();
                });
            });
        });
    </script>
@endpush