@extends('layouts.user')

@section('title', 'Pesanan saya')
@section('page_heading', 'Pesanan saya')

@php
    $sidebarActive = 'orders';
    $breadcrumb = [
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'Dashboard', 'url' => route('user.dashboard')],
        ['label' => 'Pesanan'],
    ];
@endphp

@section('content')
    <div class="card border-0 glass">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 js-datatable" data-page-length="10">
                    <thead class="table-light">
                        <tr>
                            <th data-type="date">Tanggal</th>
                            <th>No.</th>
                            <th>Produk</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            <tr>
                                <td data-order-by="{{ $order->created_at->format('Y-m-d H:i:s') }}">{{ $order->created_at->format('d M Y H:i') }}</td>
                                <td class="fw-semibold">#{{ $order->publicNumber() }}</td>
                                <td>{{ $order->product?->name ?? '—' }}</td>
                                <td>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                                <td><span class="badge rounded-pill text-bg-light border">{{ $order->statusLabel() }}</span></td>
                                <td class="text-end">
                                    <a class="btn btn-sm btn-outline-primary rounded-pill" href="{{ route('user.orders.show', $order) }}">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">Belum ada pesanan. Mulai dari <a href="{{ route('user.catalog') }}">katalog</a>.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
