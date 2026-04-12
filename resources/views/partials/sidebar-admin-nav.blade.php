@php $active = $active ?? ''; @endphp
<div class="d-grid gap-1">
    <a class="sidebar-link {{ $active === 'dashboard' ? 'active' : '' }}" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a class="sidebar-link {{ $active === 'products' ? 'active' : '' }}" href="{{ route('admin.products') }}"><i class="bi bi-collection"></i> Kelola Produk</a>
    <a class="sidebar-link {{ $active === 'orders' ? 'active' : '' }}" href="{{ route('admin.orders') }}"><i class="bi bi-bag"></i> Pesanan</a>
    <a class="sidebar-link {{ $active === 'payments' ? 'active' : '' }}" href="{{ route('admin.payments') }}"><i class="bi bi-credit-card-2-front"></i> Konfirmasi Pembayaran</a>
    <a class="sidebar-link {{ $active === 'reports' ? 'active' : '' }}" href="{{ route('admin.reports') }}"><i class="bi bi-graph-up-arrow"></i> Laporan</a>
    <hr class="my-2 opacity-25">
    <form method="post" action="{{ route('logout') }}" class="d-grid">
        @csrf
        <button type="submit" class="sidebar-link text-danger text-start border-0 bg-transparent p-0 py-1">
            <i class="bi bi-box-arrow-right"></i> Logout
        </button>
    </form>
</div>
