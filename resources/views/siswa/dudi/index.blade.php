@extends('layouts.app')

@section('title', 'Pencarian DUDI - SMKN 6 Malang')

@section('head')
<link rel="stylesheet" href="{{ asset('css/dashboard-siswa.css') }}">
<link rel="stylesheet" href="{{ asset('css/cari-dudi.css') }}">
<style>
    /* Additional CSS untuk komponen yang belum ada di cari-dudi.css */
    .pagination {
        display: flex;
        justify-content: center;
        gap: 5px;
        margin-top: 40px;
        flex-wrap: wrap;
    }

    .pagination a,
    .pagination span {
        padding: 8px 12px;
        border: 1px solid #cbd5e1;
        border-radius: 5px;
        color: #003056;
        text-decoration: none;
        transition: all 0.2s;
    }

    .pagination a:hover {
        background-color: #003056;
        color: white;
        border-color: #003056;
    }

    .pagination span.pagination-points {
        border: none;
        color: #94a3b8;
    }

    .pagination .active span {
        background-color: #003056;
        color: white;
        border-color: #003056;
    }

    .company-name {
        display: block;
        width: 100%;
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
        max-width: 100%;
    }

    .company-info {
        min-width: 0;
    }

    .company-location {
        display: block;
        width: 100%;
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
        max-width: 100%;
        font-size: 0.9rem;
        color: #64748b;
        margin-top: 4px;
    }

    .btn {
        display: inline-block;
        padding: 10px 16px;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        border: none;
        transition: all 0.3s;
        font-size: 0.95rem;
        text-align: center;
        margin-top: 8px;
    }

    .btn-primary {
        background-color: #003056;
        color: white;
        width: 100%;
    }

    .btn-primary:hover {
        background-color: #1a4a7a;
        transform: translateY(-2px);
    }
</style>
@endsection

