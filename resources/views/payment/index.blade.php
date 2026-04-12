@extends('layouts.user')

@section('title', 'Pembayaran')
@section('page_heading', 'Metode pembayaran')

@php
    $sidebarActive = 'orders';
    $breadcrumb = [
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'Checkout', 'url' => route('user.checkout')],
        ['label' => 'Bayar'],
    ];
@endphp

@section('content')
    @php($selectedMethod = old('method', $latestPayment?->method ?: ($paymentMethods[0]['code'] ?? 'pay_bank')))

    @if (!empty($mustReuploadProof) && !empty($latestPayment))
        <div class="alert alert-danger border-0 d-flex align-items-start gap-2" role="alert">
            <i class="bi bi-x-octagon-fill mt-1"></i>
            <div class="small">
                <div class="fw-semibold mb-1">Pembayaran tidak disetujui admin</div>
                <div class="mb-1">{{ $latestPayment->admin_note ?: 'Silakan unggah ulang bukti pembayaran yang lebih jelas.' }}</div>
                <div>Upload bukti baru wajib diisi sebelum kirim ulang.</div>
            </div>
        </div>
    @endif

    @if(!empty($simulateFail))
        <div class="alert alert-danger d-flex align-items-start gap-2" role="alert">
            <i class="bi bi-x-octagon-fill mt-1"></i>
            <div>
                <div class="fw-semibold">Pembayaran gagal diverifikasi</div>
                <div class="small">Silakan coba lagi atau pilih metode lain. <a class="alert-link" href="{{ route('user.payment') }}">Muat ulang halaman</a></div>
            </div>
        </div>
    @endif

    <div class="card border-0 glass mb-4">
        <div class="card-body p-4">
            <div class="row g-3 align-items-center">
                <div class="col-md-8">
                    <div class="fw-semibold mb-1">Status pembayaran</div>
                    <div class="small text-muted mb-0">Pesanan {{ $order->publicNumber() }} — total Rp {{ number_format($order->total_amount, 0, ',', '.') }}.</div>
                </div>
                <div class="col-md-4 text-md-end">
                    @if ($latestPayment)
                        @if ($latestPayment->status === \App\Models\Payment::STATUS_PENDING)
                            <span class="badge rounded-pill text-bg-warning-subtle text-warning px-3 py-2">Menunggu verifikasi</span>
                        @elseif ($latestPayment->status === \App\Models\Payment::STATUS_VERIFIED)
                            <span class="badge rounded-pill text-bg-success-subtle text-success px-3 py-2">Terverifikasi</span>
                        @else
                            <span class="badge rounded-pill text-bg-danger-subtle text-danger px-3 py-2">Ditolak</span>
                        @endif
                    @else
                        <span class="badge rounded-pill text-bg-secondary-subtle text-secondary px-3 py-2">Belum mengirim bukti</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <form id="paymentForm" method="post" action="{{ route('user.payment.store') }}" enctype="multipart/form-data" class="card border-0 glass mb-4">
        @csrf
        <div class="card-body p-4">
            <div class="fw-semibold mb-3">Kirim konfirmasi pembayaran</div>
            <div class="row g-3 mb-3">
                @foreach ($paymentMethods as $method)
                    <div class="col-md-4">
                        <input
                            type="radio"
                            class="btn-check"
                            name="method"
                            id="{{ $method['code'] }}"
                            value="{{ $method['code'] }}"
                            autocomplete="off"
                            data-method-name="{{ $method['label'] }}"
                            data-provider="{{ $method['provider_name'] ?? '-' }}"
                            data-account-name="{{ $method['account_name'] ?? '-' }}"
                            data-account-number="{{ $method['account_number'] ?? '-' }}"
                            data-notes="{{ $method['notes'] ?? '-' }}"
                            data-qris-image="{{ $method['qris_image_url'] ?? '' }}"
                            @checked($selectedMethod === $method['code'])
                            required
                        >
                        <label class="w-100" for="{{ $method['code'] }}">
                            <div class="card border-0 glass h-100 hover-lift p-4 text-center" style="cursor:pointer;">
                                <div class="d-inline-flex mx-auto align-items-center justify-content-center rounded-3 btn-gradient text-white mb-3" style="width:48px;height:48px;">
                                    <i class="bi {{ $method['icon'] }} fs-4"></i>
                                </div>
                                <div class="fw-semibold">{{ $method['label'] }}</div>
                                <div class="small text-muted">{{ $method['provider_name'] ?: 'Dikonfigurasi admin' }}</div>
                            </div>
                        </label>
                    </div>
                @endforeach
            </div>

            <div class="border rounded-4 p-3 mb-3 bg-body bg-opacity-50" id="methodDetailPanel">
                <div class="fw-semibold mb-2">Detail metode pembayaran</div>
                <div class="small text-muted mb-2">Metode: <span id="methodDetailName">-</span></div>
                <div class="row g-2 small mb-2">
                    <div class="col-md-6"><span class="text-muted">Provider:</span> <span id="methodDetailProvider">-</span></div>
                    <div class="col-md-6"><span class="text-muted">Nama akun:</span> <span id="methodDetailAccountName">-</span></div>
                    <div class="col-md-6"><span class="text-muted">Nomor akun:</span> <span id="methodDetailAccountNumber">-</span></div>
                </div>
                <div class="small mb-2"><span class="text-muted">Catatan:</span> <span id="methodDetailNotes">-</span></div>
                <div id="methodDetailQrisWrapper" class="d-none">
                    <div class="small text-muted mb-2">Scan QRIS berikut:</div>
                    <a id="methodDetailQrisLink" href="#" target="_blank" class="d-inline-block">
                        <img id="methodDetailQrisImage" src="" alt="QRIS" class="rounded-3 border" style="max-width: 220px; width: 100%; height: auto;">
                    </a>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label small text-muted">Bukti transfer (wajib)</label>
                <input type="file" class="form-control" name="proof" accept="image/*" required>
                @error('method')
                    <div class="small text-danger mt-1">{{ $message }}</div>
                @enderror
                @if (!empty($mustReuploadProof))
                    <div class="form-text text-danger">Bukti transfer wajib diunggah ulang karena pengiriman sebelumnya ditolak.</div>
                @endif
            </div>
            <button type="submit" class="btn btn-gradient rounded-pill px-4">Kirim ke admin</button>
        </div>
    </form>

    <script>
        (function () {
            const radios = document.querySelectorAll('input[name="method"]');
            if (!radios.length) return;

            const setText = function (id, value) {
                const el = document.getElementById(id);
                if (!el) return;
                el.textContent = value && String(value).trim() !== '' ? value : '-';
            };

            const renderMethodDetail = function (radio) {
                if (!radio) return;
                setText('methodDetailName', radio.dataset.methodName || '-');
                setText('methodDetailProvider', radio.dataset.provider || '-');
                setText('methodDetailAccountName', radio.dataset.accountName || '-');
                setText('methodDetailAccountNumber', radio.dataset.accountNumber || '-');
                setText('methodDetailNotes', radio.dataset.notes || '-');

                const qrisWrapper = document.getElementById('methodDetailQrisWrapper');
                const qrisImage = document.getElementById('methodDetailQrisImage');
                const qrisLink = document.getElementById('methodDetailQrisLink');
                const qrisImageUrl = radio.dataset.qrisImage || '';
                if (qrisWrapper && qrisImage && qrisLink) {
                    if (qrisImageUrl) {
                        qrisImage.src = qrisImageUrl;
                        qrisLink.href = qrisImageUrl;
                        qrisWrapper.classList.remove('d-none');
                    } else {
                        qrisImage.src = '';
                        qrisLink.href = '#';
                        qrisWrapper.classList.add('d-none');
                    }
                }
            };

            radios.forEach(function (radio) {
                radio.addEventListener('change', function () {
                    renderMethodDetail(radio);
                });
            });

            const selected = document.querySelector('input[name="method"]:checked') || radios[0];
            renderMethodDetail(selected);
        })();
    </script>

    @if (app()->environment('local'))
        <form method="post" action="{{ route('user.payment.demo-paid') }}" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-outline-secondary rounded-pill btn-sm">Demo lokal: tandai lunas &amp; selesai</button>
        </form>
    @endif

    <a class="btn btn-outline-secondary rounded-pill ms-2" href="{{ route('user.payment', ['fail' => 1]) }}">URL demo gagal (?fail=1)</a>
@endsection
