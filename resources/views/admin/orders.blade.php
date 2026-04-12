@extends('layouts.admin')

@section('title', 'Pesanan')
@section('page_heading', 'Daftar pesanan')

@php $adminActive = 'orders'; @endphp

@section('content')
    @include('partials.breadcrumb', ['items' => [
        ['label' => 'Admin', 'url' => route('admin.dashboard')],
        ['label' => 'Pesanan'],
    ]])

    <div class="card border-0 glass">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0 js-datatable" data-page-length="10">
                    <thead class="table-light">
                        <tr>
                            <th data-type="date">Tanggal</th>
                            <th>ID</th>
                            <th>Pelanggan</th>
                            <th>No. HP</th>
                            <th>Produk</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            <tr>
                                <td data-order-by="{{ $order->created_at->format('Y-m-d H:i:s') }}">{{ $order->created_at->format('d M Y H:i') }}</td>
                                <td class="fw-semibold">#{{ $order->publicNumber() }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $order->user?->name ?? '—' }}</div>
                                    <div class="small text-muted">{{ $order->user?->email ?? '—' }}</div>
                                </td>
                                <td>{{ $order->invitationDetail?->phone_number ?? '—' }}</td>
                                <td>{{ $order->product?->name ?? '—' }}</td>
                                <td style="min-width: 220px;">
                                    <form method="post" action="{{ route('admin.orders.status', $order) }}" class="d-flex gap-2 align-items-center flex-wrap">
                                        @csrf
                                        <select class="form-select form-select-sm" name="status">
                                            <option value="{{ \App\Models\Order::STATUS_PENDING_PAYMENT }}" @selected($order->status === \App\Models\Order::STATUS_PENDING_PAYMENT)>Menunggu pembayaran</option>
                                            <option value="{{ \App\Models\Order::STATUS_PAID }}" @selected($order->status === \App\Models\Order::STATUS_PAID)>Lunas</option>
                                            <option value="{{ \App\Models\Order::STATUS_PROCESSING }}" @selected($order->status === \App\Models\Order::STATUS_PROCESSING)>Diproses</option>
                                            <option value="{{ \App\Models\Order::STATUS_COMPLETED }}" @selected($order->status === \App\Models\Order::STATUS_COMPLETED)>Selesai</option>
                                            <option value="{{ \App\Models\Order::STATUS_CANCELLED }}" @selected($order->status === \App\Models\Order::STATUS_CANCELLED)>Dibatalkan</option>
                                        </select>
                                        <button type="submit" class="btn btn-sm btn-outline-primary rounded-pill">Simpan</button>
                                    </form>
                                </td>
                                <td class="text-end text-muted small">#{{ $order->id }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted py-4">Belum ada pesanan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
