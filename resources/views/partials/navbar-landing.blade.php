<nav class="navbar navbar-expand-lg navbar-light fixed-top border-bottom navbar-blur">
    <div class="container">
        <a class="navbar-brand fw-semibold d-flex align-items-center gap-2" href="{{ route('home') }}">
            <span class="d-inline-flex align-items-center justify-content-center rounded-3 text-white btn-gradient" style="width:38px;height:38px;">
                <i class="bi bi-envelope-heart-fill"></i>
            </span>
            <span>E-Invite</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain" aria-controls="navMain" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMain">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center gap-lg-1">
                <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('home') }}#katalog">Katalog</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('home') }}#harga">Harga</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('home') }}#testimoni">Testimoni</a></li>
                @auth
                    <li class="nav-item"><a class="nav-link" href="{{ route('user.catalog') }}">App</a></li>
                    @if (auth()->user()->is_admin)
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">Admin</a></li>
                    @endif
                    <li class="nav-item">
                        <form method="post" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="nav-link btn btn-link text-decoration-none p-0 m-0">Logout</button>
                        </form>
                    </li>
                @else
                    <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                @endauth
                <li class="nav-item ms-lg-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" data-theme-toggle title="Ganti tema">
                        <i class="bi bi-moon-stars-fill"></i>
                    </button>
                </li>
                @guest
                    <li class="nav-item ms-lg-2">
                        <a class="btn btn-sm btn-gradient rounded-pill px-3" href="{{ route('register') }}">Mulai Gratis</a>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>