@section('content')
    <div class="search-header-section">
        <div class="container">
            <div class="header-content">
                <div class="filter-row">
                    <form id="filterForm" action="{{ route('siswa.dudi.index') }}" method="GET">
                        <input type="hidden" name="provinsi" id="provinsiInput" value="{{ $provinsi ?? '' }}">
                        <input type="hidden" name="kota" id="kotaInput" value="{{ $kota ?? '' }}">
                        <div class="search-wrapper">
                            <div class="search-split">
                                <div class="location-search" id="locationSearch">
                                    <div class="search-box" onclick="toggleLocationDropdown()">
                                        <i class="fas fa-map-pin"></i>
                                        <span id="selectedLocationText">
                                            @if($kota)
                                                {{ ucfirst($kota) }}
                                            @elseif($provinsi)
                                                {{ ucfirst($provinsi) }}
                                            @else
                                                Lokasi
                                            @endif
                                        </span>
                                        <i class="fas fa-chevron-down chevron"></i>
                                    </div>
                                    <div class="location-dropdown" id="locationDropdown">
                                        <div class="location-tabs">
                                            <div class="location-tab active" data-level="provinsi" onclick="switchLocationTab('provinsi')">Provinsi</div>
                                            <div class="location-tab" data-level="kabupaten" onclick="switchLocationTab('kabupaten')">Kab/Kota</div>
                                        </div>

                                        <div class="location-content" id="provinsiList">
                                            <div class="location-list">
                                                @forelse($provinces as $provinsiOption)
                                                    <div class="location-item" data-provinsi="{{ $provinsiOption }}" onclick="selectProvinsi('{{ $provinsiOption }}')">
                                                        <i class="fas fa-map"></i>
                                                        <div class="item-content">
                                                            <div class="item-name">{{ $provinsiOption }}</div>
                                                        </div>
                                                        <span class="item-type">Provinsi</span>
                                                        <i class="fas fa-check check-icon"></i>
                                                    </div>
                                                @empty
                                                    <div class="location-item">
                                                        <div class="item-content">
                                                            <div class="item-name">Tidak ada provinsi</div>
                                                        </div>
                                                    </div>
                                                @endforelse
                                            </div>
                                        </div>

                                        <div class="location-content" id="kabupatenList" style="display: none;">
                                            <div class="back-link" onclick="switchLocationTab('provinsi')">
                                                <i class="fas fa-arrow-left"></i>
                                                <span>Kembali ke Provinsi</span>
                                            </div>
                                            <div class="location-list" id="kabupatenItems">
                                                <div class="location-item no-data">
                                                    <div class="item-content">
                                                        <div class="item-name">Pilih provinsi terlebih dahulu</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="dudi-search">
                                    <i class="fas fa-search"></i>
                                    <input type="text" id="searchInput" name="search" placeholder="Cari nama perusahaan atau bidang..." value="{{ $search ?? '' }}" oninput="document.getElementById('filterForm').submit()">
                                    <!-- live search: removed Cari button; form submits on input/change -->
                                </div>
                            </div>
                        </div>
                        <div class="filter-sort-group">
                            <button type="button" class="filter-sort-btn" id="filterButton" onclick="toggleFilterMenu()">
                                <i class="fas fa-sliders-h"></i>
                                <span>Filter</span>
                            </button>

                            <div class="filter-menu" id="filterMenu">
                                <div class="filter-menu-header">
                                    <i class="fas fa-filter"></i> Filter
                                </div>

                                <div class="filter-menu-content">
                                    <div class="filter-group">
                                        <div class="filter-group-title">
                                            <i class="fas fa-clock"></i> Jam Kerja PKL
                                        </div>
                                        <div class="filter-option">
                                            <input type="radio" name="jamSemua" id="jamSemua" value="semua" checked>
                                            <label for="jamSemua"><strong>Semua Jam</strong></label>
                                        </div>
                                        <div class="jam-grid">
                                            <div class="jam-group">
                                                <h4><i class="fas fa-sun"></i> Jam Berangkat</h4>
                                                <div class="jam-options" id="jamBerangkatOptions">
                                                    <div class="jam-option">
                                                        <input type="checkbox" id="berangkat0500" name="jam_berangkat[]" value="05.00">
                                                        <label for="berangkat0500">05.00</label>
                                                    </div>
                                                    <div class="jam-option">
                                                        <input type="checkbox" id="berangkat0600" name="jam_berangkat[]" value="06.00">
                                                        <label for="berangkat0600">06.00</label>
                                                    </div>
                                                    <div class="jam-option">
                                                        <input type="checkbox" id="berangkat0630" name="jam_berangkat[]" value="06.30">
                                                        <label for="berangkat0630">06.30</label>
                                                    </div>
                                                    <div class="jam-option">
                                                        <input type="checkbox" id="berangkat0700" name="jam_berangkat[]" value="07.00">
                                                        <label for="berangkat0700">07.00</label>
                                                    </div>
                                                    <div class="jam-option">
                                                        <input type="checkbox" id="berangkat0800" name="jam_berangkat[]" value="08.00">
                                                        <label for="berangkat0800">08.00</label>
                                                    </div>
                                                    <div class="jam-option">
                                                        <input type="checkbox" id="berangkat0900" name="jam_berangkat[]" value="09.00">
                                                        <label for="berangkat0900">09.00</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="jam-group">
                                                <h4><i class="fas fa-moon"></i> Jam Pulang</h4>
                                                <div class="jam-options" id="jamPulangOptions">
                                                    <div class="jam-option">
                                                        <input type="checkbox" id="pulang1430" name="jam_pulang[]" value="14.30">
                                                        <label for="pulang1430">14.30</label>
                                                    </div>
                                                    <div class="jam-option">
                                                        <input type="checkbox" id="pulang1500" name="jam_pulang[]" value="15.00">
                                                        <label for="pulang1500">15.00</label>
                                                    </div>
                                                    <div class="jam-option">
                                                        <input type="checkbox" id="pulang1530" name="jam_pulang[]" value="15.30">
                                                        <label for="pulang1530">15.30</label>
                                                    </div>
                                                    <div class="jam-option">
                                                        <input type="checkbox" id="pulang1600" name="jam_pulang[]" value="16.00">
                                                        <label for="pulang1600">16.00</label>
                                                    </div>
                                                    <div class="jam-option">
                                                        <input type="checkbox" id="pulang1700" name="jam_pulang[]" value="17.00">
                                                        <label for="pulang1700">17.00</label>
                                                    </div>
                                                    <div class="jam-option">
                                                        <input type="checkbox" id="pulang1800" name="jam_pulang[]" value="18.00">
                                                        <label for="pulang1800">18.00</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="filter-group">
                                        <div class="filter-group-title">
                                            <i class="fas fa-users"></i> Kuota
                                        </div>
                                        <div class="filter-options">
                                            <div class="filter-option">
                                                <input type="radio" name="kuota" id="kuotaSemua" value="semua" {{ !$kuota || $kuota === 'semua' ? 'checked' : '' }}>
                                                <label for="kuotaSemua">Semua</label>
                                            </div>
                                            <div class="filter-option">
                                                <input type="radio" name="kuota" id="kuotaKecil" value="kecil" {{ $kuota === 'kecil' ? 'checked' : '' }}>
                                                <label for="kuotaKecil">< 5 orang</label>
                                            </div>
                                            <div class="filter-option">
                                                <input type="radio" name="kuota" id="kuotaSedang" value="sedang" {{ $kuota === 'sedang' ? 'checked' : '' }}>
                                                <label for="kuotaSedang">5-10 orang</label>
                                            </div>
                                            <div class="filter-option">
                                                <input type="radio" name="kuota" id="kuotaBesar" value="besar" {{ $kuota === 'besar' ? 'checked' : '' }}>
                                                <label for="kuotaBesar">> 10 orang</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="filter-menu-footer">
                                    <button type="button" class="btn-reset" onclick="resetFilters()">Reset</button>
                                    <button type="button" class="btn-apply" onclick="submitFilterForm()">Terapkan</button>
                                </div>
                            </div>

                            <button type="button" class="filter-sort-btn" id="sortButton" onclick="toggleSortMenu()">
                                <i class="fas fa-arrow-down-up"></i>
                                <span id="selectedSortText">
                                    @switch($sort ?? 'default')
                                        @case('nama-az') A-Z @break
                                        @case('nama-za') Z-A @break
                                        @case('kuota-banyak') Kuota Banyak @break
                                        @case('kuota-sedikit') Kuota Sedikit @break
                                        @default Urutkan
                                    @endswitch
                                </span>
                            </button>

                            <div class="sort-dropdown-menu" id="sortDropdownMenu">
                                <input type="hidden" id="sortHiddenInput" name="sort" value="{{ $sort ?? 'default' }}">
                                <div class="sort-item-option">
                                    <input type="radio" name="sortOption" value="nama-az" onchange="updateSortAndSubmit('nama-az')" {{ $sort === 'nama-az' ? 'checked' : '' }}>
                                    <label>A-Z</label>
                                </div>
                                <div class="sort-item-option">
                                    <input type="radio" name="sortOption" value="nama-za" onchange="updateSortAndSubmit('nama-za')" {{ $sort === 'nama-za' ? 'checked' : '' }}>
                                    <label>Z-A</label>
                                </div>
                                <div class="sort-item-option">
                                    <input type="radio" name="sortOption" value="kuota-banyak" onchange="updateSortAndSubmit('kuota-banyak')" {{ $sort === 'kuota-banyak' ? 'checked' : '' }}>
                                    <label>Kuota Terbanyak</label>
                                </div>
                                <div class="sort-item-option">
                                    <input type="radio" name="sortOption" value="kuota-sedikit" onchange="updateSortAndSubmit('kuota-sedikit')" {{ $sort === 'kuota-sedikit' ? 'checked' : '' }}>
                                    <label>Kuota Tersedikit</label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="result-count">
            <i class="fas fa-briefcase"></i>
            <span id="jobCount">{{ $dudis->total() }}</span> lowongan PKL tersedia
        </div>

        <div class="job-grid" id="jobGrid">
            @forelse($dudis as $dudi)
                @php
                    $jumlahPendaftar = \App\Models\Booking::where('id_dudi', $dudi->id_dudi)->whereIn('status', ['Direview', 'Diterima'])->count();
                    $kuotaTersisa = max($dudi->kuota - $jumlahPendaftar, 0);
                @endphp
                <div class="company-card">
                    <div class="card-header">
                        <div class="image-placeholder">
                            @if($dudi->logo)
                                <img src="{{ asset('storage/' . $dudi->logo) }}" alt="{{ $dudi->nama_dudi }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 12px;">
                            @else
                                <i class="fas fa-building"></i>
                            @endif
                        </div>
                        <div class="company-info">
                            <div class="company-name">{{ $dudi->nama_dudi }}</div>
                            <div class="company-location"><i class="fas fa-map-marker-alt"></i> {{ substr($dudi->alamat, 0, 25) }}{{ strlen($dudi->alamat) > 25 ? '...' : '' }}</div>
                        </div>
                    </div>
                    <div class="detail-item"><i class="fas fa-user-tie"></i> {{ $dudi->pembimbing_dudi ?? '-' }}</div>
                    <div class="detail-item"><i class="fas fa-clock"></i> {{ $dudi->jam_masuk ?? '-' }} - {{ $dudi->jam_pulang ?? '-' }}</div>
                    <div class="kuota-highlight">
                        <i class="fas fa-users"></i>
                        <span><span class="kuota-angka">{{ $kuotaTersisa }}</span> dari <span class="kuota-angka">{{ $dudi->kuota ?? '-' }}</span> kuota tersisa</span>
                    </div>
                    <button type="button"
                        class="btn btn-primary view-detail-button"
                        data-id="{{ $dudi->id_dudi }}"
                        data-nama="{{ $dudi->nama_dudi ?? '-' }}"
                        data-industri="{{ $dudi->bidang_usaha ?? '-' }}"
                        data-alamat="{{ $dudi->alamat ?? '-' }}"
                        data-location="{{ $dudi->kota ? $dudi->kota . ', Indonesia' : ($dudi->alamat ?? '-') }}"
                        data-telepon="{{ $dudi->telepon ?? '-' }}"
                        data-email="{{ $dudi->email ?? '-' }}"
                        data-website="{{ $dudi->website ?? '-' }}"
                        data-jumlah-pegawai="{{ $dudi->jumlah_pegawai ?? '-' }}"
                        data-deskripsi="{{ $dudi->deskripsi ?? '-' }}"
                        data-jam="{{ $dudi->jam_masuk ?? '-' }} - {{ $dudi->jam_pulang ?? '-' }}"
                        data-pembimbing="{{ $dudi->pembimbing_dudi ?? '-' }}"
                        data-kuota-tersisa="{{ $kuotaTersisa }}"
                        data-kuota-total="{{ $dudi->kuota ?? '-' }}"
                        data-logo="{{ $dudi->logo ? asset('storage/' . $dudi->logo) : '' }}"
                        data-sudah-ajukan="{{ $dudi->user_sudah_ajukan ? 'true' : 'false' }}"
                        data-booking-aktif="{{ $bookingAktif ? 'true' : 'false' }}"
                        data-booking-aktif-dudi="{{ $bookingAktif ? $bookingAktif->dudi->nama_dudi : '' }}"
                        data-ajukan-url="{{ route('siswa.dudi.ajukan', $dudi->id_dudi) }}"
                    >Lihat Detail</button>
                </div>
            @empty
                <div style="grid-column: 1 / -1; text-align: center; padding: 60px 30px; color: #94a3b8;">
                    <i class="fas fa-search" style="font-size: 3rem; margin-bottom: 20px; display: block;"></i>
                    <h3 style="color: #475569;">Tidak ada DUDI yang sesuai</h3>
                    <p>Coba ubah filter atau pencarian Anda</p>
                </div>
            @endforelse
        </div>

        <div class="dudi-detail-modal" id="dudiDetailModal" aria-hidden="true">
            <div class="modal-overlay" id="dudiDetailModalOverlay"></div>
            <div class="modal-container" role="dialog" aria-modal="true" aria-labelledby="dudiDetailTitle">
                <div class="modal-header">
                    <h2 id="dudiDetailTitle">Detail DUDI</h2>
                    <button type="button" class="close-btn" id="dudiDetailClose">✕</button>
                </div>
                <div class="company-header">
                    <div class="logo-placeholder" id="dudiDetailLogo">
                        <span>🏢</span>
                    </div>
                    <div class="company-info-header">
                        <h3 id="dudiDetailCompanyName">Nama DUDI</h3>
                        <div class="company-location"><span>📍</span><span id="dudiDetailLocation">Alamat</span></div>
                    </div>
                </div>

                <div class="section">
                    <h4>Persyaratan Umum</h4>
                    <div class="info-grid">
                        <div class="info-label">Jam Kerja</div>
                        <div class="info-value" id="dudiDetailJam"></div>

                        <div class="info-label">Penanggung Jawab</div>
                        <div class="info-value" id="dudiDetailPembimbing"></div>
                    </div>
                </div>

                <div class="section">
                    <h4>Tentang Perusahaan</h4>
                    <div class="info-grid">
                        <div class="info-label">Industri</div>
                        <div class="info-value" id="dudiDetailBidang"></div>

                        <div class="info-label">Jumlah Pegawai</div>
                        <div class="info-value" id="dudiDetailJumlahPegawai"></div>

                        <div class="info-label">Website</div>
                        <div class="info-value" id="dudiDetailWebsite"></div>

                        <div class="info-label">No. Telepon</div>
                        <div class="info-value" id="dudiDetailTelepon"></div>

                        <div class="info-label">Email</div>
                        <div class="info-value" id="dudiDetailEmail"></div>
                    </div>

                    <div class="company-description" id="dudiDetailDescription">Deskripsi perusahaan akan tampil di sini.</div>
                </div>

                <div class="kuota-section">
                    <div class="kuota-display">
                        <div class="kuota-row">
                            <i class="fas fa-users"></i>
                            <div class="kuota-info">
                                <span class="kuota-available" id="dudiDetailKuotaAvailable">0</span>
                                <span>dari</span>
                                <span class="kuota-total" id="dudiDetailKuotaTotal">0</span>
                                <span>kuota tersisa</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="warning-message" id="dudiWarningMessage" style="display: none;">
                    <i class="fas fa-exclamation-circle"></i>
                    <span id="warningText"></span>
                </div>

                <div class="apply-btn-wrapper">
                    <button type="button" id="dudiDetailApply" class="apply-btn">Lamar Sekarang</button>
                </div>
            </div>
        </div>

        @if($dudis->count() > 0)
            <div class="search-footer">
                <i class="fas fa-circle"></i>
                <span>Akhir dari hasil pencarian</span>
                <i class="fas fa-circle"></i>
            </div>
        @endif

        @if($dudis->total() > 0)
            <div class="pagination-container">
                <div class="pagination-info">
                    Menampilkan {{ $dudis->firstItem() ?? 0 }} - {{ $dudis->lastItem() ?? 0 }} dari {{ $dudis->total() }} data
                </div>
                <div class="pagination-links">
                    @if ($dudis->onFirstPage())
                        <span class="disabled">← Sebelumnya</span>
                    @else
                        <a href="{{ $dudis->appends(request()->except('page'))->previousPageUrl() }}">← Sebelumnya</a>
                    @endif

                    @foreach ($dudis->getUrlRange(1, $dudis->lastPage()) as $page => $url)
                        @if ($page == $dudis->currentPage())
                            <span class="active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}{{ request()->except('page') ? '&' . http_build_query(request()->except('page')) : '' }}">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if ($dudis->hasMorePages())
                        <a href="{{ $dudis->appends(request()->except('page'))->nextPageUrl() }}">Selanjutnya →</a>
                    @else
                        <span class="disabled">Selanjutnya →</span>
                    @endif
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
<script>
    const selectedProvinsi = @json($provinsi ?? '');
    const selectedKota = @json($kota ?? '');
    const provinces = @json($provinces ?? []);
    const kabupatensByProvince = @json($kabupatensByProvince ?? []);

    let currentLevel = 'provinsi';
    let activeProvince = selectedKota || selectedProvinsi || '';

    if (!activeProvince && selectedKota) {
        activeProvince = Object.keys(kabupatensByProvince).find(provinsi => kabupatensByProvince[provinsi].includes(selectedKota)) || '';
    }

    function toggleLocationDropdown() {
        const dropdown = document.getElementById('locationDropdown');
        dropdown.classList.toggle('active');
        if (dropdown.classList.contains('active')) {
            switchLocationTab(currentLevel);
        }
    }

    function selectProvinsi(provinsiName) {
        activeProvince = provinsiName;
        document.getElementById('provinsiInput').value = provinsiName;
        document.getElementById('kotaInput').value = '';
        updateSelectedLocation(provinsiName);
        switchLocationTab('kabupaten');
        loadKabupaten(provinsiName);
    }

    function selectKota(kotaName) {
        const kotaInput = document.getElementById('kotaInput');
        const provinsiInput = document.getElementById('provinsiInput');
        kotaInput.value = kotaName;
        if (activeProvince) {
            provinsiInput.value = activeProvince;
        }
        updateSelectedLocation(kotaName);
        submitFilterForm();
        closeLocationDropdown();
    }

    function closeLocationDropdown() {
        document.getElementById('locationDropdown').classList.remove('active');
    }

    function updateSelectedLocation(locationText = '') {
        const text = document.getElementById('selectedLocationText');
        if (text) {
            text.textContent = locationText || 'Lokasi';
        }
    }

    function switchLocationTab(level) {
        currentLevel = level;
        document.querySelectorAll('.location-tab').forEach(tab => tab.classList.remove('active'));
        document.querySelector(`.location-tab[data-level="${level}"]`).classList.add('active');

        document.getElementById('provinsiList').style.display = level === 'provinsi' ? 'block' : 'none';
        document.getElementById('kabupatenList').style.display = level === 'kabupaten' ? 'block' : 'none';

        if (level === 'kabupaten') {
            loadKabupaten(activeProvince);
        }
    }

    function loadKabupaten(provinsiName) {
        const container = document.getElementById('kabupatenItems');
        container.innerHTML = '';

        const kabupatens = kabupatensByProvince[provinsiName] || [];
        if (!provinsiName || kabupatens.length === 0) {
            const item = document.createElement('div');
            item.className = 'location-item no-data';
            item.innerHTML = `
                <div class="item-content">
                    <div class="item-name">Pilih provinsi terlebih dahulu</div>
                </div>`;
            container.appendChild(item);
            return;
        }

        const allItem = document.createElement('div');
        allItem.className = 'location-item';
        allItem.onclick = () => selectProvinsiFilter(provinsiName);
        allItem.innerHTML = `
            <i class="fas fa-globe"></i>
            <div class="item-content">
                <div class="item-name">Semua ${provinsiName}</div>
            </div>
            <span class="item-type">Provinsi</span>
            <i class="fas fa-check check-icon"></i>
        `;
        container.appendChild(allItem);

        kabupatens.forEach(kab => {
            const item = document.createElement('div');
            item.className = 'location-item';
            item.onclick = () => selectKota(kab);
            item.innerHTML = `
                <i class="fas fa-city"></i>
                <div class="item-content">
                    <div class="item-name">${kab}</div>
                </div>
                <span class="item-type">Kab/Kota</span>
                <i class="fas fa-check check-icon"></i>
            `;
            container.appendChild(item);
        });
    }

    function selectProvinsiFilter(provinsiName) {
        document.getElementById('provinsiInput').value = provinsiName;
        document.getElementById('kotaInput').value = '';
        updateSelectedLocation(provinsiName);
        submitFilterForm();
    }

    function backToProvinsi() {
        switchLocationTab('provinsi');
    }

    function toggleFilterMenu() {
        document.getElementById('filterMenu').classList.toggle('active');
    }

    function toggleSortMenu() {
        const menu = document.getElementById('sortDropdownMenu');
        if (menu) {
            menu.classList.toggle('active');
        }
    }

    function resetFilters() {
        document.querySelectorAll('.filter-menu input[type="radio"]').forEach(input => {
            input.checked = input.value === 'semua';
        });
        document.querySelectorAll('.filter-menu input[type="checkbox"]').forEach(input => {
            input.checked = false;
        });
    }

    function submitFilterForm() {
        document.getElementById('filterForm').submit();
    }

    function updateSortAndSubmit(sortValue) {
        document.getElementById('sortHiddenInput').value = sortValue;
        const displayText = {
            'nama-az': 'A-Z',
            'nama-za': 'Z-A',
            'kuota-banyak': 'Kuota Banyak',
            'kuota-sedikit': 'Kuota Tersedikit'
        };
        document.getElementById('selectedSortText').textContent = displayText[sortValue] || 'Urutkan';
        document.getElementById('sortDropdownMenu').classList.remove('active');
        document.getElementById('filterForm').submit();
    }

    function closeAllMenus() {
        document.getElementById('filterMenu')?.classList.remove('active');
        document.getElementById('sortDropdownMenu')?.classList.remove('active');
        closeLocationDropdown();
        closeSidebar();
    }

    function openDudiDetailModal(button) {
        const data = button.dataset;
        const dudiDetailLogo = document.getElementById('dudiDetailLogo');
        const dudiDetailCompanyName = document.getElementById('dudiDetailCompanyName');
        const dudiDetailLocation = document.getElementById('dudiDetailLocation');
        const dudiDetailBidang = document.getElementById('dudiDetailBidang');
        const dudiDetailPembimbing = document.getElementById('dudiDetailPembimbing');
        const dudiDetailJam = document.getElementById('dudiDetailJam');
        const dudiDetailKuotaAvailable = document.getElementById('dudiDetailKuotaAvailable');
        const dudiDetailKuotaTotal = document.getElementById('dudiDetailKuotaTotal');
        const dudiDetailTelepon = document.getElementById('dudiDetailTelepon');
        const dudiDetailEmail = document.getElementById('dudiDetailEmail');
        const dudiDetailDescription = document.getElementById('dudiDetailDescription');
        const dudiDetailWebsite = document.getElementById('dudiDetailWebsite');
        const dudiDetailJumlahPegawai = document.getElementById('dudiDetailJumlahPegawai');
        const dudiDetailApply = document.getElementById('dudiDetailApply');
        const warningMessage = document.getElementById('dudiWarningMessage');
        const warningText = document.getElementById('warningText');

        dudiDetailCompanyName.textContent = data.nama || 'Detail DUDI';
        dudiDetailLocation.textContent = data.location || data.alamat || '-';
        dudiDetailBidang.textContent = data.industri || '-';
        dudiDetailPembimbing.textContent = data.pembimbing || '-';
        dudiDetailJam.textContent = data.jam || '-';
        dudiDetailTelepon.textContent = data.telepon || '-';
        dudiDetailEmail.textContent = data.email || '-';
        dudiDetailWebsite.textContent = data.website || '-';
        dudiDetailJumlahPegawai.textContent = data.jumlahPegawai || '-';
        dudiDetailDescription.textContent = data.deskripsi || '-';

        const kuotaTersisa = parseInt(data.kuotaTersisa) || 0;
        const kuotaTotal = data.kuotaTotal;
        dudiDetailKuotaAvailable.textContent = kuotaTersisa;
        dudiDetailKuotaTotal.textContent = kuotaTotal;

        dudiDetailApply.dataset.dudiId = data.id || '';
        dudiDetailApply.dataset.ajukanUrl = data.ajukanUrl || '';

        const sudahAjukan = data.sudahAjukan === 'true';
        const bookingAktif = data.bookingAktif === 'true';
        const bookingAktifDudi = data.bookingAktifDudi || '';
        const kuotaPenuh = kuotaTersisa <= 0;

        warningMessage.style.display = 'none';
        dudiDetailApply.disabled = false;
        dudiDetailApply.textContent = 'Lamar Sekarang';

        if (sudahAjukan) {
            warningMessage.style.display = 'flex';
            warningText.textContent = '✓ Anda sudah mengajukan PKL ke perusahaan ini. Silakan cek status di halaman Booking PKL.';
            warningMessage.style.backgroundColor = '#d0e8f7';
            warningMessage.style.borderColor = '#88c9f0';
            warningMessage.style.color = '#003d5c';
            warningMessage.querySelector('i').style.color = '#003d5c';
            dudiDetailApply.disabled = true;
            dudiDetailApply.textContent = 'Sudah Diajukan';
        } else if (kuotaPenuh) {
            warningMessage.style.display = 'flex';
            warningText.textContent = '❌ Kuota pendaftar untuk DUDI ini sudah penuh. Anda tidak dapat mendaftar ke DUDI ini.';
            warningMessage.style.backgroundColor = '#fee2e2';
            warningMessage.style.borderColor = '#fca5a5';
            warningMessage.style.color = '#dc2626';
            warningMessage.querySelector('i').style.color = '#dc2626';
            dudiDetailApply.disabled = true;
            dudiDetailApply.textContent = 'Kuota Penuh';
        } else if (bookingAktif) {
            warningMessage.style.display = 'flex';
            warningText.textContent = 'Anda sudah memiliki pengajuan PKL yang sedang direview ke ' + bookingAktifDudi + '. Anda hanya bisa punya 1 pengajuan hingga status diterima atau ditolak.';
            warningMessage.style.backgroundColor = '#fef3c7';
            warningMessage.style.borderColor = '#fcd34d';
            warningMessage.style.color = '#b45309';
            warningMessage.querySelector('i').style.color = '#b45309';
            dudiDetailApply.disabled = true;
            dudiDetailApply.textContent = 'Pengajuan Aktif';
        }

        if (data.logo) {
            dudiDetailLogo.innerHTML = '<img src="' + data.logo + '" alt="' + (data.nama || 'Logo') + '" style="width: 100%; height: 100%; object-fit: cover; border-radius: 18px;">';
        } else {
            dudiDetailLogo.innerHTML = '<span>🏢</span>';
        }

        document.getElementById('dudiDetailModal').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeDudiDetailModal() {
        document.getElementById('dudiDetailModal').classList.remove('active');
        document.body.style.overflow = '';
    }

    document.addEventListener('DOMContentLoaded', function() {
        const dudiDetailModal = document.getElementById('dudiDetailModal');
        const dudiDetailOverlay = document.getElementById('dudiDetailModalOverlay');
        const dudiDetailClose = document.getElementById('dudiDetailClose');
        const dudiDetailApply = document.getElementById('dudiDetailApply');

        document.querySelectorAll('.view-detail-button').forEach(button => {
            button.addEventListener('click', function (event) {
                event.preventDefault();
                openDudiDetailModal(this);
            });
        });

        dudiDetailOverlay.addEventListener('click', closeDudiDetailModal);
        dudiDetailClose.addEventListener('click', closeDudiDetailModal);

        dudiDetailApply.addEventListener('click', function () {
            if (!this.disabled) {
                const ajukanUrl = this.dataset.ajukanUrl;
                if (ajukanUrl) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = ajukanUrl;
                    form.innerHTML = '<input type="hidden" name="_token" value="' + document.querySelector('meta[name="csrf-token"]').content + '">';
                    document.body.appendChild(form);
                    form.submit();
                    document.body.removeChild(form);
                }
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeDudiDetailModal();
            }
        });

        const yearElements = document.querySelectorAll('#currentYear');
        yearElements.forEach(el => el.textContent = new Date().getFullYear());
        updateSelectedLocation(selectedKota);

        document.querySelectorAll('input[name="jam_berangkat[]"], input[name="jam_pulang[]"]').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const berangkatChecked = Array.from(document.querySelectorAll('input[name="jam_berangkat[]"]:checked')).length > 0;
                const pulangChecked = Array.from(document.querySelectorAll('input[name="jam_pulang[]"]:checked')).length > 0;
                if (berangkatChecked || pulangChecked) {
                    document.getElementById('jamSemua').checked = false;
                }
            });
        });
    });
</script>
@endpush
