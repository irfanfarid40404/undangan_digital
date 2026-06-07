@extends('layouts.base')

@section('title', $order->product->name ?? 'Undangan')

@push('styles')
    <!-- Google Fonts untuk kesan Tradisional & Mewah -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;700&family=Great+Vibes&family=Plus+Jakarta+Sans:wght@300;400;600&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        :root {
            --bg-theme: #0b0c10;
            --bg-card: #1f2833;
            --gold-light: #f4d068;
            --gold-dark: #c5a059;
            --text-light: #ffffff;
            --text-muted: #c5c6c7;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-theme);
            color: var(--text-light);
            overflow-x: hidden;
            background-image: radial-gradient(rgba(197, 160, 89, 0.05) 1px, transparent 0);
            background-size: 24px 24px;
        }

        /* Hero / Cover Utama */
        .invite-hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            color: #fff;
            padding: 2rem 1rem;
            text-align: center;
        }
        .invite-bg {
            position: absolute;
            inset: 0;
            background-size: cover;
            background-position: center;
            filter: brightness(0.3) contrast(1.1);
            z-index: 1;
        }
        .invite-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(11,12,16,0.2) 0%, rgba(11,12,16,1) 100%);
            z-index: 2;
        }

        .invite-content {
            position: relative;
            z-index: 3;
            max-width: 650px;
            width: 100%;
        }

        /* Bingkai Ornamen Sudut Emas */
        .ornament-frame {
            border: 2px solid var(--gold-dark);
            padding: 3rem 2rem;
            position: relative;
            border-radius: 8px;
            background: rgba(11, 12, 16, 0.85);
        }

        /* Pembuka & Tipografi */
        .sub-title {
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 3px;
            color: var(--gold-light);
            font-weight: 600;
        }
        .script-font {
            font-family: 'Great Vibes', cursive;
            font-size: 3.5rem;
            color: var(--gold-light);
            margin: 0.5rem 0;
        }
        .names {
            font-family: 'Cinzel', serif;
            font-weight: 700;
            font-size: 2.8rem;
            letter-spacing: 1px;
            line-height: 1.2;
            color: #ffffff;
            text-shadow: 0 2px 4px rgba(0,0,0,0.5);
        }
        .meta {
            font-size: 1.1rem;
            color: var(--gold-dark);
            font-weight: 600;
            letter-spacing: 1px;
        }
        .story {
            font-size: 0.95rem;
            color: var(--text-muted);
            line-height: 1.8;
            font-style: italic;
        }

        /* Struktur Halaman Berkelanjutan */
        .invite-page {
            background-color: var(--bg-theme);
        }

        /* Navigasi Minimalis */
        .invite-nav {
            position: sticky;
            top: 0;
            background: rgba(11, 12, 16, 0.9);
            border-bottom: 1px solid rgba(197, 160, 89, 0.2);
            backdrop-filter: blur(8px);
            z-index: 100;
        }
        .invite-nav nav a {
            margin: 0 0.8rem;
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            transition: color 0.3s;
        }
        .invite-nav nav a:hover, .invite-nav nav a.active {
            color: var(--gold-light);
        }

        /* Bagian Section */
        .invite-section {
            padding: 80px 0;
        }
        .section-title {
            font-family: 'Cinzel', serif;
            color: var(--gold-light);
            text-align: center;
            margin-bottom: 3rem;
            font-weight: 700;
            letter-spacing: 2px;
        }
        .section-title::after {
            content: "❖";
            display: block;
            font-size: 1rem;
            color: var(--gold-dark);
            margin-top: 0.5rem;
        }

        /* Gaya Kartu Lengkung Atas (Arched Window) Sesuai Gambar */
        .arch-card {
            background: rgba(31, 40, 51, 0.6);
            border: 1px solid rgba(197, 160, 89, 0.2);
            border-radius: 120px 120px 20px 20px;
            padding: 3rem 2rem 2rem;
            text-align: center;
            transition: transform 0.3s;
        }
        .arch-card:hover {
            transform: translateY(-5px);
            border-color: var(--gold-light);
        }
        .arch-img {
            width: 150px;
            height: 200px;
            object-fit: cover;
            border-radius: 100px 100px 10px 10px;
            border: 2px solid var(--gold-dark);
            margin-bottom: 1.5rem;
        }

        /* Kartu Info Standar */
        .info-card {
            background: rgba(31, 40, 51, 0.4);
            border: 1px solid rgba(197, 160, 89, 0.2);
            border-radius: 16px;
            padding: 2rem;
        }

        /* Tombol & Form Input Costum */
        .btn-gold {
            background: linear-gradient(135deg, var(--gold-dark), var(--gold-light));
            color: #000;
            font-weight: 600;
            border: none;
            padding: 0.6rem 2rem;
            border-radius: 50px;
            transition: all 0.3s;
        }
        .btn-gold:hover {
            background: linear-gradient(135deg, var(--gold-light), var(--gold-dark));
            box-shadow: 0 4px 15px rgba(244, 208, 104, 0.4);
            color: #000;
        }
        .btn-outline-gold {
            border: 1px solid var(--gold-dark);
            color: var(--gold-light);
            padding: 0.6rem 2rem;
            border-radius: 50px;
            transition: all 0.3s;
        }
        .btn-outline-gold:hover {
            background: var(--gold-dark);
            color: #000;
        }

        .form-control, .form-select {
            background-color: rgba(11, 12, 16, 0.6);
            border: 1px solid rgba(197, 160, 89, 0.3);
            color: #fff;
            border-radius: 8px;
        }
        .form-control:focus, .form-select:focus {
            background-color: rgba(11, 12, 16, 0.9);
            border-color: var(--gold-light);
            box-shadow: 0 0 0 0.25rem rgba(244, 208, 104, 0.25);
            color: #fff;
        }

        /* Custom Modal Notification Popup */
        .custom-popup-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.85);
            z-index: 1050;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
            padding: 1rem;
        }
        .custom-popup-overlay.show {
            opacity: 1;
            pointer-events: auto;
        }
        .custom-popup-box {
            background: #1a1e24;
            border: 2px solid var(--gold-light);
            border-radius: 16px;
            max-width: 450px;
            width: 100%;
            padding: 2.5rem 2rem;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.8);
            transform: scale(0.8);
            transition: transform 0.3s ease;
        }
        .custom-popup-overlay.show .custom-popup-box {
            transform: scale(1);
        }

        /* Animasi Lembut Fade-In */
        .fade-in {
            opacity: 0;
            transform: translateY(15px);
            transition: opacity 0.8s ease, transform 0.8s ease;
        }
        .fade-in.show {
            opacity: 1;
            transform: none;
        }

        @media (max-width: 768px) {
            .names { font-size: 2rem; }
            .script-font { font-size: 2.8rem; }
            .invite-nav nav a { margin: 0 0.4rem; font-size: 0.8rem; }
            .invite-section { padding: 60px 0; }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Efek Fade In Element
            setTimeout(function () {
                document.querySelectorAll('.fade-in').forEach(function (el, i) {
                    setTimeout(function () { el.classList.add('show'); }, i * 150);
                });
            }, 50);

            // LOGIKA DUMMY NOTIFIKASI RSVP
            const rsvpForm = document.getElementById('rsvpForm');
            const popupOverlay = document.getElementById('successPopup');
            const closePopupBtn = document.getElementById('closePopupBtn');
            const dummyOutput = document.getElementById('dummyOutput');

            if (rsvpForm) {
                rsvpForm.addEventListener('submit', function (e) {
                    e.preventDefault(); // Mencegah form reload / crash ke database asli
                    
                    // Ambil data input
                    const name = rsvpForm.querySelector('[name="name"]').value || 'Tamu Undangan';
                    const contact = rsvpForm.querySelector('[name="contact"]').value || '-';
                    const attendingRaw = rsvpForm.querySelector('[name="attending"]').value;
                    const attendingStatus = attendingRaw === 'yes' ? '🟢 Ya, Akan Hadir' : '🔴 Maaf, Berhalangan';

                    // Masukkan ke cetakan dummy info teks di dalam pop-up
                    dummyOutput.innerHTML = `
                        <strong>Nama:</strong> ${name} <br>
                        <strong>Kontak:</strong> ${contact} <br>
                        <strong>Status:</strong> ${attendingStatus}
                    `;

                    // Tampilkan Pop-up Sukses
                    popupOverlay.classList.add('show');
                });
            }

            // Tutup Pop-up
            if (closePopupBtn) {
                closePopupBtn.addEventListener('click', function () {
                    popupOverlay.classList.remove('show');
                    rsvpForm.reset(); // Reset form isian kembali bersih
                });
            }
        });
    </script>
    <script>
        // Smooth scroll & Navigasi Aktif Otomatis
        (function () {
            const links = document.querySelectorAll('.invite-nav nav a');
            const sections = Array.from(links).map(l => document.querySelector(l.getAttribute('href'))).filter(Boolean);

            function onScroll() {
                const y = window.scrollY + 120;
                let current = sections[0];
                for (const s of sections) {
                    if (s.offsetTop <= y) current = s;
                }
                links.forEach(l => l.classList.toggle('active', document.querySelector(l.getAttribute('href')) === current));
            }

            window.addEventListener('scroll', onScroll, {passive:true});
            onScroll();

            links.forEach(l => l.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) target.scrollIntoView({behavior:'smooth', block:'start'});
            }));
        })();
    </script>
