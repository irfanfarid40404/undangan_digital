@extends('layouts.landing')

@section('title', 'Undangan Digital Modern')

@php
    $demoMedia = config('invitation_demo_media');
    $fallbackCovers = config('invitation_demo_media.covers', []);
@endphp

@section('content')
    <section class="position-relative overflow-hidden" style="min-height: 100vh; display: flex; align-items: center;">
        <div class="hero-blob bg-primary" style="width: 320px; height: 320px; top: -40px; right: -40px;"></div>
        <div class="hero-blob" style="width: 280px; height: 280px; bottom: -60px; left: -40px; background: #e255b1;"></div>
        <div class="container position-relative" style="z-index:1;">
            <div class="row align-items-center g-4">
                <div class="col-lg-6 section-fade">
                    <div class="badge rounded-pill glass-soft text-primary px-3 py-2 mb-3">
                        <i class="bi bi-stars me-1"></i> SaaS-ready wedding invite
                    </div>
                    <h1 class="display-5 fw-bold mb-3">
                        Buat <span class="text-gradient">undangan digital</span> yang elegan dalam hitungan menit
                    </h1>
                    <p class="lead text-muted mb-4">
                        Pilih desain, isi data, bayar, dan bagikan link undangan Anda. Tampilan modern, cepat, dan ramah mobile.
                    </p>
                    <div class="d-flex flex-wrap gap-2">
                        <a class="btn btn-lg btn-gradient rounded-pill px-4" href="{{ route('register') }}">Buat Undangan Sekarang</a>
                        <a class="btn btn-lg btn-outline-secondary rounded-pill px-4" href="#katalog">Lihat Katalog</a>
                    </div>
                    <div class="d-flex align-items-center gap-3 mt-4 small text-muted">
                        <div><i class="bi bi-check-circle-fill text-success me-1"></i> Tanpa coding</div>
                        <div><i class="bi bi-check-circle-fill text-success me-1"></i> Domain custom</div>
                        <div><i class="bi bi-check-circle-fill text-success me-1"></i> Support WA</div>
                    </div>
                </div>
                <div class="col-lg-6 section-fade">
                    <div class="glass p-4 hover-lift">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="preview-phone neu-card position-relative">
                                    <img src="{{ asset('assets/pengantin-pria.jpg') }}" alt="" class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover" width="640" height="1138" loading="lazy" decoding="async">
                                    <div class="position-relative h-100 w-100 d-flex flex-column justify-content-end p-3 text-white" style="background: linear-gradient(160deg, rgba(124,92,255,.45), rgba(226,85,177,.5)); min-height: 100%;">
                                        <div class="small opacity-75">The Wedding of</div>
                                        <div class="fw-semibold">Aira &amp; Reza</div>
                                        <div class="small opacity-75">12 Jul 2026</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="preview-phone neu-card position-relative">
                                    <img src="{{ asset('assets/pengantin-wanita.webp') }}" alt="" class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover" width="640" height="1138" loading="lazy" decoding="async">
                                    <div class="position-relative h-100 w-100 d-flex flex-column justify-content-end p-3 text-white" style="background: linear-gradient(160deg, rgba(226,85,177,.45), rgba(124,92,255,.5)); min-height: 100%;">
                                        <div class="small opacity-75">Save The Date</div>
                                        <div class="fw-semibold">Luna &amp; Bima</div>
                                        <div class="small opacity-75">20 Agu 2026</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="katalog" class="py-5">
        <div class="container section-fade">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
                <div>
                    <h2 class="h3 fw-bold mb-1">Preview contoh undangan</h2>
                    <p class="text-muted mb-0">Kurasi template siap pakai dengan nuansa wedding modern.</p>
                </div>
                <a class="btn btn-outline-primary rounded-pill" href="{{ route('user.catalog') }}">Buka katalog lengkap</a>
            </div>
            <div class="row g-3">
                @forelse ($catalogPreview as $i => $product)
                    @php
                        $coverUrl = $product->resolvedImageUrl() ?? ($fallbackCovers[$i % max(count($fallbackCovers), 1)] ?? '');
                        $fallbackCover = $fallbackCovers[0] ?? config('invitation_demo_media.preview_cover');
                    @endphp
                    <div class="col-md-4">
                        <div class="card border-0 glass hover-lift h-100">
                            <div class="position-relative rounded-top overflow-hidden bg-body-secondary" style="height: 220px;">
                                <div class="position-relative w-100 h-100">
                                    <img
                                        src="{{ $coverUrl ?: $fallbackCover }}"
                                        alt="{{ $product->name }}"
                                        class="w-100 h-100 object-fit-cover catalog-image"
                                        width="1200"
                                        height="900"
                                        loading="lazy"
                                        decoding="async"
                                        data-fallback="{{ $fallbackCover }}"
                                    >
                                    <div class="position-absolute bottom-0 start-0 end-0 p-3 text-white" style="background: linear-gradient(to top, rgba(0,0,0,.72) 0%, transparent 58%); z-index: 1;">
                                        <div class="small opacity-75">Template</div>
                                        <div class="fw-semibold">{{ $product->name }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="text-muted small">{{ $product->category ?: ($product->description ?: 'Template undangan digital') }}</div>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <span class="fw-semibold">Mulai Rp {{ number_format((int) $product->price, 0, ',', '.') }}</span>
                                    <a class="btn btn-sm btn-gradient rounded-pill" href="{{ route('user.design', ['slug' => $product->slug]) }}">Pilih</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info border-0 glass-soft mb-0">Belum ada produk aktif di katalog.</div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container section-fade">
            <h2 class="h3 fw-bold text-center mb-4">Keunggulan layanan</h2>
            <div class="row g-3">
                @foreach ([['bi-lightning-charge','Cepat & ringan','CDN global + optimasi gambar otomatis.'],['bi-phone','Mobile-first','Tampilan undangan sempurna di semua layar.'],['bi-palette','Kustom warna','Tema unik sesuai identitas pasangan.'],['bi-music-note-beamed','Musik latar','Integrasi audio elegan dengan kontrol volume.']] as [$icon,$title,$desc])
                    <div class="col-md-6 col-lg-3">
                        <div class="p-4 h-100 neu-card hover-lift">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-3 text-white btn-gradient mb-3" style="width:44px;height:44px;">
                                <i class="bi {{ $icon }} fs-5"></i>
                            </div>
                            <div class="fw-semibold mb-1">{{ $title }}</div>
                            <p class="text-muted small mb-0">{{ $desc }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section id="alur" class="py-5">
        <div class="container section-fade">
            <h2 class="h3 fw-bold text-center mb-2">Cara kerja</h2>
            <p class="text-muted text-center mb-5">Alur pemesanan mengikuti journey pengguna dari awal hingga selesai.</p>
            <div class="row g-4 align-items-center">
                <div class="col-lg-6">
                    <div class="glass p-4">
                        {{--
                            Menampilkan gambar diagram workflow dari folder public/assets.
                            File SVG: workflow.svg terletak di public/assets/
                            
                            Atribut:
                            - src: menggunakan helper asset() untuk mereferensikan file dari public/assets/workflow.svg
                            - alt: teks alternatif untuk aksesibilitas
                            - class: styling Bootstrap (w-100 untuk full width, rounded untuk border radius)
                            - loading: lazy loading untuk optimasi performa
                            - decoding: async decoding untuk mencegah blocking
                        --}}
                        <img src="{{ asset('assets/workflow.png') }}" alt="Workflow diagram" class="w-100 rounded" loading="lazy" decoding="async">

                        </pre>
                    </div>
                </div>
                <div class="col-lg-6">
                    <ol class="list-group list-group-numbered">
                        @foreach ([['Daftar / login','Akses dashboard personal.'],['Pilih template','Filter tema & harga, preview detail.'],['Isi data acara','Upload galeri, cerita, musik.'],['Checkout & bayar','Transfer, e-wallet, atau QRIS.'],['Bagikan link','Pantau status hingga selesai.']] as [$h,$p])
                            <li class="list-group-item glass-soft d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-semibold">{{ $h }}</div>
                                    <small class="text-muted">{{ $p }}</small>
                                </div>
                                <i class="bi bi-arrow-right-short fs-3 text-primary"></i>
                            </li>
                        @endforeach
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section id="harga" class="py-5">
        <div class="container section-fade">
            <h2 class="h3 fw-bold text-center mb-4">Paket harga</h2>
            <div class="row g-3 justify-content-center">
                @php
                    $plans = [
                        ['name' => 'Basic', 'price' => 'Rp 149rb', 'feat' => '1 tema, 10 foto, musik', 'card' => '', 'btn' => 'btn-outline-primary'],
                        ['name' => 'Pro', 'price' => 'Rp 249rb', 'feat' => 'Semua tema, galeri unlimited', 'card' => 'border-primary', 'btn' => 'btn-gradient'],
                        ['name' => 'Enterprise', 'price' => 'Custom', 'feat' => 'White-label + domain', 'card' => '', 'btn' => 'btn-outline-primary'],
                    ];
                @endphp
                @foreach ($plans as $plan)
                    <div class="col-md-4">
                        <div class="card h-100 border-0 glass hover-lift {{ $plan['card'] }}">
                            <div class="card-body p-4">
                                <div class="text-muted small text-uppercase">{{ $plan['name'] }}</div>
                                <div class="display-6 fw-bold my-2">{{ $plan['price'] }}</div>
                                <p class="text-muted small">{{ $plan['feat'] }}</p>
                                <a class="btn w-100 rounded-pill {{ $plan['btn'] }}" href="{{ route('register') }}">Pilih paket</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section id="testimoni" class="py-5">
        <div class="container section-fade">
            <h2 class="h3 fw-bold text-center mb-4">Testimoni</h2>
            <div class="row g-3">
                @foreach ([['Maya','Undangannya cantik banget, tamu bilang premium.'],['Dimas','Prosesnya jelas, statusnya real-time.'],['Salsa','Dark mode di dashboardnya helpful!']] as $ti => [$n,$q])
                    <div class="col-md-4">
                        <div class="p-4 h-100 neu-card">
                            <div class="text-warning mb-2"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i></div>
                            <p class="mb-3">“{{ $q }}”</p>
                            <div class="d-flex align-items-center gap-3">
                                <img src="{{ $demoMedia['testimonials'][$ti] }}" alt="" class="rounded-circle object-fit-cover flex-shrink-0" width="48" height="48" loading="lazy" decoding="async">
                                <div>
                                    <div class="fw-semibold">{{ $n }}</div>
                                    <div class="small text-muted">Pengguna E-Invite</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section id="faq" class="py-5 pb-5">
        <div class="container section-fade">
            <h2 class="h3 fw-bold text-center mb-4">FAQ</h2>
            <div class="accordion glass" id="faqAcc">
                @foreach ([['Apakah bisa edit setelah bayar?','Ya, revisi minor tersedia sesuai paket.'],['Berapa lama undangan aktif?','Minimal 12 bulan sejak tayang, bisa diperpanjang.'],['Apakah mendukung RSVP?','Paket Pro ke atas mendukung RSVP digital.']] as $i => [$q,$a])
                    <div class="accordion-item bg-transparent border-0 border-bottom">
                        <h2 class="accordion-header">
                            <button class="accordion-button {{ $i ? 'collapsed' : '' }} bg-transparent shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#fq{{ $i }}">{{ $q }}</button>
                        </h2>
                        <div id="fq{{ $i }}" class="accordion-collapse collapse {{ $i ? '' : 'show' }}" data-bs-parent="#faqAcc">
                            <div class="accordion-body text-muted">{{ $a }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        (function () {
            document.querySelectorAll('.catalog-image').forEach(function (img) {
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
        })();
    </script>
@endpush
