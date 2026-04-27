@extends('layouts.landing')

@section('title', 'Profil Saya')

@push('styles')
    <style>
        .profile-page { padding-top: 7.5rem; min-height: calc(100vh - 4rem); }
    </style>
@endpush

@php
    // breadcrumb tidak dibutuhkan pada layout landing
@endphp

@section('content')
    <div class="profile-page py-6">
        <div class="container">
            <div class="mb-4">
                <h1 class="h4 fw-bold mb-0">Profil Saya</h1>
                <nav aria-label="breadcrumb" class="mt-1">
                    <ol class="breadcrumb mb-0 small">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item active">Profil</li>
                    </ol>
                </nav>
            </div>

            <div class="row g-3">
        <div class="col-lg-8">
            <div class="card border-0 glass">
                <div class="card-body p-4">
                    <h2 class="h5 fw-semibold mb-3">Edit profil</h2>

                    <form method="post" action="{{ route('user.profile.update') }}" class="row g-3">
                        @csrf
                        @method('PUT')

                        <div class="col-12">
                            <label class="form-label">Nama</label>
                            <input
                                type="text"
                                name="name"
                                value="{{ old('name', $user->name) }}"
                                class="form-control @error('name') is-invalid @enderror"
                                required
                            >
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">Email</label>
                            <input
                                type="email"
                                name="email"
                                value="{{ old('email', $user->email) }}"
                                class="form-control @error('email') is-invalid @enderror"
                                required
                            >
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Password baru</label>
                            <input
                                type="password"
                                name="password"
                                class="form-control @error('password') is-invalid @enderror"
                                minlength="8"
                                autocomplete="new-password"
                            >
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Kosongkan jika tidak ingin mengganti password.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Konfirmasi password baru</label>
                            <input
                                type="password"
                                name="password_confirmation"
                                class="form-control"
                                minlength="8"
                                autocomplete="new-password"
                            >
                        </div>

                        <div class="col-12 d-flex justify-content-end gap-2 pt-2">
                            <a href="{{ route('home') }}" class="btn btn-outline-secondary rounded-pill">Batal</a>
                            <button type="submit" class="btn btn-gradient rounded-pill">Simpan perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 glass h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <img
                            src="{{ config('invitation_demo_media.avatar') }}"
                            alt="Avatar"
                            class="rounded-circle object-fit-cover flex-shrink-0 border border-light shadow-sm"
                            width="64"
                            height="64"
                            loading="lazy"
                            decoding="async"
                        >
                        <div>
                            <div class="fw-semibold">{{ $user->name }}</div>
                            <div class="small text-muted">{{ $user->email }}</div>
                        </div>
                    </div>
                    <div class="small text-muted">Perbarui data profil agar informasi akun Anda tetap akurat.</div>
                    <div class="mt-3 d-grid gap-2">
                        <a href="{{ route('user.orders.index') }}" class="btn btn-outline-primary rounded-pill">
                            <i class="bi bi-cart3 me-1"></i> Pesanan Saya
                        </a>
                        <a href="{{ route('user.catalog') }}" class="btn btn-outline-secondary rounded-pill">
                            <i class="bi bi-grid me-1"></i> Katalog
                        </a>
                        <form method="post" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger rounded-pill w-100">
                                <i class="bi bi-box-arrow-right me-1"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
        </div>
    </div>
@endsection

@push('scripts')
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                if (window.InviteUI && typeof window.InviteUI.showToast === 'function') {
                    window.InviteUI.showToast(@json(session('success')), 'success');
                }
            });
        </script>
    @endif
    @if (session('error') || $errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                if (window.InviteUI && typeof window.InviteUI.showToast === 'function') {
                    window.InviteUI.showToast(@json($errors->any() ? $errors->first() : session('error')), 'error');
                }
            });
        </script>
    @endif
@endpush
