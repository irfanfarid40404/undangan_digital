@extends('layouts.landing')

@section('title', 'Status Pesanan')

@push('styles')
    <style>
        .status-page { padding-top: 6rem; min-height: calc(100vh - 4rem); }
    </style>
@endpush

@php
    $statusFlow = [
        \App\Models\Order::STATUS_PENDING_PAYMENT => 'Menunggu pembayaran',
        \App\Models\Order::STATUS_PAID => 'Lunas',
        \App\Models\Order::STATUS_PROCESSING => 'Diproses',
        \App\Models\Order::STATUS_COMPLETED => 'Selesai',
        \App\Models\Order::STATUS_CANCELLED => 'Dibatalkan',
    ];
    $currentIndex = array_search($order->status, array_keys($statusFlow), true);
@endphp

@section('content')
    <div class="status-page py-4">
    <div class="container">
        <div class="mb-4">
            <h1 class="h4 fw-bold mb-0">Status pesanan</h1>
            <nav aria-label="breadcrumb" class="mt-1">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('user.orders.index') }}">Pesanan</a></li>
                    <li class="breadcrumb-item active">#{{ $order->publicNumber() }}</li>
                </ol>
            </nav>
        </div>
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card border-0 glass">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <div class="text-muted small">Nomor pesanan</div>
                            <div class="h5 mb-0">#{{ $order->publicNumber() }}</div>
                        </div>
                        <span class="badge rounded-pill {{ $order->status === \App\Models\Order::STATUS_CANCELLED ? 'text-bg-danger-subtle text-danger' : 'text-bg-primary-subtle text-primary' }} px-3 py-2">{{ $order->statusLabel() }}</span>
                    </div>

                    <div class="mb-4">
                        <div class="small text-muted mb-2">Progres status</div>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach ($statusFlow as $code => $label)
                                @php
                                    $isCurrent = $order->status === $code;
                                    $isDone = $currentIndex !== false && $code !== \App\Models\Order::STATUS_CANCELLED && $order->status !== \App\Models\Order::STATUS_CANCELLED && array_search($code, array_keys($statusFlow), true) < $currentIndex;
                                @endphp
                                <span class="badge rounded-pill px-3 py-2 {{ $isCurrent ? ($code === \App\Models\Order::STATUS_CANCELLED ? 'text-bg-danger text-white' : 'text-bg-primary text-white') : ($isDone ? 'text-bg-success-subtle text-success' : 'text-bg-light text-secondary border') }}">
                                    {{ $label }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                    <div class="position-relative ps-4 ms-2">
                        <span class="position-absolute top-0 start-0 translate-middle-x bg-primary-subtle" style="width:2px;height:100%;"></span>
                        @foreach ($order->histories as $h)
                            <div class="position-relative mb-4">
                                <span class="position-absolute top-0 start-0 translate-middle timeline-dot"></span>
                                <div class="ms-3">
                                    <div class="fw-semibold">{{ $h->note ?: 'Perubahan status' }}</div>
                                    <div class="small text-muted">{{ $h->created_at->format('d M Y H:i') }}</div>
                                    @if ($h->note)
                                        <span class="badge rounded-pill bg-light text-secondary border mt-2">{{ $h->note }}</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card border-0 glass mb-3">
                <div class="card-body p-4">
                    <div class="fw-semibold mb-2">Ringkasan</div>
                    <div class="small text-muted mb-1">Produk: <span class="text-body">{{ $order->product?->name }}</span></div>
                    <div class="small text-muted mb-1">Total: <span class="text-body">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span></div>
                    @if (!empty($latestPayment))
                        <div class="small text-muted mb-1">Pembayaran: <span class="text-body">{{ $latestPayment->status === \App\Models\Payment::STATUS_PENDING ? 'Menunggu verifikasi' : ($latestPayment->status === \App\Models\Payment::STATUS_VERIFIED ? 'Disetujui' : 'Ditolak') }}</span></div>
                    @endif
                    @if ($order->invitationDetail)
                        <div class="small text-muted mb-0">Acara: {{ $order->invitationDetail->event_date->format('d M Y') }} — {{ $order->invitationDetail->location }}</div>
                    @endif
                </div>
            </div>
            @if ($order->status === \App\Models\Order::STATUS_PENDING_PAYMENT)
                <a class="btn btn-gradient w-100 rounded-pill mb-2" href="{{ route('user.payment') }}">Lanjut pembayaran</a>
            @endif
            <a class="btn btn-outline-primary w-100 rounded-pill" href="{{ route('user.catalog') }}">Pesan template lain</a>
        </div>
    </div>
    </div>{{-- container --}}
    </div>{{-- status-page --}}
@endsection
