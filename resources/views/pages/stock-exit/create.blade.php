@extends('layouts.app')

@section('title', 'Tambah Barang Keluar')

@section('content')
    <div class="container">
        <h1>Tambah Barang Keluar</h1>

        <form action="{{ route('stock-exit.store') }}" method="POST" id="stock-exit-form">
            @csrf

            <div class="form-group">
                <label for="kode_barang">Barang</label>
                <select name="kode_barang" id="kode_barang" class="form-control select2" required>
                    <option value="">Pilih Barang</option>
                    @foreach($items as $item)
                        <option value="{{ $item->kode_barang }}" {{ old('kode_barang') == $item->kode_barang ? 'selected' : '' }}>
                            {{ $item->kode_barang }} - {{ $item->nama_barang }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Menampilkan gambar barang setelah dipilih, tampilkan di atas input -->
            <div class="form-group" id="item-image-container" style="display: none;">
                <label for="item_image">Gambar Barang</label>
                <div class="text-center">
                    <img id="item_image" src="" alt="Gambar Barang" class="img-fluid" style="max-width: 200px;">
                </div>
            </div>

            <div class="form-group">
                <label for="stok_keluar">Jumlah Stok Keluar</label>
                <input type="number" name="stok_keluar" id="stok_keluar" class="form-control"
                    value="{{ old('stok_keluar') }}" oninput="this.value = this.value.toUpperCase()" required>
            </div>

            <div class="form-group">
                <label for="keterangan">Keterangan (Opsional)</label>
                <input type="text" name="keterangan" id="keterangan" class="form-control" value="{{ old('keterangan') }}">
            </div>

            <button type="submit" class="btn btn-danger mt-3" id="submit-button">Simpan Barang Keluar</button>
        </form>
    </div>
@endsection

@push('scripts')
    @if(session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                confirmButtonText: 'OK'
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: '{{ session('error') }}',
                confirmButtonText: 'OK'
            });
        </script>
    @endif

    <script>
        $(document).ready(function () {
            // Initialize select2 for searchable dropdown
            $('#kode_barang').select2({
                placeholder: "Pilih Barang",
                allowClear: true
            });

            // Show item image when a barang is selected
            $('#kode_barang').change(function () {
                var kodeBarang = $(this).val();
                if (kodeBarang) {
                    var items = @json($items);
                    var selectedItem = items.find(i => i.kode_barang === kodeBarang);

                    if (selectedItem && selectedItem.gambar) {
                        $('#item_image').attr('src', '/storage/' + selectedItem.gambar);
                        $('#item-image-container').show();
                    } else {
                        $('#item-image-container').hide();
                    }
                } else {
                    $('#item-image-container').hide();
                }
            });

            // Disable submit button on form submit to prevent multiple submissions
            document.getElementById('stock-exit-form').addEventListener('submit', function (event) {
                document.getElementById('submit-button').disabled = true;
                document.getElementById('submit-button').innerText = "Sedang Memproses...";
            });
        });
    </script>
@endpush