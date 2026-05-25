<!-- Navbar -->
<nav class="navbar">
    <div class="nav-container">
        <div class="nav-left">
            @if(!(Auth::check() && in_array(Auth::user()->role, ['kakonsli','wali_kelas'])))
            <button id="hamburger" class="hamburger-btn" aria-label="Toggle sidebar">
                <i class="fas fa-bars"></i>
            </button>
            @endif

            <a href="{{ Auth::check() && Auth::user()->role === 'admin' ? route('admin.dashboard') : (Auth::check() && Auth::user()->role === 'siswa' ? route('siswa.dashboard') : url('/')) }}" class="nav-logo-container">
                <img src="{{ asset('images/logo-vohisix.png') }}" alt="VOHISIX Logo" class="nav-logo-img">
            </a>

            <div class="nav-separator"></div>

            <div class="nav-page-title">
                @if(Auth::check() && Auth::user()->role === 'admin')
                    @if(request()->routeIs('admin.dashboard'))
                        Dashboard
                    @elseif(request()->routeIs('admin.siswa.*'))
                        Data Siswa
                    @elseif(request()->routeIs('admin.dudi.*'))
                        Data DUDI
                    @elseif(request()->routeIs('admin.booking.*'))
                        Booking PKL
                    @elseif(request()->routeIs('admin.login.*'))
                        Manajemen Login
                    @else
                        Admin
                    @endif
                @elseif(Auth::check() && Auth::user()->role === 'siswa')
                    @if(request()->routeIs('siswa.dashboard'))
                        Dashboard
                    @elseif(request()->routeIs('siswa.profile.*'))
                        Profil
                    @elseif(request()->routeIs('siswa.dudi.*'))
                        Cari DUDI
                    @elseif(request()->routeIs('siswa.booking.*'))
                        Status Pengajuan
                    @else
                        Siswa
                    @endif
                @else
                    Website Booking PKL
                @endif
            </div>

            @if(Auth::check() && Auth::user()->role === 'admin')
            <div class="nav-stats">
                @if(request()->routeIs('admin.siswa.*'))
                    <div class="nav-stat-item">
                        <span class="stat-label">Total Siswa:</span>
                        <span class="stat-value">{{ \App\Models\Siswa::count() }}</span>
                    </div>
                @elseif(request()->routeIs('admin.dudi.*'))
                    <div class="nav-stat-item">
                        <span class="stat-label">Total DUDI:</span>
                        <span class="stat-value">{{ \App\Models\Dudi::count() }}</span>
                    </div>
                @elseif(request()->routeIs('admin.booking.*'))
                    <div class="nav-stat-item">
                        <span class="stat-label">Total Booking:</span>
                        <span class="stat-value">{{ \App\Models\Booking::count() }}</span>
                    </div>
                    <div class="nav-stat-item">
                        <span class="stat-label">Direview:</span>
                        <span class="stat-value">{{ \App\Models\Booking::where('status', 'direview')->count() }}</span>
                    </div>
                    <div class="nav-stat-item">
                        <span class="stat-label">Diterima:</span>
                        <span class="stat-value">{{ \App\Models\Booking::where('status', 'diterima')->count() }}</span>
                    </div>
                @elseif(request()->routeIs('admin.dashboard'))
                    <div class="nav-stat-item">
                        <span class="stat-label">Total Siswa:</span>
                        <span class="stat-value">{{ \App\Models\Siswa::count() }}</span>
                    </div>
                    <div class="nav-stat-item">
                        <span class="stat-label">Total Booking:</span>
                        <span class="stat-value">{{ \App\Models\Booking::count() }}</span>
                    </div>
                @endif
            </div>
            @elseif(Auth::check() && Auth::user()->role === 'siswa')
            <div class="nav-stats">
                @if(request()->routeIs('siswa.booking.*'))
                    <div class="nav-stat-item">
                        <span class="stat-label">Pengajuan Anda:</span>
                        <span class="stat-value">{{ \App\Models\Booking::where('nis', Auth::user()->siswa->nis ?? null)->count() }}</span>
                    </div>
                @endif
            </div>
            @endif
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
