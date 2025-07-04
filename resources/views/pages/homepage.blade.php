@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="container mt-4">
        <div class="row g-4">
            <!-- Menampilkan Pesan Error Jika Ada -->
            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Total Barang -->
            <div class="col-md-3">
                <div class="card shadow rounded border-0" style="background-color: #4a63d2; color: white;">
                    <div class="card-body d-flex flex-column justify-content-between" style="min-height: 140px;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="card-title mb-0">Total Barang</h5>
                            <i class="fas fa-box fa-2x"></i>
                        </div>
                        <h2 class="card-text fw-bold">{{ $totalBarang }}</h2>
                        <p class="mb-2" style="font-size: 0.9rem; opacity: 0.8;">Jumlah total barang yang tersedia</p>
                        <a href="{{ route('item.index') }}"
                            class="text-white text-decoration-none align-self-start fw-semibold"
                            style="transition: color 0.3s;">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Total Supplier -->
            <div class="col-md-3">
                <div class="card shadow rounded border-0" style="background-color: #6c757d; color: white;">
                    <div class="card-body d-flex flex-column justify-content-between" style="min-height: 140px;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="card-title mb-0">Total Supplier</h5>
                            <i class="fas fa-truck fa-2x"></i>
                        </div>
                        <h2 class="card-text fw-bold">{{ $totalSupplier }}</h2>
                        <p class="mb-2" style="font-size: 0.9rem; opacity: 0.8;">Jumlah total supplier yang terdaftar</p>
                        <a href="{{ route('supplier.index') }}"
                            class="text-white text-decoration-none align-self-start fw-semibold"
                            style="transition: color 0.3s;">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Total User -->
            <div class="col-md-3">
                <div class="card shadow rounded border-0" style="background-color: #17a2b8; color: white;">
                    <div class="card-body d-flex flex-column justify-content-between" style="min-height: 140px;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="card-title mb-0">Total User</h5>
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                        <h2 class="card-text fw-bold">{{ $totalUser }}</h2>
                        <p class="mb-2" style="font-size: 0.9rem; opacity: 0.8;">Jumlah total pengguna sistem</p>
                        <a href="{{ route('setting.index') }}"
                            class="text-white text-decoration-none align-self-start fw-semibold"
                            style="transition: color 0.3s;">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Pending Orders -->
            <div class="col-md-3">
                <div class="card shadow rounded border-0" style="background-color: #d39e00; color: white;">
                    <div class="card-body d-flex flex-column justify-content-between" style="min-height: 140px;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="card-title mb-0">Pending Orders</h5>
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                        <h2 class="card-text fw-bold">{{ $pendingOrders }}</h2>
                        <p class="mb-2" style="font-size: 0.9rem; opacity: 0.8;">Jumlah order yang sedang pending</p>
                        <a href="{{ route('order.index', ['status_order' => 'pending']) }}"
                            class="text-white text-decoration-none align-self-start fw-semibold"
                            style="transition: color 0.3s;">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Barang Keluar Hari Ini -->
            <div class="col-md-3">
                <div class="card shadow rounded border-0" style="background-color: #b33a3a; color: white;">
                    <div class="card-body d-flex flex-column justify-content-between" style="min-height: 140px;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="card-title mb-0">Barang Keluar Hari Ini</h5>
                            <i class="fas fa-sign-out-alt fa-2x"></i>
                        </div>
                        <h2 class="card-text fw-bold">{{ $barangKeluarHariIni }}</h2>
                        <p class="mb-2" style="font-size: 0.9rem; opacity: 0.8;">Jumlah barang yang keluar hari ini</p>
                        <a href="{{ route('stock-exit.index', ['bulan_keluar' => \Carbon\Carbon::now()->format('Y-m')]) }}"
                            class="text-white text-decoration-none align-self-start fw-semibold"
                            style="transition: color 0.3s;">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Barang Masuk Hari Ini -->
            <div class="col-md-3">
                <div class="card shadow rounded border-0" style="background-color: #2e8b57; color: white;">
                    <div class="card-body d-flex flex-column justify-content-between" style="min-height: 140px;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="card-title mb-0">Barang Masuk Hari Ini</h5>
                            <i class="fas fa-sign-in-alt fa-2x"></i>
                        </div>
                        <h2 class="card-text fw-bold">{{ $barangMasukHariIni }}</h2>
                        <p class="mb-2" style="font-size: 0.9rem; opacity: 0.8;">Jumlah barang yang masuk hari ini</p>
                        <a href="{{ route('stock-entry.index', ['bulan_masuk' => \Carbon\Carbon::now()->format('Y-m')]) }}"
                            class="text-white text-decoration-none align-self-start fw-semibold"
                            style="transition: color 0.3s;">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Barang Keluar Bulan Ini -->
            <div class="col-md-3">
                <div class="card shadow rounded border-0" style="background-color: #a67c1e; color: white;">
                    <div class="card-body d-flex flex-column justify-content-between" style="min-height: 140px;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="card-title mb-0">Barang Keluar Bulan Ini</h5>
                            <i class="fas fa-calendar-minus fa-2x"></i>
                        </div>
                        <h2 class="card-text fw-bold">{{ $barangKeluarBulanIni }}</h2>
                        <p class="mb-2" style="font-size: 0.9rem; opacity: 0.8;">Jumlah barang yang keluar bulan ini</p>
                        <a href="{{ route('stock-exit.index') }}"
                            class="text-white text-decoration-none align-self-start fw-semibold"
                            style="transition: color 0.3s;">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Barang Masuk Bulan Ini -->
            <div class="col-md-3">
                <div class="card shadow rounded border-0" style="background-color: #3a7ca5; color: white;">
                    <div class="card-body d-flex flex-column justify-content-between" style="min-height: 140px;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="card-title mb-0">Barang Masuk Bulan Ini</h5>
                            <i class="fas fa-calendar-plus fa-2x"></i>
                        </div>
                        <h2 class="card-text fw-bold">{{ $barangMasukBulanIni }}</h2>
                        <p class="mb-2" style="font-size: 0.9rem; opacity: 0.8;">Jumlah barang yang masuk bulan ini</p>
                        <a href="{{ route('stock-entry.index') }}"
                            class="text-white text-decoration-none align-self-start fw-semibold"
                            style="transition: color 0.3s;">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Barang Hampir Habis -->
            <div class="col-md-12">
                <div class="card shadow border-0 mt-4">
                    <div class="card-header bg-warning text-dark">
                        <strong>Barang Hampir Habis (≤ 10)</strong>
                    </div>
                    <div class="card-body p-0">
                        @if($barangHampirHabis->isEmpty())
                            <p class="p-3 mb-0">Tidak ada barang yang hampir habis.</p>
                        @else
                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Kode Barang</th>
                                            <th>Nama Barang</th>
                                            <th>Stok</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($barangHampirHabis as $index => $item)
                                            <tr>
                                                <td>{{ $barangHampirHabis->firstItem() + $index }}</td>
                                                <td>{{ $item->kode_barang }}</td>
                                                <td>{{ $item->nama_barang }}</td>
                                                <td class="text-danger fw-bold">{{ $item->stok }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="p-3">
                                {{ $barangHampirHabis->links('pagination::bootstrap-4') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>

    </div>
    </div>
@endsection