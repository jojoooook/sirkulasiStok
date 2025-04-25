@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <h1 class="mb-4 text-center">Riwayat Barang Masuk</h1>

        <a href="{{ route('stock-entry.create') }}" class="btn btn-primary mb-3 shadow-sm">
            <i class="fas fa-plus"></i> Tambah Barang Masuk
        </a>

        <div class="table-responsive">
            <table class="table table-hover table-bordered shadow-sm">
                <thead class="thead-light">
                    <tr>
                        <th>Nama Barang</th>
                        <th>Jumlah Stok Masuk</th>
                        <th>Tanggal Masuk</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stockEntries as $entry)
                        <tr>
                            <td>{{ $entry->item->nama_barang }}</td>
                            <td>{{ $entry->stok_masuk }}</td>
                            <td>{{ $entry->tanggal_masuk }}</td>
                            <td>{{ $entry->keterangan }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="d-flex justify-content-center">
                {!! $stockEntries->links('pagination::bootstrap-4') !!}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- SweetAlert untuk pesan sukses atau error -->
    @if(session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                confirmButtonText: 'OK',
                confirmButtonColor: '#28a745'
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: '{{ session('error') }}',
                confirmButtonText: 'OK',
                confirmButtonColor: '#dc3545'
            });
        </script>
    @endif
@endpush