@php $active = $active ?? ''; @endphp
<div class="d-grid gap-1">
    <a class="sidebar-link {{ $active === 'dashboard' ? 'active' : '' }}" href="{{ route('user.dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a class="sidebar-link {{ $active === 'catalog' ? 'active' : '' }}" href="{{ route('user.catalog') }}"><i class="bi bi-grid"></i> Katalog</a>
    <a class="sidebar-link {{ $active === 'orders' ? 'active' : '' }}" href="{{ route('user.orders.index') }}"><i class="bi bi-bag-check"></i> Pesanan Saya</a>
    <a class="sidebar-link {{ $active === 'profile' ? 'active' : '' }}" href="{{ route('user.profile') }}"><i class="bi bi-person-circle"></i> Profile</a>
    <hr class="my-2 opacity-25">
    <form method="post" action="{{ route('logout') }}" class="d-grid">
        @csrf
        <button type="submit" class="sidebar-link text-danger text-start border-0 bg-transparent p-0 py-1">
            <i class="bi bi-box-arrow-right"></i> Logout
        </button>
    </form>
</div>
