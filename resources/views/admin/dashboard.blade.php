@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page_heading', 'Ringkasan bisnis')

@php $adminActive = 'dashboard'; @endphp

@section('content')
    <div class="row g-3 mb-4">
        @foreach ([
            ['Pendapatan bulan ini', $revenueMonthLabel, 'bi-graph-up-arrow', 'success'],
            ['Pesanan aktif', (string) $activeOrders, 'bi-bag', 'primary'],
            ['Menunggu konfirmasi', (string) $pendingPayments, 'bi-hourglass-split', 'warning'],
        ] as [$l,$v,$ic,$c])
            <div class="col-md-4">
                <div class="p-4 neu-card hover-lift h-100">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-muted small">{{ $l }}</div>
                            <div class="h3 fw-bold mb-0">{{ $v }}</div>
                        </div>
                        <span class="rounded-3 bg-{{ $c }}-subtle text-{{ $c }} p-2"><i class="bi {{ $ic }}"></i></span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card border-0 glass">
        <div class="card-body p-4">
            <div class="fw-semibold mb-3">Aktivitas terbaru</div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 js-datatable" data-page-length="5">
                    <thead class="small text-muted">
                        <tr>
                            <th data-type="date">Waktu</th>
                            <th>Event</th>
                            <th>User</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        @forelse ($activity as $row)
                            <tr>
                                <td data-order-by="{{ $row['timestamp'] }}">{{ $row['time'] }}</td>
                                <td>{{ $row['event'] }}</td>
                                <td>{{ $row['user'] }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-muted">Belum ada aktivitas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card border-0 glass mt-4">
        <div class="card-body p-4">
            <div class="fw-semibold mb-3">Data checkout terbaru</div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 js-datatable" data-page-length="10">
                    <thead class="small text-muted">
                        <tr>
                            <th data-type="date">Order</th>
                            <th>Pelanggan</th>
                            <th>Data acara</th>
                            <th>Pembayaran</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        @forelse ($checkoutData as $row)
                            <tr>
                                <td data-order-by="{{ $row['created_at_timestamp'] }}">
                                    <div class="fw-semibold">#{{ $row['order_number'] }}</div>
                                    <div class="text-muted">{{ $row['product'] }}</div>
                                </td>
                                <td>
                                    <div>{{ $row['customer'] }}</div>
                                    <div class="text-muted">{{ $row['email'] }}</div>
                                </td>
                                <td>
                                    <div>{{ $row['couple'] }}</div>
                                    <div class="text-muted">{{ $row['event_date'] }} — {{ $row['location'] }}</div>
                                </td>
                                <td>
                                    <div class="text-capitalize">{{ $row['payment_status'] }}</div>
                                    <div class="text-muted">{{ $row['order_status'] }}</div>
                                    @if (!empty($row['notes']) && $row['notes'] !== '-')
                                        <span class="badge rounded-pill text-bg-info-subtle text-info border mt-1">Ada catatan</span>
                                    @else
                                        <span class="badge rounded-pill text-bg-secondary-subtle text-secondary border mt-1">Tanpa catatan</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @php
                                        $detailPayload = base64_encode(json_encode($row, JSON_UNESCAPED_UNICODE));
                                    @endphp
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary rounded-pill"
                                        data-bs-toggle="modal"
                                        data-bs-target="#checkoutDetailModal"
                                        data-order-detail="{{ $detailPayload }}"
                                    >
                                        Detail
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-muted">Belum ada data checkout.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="checkoutDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content border-0 glass">
                <div class="modal-header border-0">
                    <h5 class="modal-title">Detail checkout</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body pt-0">
                    <div class="row g-3 small">
                        <div class="col-md-6">
                            <div class="fw-semibold mb-2">Informasi order</div>
                            <ul class="list-group list-group-flush border rounded-3 overflow-hidden">
                                <li class="list-group-item d-flex justify-content-between"><span class="text-muted">No. Order</span><span id="detailOrderNumber">-</span></li>
                                <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Status Order</span><span id="detailOrderStatus">-</span></li>
                                <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Waktu Checkout</span><span id="detailCreatedAt">-</span></li>
                                <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Produk</span><span id="detailProduct">-</span></li>
                                <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Subtotal</span><span id="detailSubtotal">-</span></li>
                                <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Diskon</span><span id="detailDiscount">-</span></li>
                                <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Total</span><span id="detailTotal">-</span></li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <div class="fw-semibold mb-2">Data pelanggan &amp; acara</div>
                            <ul class="list-group list-group-flush border rounded-3 overflow-hidden">
                                <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Nama</span><span id="detailCustomer">-</span></li>
                                <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Email</span><span id="detailEmail">-</span></li>
                                <li class="list-group-item d-flex justify-content-between"><span class="text-muted">No. HP</span><span id="detailPhone">-</span></li>
                                <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Mempelai 1</span><span id="detailPartnerOne">-</span></li>
                                <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Mempelai 2</span><span id="detailPartnerTwo">-</span></li>
                                <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Tanggal Acara</span><span id="detailEventDate">-</span></li>
                                <li class="list-group-item"><span class="text-muted d-block mb-1">Lokasi</span><span id="detailLocation">-</span></li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <div class="fw-semibold mb-2">Konten undangan</div>
                            <ul class="list-group list-group-flush border rounded-3 overflow-hidden">
                                <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Musik</span><span id="detailMusic">-</span></li>
                                <li class="list-group-item"><span class="text-muted d-block mb-1">Quote</span><span id="detailQuote">-</span></li>
                                <li class="list-group-item"><span class="text-muted d-block mb-1">Cerita</span><span id="detailStory">-</span></li>
                                <li class="list-group-item"><span class="text-muted d-block mb-1">Catatan</span><span id="detailNotes">-</span></li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <div class="fw-semibold mb-2">Pembayaran &amp; galeri</div>
                            <ul class="list-group list-group-flush border rounded-3 overflow-hidden mb-3">
                                <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Status Pembayaran</span><span id="detailPaymentStatus" class="text-capitalize">-</span></li>
                                <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Metode</span><span id="detailPaymentMethod">-</span></li>
                                <li class="list-group-item"><span class="text-muted d-block mb-1">Catatan Admin Pembayaran</span><span id="detailPaymentAdminNote">-</span></li>
                                <li class="list-group-item"><a id="detailProofLink" href="#" target="_blank" class="link-primary d-none">Lihat bukti pembayaran</a><span id="detailNoProof" class="text-muted">Belum ada bukti pembayaran</span></li>
                            </ul>
                            <div class="text-muted mb-2">Foto galeri (<span id="detailPhotoCount">0</span>)</div>
                            <div id="detailPhotoGrid" class="d-flex flex-wrap gap-2"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            const modal = document.getElementById('checkoutDetailModal');
            if (!modal) return;

            const formatCurrency = function (value) {
                const n = Number(value || 0);
                return 'Rp ' + n.toLocaleString('id-ID');
            };

            const setText = function (id, value) {
                const el = document.getElementById(id);
                if (el) el.textContent = value && String(value).trim() !== '' ? value : '-';
            };

            const decodeDetailPayload = function (payload) {
                if (!payload) return {};
                try {
                    const bin = window.atob(payload);
                    const bytes = Uint8Array.from(bin, function (c) { return c.charCodeAt(0); });
                    const jsonText = new TextDecoder().decode(bytes);
                    return JSON.parse(jsonText);
                } catch (e) {
                    try {
                        return JSON.parse(window.atob(payload));
                    } catch (_) {
                        return {};
                    }
                }
            };

            modal.addEventListener('show.bs.modal', function (event) {
                const btn = event.relatedTarget;
                if (!btn) return;

                const data = decodeDetailPayload(btn.getAttribute('data-order-detail'));

                setText('detailOrderNumber', data.order_number);
                setText('detailOrderStatus', data.order_status);
                setText('detailCreatedAt', data.created_at);
                setText('detailProduct', data.product);
                setText('detailSubtotal', formatCurrency((Number(data.total_amount || 0) + Number(data.discount_amount || 0))));
                setText('detailDiscount', formatCurrency(data.discount_amount));
                setText('detailTotal', formatCurrency(data.total_amount));

                setText('detailCustomer', data.customer);
                setText('detailEmail', data.email);
                setText('detailPhone', data.phone);
                setText('detailPartnerOne', data.partner_one_name);
                setText('detailPartnerTwo', data.partner_two_name);
                setText('detailEventDate', data.event_date);
                setText('detailLocation', data.location);

                setText('detailMusic', data.music);
                setText('detailQuote', data.quote);
                setText('detailStory', data.story);
                setText('detailNotes', data.notes);

                setText('detailPaymentStatus', data.payment_status);
                setText('detailPaymentMethod', data.payment_method);
                setText('detailPaymentAdminNote', data.payment_admin_note);

                const proofLink = document.getElementById('detailProofLink');
                const noProof = document.getElementById('detailNoProof');
                if (proofLink && noProof) {
                    if (data.proof_url) {
                        proofLink.href = data.proof_url;
                        proofLink.classList.remove('d-none');
                        noProof.classList.add('d-none');
                    } else {
                        proofLink.href = '#';
                        proofLink.classList.add('d-none');
                        noProof.classList.remove('d-none');
                    }
                }

                const photos = Array.isArray(data.photo_urls) ? data.photo_urls : [];
                setText('detailPhotoCount', String(photos.length));
                const photoGrid = document.getElementById('detailPhotoGrid');
                if (photoGrid) {
                    photoGrid.innerHTML = '';
                    if (!photos.length) {
                        photoGrid.innerHTML = '<span class="text-muted">Tidak ada foto.</span>';
                    } else {
                        photos.forEach(function (url) {
                            const a = document.createElement('a');
                            a.href = url;
                            a.target = '_blank';
                            a.className = 'd-inline-block';
                            const img = document.createElement('img');
                            img.src = url;
                            img.alt = 'Foto galeri';
                            img.className = 'rounded-2 object-fit-cover border';
                            img.style.width = '72px';
                            img.style.height = '72px';
                            a.appendChild(img);
                            photoGrid.appendChild(a);
                        });
                    }
                }
            });
        })();
    </script>
@endpush

