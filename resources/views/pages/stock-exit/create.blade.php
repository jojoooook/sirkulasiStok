@extends('layouts.app')

@section('title', 'Tambah Barang Keluar')

@section('content')
    <div class="container">
        <h1 class="text-center mb-4">Tambah Barang Keluar</h1>

        <form action="{{ route('stock-exit.store') }}" method="POST" id="stock-exit-form">
            @csrf

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <div class="mb-3">
                <label for="nomor_nota" class="form-label">Nomor Nota</label>
                <input type="text" name="nomor_nota" id="nomor_nota" class="form-control" value="{{ $nomorNota }}" readonly>
            </div>

            <div class="mb-3">
                <label for="tanggal_keluar" class="form-label">Tanggal Keluar</label>
                <input type="date" name="tanggal_keluar" id="tanggal_keluar" class="form-control"
                    value="{{ old('tanggal_keluar', date('Y-m-d')) }}">
            </div>

            <div id="items-container" style="display:block;">
                <div class="order-item-container card p-3 mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="item_id_0" class="form-label">Pilih Barang</label>
                            <select name="items[0][item_id]" id="item_id_0" class="form-control item-select" required>
                                <option value="">Pilih Barang</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="jumlah_keluar" class="form-label">Jumlah Keluar</label>
                            <input type="number" name="items[0][jumlah_keluar]" class="form-control" min="1" required>
                        </div>
                        <div class="col-md-4">
                            <label for="catatan_0" class="form-label">Catatan (Opsional)</label>
                            <input type="text" name="items[0][catatan]" id="catatan_0" class="form-control">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-primary" id="add-item">Tambah Barang</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-danger" id="submit-button">Simpan Barang Keluar</button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            function initItemSelect() {
                $('.item-select').select2({ placeholder: "Pilih Barang", allowClear: true });
            }

            function loadItems(selectElement) {
                $.ajax({
                    url: '{{ route("stock-exit.getItems") }}',
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        selectElement.empty().append('<option value="">Pilih Barang</option>');
                        $.each(data, function (key, item) {
                            selectElement.append('<option value="' + item.kode_barang + '">' + item.kode_barang + ' - ' + item.nama_barang + ' - sisa stok sekarang ' + item.stok + '</option>');
                        });
                        initItemSelect();
                        selectElement.trigger('change');
                    },
                    error: function () {
                        alert('Gagal mengambil data barang.');
                    }
                });
            }

            function checkDuplicateItem() {
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
                        return;
                    }
                });
            }

            // Load items for the first select on page load
            loadItems($('#item_id_0'));
            onItemSelectChange();

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
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="jumlah_keluar" class="form-label">Jumlah Keluar</label>
                                                        <input type="number" name="items[${itemCount}][jumlah_keluar]" class="form-control" min="1" required>
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
                $('#items-container').append(newItem);
                let newSelect = $('#item_id_' + itemCount);
                loadItems(newSelect);
                onItemSelectChange();
            });

            $(document).on('click', '.remove-item', function () {
                $(this).closest('.order-item-container').remove();
            });

            // Add SweetAlert confirmation on form submit
            $('#stock-exit-form').on('submit', function (e) {
                e.preventDefault();

                const nomorNota = $('#nomor_nota').val();
                const tanggalKeluar = $('#tanggal_keluar').val();

                let itemsList = '';
                $('.order-item-container').each(function () {
                    const itemName = $(this).find('.item-select option:selected').text();
                    const quantity = $(this).find('input[name$="[jumlah_keluar]"]').val();
                    const note = $(this).find('input[name$="[catatan]"]').val();
                    if (itemName && quantity) {
                        itemsList += `- ${itemName}: ${quantity} unit${note ? ' (Catatan: ' + note + ')' : ''}\n`;
                    }
                });

                Swal.fire({
                    title: 'Konfirmasi Barang Keluar',
                    html: `<div class="text-left">
                                                            <p>Nomor Nota: ${nomorNota}</p>
                                                            <p>Tanggal Keluar: ${tanggalKeluar}</p>
                                                            <p>Detail Barang Keluar:</p>
                                                            <pre>${itemsList}</pre>
                                                            </div>`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Simpan Barang Keluar',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Disable submit button to prevent multiple submissions
                        $('#submit-button').prop('disabled', true).text('Sedang Memproses...');
                        this.submit();
                    }
                });
            });
        });
    </script>
@endpush