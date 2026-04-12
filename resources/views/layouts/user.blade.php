@extends('layouts.base')

@php
    $sidebarActive = $sidebarActive ?? '';
    $userNotifications = $userNotifications ?? [];
    $userNotificationCount = $userNotificationCount ?? 0;
@endphp

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.min.css">
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.min.js"></script>
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                if (window.InviteUI && typeof window.InviteUI.showToast === 'function') {
                    window.InviteUI.showToast(@json(session('success')), 'success');
                }
            });
        </script>
    @endif
    @if (session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                if (window.InviteUI && typeof window.InviteUI.showToast === 'function') {
                    window.InviteUI.showToast(@json(session('error')), 'error');
                }
            });
        </script>
    @endif
@endpush

@section('body')
    <div class="min-vh-100 d-lg-flex">
        <aside class="border-end glass-soft p-3 p-lg-4" style="min-width: 260px;">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <a class="text-decoration-none text-body fw-semibold d-flex align-items-center gap-2" href="{{ route('user.dashboard') }}">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-3 text-white btn-gradient" style="width:36px;height:36px;">
                        <i class="bi bi-envelope-heart-fill"></i>
                    </span>
                    E-Invite
                </a>
                <button type="button" class="btn btn-sm btn-outline-secondary d-lg-none" data-bs-toggle="offcanvas" data-bs-target="#userSidebar">
                    <i class="bi bi-list"></i>
                </button>
            </div>
            <nav class="d-none d-lg-block">
                @include('partials.sidebar-user-nav', ['active' => $sidebarActive])
            </nav>
        </aside>

        <div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="userSidebar">
            <div class="offcanvas-header">
                <h6 class="offcanvas-title">Menu</h6>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
            </div>
            <div class="offcanvas-body">
                @include('partials.sidebar-user-nav', ['active' => $sidebarActive])
            </div>
        </div>

        <div class="offcanvas offcanvas-end" tabindex="-1" id="userNotificationPanel">
            <div class="offcanvas-header border-bottom">
                <h6 class="offcanvas-title">Notifikasi</h6>
                @if ($userNotificationCount > 0)
                    <form action="{{ route('user.notifications.read-all') }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-link text-secondary me-2" title="Tandai semua sudah dibaca">Tandai semua dibaca</button>
                    </form>
                @endif
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
            </div>
            <div class="offcanvas-body p-0">
                @forelse ($userNotifications as $notif)
                    <div class="border-bottom alert alert-{{ $notif->data['level'] }} border-0 m-0 py-3 px-3 {{ $notif->isUnread() ? 'fw-semibold' : 'opacity-75' }}">
                        <div class="d-flex gap-2 align-items-start">
                            <div class="flex-grow-1 small">
                                {{ $notif->data['message'] }}
                                <div class="text-muted small mt-1">{{ $notif->created_at->diffForHumans() }}</div>
                            </div>
                            <div class="d-flex gap-1">
                                @if ($notif->isUnread())
                                    <form action="{{ route('user.notifications.read', $notif->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-link p-0 text-secondary" title="Tandai sudah dibaca">
                                            <i class="bi bi-check2"></i>
                                        </button>
                                    </form>
                                @endif
                                <form action="{{ route('user.notifications.delete', $notif->id) }}" method="POST" style="display:inline;" data-confirm="Hapus notifikasi ini?" data-confirm-title="Hapus Notifikasi" data-confirm-confirm-text="Ya, hapus">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-link p-0 text-danger" title="Hapus">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-muted small p-3">Belum ada notifikasi.</div>
                @endforelse
            </div>
        </div>

        <div class="flex-grow-1">
            <header class="border-bottom glass-soft px-3 px-lg-4 py-3 d-flex align-items-center justify-content-between gap-3">
                <div class="min-w-0">
                    @isset($breadcrumb)
                        @include('partials.breadcrumb', ['items' => $breadcrumb])
                    @endisset
                    <h1 class="h5 mb-0 text-truncate">@yield('page_heading')</h1>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" data-theme-toggle>
                        <i class="bi bi-moon-stars-fill"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill position-relative" data-bs-toggle="offcanvas" data-bs-target="#userNotificationPanel">
                        <i class="bi bi-bell"></i>
                        @if ($userNotificationCount > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:.6rem;">{{ $userNotificationCount > 99 ? '99+' : $userNotificationCount }}</span>
                        @endif
                    </button>
                </div>
            </header>
            <div class="p-3 p-lg-4">
                @yield('content')
            </div>
        </div>
    </div>
@endsection
