@extends('layouts.base')

@section('body')
    <div class="min-vh-100 d-flex flex-column">
        <nav class="navbar navbar-light border-bottom navbar-blur">
            <div class="container">
                <a class="navbar-brand fw-semibold d-flex align-items-center gap-2" href="{{ route('home') }}">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-3 text-white btn-gradient" style="width:38px;height:38px;">
                        <i class="bi bi-envelope-heart-fill"></i>
                    </span>
                    E-Invite
                </a>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" data-theme-toggle>
                        <i class="bi bi-moon-stars-fill"></i>
                    </button>
                    <a class="btn btn-sm btn-outline-primary rounded-pill" href="{{ route('home') }}">Kembali</a>
                </div>
            </div>
        </nav>
        <div class="flex-grow-1 d-flex align-items-center py-5">
            <div class="container">
                @yield('content')
            </div>
        </div>
    </div>
@endsection
