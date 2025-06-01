@extends('layouts.app')
@section('title', 'Tambah Order Barang')
@section('content')
    <div class="container">
        <h1 class="text-center mb-4">Tambah Order Barang</h1>
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('order.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="nomor_order" class="form-label">Nomor Order</label>
                <input type="text" name="nomor_order" id="nomor_order" class="form-control"
                    value="{{ old('nomor_order', $nextNomorOrder) }}" readonly>
            </div>
            <div class="mb-3">
                <label for="tanggal_order" class="form-label">Tanggal Order</label>
                <input type="date" name="tanggal_order" id="tanggal_order" class="form-control"
                    value="{{ old('tanggal_order', date('Y-m-d')) }}" required>
            </div>

            <div class="mb-3">
                <label for="supplier_id" class="form-label">Pilih Supplier</label>
                <select name="supplier_id" id="supplier_id" class="form-control select2" required>
                    <option value="">Pilih Supplier</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->kode_supplier }}" {{ old('supplier_id') == $supplier->kode_supplier ? 'selected' : '' }}>{{ $supplier->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div id="loading-items" class="mb-3" style="display:none;">
                <div class="alert alert-info">Memuat barang, mohon tunggu...</div>
            </div>
            <div id="order-items" class="mb-3" style="display:none;">
                <div class="order-item-container card p-3 mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="item_id_0" class="form-label">Pilih Barang</label>
                            <select name="items[0][item_id]" id="item_id_0" class="form-control item-select" required>
                                <option value="">Pilih Barang</option>
                            </select>
                            <small class="text-muted" id="stock_info_0"></small>
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
            $('#supplier_id').select2({ placeholder: "Pilih Supplier", allowClear: true });
            function initItemSelect() {
                $('.item-select').select2({ placeholder: "Pilih Barang", allowClear: true });
            }
            function checkDuplicateItem(selectedId) {
                let selectedItems = [];
                let duplicateFound = false;
                $('.item-select').each(function () {
                    let val = $(this).val();
                    if (val) {
                        if (selectedItems.includes(val)) {
                            duplicateFound = true;
                            return false;
                        }
                        selectedItems.push(val);
                    }
                });
                return duplicateFound;
            }
            function onItemSelectChange() {
                $('.item-select').off('change').on('change', function () {
                    let currentVal = $(this).val();
                    if (!currentVal) return;
                    let selectedItems = [];
                    let duplicate = false;
                    $('.item-select').each(function () {
                        let val = $(this).val();
                        if (val) {
                            if (selectedItems.includes(val)) {
                                duplicate = true;
                                return false;
                            }
                            selectedItems.push(val);
                        }
                    });
                    if (duplicate) {
                        Swal.fire({ icon: 'warning', title: 'Duplikat Barang', text: 'Barang sudah dipilih sebelumnya.', });
                        $(this).val(null).trigger('change');
                    }
                });
            }
            initItemSelect();
            onItemSelectChange();
            @if(session('success'))
                Swal.fire({ icon: 'success', title: 'Berhasil', text: '{{ session('success') }}', });
            @endif
            @if(session('error'))
                Swal.fire({ icon: 'error', title: 'Gagal', text: '{{ session('error') }}', });
            @endif
            $('#supplier_id').on('change', function () {
                var supplierId = $(this).val();
                $('#order-items').hide();
                $('#loading-items').show();
                // Remove all item containers except the first one
                $('.item-select').not('#item_id_0').closest('.order-item-container').remove();
                $('#item_id_0').empty().append('<option value="">Pilih Barang</option>');
                $('small[id^="stock_info_"]').text('');
                if (supplierId) {
                    $.ajax({
                        url: '/get-items/' + supplierId,
                        type: 'GET',
                        dataType: 'json',
                        success: function (data) {
                            $.each(data, function (key, item) {
                                $('#item_id_0').append('<option value="' + item.kode_barang + '">' + item.kode_barang + ' - ' + item.nama_barang + ' - sisa stok ' + item.stok + '</option>');
                            });
                            // After appending options, check if there is a selected value and add option if missing
                            var selectedVal = $('#item_id_0').val();
                            if (selectedVal && $('#item_id_0').find("option[value='" + selectedVal + "']").length === 0) {
                                var foundItem = data.find(item => item.kode_barang === selectedVal);
                                if (foundItem) {
                                    $('#item_id_0').append('<option value="' + foundItem.kode_barang + '">' + foundItem.kode_barang + ' - ' + foundItem.nama_barang + ' - sisa stok ' + foundItem.stok + '</option>');
                                }
                            }
                            // Destroy and reinitialize select2 to refresh display
                            $('#item_id_0').select2('destroy');
                            $('#item_id_0').select2({ placeholder: "Pilih Barang", allowClear: true });
                            // Set selected value explicitly
                            if (selectedVal) {
                                $('#item_id_0').val(selectedVal).trigger('change');
                            }
                            $('#loading-items').hide();
                            $('#order-items').show();
                        },
                        error: function () {
                            alert('Gagal mengambil data barang.');
                            $('#loading-items').hide();
                        }
                    });
                } else {
                    $('#loading-items').hide();
                    $('#order-items').hide();
                }
            });
            let itemCount = 0;
            $('#add-item').on('click', function () {
                itemCount++;
                let newItem = `
                                                                                <div class="order-item-container card p-3 mb-3">
                                                                                <div class="row">
                                                                                <div class="col-md-6">
                                                                                <label for="item_id_${itemCount}" class="form-label">Pilih Barang</label>
                                                                                <select name="items[${itemCount}][item_id]" id="item_id_${itemCount}" class="form-control item-select" required>
                                                                                <option value="">Pilih Barang</option>
                                                                                </select>
                                                                                <small class="text-muted" id="stock_info_${itemCount}"></small>
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
                initItemSelect();
                onItemSelectChange();
                var supplierId = $('#supplier_id').val();
                if (supplierId) {
                    $.ajax({
                        url: '/get-items/' + supplierId,
                        type: 'GET',
                        dataType: 'json',
                        success: function (data) {
                            $.each(data, function (key, item) {
                                $('#item_id_' + itemCount).append('<option value="' + item.kode_barang + '">' + item.kode_barang + ' - ' + item.nama_barang + ' - sisa stok ' + item.stok + '</option>');
                            });
                            // Destroy and reinitialize select2 to refresh display
                            $('#item_id_' + itemCount).select2('destroy');
                            $('#item_id_' + itemCount).select2({ placeholder: "Pilih Barang", allowClear: true });
                            // Set selected value explicitly if any
                            var selectedVal = $('#item_id_' + itemCount).val();
                            if (selectedVal) {
                                $('#item_id_' + itemCount).val(selectedVal).trigger('change');
                            }
                        },
                        error: function () {
                            alert('Gagal mengambil data barang.');
                        }
                    });
                }
                $('.remove-item').off('click').on('click', function () {
                    $(this).closest('.order-item-container').remove();
                });
            });
            // Fix for showing selected item text on page load
            $('.item-select').each(function () {
                var select = $(this);
                var selectedVal = select.val();
                if (selectedVal) {
                    var supplierId = $('#supplier_id').val();
                    if (supplierId) {
                        $.ajax({
                            url: '/get-items/' + supplierId,
                            type: 'GET',
                            dataType: 'json',
                            success: function (data) {
                                var found = false;
                                $.each(data, function (key, item) {
                                    if (item.kode_barang === selectedVal) {
                                        if (select.find("option[value='" + selectedVal + "']").length === 0) {
                                            select.append('<option value="' + item.kode_barang + '">' + item.kode_barang + ' - ' + item.nama_barang + ' - sisa stok ' + item.stok + '</option>');
                                        }
                                        found = true;
                                        return false;
                                    }
                                });
                                if (found) {
                                    select.trigger('change');
                                }
                            }
                        });
                    }
                }
            });
            // Additional fix: on page load, if #item_id_0 has a selected value but no options, fetch and add option
            var firstSelect = $('#item_id_0');
            var firstVal = firstSelect.val();
            if (firstVal && firstSelect.find("option[value='" + firstVal + "']").length === 0) {
                var supplierId = $('#supplier_id').val();
                if (supplierId) {
                    $.ajax({
                        url: '/get-items/' + supplierId,
                        type: 'GET',
                        dataType: 'json',
                        success: function (data) {
                            var foundItem = data.find(item => item.kode_barang === firstVal);
                            if (foundItem) {
                                firstSelect.append('<option value="' + foundItem.kode_barang + '">' + foundItem.kode_barang + ' - ' + foundItem.nama_barang + ' - sisa stok ' + foundItem.stok + '</option>');
                                firstSelect.trigger('change');
                            }
                        }
                    });
                }
            }

            // Add SweetAlert confirmation on form submit
            $('form').on('submit', function (e) {
                e.preventDefault();

                const supplierName = $('#supplier_id option:selected').text();
                const orderDate = $('#tanggal_order').val();
                const orderNumber = $('#nomor_order').val();

                let itemsList = '';
                $('.order-item-container').each(function () {
                    const itemName = $(this).find('.item-select option:selected').text();
                    const quantity = $(this).find('input[name$="[jumlah_order]"]').val();
                    if (itemName && quantity) {
                        itemsList += `- ${itemName}: ${quantity} unit\n`;
                    }
                });

                Swal.fire({
                    title: 'Konfirmasi Order',
                    html: `<div class="text-left">
                                                        <p>Nomor Order: ${orderNumber}</p>
                                                        <p>Tanggal: ${orderDate}</p>
                                                        <p>Supplier: ${supplierName}</p>
                                                        <p>Detail Pesanan:</p>
                                                        <pre>${itemsList}</pre>
                                                        </div>`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Buat Order',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });
        });
    </script>

@endpush