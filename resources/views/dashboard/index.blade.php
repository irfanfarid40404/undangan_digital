@extends('layouts.user')

@section('title', 'Dashboard')
@section('page_heading', 'Ringkasan akun')

@php
    $sidebarActive = 'dashboard';
    $breadcrumb = [
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'Dashboard'],
    ];
@endphp

@section('content')
    <div class="row g-3 mb-4">
        @foreach ([
            ['Pesanan aktif', (string) $activeOrders, 'bi-bag-check', 'primary'],
            ['Menunggu bayar', (string) $awaitingPayment, 'bi-hourglass-split', 'warning'],
            ['Selesai', (string) $completed, 'bi-check2-circle', 'success'],
        ] as [$l,$v,$ic,$variant])
            <div class="col-md-4">
                <div class="p-4 neu-card hover-lift h-100">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small">{{ $l }}</div>
                            <div class="display-6 fw-bold">{{ $v }}</div>
                        </div>
                        <span class="d-inline-flex align-items-center justify-content-center rounded-3 bg-{{ $variant }}-subtle text-{{ $variant }} px-2 py-1">
                            <i class="bi {{ $ic }}"></i>
                        </span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-3">
        <div class="col-lg-7">
            <div class="card border-0 glass h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="fw-semibold">Notifikasi</div>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" data-demo-toast="Notifikasi ditandai dibaca">Tandai dibaca</button>
                    </div>
                    <div class="list-group list-group-flush">
                        <div class="list-group-item bg-transparent px-0">
                            <div class="d-flex gap-3">
                                <div class="rounded-3 bg-primary-subtle text-primary p-2"><i class="bi bi-credit-card"></i></div>
                                <div>
                                    <div class="fw-semibold">Pembayaran diverifikasi</div>
                                    <div class="small text-muted">Pesanan #INV-2048 siap diproses tim desain.</div>
                                </div>
                            </div>
                        </div>
                        <div class="list-group-item bg-transparent px-0">
                            <div class="d-flex gap-3">
                                <div class="rounded-3 bg-success-subtle text-success p-2"><i class="bi bi-send-check"></i></div>
                                <div>
                                    <div class="fw-semibold">Link undangan aktif</div>
                                    <div class="small text-muted">Tamu dapat mengakses undangan Anda sekarang.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card border-0 glass h-100">
                <div class="card-body">
                    <div class="fw-semibold mb-3" id="profil">Profil singkat</div>
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <img src="{{ config('invitation_demo_media.avatar') }}" alt="" class="rounded-circle object-fit-cover flex-shrink-0 border border-light shadow-sm" width="56" height="56" loading="lazy" decoding="async">
                        <div>
                            <div class="fw-semibold">{{ auth()->user()->name }}</div>
                            <div class="small text-muted">{{ auth()->user()->email }}</div>
                        </div>
                    </div>
                    <div class="d-grid gap-2">
                        <a class="btn btn-outline-primary rounded-pill" href="{{ route('user.profile') }}">Edit profil</a>
                        <a class="btn btn-outline-secondary rounded-pill" href="{{ route('user.catalog') }}">Lanjut ke katalog</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
