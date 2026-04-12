@extends('layouts.auth')

@section('title', $title)

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="alert alert-danger d-flex align-items-start gap-3 shadow-sm" role="alert">
                <i class="bi bi-x-circle-fill fs-3"></i>
                <div>
                    <div class="fw-bold">{{ $title }}</div>
                    <div class="small">{{ $message }}</div>
                </div>
            </div>
            <div class="card border-0 glass">
                <div class="card-body p-4 text-center">
                    <p class="text-muted small mb-4">Periksa kembali data Anda atau hubungi tim support jika masalah berlanjut.</p>
                    <div class="d-flex flex-column flex-sm-row gap-2 justify-content-center">
                        <a class="btn btn-gradient rounded-pill px-4" href="{{ $backUrl }}">Kembali</a>
                        <a class="btn btn-outline-secondary rounded-pill px-4" href="{{ route('home') }}">Ke beranda</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
