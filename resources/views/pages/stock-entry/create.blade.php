@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Tambah Barang Masuk</h1>

        <form action="{{ route('stock-entry.store') }}" method="POST" id="stock-entry-form">
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


            <div class="form-group">
                <label for="stok_masuk">Jumlah Stok Masuk</label>
                <input type="number" name="stok_masuk" id="stok_masuk" class="form-control" value="{{ old('stok_masuk') }}"
                    required>
            </div>

            <div class="form-group">
                <label for="keterangan">Keterangan (Opsional)</label>
                <input type="text" name="keterangan" id="keterangan" class="form-control" value="{{ old('keterangan') }}">
            </div>

            <button type="submit" class="btn btn-success mt-3" id="submit-button">Simpan Barang Masuk</button>
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
        });

        document.getElementById('stock-entry-form').addEventListener('submit', function (event) {
            document.getElementById('submit-button').disabled = true;
            document.getElementById('submit-button').innerText = "Sedang Memproses...";
        });
    </script>
@endpush