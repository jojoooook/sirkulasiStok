@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Tambah Barang Keluar</h1>

        <form action="{{ route('stock-exit.store') }}" method="POST" id="stock-exit-form">
            @csrf

            <div class="form-group">
                <label for="item_id">Barang</label>
                <select name="item_id" id="item_id" class="form-control" required>
                    <option value="">Pilih Barang</option>
                    @foreach($items as $item)
                        <option value="{{ $item->id }}" {{ old('item_id') == $item->id ? 'selected' : '' }}>
                            {{ $item->nama_barang }}
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
                    value="{{ old('stok_keluar') }}" required>
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
            $('#item_id').select2({
                placeholder: "Pilih Barang",
                allowClear: true
            });

            // Ketika item dipilih, tampilkan gambar barang
            $('#item_id').change(function () {
                var itemId = $(this).val();
                if (itemId) {
                    // Cari item berdasarkan id dan tampilkan gambar
                    var item = @json($items); // Convert $items ke JavaScript
                    var selectedItem = item.find(i => i.id == itemId);

                    if (selectedItem && selectedItem.gambar) {
                        // Menampilkan gambar dengan URL yang benar
                        $('#item_image').attr('src', '/storage/' + selectedItem.gambar);
                        $('#item-image-container').show(); // Tampilkan gambar
                    } else {
                        $('#item-image-container').hide(); // Sembunyikan gambar jika tidak ada
                    }
                } else {
                    $('#item-image-container').hide(); // Sembunyikan gambar jika tidak ada pilihan
                }
            });
        });

        document.getElementById('stock-exit-form').addEventListener('submit', function (event) {
            document.getElementById('submit-button').disabled = true;
            document.getElementById('submit-button').innerText = "Sedang Memproses...";
        });
    </script>
@endpush