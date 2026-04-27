@extends('layouts.landing')

@section('title', 'Pesanan Saya')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.min.css">
    <style>
        .orders-page { padding-top: 6rem; min-height: calc(100vh - 4rem); }
    </style>
@endpush

@section('content')
    <div class="orders-page py-6">
        <div class="container">
            <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2 mb-4">
                <div>
                    <h1 class="h4 fw-bold mb-0">Pesanan Saya</h1>
                    <nav aria-label="breadcrumb" class="mt-1">
                        <ol class="breadcrumb mb-0 small">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item active">Pesanan</li>
                        </ol>
                    </nav>
                </div>
                <a href="{{ route('user.catalog') }}" class="btn btn-gradient rounded-pill px-4">
                    <i class="bi bi-plus-lg me-1"></i> Pesan baru
                </a>
            </div>

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
                                        <td colspan="6" class="text-center text-muted py-5">
                                            Belum ada pesanan. Mulai dari <a href="{{ route('user.catalog') }}">katalog</a>.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.min.js"></script>
@endpush
