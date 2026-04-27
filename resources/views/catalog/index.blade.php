@extends('layouts.landing')

@section('title', 'Katalog Undangan')

@push('styles')
    <style>
        .catalog-page { padding-top: 7rem; min-height: calc(100vh - 4rem); }
    </style>
@endpush

@php
    $fallbackCovers = config('invitation_demo_media.covers', []);
    $categories = $products->pluck('category')->filter()->unique()->sort()->values();
    $themes = $products->pluck('theme')->filter()->unique()->sort()->values();
@endphp

@section('content')
    <div class="catalog-page py-6">
        <div class="container">
            <div class="mb-4">
                <h1 class="h4 fw-bold mb-0">Katalog Undangan</h1>
                <nav aria-label="breadcrumb" class="mt-1">
                    <ol class="breadcrumb mb-0 small">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item active">Katalog</li>
                    </ol>
                </nav>
            </div>
    <div class="card border-0 glass mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-lg-4">
                    <label class="form-label small text-muted">Cari</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent"><i class="bi bi-search"></i></span>
                        <input type="search" class="form-control" placeholder="Cari nama template…" id="catalogSearch">
                    </div>
                </div>
                <div class="col-md-4 col-lg-3">
                    <label class="form-label small text-muted">Kategori</label>
                    <select class="form-select" id="filterCategory">
                        <option value="">Semua</option>
                        @foreach ($categories as $c)
                            <option value="{{ $c }}">{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 col-lg-3">
                    <label class="form-label small text-muted">Tema</label>
                    <select class="form-select" id="filterTheme">
                        <option value="">Semua</option>
                        @foreach ($themes as $t)
                            <option value="{{ $t }}">{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 col-lg-2">
                    <label class="form-label small text-muted">Harga</label>
                    <select class="form-select" id="filterPrice">
                        <option value="">Semua</option>
                        <option value="lt200">&lt; Rp 200rb</option>
                        <option value="gte200">≥ Rp 200rb</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    @if ($products->isEmpty())
        <div class="alert alert-info border-0 glass-soft">Belum ada produk aktif. Hubungi admin.</div>
    @else
        <div class="row g-3" id="catalogGrid">
            @foreach ($products as $i => $product)
                @php
                    $cover = $product->resolvedImageUrl() ?? ($fallbackCovers[$i % max(count($fallbackCovers), 1)] ?? '');
                    $fallbackCover = $fallbackCovers[0] ?? '';
                @endphp
                <div class="col-md-6 col-xl-4 catalog-card" data-name="{{ strtolower($product->name) }}" data-cat="{{ $product->category }}" data-theme="{{ $product->theme }}" data-price="{{ $product->price }}">
                    <div class="card border-0 glass h-100 hover-lift">
                        <div class="position-relative rounded-top overflow-hidden bg-body-secondary" style="height: 220px;">
                            <img
                                src="{{ $cover ?: $fallbackCover }}"
                                alt="{{ $product->name }}"
                                class="w-100 h-100 object-fit-cover catalog-image"
                                width="1200"
                                height="900"
                                loading="lazy"
                                decoding="async"
                                data-fallback="{{ $fallbackCover }}"
                            >
                            <div class="position-absolute bottom-0 start-0 end-0 p-3 text-white" style="background: linear-gradient(to top, rgba(0,0,0,.72) 0%, transparent 55%); z-index: 1;">
                                <div class="small opacity-75">{{ $product->category }} · {{ $product->theme }}</div>
                                <div class="h5 mb-0">{{ $product->name }}</div>
                            </div>
                        </div>
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div class="fw-semibold">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" data-bs-toggle="modal" data-bs-target="#previewModal" data-invite-preview data-title="{{ $product->name }}" data-cover="{{ $cover }}">Preview</button>
                                <a class="btn btn-sm btn-gradient rounded-pill" href="{{ route('user.design', ['slug' => $product->slug]) }}">Pilih desain</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $products->withQueryString()->links() }}
        </div>
    @endif

    {{-- <div class="mt-5">
        <div class="fw-semibold mb-2">Skeleton loading (demo)</div>
        <div class="row g-2">
            @for ($s = 0; $s < 3; $s++)
                <div class="col-md-4">
                    <div class="skeleton" style="height: 120px;"></div>
                </div>
            @endfor
        </div>
    </div> --}}

    <div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 glass">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="catalogPreviewTitle">Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body pt-0" id="catalogPreviewBody"></div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Tutup</button>
                    <a class="btn btn-gradient rounded-pill" href="{{ route('user.design') }}">Pilih desain ini</a>
                </div>
            </div>
        </div>
    </div>{{-- modal --}}
        </div>{{-- container --}}
    </div>{{-- catalog-page --}}
@endsection

@push('scripts')
    <script>
        (function () {
            const grid = document.getElementById('catalogGrid');
            if (!grid) return;
            const q = document.getElementById('catalogSearch');
            const fc = document.getElementById('filterCategory');
            const ft = document.getElementById('filterTheme');
            const fp = document.getElementById('filterPrice');

            grid.querySelectorAll('.catalog-image').forEach(function (img) {
                img.addEventListener('error', function () {
                    const fallback = img.dataset.fallback || '';
                    if (!fallback) {
                        img.style.display = 'none';
                        return;
                    }
                    const currentPath = new URL(img.src, window.location.origin).pathname;
                    const fallbackPath = new URL(fallback, window.location.origin).pathname;
                    if (currentPath !== fallbackPath) {
                        img.src = fallback;
                    } else {
                        img.style.display = 'none';
                    }
                });
            });

            function apply() {
                const term = (q.value || '').toLowerCase();
                const cat = fc.value;
                const theme = ft.value;
                const priceRule = fp.value;
                grid.querySelectorAll('.catalog-card').forEach(function (card) {
                    const okName = !term || card.dataset.name.includes(term);
                    const okCat = !cat || card.dataset.cat === cat;
                    const okTheme = !theme || card.dataset.theme === theme;
                    let okPrice = true;
                    const p = Number(card.dataset.price || 0);
                    if (priceRule === 'lt200') okPrice = p < 200000;
                    if (priceRule === 'gte200') okPrice = p >= 200000;
                    card.classList.toggle('d-none', !(okName && okCat && okTheme && okPrice));
                });
            }
            [q, fc, ft, fp].forEach(function (el) {
                el && el.addEventListener('input', apply);
                el && el.addEventListener('change', apply);
            });
        })();
    </script>
@endpush
