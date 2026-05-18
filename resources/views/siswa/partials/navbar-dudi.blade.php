<!-- Top Navigation Bar -->
<nav class="navbar-dudi">
    <div class="nav-left">
        <button class="nav-hamburger" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
        <span class="nav-title">Pencarian DUDI</span>
    </div>
</nav>

<!-- Sidebar Navigation -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>
<nav class="sidebar-nav" id="sidebarNav">
    <div class="sidebar-nav-header">
        <span class="sidebar-nav-title">Menu</span>
        <button class="sidebar-close" onclick="closeSidebar()">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="sidebar-nav-items">
        <a href="{{ route('siswa.dashboard') }}" class="sidebar-nav-link sidebar-nav-item {{ request()->routeIs('siswa.dashboard') ? 'active' : '' }}">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('siswa.dudi.index') }}" class="sidebar-nav-link sidebar-nav-item {{ request()->routeIs('siswa.dudi.*') ? 'active' : '' }}">
            <i class="fas fa-briefcase"></i>
            <span>Cari DUDI</span>
        </a>
        <a href="{{ route('siswa.booking.index') }}" class="sidebar-nav-link sidebar-nav-item {{ request()->routeIs('siswa.booking.*') ? 'active' : '' }}">
            <i class="fas fa-calendar-check"></i>
            <span>Status Pengajuan</span>
        </a>
        <a href="{{ route('siswa.profile.index') }}" class="sidebar-nav-link sidebar-nav-item {{ request()->routeIs('siswa.profile.*') ? 'active' : '' }}">
            <i class="fas fa-file-upload"></i>
            <span>Profil</span>
        </a>
        <div class="sidebar-nav-item sidebar-footer">
            <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                @csrf
                <button type="submit" class="sidebar-logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </div>
</nav>