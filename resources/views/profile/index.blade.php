@extends('layouts.user')

@section('title', 'Profil Saya')
@section('page_heading', 'Profil saya')

@php
    $sidebarActive = 'profile';
    $breadcrumb = [
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'Dashboard', 'url' => route('user.dashboard')],
        ['label' => 'Profil'],
    ];
@endphp

@section('content')
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
                            <a href="{{ route('user.dashboard') }}" class="btn btn-outline-secondary rounded-pill">Batal</a>
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
                </div>
            </div>
        </div>
    </div>
@endsection
