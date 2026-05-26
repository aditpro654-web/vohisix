<div class="sidebar-overlay" id="sidebar-overlay"></div>

<aside class="sidebar" id="sidebar" aria-label="Sidebar navigation">
    <div class="sidebar-header">
        <div class="sidebar-user-avatar">
            {{ Auth::check() ? strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) : 'U' }}
        </div>
        <h2 class="sidebar-user-name">{{ Auth::check() ? Auth::user()->name : 'Pengguna' }}</h2>
        <p class="sidebar-user-role">
            @if(Auth::check() && Auth::user()->role === 'admin')
                Admin PKL
            @elseif(Auth::check() && Auth::user()->role === 'siswa')
                Siswa {{ Auth::user()->siswa->nis ?? 'PKL' }}
            @else
                Pengguna
            @endif
        </p>
    </div>

    <nav class="sidebar-nav">
        @if(Auth::check() && Auth::user()->role === 'admin')
            <a href="{{ route('admin.dashboard') }}" class="sidebar-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-th"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('admin.siswa.index') }}" class="sidebar-nav-item {{ request()->routeIs('admin.siswa.*') ? 'active' : '' }}">
                <i class="fas fa-users"></i>
                <span>Data Siswa</span>
            </a>
            <a href="{{ route('admin.dudi.index') }}" class="sidebar-nav-item {{ request()->routeIs('admin.dudi.*') ? 'active' : '' }}">
                <i class="fas fa-building"></i>
                <span>Data DUDI</span>
            </a>
            <a href="{{ route('admin.booking.index') }}" class="sidebar-nav-item {{ request()->routeIs('admin.booking.*') ? 'active' : '' }}">
                <i class="fas fa-calendar"></i>
                <span>Booking PKL</span>
            </a>
            <a href="{{ route('admin.login.index') }}" class="sidebar-nav-item {{ request()->routeIs('admin.login.*') ? 'active' : '' }}">
                <i class="fas fa-user-shield"></i>
                <span>Manajemen Login</span>
            </a>
            <a href="{{ route('admin.pengembang') }}" class="sidebar-nav-item {{ request()->routeIs('admin.pengembang') ? 'active' : '' }}">
                <i class="fas fa-code"></i>
                <span>Pengembang</span>
            </a>
        @elseif(Auth::check() && Auth::user()->role === 'siswa')
            <a href="{{ route('siswa.dashboard') }}" class="sidebar-nav-item {{ request()->routeIs('siswa.dashboard') ? 'active' : '' }}">
                <i class="fas fa-th"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('siswa.profile.index') }}" class="sidebar-nav-item {{ request()->routeIs('siswa.profile.*') ? 'active' : '' }}">
                <i class="fas fa-user"></i>
                <span>Profil</span>
            </a>
            <a href="{{ route('siswa.dudi.index') }}" class="sidebar-nav-item {{ request()->routeIs('siswa.dudi.*') ? 'active' : '' }}">
                <i class="fas fa-search"></i>
                <span>Cari DUDI</span>
            </a>
            <a href="{{ route('siswa.booking.index') }}" class="sidebar-nav-item {{ request()->routeIs('siswa.booking.*') ? 'active' : '' }}">
                <i class="fas fa-file"></i>
                <span>Status Pengajuan</span>
            </a>
            <a href="{{ route('siswa.pengembang') }}" class="sidebar-nav-item {{ request()->routeIs('siswa.pengembang') ? 'active' : '' }}">
                <i class="fas fa-code"></i>
                <span>Pengembang</span>
            </a>
        @endif
    </nav>

    <div class="sidebar-footer">
        @if(Auth::check())
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="sidebar-nav-item">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </button>
            </form>
        @endif
    </div>
</aside>
