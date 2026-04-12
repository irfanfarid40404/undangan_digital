@extends('layouts.admin')

@section('title', 'Laporan')
@section('page_heading', 'Laporan penjualan')

@php $adminActive = 'reports'; @endphp

@section('content')
    <form method="get" class="card border-0 glass mb-4">
        <div class="card-body p-3 p-lg-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small text-muted">Tanggal mulai</label>
                    <input type="date" class="form-control" name="start_date" value="{{ $startDate }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-muted">Tanggal akhir</label>
                    <input type="date" class="form-control" name="end_date" value="{{ $endDate }}">
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Terapkan</button>
                    <a href="{{ route('admin.reports') }}" class="btn btn-outline-secondary rounded-pill px-4">Reset</a>
                </div>
            </div>
        </div>
    </form>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 glass h-100">
                <div class="card-body p-4">
                    <div class="small text-muted">Total pesanan</div>
                    <div class="h4 fw-bold mb-0">{{ number_format($totalOrders, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 glass h-100">
                <div class="card-body p-4">
                    <div class="small text-muted">Pesanan terbayar</div>
                    <div class="h4 fw-bold mb-0">{{ number_format($paidOrders, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 glass h-100">
                <div class="card-body p-4">
                    <div class="small text-muted">Pendapatan</div>
                    <div class="h4 fw-bold mb-0">Rp {{ number_format($revenue, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 glass h-100">
                <div class="card-body p-4">
                    <div class="small text-muted">Konversi</div>
                    <div class="h4 fw-bold mb-0">{{ $conversion }}%</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="card border-0 glass h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="fw-semibold">Grafik pendapatan terverifikasi</div>
                        <span class="small text-muted">Chart.js</span>
                    </div>
                    <canvas id="salesChart" height="120"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 glass h-100">
                <div class="card-body p-4">
                    <div class="fw-semibold mb-3">Ringkasan</div>
                    <ul class="list-unstyled small mb-0">
                        <li class="d-flex justify-content-between py-2 border-bottom"><span class="text-muted">Conversion</span><span class="fw-semibold">{{ $conversion }}%</span></li>
                        <li class="d-flex justify-content-between py-2 border-bottom"><span class="text-muted">AOV</span><span class="fw-semibold">Rp {{ number_format($aov, 0, ',', '.') }}</span></li>
                        <li class="d-flex justify-content-between py-2"><span class="text-muted">Refund</span><span class="fw-semibold">{{ $refundRate }}%</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-5">
            <div class="card border-0 glass h-100">
                <div class="card-body p-4">
                    <div class="fw-semibold mb-3">Top produk (berdasarkan omzet)</div>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead class="small text-muted">
                                <tr>
                                    <th>Produk</th>
                                    <th>Pesanan</th>
                                    <th class="text-end">Omzet</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($topProducts as $row)
                                    <tr>
                                        <td>{{ $row['name'] }}</td>
                                        <td>{{ number_format($row['orders_count'], 0, ',', '.') }}</td>
                                        <td class="text-end">Rp {{ number_format($row['revenue'], 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-muted">Belum ada data.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card border-0 glass h-100">
                <div class="card-body p-4">
                    <div class="fw-semibold mb-3">Detail order periode terpilih</div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 js-datatable" data-page-length="10">
                            <thead class="small text-muted">
                                <tr>
                                    <th data-type="date">Tanggal</th>
                                    <th>Order</th>
                                    <th>Pelanggan</th>
                                    <th>Status</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody class="small">
                                @forelse ($reportOrders as $order)
                                    @php
                                        $latestPayment = $order->payments->first();
                                    @endphp
                                    <tr>
                                        <td data-order-by="{{ $order->created_at->format('Y-m-d H:i:s') }}">{{ $order->created_at->format('d M Y H:i') }}</td>
                                        <td>
                                            <div class="fw-semibold">#{{ $order->publicNumber() }}</div>
                                            <div class="text-muted">{{ $order->product?->name ?? '-' }}</div>
                                        </td>
                                        <td>
                                            <div>{{ $order->user?->name ?? '-' }}</div>
                                            <div class="text-muted">{{ $order->user?->email ?? '-' }}</div>
                                        </td>
                                        <td>
                                            <div>{{ $order->statusLabel() }}</div>
                                            <div class="text-muted">Pembayaran: {{ $latestPayment?->status ?? '-' }}</div>
                                        </td>
                                        <td class="text-end">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-muted">Belum ada order dalam periode ini.</td></tr>
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('salesChart');
            if (!ctx || typeof Chart === 'undefined') return;
            const labels = @json($chartLabels);
            const data = @json($chartValues);
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Pendapatan (jt)',
                        data: data,
                        borderColor: '#7c5cff',
                        backgroundColor: 'rgba(124,92,255,0.15)',
                        fill: true,
                        tension: 0.35
                    }]
                },
                options: {
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { grid: { color: 'rgba(124,92,255,0.08)' } },
                        x: { grid: { display: false } }
                    }
                }
            });
        });
    </script>
@endpush
