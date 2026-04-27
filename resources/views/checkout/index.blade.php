@extends('layouts.landing')

@section('title', 'Checkout')

@push('styles')
    <style>
        .checkout-page { padding-top: 6rem; min-height: calc(100vh - 4rem); }
    </style>
@endpush

@php
    $subtotal = $order->total_amount + $order->discount_amount;
    $discount = $order->discount_amount;
    $total = $order->total_amount;
@endphp

@section('content')
    <div class="checkout-page py-4">
    <div class="container">
        <div class="mb-4">
            <h1 class="h4 fw-bold mb-0">Ringkasan checkout</h1>
            <nav aria-label="breadcrumb" class="mt-1">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('user.order.form', ['product' => $order->product_id]) }}">Data</a></li>
                    <li class="breadcrumb-item active">Checkout</li>
                </ol>
            </nav>
        </div>
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card border-0 glass mb-3">
                <div class="card-body p-4">
                    <div class="fw-semibold mb-3">Ringkasan pesanan</div>
                    <div class="d-flex justify-content-between small text-muted mb-2">
                        <span>{{ $order->product?->name ?? 'Template' }}</span>
                        <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                    @if ($discount > 0)
                        <div class="d-flex justify-content-between small mb-0">
                            <span class="text-success">Diskon</span>
                            <span class="text-success">- Rp {{ number_format($discount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                </div>
            </div>
            <div class="alert alert-primary border-0 glass-soft d-flex gap-2 align-items-start">
                <i class="bi bi-info-circle mt-1"></i>
                <div class="small">Lanjut ke pembayaran, unggah bukti bila perlu, lalu tunggu verifikasi admin.</div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card border-0 glass sticky-lg-top" style="top: 96px;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Subtotal</span>
                        <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                    @if ($discount > 0)
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Diskon</span>
                            <span class="text-success">- Rp {{ number_format($discount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <hr>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="fw-semibold">Total pembayaran</span>
                        <span class="h4 mb-0 text-gradient">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                    <a class="btn btn-lg btn-gradient w-100 rounded-pill" href="{{ route('user.payment') }}">Lanjut ke pembayaran</a>
                    <button type="button" class="btn btn-outline-secondary w-100 rounded-pill mt-2" onclick="history.back()">Kembali</button>
                </div>
            </div>
        </div>
    </div>
    </div>{{-- container --}}
    </div>{{-- checkout-page --}}
@endsection
