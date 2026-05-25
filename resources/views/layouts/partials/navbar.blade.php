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
            {{-- small recap stats positioned to the right near the user greeting --}}
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

        <div class="nav-right" style="display:flex; align-items:center; gap:12px;">
            @if(Auth::check())
                {{-- Compact recap boxes placed left of greeting --}}
                <div style="display:flex; gap:8px; align-items:center; margin-right:6px;">
                    @if(request()->routeIs('admin.booking.*') || request()->routeIs('admin.booking.index'))
                        <div style="background:#fff; padding:6px 10px; border-radius:8px; font-size:12px; font-weight:700; color:#003056; text-align:center;">
                            <div style="font-size:13px">{{ \App\Models\Booking::count() }}</div>
                            <div style="font-size:9px; color:#6b7280;">Total</div>
                        </div>
                        <div style="background:#fff; padding:6px 10px; border-radius:8px; font-size:12px; font-weight:700; color:#b45309; text-align:center;">
                            <div style="font-size:13px">{{ \App\Models\Booking::where('status', 'direview')->count() }}</div>
                            <div style="font-size:9px; color:#6b7280;">Direview</div>
                        </div>
                        <div style="background:#fff; padding:6px 10px; border-radius:8px; font-size:12px; font-weight:700; color:#15803d; text-align:center;">
                            <div style="font-size:13px">{{ \App\Models\Booking::where('status', 'diterima')->count() }}</div>
                            <div style="font-size:9px; color:#6b7280;">Diterima</div>
                        </div>
                        <div style="background:#fff; padding:6px 10px; border-radius:8px; font-size:12px; font-weight:700; color:#b91c1c; text-align:center;">
                            <div style="font-size:13px">{{ \App\Models\Booking::where('status', 'ditolak')->count() }}</div>
                            <div style="font-size:9px; color:#6b7280;">Ditolak</div>
                        </div>
                    @elseif(request()->routeIs('admin.dudi.*') || request()->routeIs('admin.dudi.index'))
                        <div style="background:#fff; padding:6px 10px; border-radius:8px; font-size:12px; font-weight:700; color:#003056; text-align:center;">
                            <div style="font-size:13px">{{ \App\Models\Dudi::count() }}</div>
                            <div style="font-size:9px; color:#6b7280;">Total DUDI</div>
                        </div>
                        <div style="background:#fff; padding:6px 10px; border-radius:8px; font-size:12px; font-weight:700; color:#003056; text-align:center;">
                            <div style="font-size:13px">{{ \App\Models\Dudi::sum('kuota') }}</div>
                            <div style="font-size:9px; color:#6b7280;">Total Kuota</div>
                        </div>
                    @elseif(request()->routeIs('admin.siswa.*') || request()->routeIs('admin.siswa.index'))
                        <div style="background:#fff; padding:6px 10px; border-radius:8px; font-size:12px; font-weight:700; color:#003056; text-align:center;">
                            <div style="font-size:13px">{{ \App\Models\Siswa::count() }}</div>
                            <div style="font-size:9px; color:#6b7280;">Total Siswa</div>
                        </div>
                        <div style="background:#fff; padding:6px 10px; border-radius:8px; font-size:12px; font-weight:700; color:#15803d; text-align:center;">
                            <div style="font-size:13px">{{ \App\Models\Berkas::where('lengkap', true)->count() }}</div>
                            <div style="font-size:9px; color:#6b7280;">Siswa Berkas Lengkap</div>
                        </div>
                    @elseif(request()->routeIs('wali-kelas.*') || request()->routeIs('wali-kelas.dashboard'))
                        <div style="background:#fff; padding:6px 10px; border-radius:8px; font-size:12px; font-weight:700; color:#003056; text-align:center;">
                            <div style="font-size:13px">{{ \App\Models\Siswa::count() }}</div>
                            <div style="font-size:9px; color:#6b7280;">Total Siswa</div>
                        </div>
                        <div style="background:#fff; padding:6px 10px; border-radius:8px; font-size:12px; font-weight:700; color:#15803d; text-align:center;">
                            <div style="font-size:13px">{{ \App\Models\Berkas::where('lengkap', true)->count() }}</div>
                            <div style="font-size:9px; color:#6b7280;">Siswa Berkas Lengkap</div>
                        </div>
                    @endif
                </div>

                <div class="user-greeting" style="display:flex; align-items:center; gap:8px;">
                    <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                    <span style="white-space:nowrap;">Halo, {{ Auth::user()->name }}</span>
                </div>
            @endif
        </div>
    </div>
</nav>
