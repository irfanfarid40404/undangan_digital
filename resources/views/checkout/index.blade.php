@extends('layouts.user')

@section('title', 'Checkout')
@section('page_heading', 'Ringkasan checkout')

@php
    $sidebarActive = 'orders';
    $breadcrumb = [
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'Data', 'url' => route('user.order.form', ['product' => $order->product_id])],
        ['label' => 'Checkout'],
    ];
    $subtotal = $order->total_amount + $order->discount_amount;
    $discount = $order->discount_amount;
    $total = $order->total_amount;
@endphp

@section('content')
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
@endsection
