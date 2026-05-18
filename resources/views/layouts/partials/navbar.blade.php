<!-- Navbar -->
<nav class="navbar">
    <div class="nav-container">
        <div class="nav-left">
            <button id="hamburger" class="hamburger-btn" aria-label="Toggle sidebar">
                <i class="fas fa-bars"></i>
            </button>

            <a href="{{ Auth::check() && Auth::user()->role === 'admin' ? route('admin.dashboard') : (Auth::check() && Auth::user()->role === 'siswa' ? route('siswa.dashboard') : url('/')) }}" class="nav-logo-container">
                <img src="{{ asset('images/logo-vohisix.png') }}" alt="VOHISIX Logo" class="nav-logo-img">
            </a>

            <div class="nav-title">
                @if(Auth::check() && Auth::user()->role === 'admin')
                    VOHISIX Admin
                @else
                    Website Booking PKL
                @endif
            </div>
        </div>

        <div class="nav-right">
            @if(Auth::check())
                <div class="user-greeting">
                    <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                    <span>Halo, {{ Auth::user()->name }}</span>
                </div>
            @endif
        </div>
    </div>
</nav>
