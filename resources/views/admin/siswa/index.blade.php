@extends('layouts.app')

@section('title', 'Data Siswa PKL')

@section('content')
<div class="card">
    <div class="toolbar-panel">
        <div>
            <h2>Daftar Siswa</h2>
            <p class="form-helper">Filter dan cari siswa berdasarkan nama atau kelas untuk mempercepat akses data.</p>
        </div>
        <form action="{{ route('admin.siswa.index') }}" method="GET" class="toolbar-grid" id="filterForm">
            <input type="text" name="search" placeholder="Cari NIS atau Nama..." value="{{ $search ?? '' }}" id="searchInput" oninput="document.getElementById('filterForm').submit()" />
            <select name="kelas" id="kelasSelect">
                <option value="">Semua Kelas</option>
                @foreach($allKelas as $k)
                    <option value="{{ $k }}" {{ ($kelas ?? '') == $k ? 'selected' : '' }}>{{ $k }}</option>
                @endforeach
            </select>
            <!-- live search: removed submit button -->
            <a href="{{ route('admin.siswa.export', request()->query()) }}" class="btn btn-secondary">Export CSV</a>
            <a href="{{ route('admin.siswa.export.pdf', request()->query()) }}" class="btn btn-secondary">Export PDF</a>
            <a href="{{ route('admin.siswa.create') }}" class="btn btn-primary">+ Tambah Siswa Baru</a>
        </form>
    </div>

    <div class="table-card" id="tableAnchor">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-primary/5 text-primary text-xs uppercase font-bold tracking-widest border-b border-slate-200">
                            <th class="px-4 py-5 text-center w-70">No Absen</th>
                            <th class="px-4 py-5 text-center w-120">Foto</th>
                            <th class="px-4 py-5 w-180">Nama</th>
                            <th class="px-4 py-5 w-140">NIS</th>
                            <th class="px-4 py-5 text-center w-120">Kelas</th>
                            <th class="px-4 py-5 w-240">Verifikasi Berkas</th>
                            <th class="px-4 py-5 text-center w-120">Kelola</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($siswas as $key => $siswa)
                        <tr class="hover-row transition-colors group">
                            <td class="px-4 py-4 text-center text-slate-400 font-mono text-xs">{{ $siswa->nomor_absen ?? '-' }}</td>
                            <td class="px-4 py-4 text-center">
                                <div class="avatar-wrapper" onclick="viewDocument('Foto Profil - {{ $siswa->nama }}', '{{ $siswa->foto ? asset('storage/'.$siswa->foto) : '' }}')">
                                    @if($siswa->foto)
                                        <img src="{{ asset('storage/'.$siswa->foto) }}" alt="{{ $siswa->nama }}" class="avatar-img">
                                    @else
                                        <div class="avatar-placeholder">{{ substr($siswa->nama, 0, 2) }}</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="font-bold text-primary text-sm">{{ $siswa->nama }}</div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="text-slate-500 font-mono text-xs">{{ $siswa->nis }}</div>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <span class="kelas-badge">{{ $siswa->kelas }}</span>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex flex-wrap">
                                    @php
                                        $berkas = $siswa->berkas;
                                    @endphp
                                    @if($berkas && $berkas->ktp_kia)
                                        <button onclick="viewDocument('KTP/KIA - {{ $siswa->nama }}', '{{ asset('storage/'.$berkas->ktp_kia) }}')" class="doc-pill doc-pill-active">KTP/KIA</button>
                                    @else
                                        <span class="doc-pill doc-pill-inactive">KTP/KIA</span>
                                    @endif

                                    @if($berkas && $berkas->surat_sehat)
                                        <button onclick="viewDocument('Surat Sehat - {{ $siswa->nama }}', '{{ asset('storage/'.$berkas->surat_sehat) }}')" class="doc-pill doc-pill-active">SEHAT</button>
                                    @else
                                        <span class="doc-pill doc-pill-inactive">SEHAT</span>
                                    @endif

                                    @if($berkas && $berkas->kartu_bpjs)
                                        <button onclick="viewDocument('BPJS - {{ $siswa->nama }}', '{{ asset('storage/'.$berkas->kartu_bpjs) }}')" class="doc-pill doc-pill-active">BPJS</button>
                                    @else
                                        <span class="doc-pill doc-pill-inactive">BPJS</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <a href="{{ route('admin.siswa.edit', $siswa->nis) }}" class="btn-edit" title="Edit">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M17 3l4 4L7 21H3v-4L17 3z"></path>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-4 py-20 text-center text-slate-300">
                                <div class="flex flex-col items-center gap-3">
                                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="opacity-10">
                                        <circle cx="11" cy="11" r="8"></circle>
                                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                    </svg>
                                    <p class="font-bold text-xs uppercase tracking-widest">Data Tidak Ditemukan</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($siswas->hasPages())
            <div class="pagination-container">
                <div class="pagination-info">
                    Menampilkan {{ $siswas->firstItem() ?? 0 }} - {{ $siswas->lastItem() ?? 0 }} dari {{ $siswas->total() }} data
                </div>
                <div class="pagination-links">
                    @if($siswas->onFirstPage())
                        <span class="disabled">← Sebelumnya</span>
                    @else
                        <a href="{{ $siswas->appends(request()->except('page'))->previousPageUrl() }}">← Sebelumnya</a>
                    @endif

                    @foreach($siswas->getUrlRange(1, $siswas->lastPage()) as $page => $url)
                        @if($page == $siswas->currentPage())
                            <span class="active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}{{ request()->except('page') ? '&'.http_build_query(request()->except('page')) : '' }}">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if($siswas->hasMorePages())
                        <a href="{{ $siswas->appends(request()->except('page'))->nextPageUrl() }}">Selanjutnya →</a>
                    @else
                        <span class="disabled">Selanjutnya →</span>
                    @endif
                </div>
            </div>
            @endif

            <!-- Footer Info -->
            <div class="footer-info">
                <div class="footer-info-item">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                        <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
                        <path d="M3 12c0 1.66 4 3 9 3s9-1.34 9-3"></path>
                    </svg>
                    <span>Total data tersimpan: <strong>{{ $totalSiswa }}</strong> siswa</span>
                </div>
                <div class="footer-info-item">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    <span>Terakhir diperbarui: <strong>{{ now()->translatedFormat('d F Y') }}, {{ now()->format('H:i') }}</strong></span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Lihat Berkas -->
