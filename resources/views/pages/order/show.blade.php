@extends('layouts.app')

@section('title', 'Detail Order Barang')

@section('content')
    <div class="container mt-4">
        <h1 class="mb-4 text-center">Detail Order: {{ $nomor_order }}</h1>

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="mb-3">
            <a href="{{ route('order.index') }}" class="btn btn-secondary">Kembali ke Daftar Order</a>
        </div>

        <div class="mb-4">
            <table class="table table-borderless">
                <tbody>
                    <tr>
                        <th>Nomor Order:</th>
                        <td>{{ $nomor_order }}</td>
                        <th>Supplier:</th>
                        <td>{{ $orders->first()->supplier->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Status Order:</th>
                        <td>{{ ucfirst($orders->first()->status_order) }}</td>
                        <th>Tanggal Order:</th>
                        <td>{{ \Carbon\Carbon::parse($orders->first()->tanggal_order)->format('d-m-Y') }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Selesai:</th>
                        <td>{{ $orders->first()->tanggal_selesai ? \Carbon\Carbon::parse($orders->first()->tanggal_selesai)->format('d-m-Y') : '-' }}
                        </td>
                        <th>Nomor Invoice:</th>
                        <td>{{ $orders->first()->nomor_invoice ?? '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Jumlah Order</th>
                    <th>Jumlah Barang Masuk</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                    <tr>
                        <td>{{ $order->kode_barang }}</td>
                        <td>{{ $order->item->nama_barang ?? '-' }}</td>
                        <td>{{ $order->jumlah_order }}</td>
                        <td>
                            @if($orders->first()->status_order === 'selesai')
                                {{ $order->jumlah_barang_masuk ?? '-' }}
                            @else
                                -
                            @endif
                        </td>

                        <td>{{ $order->catatan ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection