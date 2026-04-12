@extends('layouts.admin')

@section('title', 'Konfirmasi Pembayaran')
@section('page_heading', 'Verifikasi pembayaran')

@php $adminActive = 'payments'; @endphp

@section('content')
    <div class="card border-0 glass mb-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <div class="fw-semibold">Data metode pembayaran</div>
                    <div class="small text-muted">Atur rekening, e-wallet, dan QRIS yang akan tampil di halaman user.</div>
                </div>
            </div>
            <div class="row g-3">
                @foreach ($methodPresets as $method)
                    @php($setting = $method['setting'])
                    <div class="col-lg-4">
                        <div class="border rounded-4 p-3 h-100 bg-body bg-opacity-50">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <span class="d-inline-flex align-items-center justify-content-center rounded-3 btn-gradient text-white" style="width:34px;height:34px;">
                                    <i class="bi {{ $method['icon'] }}"></i>
                                </span>
                                <div class="fw-semibold">{{ $method['label'] }}</div>
                            </div>
                            <form method="post" action="{{ route('admin.payment-methods.save', $method['code']) }}" enctype="multipart/form-data" class="small">
                                @csrf
                                <div class="mb-2">
                                    <label class="form-label">Nama metode</label>
                                    <input type="text" name="display_name" class="form-control form-control-sm" value="{{ old('display_name', $setting?->display_name ?? $method['label']) }}" required>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Provider</label>
                                    <input type="text" name="provider_name" class="form-control form-control-sm" value="{{ old('provider_name', $setting?->provider_name) }}" placeholder="{{ $method['placeholder_provider'] }}">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Nama pemilik akun</label>
                                    <input type="text" name="account_name" class="form-control form-control-sm" value="{{ old('account_name', $setting?->account_name) }}" placeholder="{{ $method['placeholder_name'] }}">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Nomor akun</label>
                                    <input type="text" name="account_number" class="form-control form-control-sm" value="{{ old('account_number', $setting?->account_number) }}" placeholder="{{ $method['placeholder_number'] }}">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Catatan</label>
                                    <textarea name="notes" rows="2" class="form-control form-control-sm" placeholder="Instruksi tambahan untuk user">{{ old('notes', $setting?->notes) }}</textarea>
                                </div>
                                @if ($method['code'] === \App\Models\PaymentMethodSetting::METHOD_QRIS)
                                    <div class="mb-2">
                                        <label class="form-label">Gambar QRIS</label>
                                        <input type="file" name="qris_image" accept="image/*" class="form-control form-control-sm">
                                    </div>
                                    @if (!empty($method['qris_image_url']))
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <a href="{{ $method['qris_image_url'] }}" target="_blank" class="small">Lihat QRIS aktif</a>
                                            <div class="form-check mb-0">
                                                <input class="form-check-input" type="checkbox" value="1" name="remove_qris_image" id="remove_qr_{{ $method['code'] }}">
                                                <label class="form-check-label" for="remove_qr_{{ $method['code'] }}">Hapus</label>
                                            </div>
                                        </div>
                                    @endif
                                @endif
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" role="switch" name="is_active" value="1" id="active_{{ $method['code'] }}" @checked(old('is_active', $setting?->is_active ?? true))>
                                    <label class="form-check-label" for="active_{{ $method['code'] }}">Aktifkan metode</label>
                                </div>
                                <button type="submit" class="btn btn-sm btn-gradient rounded-pill">Simpan metode</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="row g-3">
        @forelse ($payments as $payment)
            @php($order = $payment->order)
            <div class="col-lg-6">
                <div class="card border-0 glass h-100 hover-lift">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <div class="text-muted small">Pesanan</div>
                                <div class="fw-semibold">#{{ $order?->publicNumber() ?? '—' }}</div>
                            </div>
                            <span class="badge text-bg-warning-subtle text-warning">Menunggu bukti</span>
                        </div>
                        <div class="h4 mb-3">Rp {{ number_format($payment->amount, 0, ',', '.') }}</div>
                        @if ($payment->proof_path)
                            <div class="small mb-3">
                                <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($payment->proof_path) }}" target="_blank">Lihat bukti</a>
                            </div>
                        @endif
                        <div class="d-flex flex-wrap gap-2">
                            <form method="post" action="{{ route('admin.payments.verify', $payment) }}">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success rounded-pill">Konfirmasi</button>
                            </form>
                            <form method="post" action="{{ route('admin.payments.reject', $payment) }}" class="d-flex gap-2 align-items-start flex-grow-1">
                                @csrf
                                <input type="text" name="admin_note" class="form-control form-control-sm" placeholder="Alasan tolak (opsional)">
                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill text-nowrap">Tolak</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-light border mb-0">Tidak ada pembayaran menunggu verifikasi.</div>
            </div>
        @endforelse
    </div>
    @if ($payments->hasPages())
        <div class="mt-3">{{ $payments->withQueryString()->links() }}</div>
    @endif
@endsection
