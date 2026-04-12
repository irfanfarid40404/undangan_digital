@extends('layouts.user')

@section('title', 'Pilih desain')
@section('page_heading', 'Kustomisasi template')

@php
    $sidebarActive = 'catalog';
    $previewCover = $product->resolvedImageUrl() ?: config('invitation_demo_media.preview_cover');
    $fallbackCover = config('invitation_demo_media.preview_cover');
    $breadcrumb = [
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'Katalog', 'url' => route('user.catalog')],
        ['label' => 'Desain'],
    ];
@endphp

@section('content')
    <div class="row g-4">
        <div class="col-lg-5">
            <div class="glass p-3 p-lg-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <div class="text-muted small">Template aktif</div>
                        <div class="h5 mb-0">{{ $product->name }}</div>
                    </div>
                    <span class="badge text-bg-primary-subtle text-primary rounded-pill">Live preview</span>
                </div>
                <div id="invitePreview" class="preview-phone neu-card mb-3">
                    <div class="h-100 w-100 position-relative">
                        <img
                            src="{{ $previewCover }}"
                            alt="{{ $product->name }}"
                            class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover"
                            width="720"
                            height="1280"
                            loading="lazy"
                            decoding="async"
                            onerror="if (this.src !== '{{ $fallbackCover }}') { this.src = '{{ $fallbackCover }}'; }"
                        >
                        <div class="position-relative h-100 w-100 d-flex flex-column justify-content-between p-4 text-white" data-preview-bg style="background: linear-gradient(160deg, rgba(124,92,255,.55), rgba(226,85,177,.45)); min-height: 100%;">
                            <div>
                                <div class="small opacity-75 text-uppercase">The Wedding of</div>
                                <h3 class="h4 fw-bold mb-0" data-preview-names>Aira &amp; Reza</h3>
                            </div>
                            <div>
                                <div class="small opacity-75" data-preview-date>Sabtu, 12 Juli 2026</div>
                                <div class="small opacity-75" data-preview-loc>Gedung Harmoni, Jakarta</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="small text-muted">Preview ini akan terhubung ke data form pada langkah berikutnya.</div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card border-0 glass h-100">
                <div class="card-body p-4">
                    <h2 class="h5 fw-bold mb-3">Warna &amp; tema</h2>
                    <p class="text-muted small">Pilih palet — preview akan memperbarui gradien utama.</p>
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        @foreach ([
                            ['Lavender', 'linear-gradient(160deg, rgba(124,92,255,.65), rgba(226,85,177,.45))'],
                            ['Rose', 'linear-gradient(160deg, rgba(226,85,177,.65), rgba(124,92,255,.35))'],
                            ['Ocean', 'linear-gradient(160deg, rgba(56,189,248,.55), rgba(124,92,255,.45))'],
                            ['Night', 'linear-gradient(160deg, rgba(15,23,42,.85), rgba(124,92,255,.55))'],
                        ] as [$label, $bg])
                            <button type="button" class="btn btn-sm rounded-pill px-3 btn-outline-secondary theme-swatch" data-bg="{{ $bg }}">{{ $label }}</button>
                        @endforeach
                    </div>
                    <h2 class="h5 fw-bold mb-3">Musik latar (demo)</h2>
                    <select class="form-select mb-4" id="musicSelect">
                        <option>Tanpa musik</option>
                        <option>Piano soft</option>
                        <option>Acoustic love</option>
                    </select>
                    <div class="d-flex flex-wrap gap-2">
                        <a class="btn btn-outline-secondary rounded-pill" href="{{ route('user.catalog') }}">Kembali</a>
                        <a class="btn btn-gradient rounded-pill px-4" href="{{ route('user.order.form', ['product' => $product->id]) }}">Lanjut isi data</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('.theme-swatch').forEach(function (btn) {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.theme-swatch').forEach(function (b) {
                    b.classList.remove('btn-gradient', 'text-white');
                    b.classList.add('btn-outline-secondary');
                });
                btn.classList.remove('btn-outline-secondary');
                btn.classList.add('btn-gradient', 'text-white');
                var bg = btn.getAttribute('data-bg');
                var layer = document.querySelector('[data-preview-bg]');
                if (layer && bg) {
                    layer.style.background = bg;
                    layer.style.minHeight = '100%';
                }
            });
        });
    </script>
@endpush
