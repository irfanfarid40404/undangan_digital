@extends('layouts.user')

@section('title', 'Data pemesanan')
@section('page_heading', 'Form data undangan')

@php
    $sidebarActive = 'orders';
    $breadcrumb = [
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'Desain', 'url' => route('user.design', ['slug' => $product->slug])],
        ['label' => 'Data'],
    ];
@endphp

@section('content')
    <div class="alert alert-light border glass-soft small mb-4">
        Template: <strong>{{ $product->name }}</strong> — Rp {{ number_format($product->price, 0, ',', '.') }}
    </div>

    <div class="d-flex align-items-center gap-2 mb-4 step-indicator">
        @foreach (['Data','Galeri','Cerita','Musik'] as $i => $label)
            <div class="d-flex align-items-center gap-2 flex-grow-1">
                <div class="step-dot {{ $i === 0 ? 'active' : '' }}" data-step-indicator></div>
                <span class="small text-muted d-none d-md-inline">{{ $label }}</span>
            </div>
        @endforeach
    </div>

    <form id="orderWizardForm" class="card border-0 glass" method="post" action="{{ route('user.orders.store') }}" enctype="multipart/form-data" novalidate>
        @csrf
        <input type="hidden" name="product_id" value="{{ $product->id }}">

        <div class="card-body p-4">
            <div data-step="1">
                <h2 class="h5 fw-bold mb-3">Informasi mempelai &amp; acara</h2>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama mempelai 1</label>
                        <input class="form-control" name="p1" value="{{ old('p1') }}" required placeholder="Nama lengkap">
                        <div class="invalid-feedback">Wajib diisi.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nama mempelai 2</label>
                        <input class="form-control" name="p2" value="{{ old('p2') }}" required placeholder="Nama lengkap">
                        <div class="invalid-feedback">Wajib diisi.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nomor HP / WhatsApp</label>
                        <input class="form-control" name="phone_number" value="{{ old('phone_number') }}" required placeholder="Contoh: 081234567890">
                        <div class="invalid-feedback">Wajib diisi.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tanggal acara</label>
                        <input type="date" class="form-control" name="date" value="{{ old('date') }}" required>
                        <div class="invalid-feedback">Pilih tanggal.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Lokasi</label>
                        <input class="form-control" name="loc" value="{{ old('loc') }}" required placeholder="Alamat / venue">
                        <div class="invalid-feedback">Wajib diisi.</div>
                    </div>
                </div>
            </div>

            <div data-step="2" class="d-none">
                <h2 class="h5 fw-bold mb-3">Galeri foto</h2>
                <p class="text-muted small">Unggah minimal 1 foto (maksimal 10 foto, max 5 MB per file).</p>
                <input type="file" class="form-control" name="photos[]" multiple accept="image/*" required>
                <div class="invalid-feedback">Unggah minimal 1 foto.</div>
                @php
                    $galleryExamples = array_slice(config('invitation_demo_media.covers'), 0, 4);
                @endphp
                <p class="small text-muted mt-3 mb-2">Contoh tata letak (foto stok Unsplash):</p>
                <div class="row g-2">
                    @foreach ($galleryExamples as $src)
                        <div class="col-6 col-sm-3">
                            <img src="{{ $src }}" alt="" class="img-fluid rounded-3 border object-fit-cover w-100" style="aspect-ratio: 1; max-height: 120px;" loading="lazy" decoding="async">
                        </div>
                    @endforeach
                </div>
            </div>

            <div data-step="3" class="d-none">
                <h2 class="h5 fw-bold mb-3">Cerita singkat</h2>
                <textarea class="form-control" rows="5" name="story" required placeholder="Ceritakan perjalanan cinta Anda…">{{ old('story') }}</textarea>
                <div class="invalid-feedback">Cerita wajib diisi.</div>
            </div>

            <div data-step="4" class="d-none">
                <h2 class="h5 fw-bold mb-3">Musik &amp; teks kustom</h2>
                <div class="mb-3">
                    <label class="form-label">Musik background</label>
                    <select class="form-select" name="music" required>
                        <option value="" disabled @selected(old('music') === null)>Pilih musik</option>
                        <option value="Piano soft" @selected(old('music') === 'Piano soft')>Piano soft</option>
                        <option value="Acoustic love" @selected(old('music') === 'Acoustic love')>Acoustic love</option>
                    </select>
                    <div class="invalid-feedback">Pilih musik background.</div>
                </div>
                <div class="mb-0">
                    <label class="form-label">Custom teks (footer / quote)</label>
                    <input class="form-control" name="quote" value="{{ old('quote') }}" required placeholder="Mis. Terima kasih atas doa dan restunya">
                    <div class="invalid-feedback">Teks kustom wajib diisi.</div>
                </div>
                <div class="mt-3">
                    <label class="form-label">Catatan tambahan (opsional)</label>
                    <textarea class="form-control" name="notes" rows="3" placeholder="Contoh: Mohon tema warna soft, tampilkan nama panggilan, dll.">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>
        <div class="card-footer bg-transparent border-0 d-flex justify-content-between p-4 pt-0">
            <button type="button" class="btn btn-outline-secondary rounded-pill" id="orderPrevBtn">Sebelumnya</button>
            <button type="button" class="btn btn-gradient rounded-pill px-4" id="orderNextBtn">Lanjut</button>
        </div>
    </form>
@endsection
