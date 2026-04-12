@extends('layouts.auth')

@section('title', 'Daftar')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card border-0 glass hover-lift shadow-lg">
                <div class="card-body p-4 p-md-5">
                    <h1 class="h4 fw-bold mb-1">Buat akun</h1>
                    <p class="text-muted small mb-4">Mulai perjalanan undangan digital Anda.</p>

                    <form id="registerForm" method="post" action="{{ route('register.store') }}" novalidate>
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label">Nama lengkap</label>
                                <input type="text" name="name" value="{{ old('name') }}" class="form-control form-control-lg rounded-3" required minlength="3" placeholder="Nama Anda">
                                <div class="invalid-feedback">Nama minimal 3 karakter.</div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" value="{{ old('email') }}" class="form-control form-control-lg rounded-3" required placeholder="nama@email.com">
                                <div class="invalid-feedback">Email tidak valid.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control form-control-lg rounded-3" required minlength="8" placeholder="Minimal 8 karakter" autocomplete="new-password">
                                <div class="invalid-feedback">Minimal 8 karakter.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Konfirmasi password</label>
                                <input type="password" name="password_confirmation" class="form-control form-control-lg rounded-3" required minlength="8" placeholder="Ulangi password" autocomplete="new-password">
                                <div class="invalid-feedback">Wajib diisi.</div>
                            </div>
                        </div>
                        <div class="form-check mt-3">
                            <input class="form-check-input" type="checkbox" id="agree" required>
                            <label class="form-check-label small" for="agree">Saya setuju dengan syarat &amp; kebijakan privasi.</label>
                            <div class="invalid-feedback">Anda harus menyetujui syarat.</div>
                        </div>
                        <button type="submit" class="btn btn-lg btn-gradient w-100 rounded-pill mt-4">Daftar</button>
                    </form>

                    <div class="text-center small text-muted mt-4">
                        Sudah punya akun? <a class="text-decoration-none fw-semibold" href="{{ route('login') }}">Masuk</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
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
