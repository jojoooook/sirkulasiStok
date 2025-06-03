@php
    $userRole = Auth::user()->role ?? '';
@endphp

<div id="sidebar" class="d-flex flex-column p-3 text-white"
    style="background-color: #1F2C3C; width: 250px; height: 100vh; position: fixed; z-index: 1000; overflow-y: auto;">

    <a href="/" class="d-flex align-items-center mb-3 text-white text-decoration-none">
        <span class="fs-4 fw-bold">CV Sri Lestari</span>
    </a>
    <hr>

    <ul class="nav nav-pills flex-column">
        <li class="nav-item">
            <a href="/" class="nav-link text-white {{ Request::is('/') ? 'active' : '' }}">
                <i class="fas fa-home me-2"></i> Dashboard
            </a>
        </li>

        @if($userRole === 'admin')
            <li>
                <a href="/item" class="nav-link text-white {{ Request::is('item*') ? 'active' : '' }}">
                    <i class="fas fa-boxes me-2"></i> Daftar Barang
                </a>
            </li>
            <li>
                <a href="/stock-entry" class="nav-link text-white {{ Request::is('stock-entry*') ? 'active' : '' }}">
                    <i class="fas fa-file-alt me-2"></i> Barang Masuk
                </a>
            </li>
            <li>
                <a href="/stock-exit" class="nav-link text-white {{ Request::is('stock-exit*') ? 'active' : '' }}">
                    <i class="fas fa-arrow-alt-circle-right me-2"></i> Barang Keluar
                </a>
            </li>
            <li>
                <a href="/supplier" class="nav-link text-white {{ Request::is('supplier*') ? 'active' : '' }}">
                    <i class="fas fa-user-tie me-2"></i> List Supplier
                </a>
            </li>
            <li>
                <a href="/order" class="nav-link text-white {{ Request::is('order*') ? 'active' : '' }}">
                    <i class="fas fa-shopping-cart me-2"></i> Order Barang
                </a>
            </li>
            <li>
                <a href="/setting" class="nav-link text-white {{ Request::is('setting*') ? 'active' : '' }}">
                    <i class="fas fa-cog me-2"></i> Pengaturan
                </a>
            </li>
        @elseif($userRole === 'karyawan')
            <li>
                <a href="/item" class="nav-link text-white {{ Request::is('item*') ? 'active' : '' }}">
                    <i class="fas fa-boxes me-2"></i> Daftar Barang
                </a>
            </li>
            <li>
                <a href="/stock-exit" class="nav-link text-white {{ Request::is('stock-exit*') ? 'active' : '' }}">
                    <i class="fas fa-arrow-alt-circle-right me-2"></i> Barang Keluar
                </a>
            </li>
        @endif

        <hr>
        <li>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-danger w-100">
                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                </button>
            </form>
        </li>
    </ul>

</div>