<div id="docModal" class="modal-backdrop">
    <div class="modal-container modal-scale-in">
        <div class="modal-header">
            <div class="modal-title">
                <div class="p-2 bg-primary/5 rounded-xl">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#003056" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                    </svg>
                </div>
                <span id="modalTitle"></span>
            </div>
            <button onclick="closeModal()" class="modal-close">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
        <div class="modal-body">
            <img id="modalImage" src="" alt="" class="modal-image">
        </div>
        <div class="modal-footer">
            <button onclick="closeModal()" class="modal-btn">Tutup Berkas</button>
        </div>
    </div>
</div>

<!-- Modal Kredensial -->
@if(session('siswa_created'))
<div id="credentialsModal" class="modal-backdrop">
    <div class="modal-card modal-scale-in">
        <div class="modal-header">
            <h2 class="modal-title">✅ Siswa Berhasil Ditambahkan</h2>
            <button onclick="closeCredModal()" class="modal-close">✕</button>
        </div>
        <div class="modal-body">
            <div class="credential-box">
                <div>
                    <strong>Nama Siswa:</strong>
                    <p>{{ session('siswa_created')['nama'] }}</p>
                </div>
                <hr>
                <div>
                    <p class="form-helper">Username untuk Login:</p>
                    <div class="credential-value">{{ session('siswa_created')['username'] }}</div>
                </div>
                <div>
                    <p class="form-helper">Password untuk Login:</p>
                    <div class="credential-value">{{ session('siswa_created')['password'] }}</div>
                </div>
            </div>
            <div class="notice-box">
                <strong>💡 Catatan:</strong> Simpan kredensial di atas untuk diberikan kepada siswa. Siswa dapat langsung login dengan username dan password tersebut.
            </div>
            <button onclick="closeCredModal()" class="modal-btn">Tutup & Lanjutkan</button>
        </div>
    </div>
</div>
<script>
    function closeCredModal() {
        document.getElementById('credentialsModal').style.display = 'none';
    }
</script>
@endif

<script>
    const searchInputEl = document.getElementById('searchInput');
    if (searchInputEl) {
        searchInputEl.addEventListener('input', function() {
            document.getElementById('filterForm').submit();
        });
    }
    const kelasSelectEl = document.getElementById('kelasSelect');
    if (kelasSelectEl) {
        kelasSelectEl.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    }

    // improve preview handling: show fallback text if image fails to load
    function viewDocument(title, url) {
        document.getElementById('modalTitle').innerText = title;
        const img = document.getElementById('modalImage');
        const modalBody = document.querySelector('.modal-body');
        const existingNoImg = document.getElementById('noImageText');
        if (existingNoImg) existingNoImg.remove();
        if (url && url !== '') {
            img.src = url;
            img.style.display = 'block';
            img.onerror = function() {
                img.style.display = 'none';
                const noImg = document.createElement('div');
                noImg.id = 'noImageText';
                noImg.style.color = '#94a3b8';
                noImg.innerText = 'Gambar tidak dapat ditampilkan';
                modalBody.appendChild(noImg);
            };
        } else {
            img.src = '';
            img.style.display = 'none';
            const noImg = document.createElement('div');
            noImg.id = 'noImageText';
            noImg.style.color = '#94a3b8';
            noImg.innerText = 'Dokumen tidak tersedia';
            modalBody.appendChild(noImg);
            return;
        }
        document.getElementById('docModal').style.display = 'flex';
    }
    function closeModal() {
        document.getElementById('docModal').style.display = 'none';
    }
    window.viewDocument = viewDocument;
</script>
@endsection

