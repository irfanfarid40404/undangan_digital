@extends('layouts.base')

@php $adminActive = $adminActive ?? ''; @endphp

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
        <aside class="border-end glass-soft p-3 p-lg-4" style="min-width: 270px;">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="fw-semibold"><i class="bi bi-shield-check me-1 text-primary"></i> Admin</div>
                <button type="button" class="btn btn-sm btn-outline-secondary d-lg-none" data-bs-toggle="offcanvas" data-bs-target="#adminSidebar">
                    <i class="bi bi-list"></i>
                </button>
            </div>
            <div class="small text-muted mb-3">E-Invite Console</div>
            <nav class="d-none d-lg-block">
                @include('partials.sidebar-admin-nav', ['active' => $adminActive])
            </nav>
        </aside>
        <div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="adminSidebar">
            <div class="offcanvas-header">
                <h6 class="offcanvas-title">Admin Menu</h6>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
            </div>
            <div class="offcanvas-body">
                @include('partials.sidebar-admin-nav', ['active' => $adminActive])
            </div>
        </div>
        <div class="flex-grow-1">
            <header class="border-bottom glass-soft px-3 px-lg-4 py-3 d-flex align-items-center justify-content-between">
                <div class="min-w-0">
                    @isset($breadcrumb)
                        @include('partials.breadcrumb', ['items' => $breadcrumb])
                    @endisset
                    <h1 class="h5 mb-0">@yield('page_heading')</h1>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" data-theme-toggle>
                        <i class="bi bi-moon-stars-fill"></i>
                    </button>
                    <a class="btn btn-sm btn-outline-secondary rounded-pill" href="{{ route('home') }}">Lihat Situs</a>
                </div>
            </header>
            <div class="p-3 p-lg-4">
                @yield('content')
            </div>
        </div>
    </div>
@endsection
