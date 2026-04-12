<footer class="pt-5 pb-4 mt-5 border-top glass-soft">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-3 text-white btn-gradient" style="width:38px;height:38px;">
                        <i class="bi bi-envelope-heart-fill"></i>
                    </span>
                    <span class="fw-semibold">E-Invite</span>
                </div>
                <p class="text-muted small mb-3">Platform undangan digital berbasis web — cepat, elegan, dan mudah dibagikan.</p>
                <div class="d-flex gap-2">
                    <a class="btn btn-sm btn-outline-secondary rounded-circle" href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                    <a class="btn btn-sm btn-outline-secondary rounded-circle" href="#" aria-label="WhatsApp"><i class="bi bi-whatsapp"></i></a>
                    <a class="btn btn-sm btn-outline-secondary rounded-circle" href="#" aria-label="TikTok"><i class="bi bi-tiktok"></i></a>
                </div>
            </div>
            <div class="col-6 col-lg-2">
                <div class="fw-semibold mb-2">Produk</div>
                <ul class="list-unstyled small text-muted">
                    <li><a class="link-secondary text-decoration-none" href="{{ route('user.catalog') }}">Katalog</a></li>
                    <li><a class="link-secondary text-decoration-none" href="{{ route('home') }}#harga">Paket</a></li>
                    <li><a class="link-secondary text-decoration-none" href="{{ route('home') }}#faq">FAQ</a></li>
                </ul>
            </div>
            <div class="col-6 col-lg-3">
                <div class="fw-semibold mb-2">Kontak</div>
                <ul class="list-unstyled small text-muted mb-0">
                    <li><i class="bi bi-envelope me-1"></i> halo@einvite.id</li>
                    <li><i class="bi bi-telephone me-1"></i> +62 812-0000-0000</li>
                    <li><i class="bi bi-geo-alt me-1"></i> Jakarta, Indonesia</li>
                </ul>
            </div>
            <div class="col-lg-3">
                <div class="fw-semibold mb-2">Newsletter</div>
                <form class="d-flex gap-2" onsubmit="event.preventDefault(); InviteUI.showToast('Terima kasih! (demo)', 'success');">
                    <input type="email" class="form-control form-control-sm" placeholder="Email Anda">
                    <button class="btn btn-sm btn-gradient" type="submit">Kirim</button>
                </form>
            </div>
        </div>
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 mt-4 pt-3 border-top small text-muted">
            <span>© {{ date('Y') }} E-Invite. Semua hak dilindungi.</span>
            <span><a class="link-secondary text-decoration-none" href="#">Kebijakan Privasi</a> · <a class="link-secondary text-decoration-none" href="#">Syarat Layanan</a></span>
        </div>
    </div>
</footer>
