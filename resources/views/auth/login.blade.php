@extends('layouts.auth')

@section('title', 'Masuk')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-5">
            <div class="card border-0 glass hover-lift shadow-lg">
                <div class="card-body p-4 p-md-5">
                    <h1 class="h4 fw-bold mb-1">Selamat datang kembali</h1>
                    <p class="text-muted small mb-4">Masuk untuk melanjutkan pemesanan undangan digital.</p>

                    <form id="loginForm" method="post" action="{{ route('login.store') }}" novalidate>
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="form-control form-control-lg rounded-3" required placeholder="nama@email.com" autocomplete="username">
                            <div class="invalid-feedback">Email tidak valid
                                .</div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control form-control-lg rounded-3" required minlength="6" placeholder="••••••••" autocomplete="current-password">
                            <div class="invalid-feedback">Minimal 6 karakter.</div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-4 small">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" value="1" id="remember">
                                <label class="form-check-label" for="remember">Ingat saya</label>
                            </div>
                            <a class="text-decoration-none" href="#">Lupa password?</a>
                        </div>
                        <button type="submit" class="btn btn-lg btn-gradient w-100 rounded-pill">Masuk</button>
                    </form>

                    <div class="text-center small text-muted mt-4">
                        Belum punya akun? <a class="text-decoration-none fw-semibold" href="{{ route('register') }}">Daftar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @if (request()->boolean('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                if (window.InviteUI && typeof window.InviteUI.showToast === 'function') {
                    window.InviteUI.showToast('Login gagal. Periksa email dan kata sandi Anda.', 'error');
                }
            });
        </script>
    @endif
    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                if (window.InviteUI && typeof window.InviteUI.showToast === 'function') {
                    window.InviteUI.showToast(@json($errors->first()), 'error');
                }
            });
        </script>
    @endif
@endpush
