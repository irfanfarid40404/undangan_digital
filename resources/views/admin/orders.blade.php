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
                                <td class="text-end">
                                    @php
                                        $latestPayment = $order->payments->first();
                                        $photoPaths = \Illuminate\Support\Facades\Storage::disk('public')->files("order-photos/{$order->id}");
                                        $photoUrls = collect($photoPaths)->map(fn ($p) => \Illuminate\Support\Facades\Storage::disk('public')->url($p))->values()->all();
                                        $detailPayload = base64_encode(json_encode([
                                            'order_number'       => $order->publicNumber(),
                                            'order_status'       => $order->statusLabel(),
                                            'created_at'         => $order->created_at->format('d M Y H:i'),
                                            'product'            => $order->product?->name ?? '-',
                                            'total_amount'       => $order->total_amount,
                                            'discount_amount'    => $order->discount_amount,
                                            'customer'           => $order->user?->name ?? '-',
                                            'email'              => $order->user?->email ?? '-',
                                            'phone'              => $order->invitationDetail?->phone_number ?? '-',
                                            'partner_one_name'   => $order->invitationDetail?->partner_one_name ?? '-',
                                            'partner_two_name'   => $order->invitationDetail?->partner_two_name ?? '-',
                                            'event_date'         => $order->invitationDetail?->event_date?->format('d M Y') ?? '-',
                                            'location'           => $order->invitationDetail?->location ?? '-',
                                            'story'              => $order->invitationDetail?->story ?? '-',
                                            'music'              => $order->invitationDetail?->music_choice ?? '-',
                                            'quote'              => $order->invitationDetail?->quote_text ?? '-',
                                            'notes'              => $order->invitationDetail?->notes ?? '-',
                                            'photo_urls'         => $photoUrls,
                                            'payment_status'     => $latestPayment?->status ?? '-',
                                            'payment_method'     => $latestPayment?->method ?? '-',
                                            'payment_admin_note' => $latestPayment?->admin_note ?? '-',
                                            'proof_url'          => $latestPayment?->proof_path ? \Illuminate\Support\Facades\Storage::disk('public')->url($latestPayment->proof_path) : null,
                                        ], JSON_UNESCAPED_UNICODE));
                                    @endphp
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary rounded-pill"
                                        data-bs-toggle="modal"
                                        data-bs-target="#orderDetailModal"
                                        data-order-detail="{{ $detailPayload }}"
                                    >Detail</button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted py-4">Belum ada pesanan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="orderDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content border-0 glass">
                <div class="modal-header border-0">
                    <h5 class="modal-title">Detail pesanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body pt-0">
                    <div class="row g-3 small">
                        <div class="col-md-6">
                            <div class="fw-semibold mb-2">Informasi order</div>
                            <ul class="list-group list-group-flush border rounded-3 overflow-hidden">
                                <li class="list-group-item d-flex justify-content-between"><span class="text-muted">No. Order</span><span id="odOrderNumber">-</span></li>
                                <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Status Order</span><span id="odOrderStatus">-</span></li>
                                <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Waktu Checkout</span><span id="odCreatedAt">-</span></li>
                                <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Produk</span><span id="odProduct">-</span></li>
                                <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Subtotal</span><span id="odSubtotal">-</span></li>
                                <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Diskon</span><span id="odDiscount">-</span></li>
                                <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Total</span><span id="odTotal">-</span></li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <div class="fw-semibold mb-2">Data pelanggan &amp; acara</div>
                            <ul class="list-group list-group-flush border rounded-3 overflow-hidden">
                                <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Nama</span><span id="odCustomer">-</span></li>
                                <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Email</span><span id="odEmail">-</span></li>
                                <li class="list-group-item d-flex justify-content-between"><span class="text-muted">No. HP</span><span id="odPhone">-</span></li>
                                <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Mempelai 1</span><span id="odPartnerOne">-</span></li>
                                <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Mempelai 2</span><span id="odPartnerTwo">-</span></li>
                                <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Tanggal Acara</span><span id="odEventDate">-</span></li>
                                <li class="list-group-item"><span class="text-muted d-block mb-1">Lokasi</span><span id="odLocation">-</span></li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <div class="fw-semibold mb-2">Konten undangan</div>
                            <ul class="list-group list-group-flush border rounded-3 overflow-hidden">
                                <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Musik</span><span id="odMusic">-</span></li>
                                <li class="list-group-item"><span class="text-muted d-block mb-1">Quote</span><span id="odQuote">-</span></li>
                                <li class="list-group-item"><span class="text-muted d-block mb-1">Cerita</span><span id="odStory">-</span></li>
                                <li class="list-group-item"><span class="text-muted d-block mb-1">Catatan</span><span id="odNotes">-</span></li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <div class="fw-semibold mb-2">Pembayaran &amp; galeri</div>
                            <ul class="list-group list-group-flush border rounded-3 overflow-hidden mb-3">
                                <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Status Pembayaran</span><span id="odPaymentStatus" class="text-capitalize">-</span></li>
                                <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Metode</span><span id="odPaymentMethod">-</span></li>
                                <li class="list-group-item"><span class="text-muted d-block mb-1">Catatan Admin</span><span id="odPaymentAdminNote">-</span></li>
                                <li class="list-group-item"><a id="odProofLink" href="#" target="_blank" class="link-primary d-none">Lihat bukti pembayaran</a><span id="odNoProof" class="text-muted">Belum ada bukti pembayaran</span></li>
                            </ul>
                            <div class="text-muted mb-2">Foto galeri (<span id="odPhotoCount">0</span>)</div>
                            <div id="odPhotoGrid" class="d-flex flex-wrap gap-2"></div>
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
            const modal = document.getElementById('orderDetailModal');
            if (!modal) return;

            const fmt = function (v) { return 'Rp ' + Number(v || 0).toLocaleString('id-ID'); };
            const set = function (id, v) {
                const el = document.getElementById(id);
                if (el) el.textContent = v && String(v).trim() !== '' ? v : '-';
            };
            const decode = function (payload) {
                if (!payload) return {};
                try {
                    const bytes = Uint8Array.from(window.atob(payload), function (c) { return c.charCodeAt(0); });
                    return JSON.parse(new TextDecoder().decode(bytes));
                } catch (e) {
                    try { return JSON.parse(window.atob(payload)); } catch (_) { return {}; }
                }
            };

            modal.addEventListener('show.bs.modal', function (event) {
                const d = decode(event.relatedTarget?.getAttribute('data-order-detail'));
                set('odOrderNumber', d.order_number);
                set('odOrderStatus', d.order_status);
                set('odCreatedAt', d.created_at);
                set('odProduct', d.product);
                set('odSubtotal', fmt(Number(d.total_amount || 0) + Number(d.discount_amount || 0)));
                set('odDiscount', fmt(d.discount_amount));
                set('odTotal', fmt(d.total_amount));
                set('odCustomer', d.customer);
                set('odEmail', d.email);
                set('odPhone', d.phone);
                set('odPartnerOne', d.partner_one_name);
                set('odPartnerTwo', d.partner_two_name);
                set('odEventDate', d.event_date);
                set('odLocation', d.location);
                set('odMusic', d.music);
                set('odQuote', d.quote);
                set('odStory', d.story);
                set('odNotes', d.notes);
                set('odPaymentStatus', d.payment_status);
                set('odPaymentMethod', d.payment_method);
                set('odPaymentAdminNote', d.payment_admin_note);

                const proofLink = document.getElementById('odProofLink');
                const noProof = document.getElementById('odNoProof');
                if (d.proof_url) {
                    proofLink.href = d.proof_url;
                    proofLink.classList.remove('d-none');
                    noProof.classList.add('d-none');
                } else {
                    proofLink.href = '#';
                    proofLink.classList.add('d-none');
                    noProof.classList.remove('d-none');
                }

                const photos = Array.isArray(d.photo_urls) ? d.photo_urls : [];
                set('odPhotoCount', String(photos.length));
                const grid = document.getElementById('odPhotoGrid');
                if (grid) {
                    grid.innerHTML = photos.length ? '' : '<span class="text-muted">Tidak ada foto.</span>';
                    photos.forEach(function (url) {
                        const a = document.createElement('a');
                        a.href = url; a.target = '_blank'; a.className = 'd-inline-block';
                        const img = document.createElement('img');
                        img.src = url; img.alt = 'Foto galeri';
                        img.className = 'rounded-2 object-fit-cover border';
                        img.style.cssText = 'width:72px;height:72px;';
                        a.appendChild(img); grid.appendChild(a);
                    });
                }
            });
        })();
    </script>
@endpush
