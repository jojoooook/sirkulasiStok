@extends('layouts.app')

@section('title', 'Riwayat Barang Keluar')

@section('content')
    <div class="container mt-4">
        <h1 class="mb-4 text-center">Riwayat Barang Keluar</h1>

        <a href="{{ route('stock-exit.create') }}" class="btn btn-primary mb-3 shadow-sm">
            <i class="fas fa-plus"></i> Tambah Barang Keluar
        </a>

        <div class="table-responsive">
            <table class="table table-hover table-bordered shadow-sm">
                <thead class="thead-light">
                    <tr>
                        <th>Nama Barang</th>
                        <th>Jumlah Stok Keluar</th>
                        <th>Tanggal Keluar</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stockExits as $exit)
                        <tr>
                            <td>{{ $exit->item->nama_barang }}</td>
                            <td>{{ $exit->stok_keluar }}</td>
                            <td>{{ $exit->tanggal_keluar }}</td>
                            <td>{{ $exit->keterangan }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="d-flex justify-content-center">
                {!! $stockExits->links('pagination::bootstrap-4') !!}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
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