@endpush

@section('body')
    <!-- COVER HERO -->
    <section id="hero" class="invite-hero">
        <div class="invite-bg" style="background-image: url('{{ $order->product->resolvedImageUrl() ?: config('invitation_demo_media.preview_cover') }}')"></div>
        <div class="invite-overlay"></div>

        <div class="invite-content px-3">
            <div class="ornament-frame">
                <div class="fade-in text-center mb-3">
                    <i class="bi bi-flower1" style="font-size: 2.5rem; color: var(--gold-light);"></i>
                </div>
                <div class="fade-in">
                    <div class="sub-title">The Wedding Of</div>
                </div>
                <div class="fade-in">
                    <div class="script-font">Walimatul 'Ursy</div>
                </div>
                <div class="fade-in my-3">
                    <h1 class="names">{{ $order->invitationDetail->partner_one_name }} &amp; {{ $order->invitationDetail->partner_two_name }}</h1>
                </div>
                <div class="fade-in meta mb-4">
                    <div>{{ $order->invitationDetail->event_date->format('l, d F Y') }}</div>
                </div>
                
                <div class="fade-in mt-3">
                    <a class="btn btn-gold px-4 me-2" href="#details">Lihat Detail</a>
                    <a class="btn btn-outline-gold px-4" href="mailto:?subject=Undangan%20{{ rawurlencode($order->invitationDetail->partner_one_name.' & '.$order->invitationDetail->partner_two_name) }}&body={{ rawurlencode(url()->current()) }}"><i class="bi bi-share-fill small me-1"></i> Bagikan</a>
                </div>
            </div>
        </div>
    </section>

    <!-- KONTEN UTAMA -->
    <div id="invitePage" class="invite-page">
        <!-- NAVBAR STICKY -->
        <header class="invite-nav py-3">
            <div class="container d-flex align-items-center justify-content-center justify-content-md-between">
                <div class="fw-bold d-none d-md-block" style="font-family: 'Cinzel', serif; color: var(--gold-light);">
                    {{ $order->invitationDetail->partner_one_name }} &amp; {{ $order->invitationDetail->partner_two_name }}
                </div>
                <nav>
                    <a href="#hero">Home</a>
                    <a href="#details">Mempelai</a>
                    <a href="#gallery">Galeri</a>
                    <a href="#venue">Lokasi</a>
                    <a href="#rsvp">RSVP</a>
                </nav>
            </div>
        </header>

        <!-- KATA PENGANTAR & MEMPELAI -->
        <section id="details" class="invite-section">
            <div class="container text-center">
                <div class="row justify-content-center mb-5">
                    <div class="col-md-8 px-4">
                        <p class="small text-uppercase tracking-wide" style="color: var(--gold-light);">Assalamu'alaikum Warahmatullahi Wabarakatuh</p>
                        <p class="text-muted small mb-4">Maha Suci Allah yang telah menciptakan makhluk-Nya berpasang-pasangan. Ya Allah, perkenankanlah kami merangkaikan kasih sayang yang Kau ciptakan dalam pernikahan putra-putri kami:</p>
                    </div>
                </div>

                <!-- Kartu Lengkung Atas Sesuai Gambar Pendukung -->
                <div class="row g-4 justify-content-center">
                    <div class="col-sm-6 col-md-4">
                        <div class="arch-card">
                            <img src="{{ $order->product->resolvedImageUrl() ?: config('invitation_demo_media.preview_cover') }}" class="arch-img" alt="Partner One">
                            <h4 style="font-family: 'Cinzel', serif; color: var(--gold-light);">{{ $order->invitationDetail->partner_one_name }}</h4>
                            <p class="small text-muted mb-0">Putra pertama dari pasangan Bapak & Ibu Kedua Orang Tua</p>
                        </div>
                    </div>
                    <div class="col-sm-1 d-flex align-items-center justify-content-center my-3">
                        <span class="script-font" style="font-size: 2.5rem;">&amp;</span>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <div class="arch-card">
                            <img src="{{ $order->product->resolvedImageUrl() ?: config('invitation_demo_media.preview_cover') }}" class="arch-img" alt="Partner Two">
                            <h4 style="font-family: 'Cinzel', serif; color: var(--gold-light);">{{ $order->invitationDetail->partner_two_name }}</h4>
                            <p class="small text-muted mb-0">Putri kedua dari pasangan Bapak & Ibu Kedua Orang Tua</p>
                        </div>
                    </div>
                </div>

                @if($order->invitationDetail->story)
                    <div class="row justify-content-center mt-5">
                        <div class="col-md-7 info-card mt-3 mx-3">
                            <i class="bi bi-quote fs-2 text-muted opacity-50"></i>
                            <p class="story">{{ $order->invitationDetail->story }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </section>

        <!-- DETAIL WAKTU ACARA -->
        <section class="invite-section" style="background: rgba(31, 40, 51, 0.2);">
            <div class="container">
                <h3 class="section-title">Waktu Pelaksanaan</h3>
                <div class="row g-4 justify-content-center text-center">
                    <div class="col-md-5">
                        <div class="info-card h-100">
                            <i class="bi bi-calendar-heart fs-3 mb-3" style="color: var(--gold-light);"></i>
                            <h5 style="font-family: 'Cinzel', serif; color: var(--gold-light);">Akad Nikah</h5>
                            <hr style="border-color: rgba(197,160,89,0.3)">
                            <p class="mb-1"><strong>{{ $order->invitationDetail->event_date->format('l, d F Y') }}</strong></p>
                            <p class="small text-muted">Pukul 09.00 WIB - Selesai</p>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="info-card h-100">
                            <i class="bi bi-clock-history fs-3 mb-3" style="color: var(--gold-light);"></i>
                            <h5 style="font-family: 'Cinzel', serif; color: var(--gold-light);">Resepsi Pernikahan</h5>
                            <hr style="border-color: rgba(197,160,89,0.3)">
                            <p class="mb-1"><strong>{{ $order->invitationDetail->event_date->format('l, d F Y') }}</strong></p>
                            <p class="small text-muted">Pukul 11.00 WIB s/d Selesai</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- GALERI FOTO -->
        <section id="gallery" class="invite-section">
            <div class="container">
                <h3 class="section-title">Galeri Foto</h3>
                <div class="row g-3 justify-content-center">
                    @php
                        $images = [$order->product->resolvedImageUrl() ?: config('invitation_demo_media.preview_cover')];
                    @endphp
                    @foreach($images as $img)
                        <div class="col-sm-6 col-md-4">
                            <div class="overflow-hidden rounded" style="border: 1px solid rgba(197,160,89,0.2); box-shadow: 0 10px 20px rgba(0,0,0,0.5);">
                                <img src="{{ $img }}" class="w-100" style="height:250px; object-fit:cover; display:block;" alt="gallery">
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- LOKASI / PETA -->
        <section id="venue" class="invite-section" style="background: rgba(31, 40, 51, 0.2);">
            <div class="container">
                <h3 class="section-title">Lokasi Acara</h3>
                <div class="row g-4 align-items-center justify-content-center">
                    <div class="col-md-5">
                        <div class="info-card">
                            <i class="bi bi-geo-alt-fill fs-3 text-danger mb-2"></i>
                            <h5 style="font-family: 'Cinzel', serif;" class="text-white mb-2">{{ $order->invitationDetail->location }}</h5>
                            <p class="small text-muted mb-4">Catatan: {{ $order->invitationDetail->notes ?? 'Mohon datang tepat waktu.' }}</p>
                            @php
                                $mapQuery = rawurlencode($order->invitationDetail->location ?: '');
                            @endphp
                            <a class="btn btn-gold w-100" target="_blank" href="https://www.google.com/maps/search/?api=1&query={{ $mapQuery }}"><i class="bi bi-map me-1"></i> Buka Google Maps</a>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="ratio ratio-16x9 rounded overflow-hidden shadow" style="border: 1px solid rgba(197,160,89,0.3)">
                            <iframe src="https://www.google.com/maps?q={{ $mapQuery }}&output=embed" style="border:0;" allowfullscreen loading="lazy"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- KONFIRMASI KEHADIRAN (RSVP) -->
        <section id="rsvp" class="invite-section">
            <div class="container">
                <h3 class="section-title">Konfirmasi Kehadiran</h3>
                <div class="row justify-content-center g-4">
                    <div class="col-lg-6">
                        <div class="info-card">
                            <!-- Diubah menggunakan id="rsvpForm" -->
                            <form id="rsvpForm" method="post" action="javascript:void(0);">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label small text-muted">Nama Anda</label>
                                    <input class="form-control" type="text" name="name" required placeholder="Tuliskan nama lengkap">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small text-muted">Kontak (WhatsApp / Email)</label>
                                    <input class="form-control" type="text" name="contact"  required placeholder="Nomor aktif atau email">
                                </div>
                                <div class="mb-4">
                                    <label class="form-label small text-muted">Keterangan Kehadiran</label>
                                    <select class="form-select" name="attending">
                                        <option value="yes">Ya, Saya Akan Hadir</option>
                                        <option value="no">Maaf, Berhalangan Hadir</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-gold w-100">Kirim Konfirmasi Kehadiran</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- FOOTER -->
        <footer class="py-4 text-center" style="border-top: 1px solid rgba(197,160,89,0.1); background: #07080a;">
            <div class="container small text-muted">Undangan dibuat secara eksklusif dengan E-Invite</div>
        </footer>
    </div>

    <!-- POPUP MODAL NOTIFIKASI SUKSES (DUMMY DATA) -->
    <div class="custom-popup-overlay" id="successPopup">
        <div class="custom-popup-box">
            <div class="text-center mb-3">
                <i class="bi bi-check-circle-fill text-success" style="font-size: 3.5rem;"></i>
            </div>
            <h4 class="mb-2" style="font-family: 'Cinzel', serif; color: var(--gold-light);">Konfirmasi Terkirim!</h4>
            <p class="text-muted small mb-4">Terima kasih banyak atas konfirmasi kehadiran yang Anda berikan. Data Anda telah berhasil tercatat di simulasi sistem kami:</p>
            
            <!-- Kotak Output Isian Data Dummy -->
            <div class="text-start p-3 rounded mb-4 text-white-50 small" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(197,160,89,0.2); line-height: 1.8;">
                <div id="dummyOutput"></div>
            </div>

            <button type="button" class="btn btn-gold w-100" id="closePopupBtn">Selesai</button>
        </div>
    </div>
@endsection