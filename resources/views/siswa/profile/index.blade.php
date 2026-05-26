@extends('layouts.app')

@section('title', 'Data Kelengkapan Berkas PKL')

@php
    $siswaNama = $siswa->nama ?? 'Siswa';
    $siswaNis = $siswa->nis ?? '-';
    $siswaKelas = $siswa->kelas ?? '-';
    $siswaFoto = $siswa->foto ? asset('storage/'.$siswa->foto) : null;

    $berkasCompleted = 0;
    if ($berkas && $berkas->ktp_kia) $berkasCompleted++;
    if ($berkas && $berkas->surat_sehat) $berkasCompleted++;
    if ($berkas && $berkas->kartu_bpjs) $berkasCompleted++;

    $documents = [
        [
            'id' => 'ktp_kia',
            'name' => 'Fotocopy KTP / KIA',
            'status' => $berkas && $berkas->ktp_kia ? 'uploaded' : 'empty',
            'imageUrl' => $berkas && $berkas->ktp_kia ? asset('storage/' . $berkas->ktp_kia) : null,
            'originalName' => $berkas->ktp_kia_name ?? null,
        ],
        [
            'id' => 'surat_sehat',
            'name' => 'Surat Keterangan Sehat',
            'status' => $berkas && $berkas->surat_sehat ? 'uploaded' : 'empty',
            'imageUrl' => $berkas && $berkas->surat_sehat ? asset('storage/' . $berkas->surat_sehat) : null,
            'originalName' => $berkas->surat_sehat_name ?? null,
        ],
        [
            'id' => 'kartu_bpjs',
            'name' => 'Kartu BPJS Ketenagakerjaan',
            'status' => $berkas && $berkas->kartu_bpjs ? 'uploaded' : 'empty',
            'imageUrl' => $berkas && $berkas->kartu_bpjs ? asset('storage/' . $berkas->kartu_bpjs) : null,
            'originalName' => $berkas->kartu_bpjs_name ?? null,
        ],
    ];
@endphp

@section('head')
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
    /* ===== RESET & GLOBAL ===== */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Outfit', system-ui, -apple-system, sans-serif;
        background-color: #FFFFFF;
        color: #1E293B;
    }

    /* ===== ANIMASI ===== */
    @keyframes pulse-custom {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    .animate-pulse-custom {
        animation: pulse-custom 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    @keyframes scaleIn {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
    }
    .modal-scale-in {
        animation: scaleIn 0.2s ease-out;
    }

    /* ===== LAYOUT UTAMA ===== */
    .container {
        max-width: 1280px;
        margin: 0 auto;
        padding: 20px 40px 32px 40px;
    }

    /* Grid: kiri (info) 1fr (sempit), kanan (form) 2fr (lebar) */
    .two-columns {
        display: grid;
        grid-template-columns: 1fr 2fr;
        gap: 40px;
        align-items: stretch;
    }

    /* Kolom info (kiri) - sempit, tinggi sejajar */
    .info-column {
        display: flex;
        flex-direction: column;
        gap: 24px;
        height: 100%;
    }

    /* Ketentuan card: padding dikurangi, footer dengan margin-top auto */
    .ketentuan-card {
        background: white;
        border: 1px solid #E8EDF2;
        border-radius: 20px;
        padding: 20px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .ketentuan-content {
        flex: 1;
    }

    .ketentuan-footer {
        margin-top: auto;
        padding-top: 20px;
        border-top: 1px solid #E8EDF2;
    }

    /* ===== FLEX UTILITIES ===== */
    .flex-between { display: flex; justify-content: space-between; align-items: center; }
    .items-center { display: flex; align-items: center; }
    .gap-2 { gap: 8px; }
    .gap-3 { gap: 12px; }
    .mb-2 { margin-bottom: 8px; }
    .mb-3 { margin-bottom: 12px; }
    .mb-4 { margin-bottom: 16px; }
    .mb-6 { margin-bottom: 24px; }
    .mt-1 { margin-top: 4px; }
    .mt-2 { margin-top: 8px; }
    .mt-3 { margin-top: 12px; }
    .mt-4 { margin-top: 16px; }
    .mt-6 { margin-top: 24px; }
    .p-4 { padding: 16px; }
    .p-5 { padding: 20px; }
    .p-6 { padding: 24px; }
    .px-3 { padding-left: 12px; padding-right: 12px; }
    .py-2 { padding-top: 8px; padding-bottom: 8px; }
    .py-3 { padding-top: 12px; padding-bottom: 12px; }
    .py-4 { padding-top: 16px; padding-bottom: 16px; }
    .w-full { width: 100%; }
    .text-center { text-align: center; }
    .text-sm { font-size: 13px; }
    .text-xs { font-size: 10px; }
    .text-\[9px\] { font-size: 9px; }
    .font-bold { font-weight: 700; }
    .font-medium { font-weight: 500; }
    .uppercase { text-transform: uppercase; }
    .tracking-wider { letter-spacing: 0.05em; }
    .tracking-widest { letter-spacing: 0.1em; }
    .tracking-tighter { letter-spacing: -0.05em; }
    .rounded-lg { border-radius: 8px; }
    .rounded-xl { border-radius: 12px; }
    .rounded-2xl { border-radius: 16px; }
    .border { border: 1px solid #E2E8F0; }
    .border-t { border-top: 1px solid #E8EDF2; }
    .w-1\.5 { width: 6px; height: 6px; }
    .h-1\.5 { height: 6px; }
    .rounded-full { border-radius: 9999px; }
    .overflow-hidden { overflow: hidden; }
    .shadow-sm { box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
    .shadow-inner { box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); }
    .cursor-pointer { cursor: pointer; }
    .transition-all { transition: all 0.2s ease; }
    .duration-1000 { transition-duration: 1000ms; }
    .shrink-0 { flex-shrink: 0; }
    .opacity-80 { opacity: 0.8; }
    .leading-relaxed { line-height: 1.6; }

    /* ===== CARD & KOMPONEN ===== */
    .glass-card {
        background: white;
        border: 1px solid #E8EDF2;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
        border-radius: 20px;
        height: 100%;
    }

    .upload-zone {
        border: 2px dashed #E2E8F0;
        transition: all 0.2s ease;
        cursor: pointer;
        background: #FAFCFE;
        border-radius: 16px;
        text-align: center;
        padding: 28px 32px;
    }
    .upload-zone:hover {
        border-color: #003056;
        background-color: #F5F8FC;
    }

    /* Upload subtitle lebih kecil */
    .upload-subtext {
        font-size: 10px;
        color: #94A3B8;
        margin-top: 4px;
        font-weight: 600;
    }

    .uploaded-card {
        background-color: #003056;
        border-radius: 16px;
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 18px 20px;
        cursor: pointer;
        color: white !important;
    }
    .uploaded-card-content {
        flex: 1;
        cursor: pointer;
    }
    /* Perkecil teks "Klik untuk melihat berkas" */
    .uploaded-card-content .click-text {
        font-size: 9px;
        color: rgba(255, 255, 255, 0.7);
        font-weight: 500;
    }
    .ganti-button {
        cursor: pointer;
        background: none;
        border: none;
        color: rgba(255, 255, 255, 0.8);
        font-size: 10px;
        font-weight: bold;
        text-decoration: underline;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 8px;
        margin: -8px;
        transition: color 0.2s;
    }
    .ganti-button:hover {
        color: white;
    }

    /* Profile Card: lebih tinggi */
    .profile-card {
        background-color: #003056;
        border-radius: 20px;
        padding: 26px 20px;
        color: white;
        display: flex;
        align-items: center;
        gap: 20px;
    }
    .profile-avatar {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        overflow: hidden;
        background: #f1f5f9;
        cursor: pointer;
        flex-shrink: 0;
    }
    .profile-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .profile-avatar-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #e2e8f0;
        color: #003056;
        font-size: 24px;
        font-weight: bold;
    }
    .profile-info {
        flex: 1;
    }
    .profile-name {
        font-size: 16px;
        font-weight: 700;
        margin-bottom: 4px;
    }
    .profile-nis, .profile-kelas {
        font-size: 12px;
        opacity: 0.8;
        margin-bottom: 2px;
    }

    /* Status Badge dengan lingkaran pulse */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        border-radius: 14px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        width: 100%;
    }
    .status-badge-red {
        background-color: #FEF2F2;
        border: 1px solid #FECACA;
        color: #B91C1C;
    }
    .status-badge-green {
        background-color: #ECFDF5;
        border: 1px solid #A7F3D0;
        color: #047857;
    }
    .status-badge-blue {
        background-color: #EFF6FF;
        border: 1px solid #BFDBFE;
        color: #1D4ED8;
    }
    .pulse-dot {
        display: inline-block;
        width: 8px;
        height: 8px;
        background-color: currentColor;
        border-radius: 50%;
        animation: pulse-custom 2s infinite;
    }

    /* Ketentuan list */
    .ketentuan-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
        color: #003056;
    }
    .ketentuan-list {
        display: flex;
        flex-direction: column;
        gap: 16px;
        margin-left: 8px;
        padding-left: 0;
        margin-bottom: 0;
    }
    .ketentuan-list li {
        display: flex;
        gap: 12px;
        align-items: flex-start;
    }
    .ketentuan-list li p {
        font-size: 13px;
        line-height: 1.5;
        color: #475569;
    }

    /* Tombol */
    .btn-primary {
        background-color: #003056;
        color: white;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        padding: 14px 20px;
        font-size: 14px;
        font-weight: 700;
        border-radius: 16px;
        width: 100%;
    }
    .btn-primary:hover {
        background-color: #002543;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 48, 86, 0.15);
    }
    .btn-disabled {
        background-color: #F1F5F9;
        color: #94A3B8;
        cursor: not-allowed;
        padding: 14px 20px;
        font-size: 14px;
        font-weight: 700;
        border-radius: 16px;
        width: 100%;
    }
    /* Warna tombol saved lebih redup */
    .btn-saved {
        background-color: #1e4d8c;
        color: white;
        padding: 14px 20px;
        font-size: 14px;
        font-weight: 700;
        border-radius: 16px;
        width: 100%;
    }
    .btn-saved:hover {
        background-color: #153e6b;
        transform: translateY(-1px);
    }

    /* Last updated text - abu-abu muda */
    .last-updated-text {
        color: #a0aec0 !important;
        font-size: 12px;
    }

    /* Document Item */
    .document-item {
        margin-bottom: 32px;
    }
    .document-item:last-child {
        margin-bottom: 0;
    }
    .document-label {
        margin-bottom: 14px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 14px;
        font-weight: 700;
        color: #003056;
    }

    /* Modal */
    .modal-backdrop {
        position: fixed;
        inset: 0;
        z-index: 100;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        background-color: rgba(0, 48, 86, 0.4);
        backdrop-filter: blur(4px);
    }
    .modal-container {
        background: white;
        border-radius: 1.5rem;
        max-width: 560px;
        width: 100%;
        overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }
    .modal-header {
        padding: 1.25rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .modal-close {
        background: transparent;
        border: none;
        cursor: pointer;
        padding: 0.5rem;
        border-radius: 9999px;
        transition: all 0.2s;
        color: #94a3b8;
    }
    .modal-close:hover {
        background: #f1f5f9;
        color: #003056;
        transform: rotate(90deg);
    }
    .modal-body {
        padding: 1.5rem;
        background: #f8fafc;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 300px;
    }
    .modal-body img {
        max-width: 100%;
        max-height: 50vh;
        border-radius: 0.75rem;
    }
    .modal-footer {
        padding: 1.25rem;
        display: flex;
        justify-content: flex-end;
    }
    .modal-btn {
        background: #003056;
        color: white;
        font-weight: 700;
        padding: 0.6rem 1.5rem;
        border-radius: 2rem;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
    }
    .modal-btn:hover {
        background: #002542;
        transform: translateY(-1px);
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .two-columns {
            grid-template-columns: 1fr;
            gap: 32px;
        }
        .info-column {
            order: 1;
        }
        .glass-card {
            order: 2;
        }
        .container {
            padding: 20px;
        }
        .profile-card {
            flex-direction: column;
            text-align: center;
        }
        .profile-avatar {
            margin: 0 auto;
        }
    }
    @media (max-width: 768px) {
        .container {
            padding: 16px;
        }
        .upload-zone {
            padding: 20px;
        }
        .ketentuan-card {
            padding: 16px;
        }
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="two-columns">
        <!-- Kolom KIRI (info) – sempit -->
        <div class="info-column">
            <!-- Status Badge dengan lingkaran pulse -->
            <div class="status-badge status-badge-red" id="statusBadge">
                <span class="pulse-dot"></span>
                <span id="statusText">Status: Belum Lengkap</span>
            </div>

            <!-- Profile Card (lebih tinggi) -->
            <div class="profile-card">
                <div class="profile-avatar" onclick="showImagePreview('{{ $siswaFoto ?: '' }}', '{{ $siswaNama }}')">
                    @if($siswaFoto)
                        <img src="{{ $siswaFoto }}" alt="Foto Siswa">
                    @else
                        <div class="profile-avatar-placeholder">{{ substr($siswaNama, 0, 2) }}</div>
                    @endif
                </div>
                <div class="profile-info">
                    <div class="profile-name">{{ $siswaNama }}</div>
                    <div class="profile-nis">NIS: {{ $siswaNis }}</div>
                    <div class="profile-kelas">Kelas: {{ $siswaKelas }}</div>
                </div>
            </div>

            <!-- Ketentuan Card dengan footer di bawah -->
            <div class="ketentuan-card">
                <div class="ketentuan-content">
                    <div class="ketentuan-header">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="16" x2="12" y2="12"/>
                            <line x1="12" y1="8" x2="12.01" y2="8"/>
                        </svg>
                        <h3 class="font-bold uppercase text-sm tracking-wider">Ketentuan Unggah</h3>
                    </div>
                    <ul class="ketentuan-list">
                        <li>
                            <span class="w-1.5 h-1.5 bg-[#003056] rounded-full mt-1.5 shrink-0"></span>
                            <p>Dokumen digital harus merupakan hasil scan (bukan foto) agar terbaca jelas.</p>
                        </li>
                        <li>
                            <span class="w-1.5 h-1.5 bg-[#003056] rounded-full mt-1.5 shrink-0"></span>
                            <p>Ukuran berkas dibatasi maksimal 2MB untuk efisiensi penyimpanan server.</p>
                        </li>
                        <li>
                            <span class="w-1.5 h-1.5 bg-[#003056] rounded-full mt-1.5 shrink-0"></span>
                            <p>Verifikasi manual oleh Hubin memerlukan waktu maksimal 2 hari kerja.</p>
                        </li>
                    </ul>
                </div>
                <div class="ketentuan-footer">
                    <button id="saveBtn" class="btn-disabled" disabled>Simpan Seluruh Progress</button>
                    <p class="text-center last-updated-text mt-3 font-medium tracking-tighter" id="lastUpdated">Terakhir diperbarui: --</p>
                </div>
            </div>
        </div>

        <!-- Kolom KANAN (form upload) – lebar -->
        <div class="glass-card p-6 rounded-2xl">
            <div id="documentsContainer"></div>
        </div>
    </div>
</div>

<!-- Modal Preview -->
<div id="previewModal" class="modal-backdrop">
    <div class="modal-container modal-scale-in">
        <div class="modal-header">
            <h3 id="modalTitle" style="font-weight: 700; color: #003056;">Preview Dokumen</h3>
            <button onclick="closeModal()" class="modal-close">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>
        <div class="modal-body" id="modalBodyContainer">
            <!-- Preview content will be injected here -->
        </div>
        <div class="modal-footer">
            <button onclick="closeModal()" class="modal-btn">Tutup</button>
        </div>
    </div>
</div>

<script>
    // Data dari server
    const documents = @json($documents);
    const isSaved = documents.every(doc => doc.status === 'uploaded');

    // Ikon SVG
    function getFileTextIcon() {
        return `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: white;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>`;
    }

    function getIcon(id) {
        if (id === 'ktp_kia') {
            return `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#003056" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>`;
        }
        if (id === 'surat_sehat') {
            return `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#003056" stroke-width="2"><circle cx="18" cy="18" r="2"/><path d="M18 14v2"/><path d="M18 20v2"/><circle cx="12" cy="12" r="2"/><path d="M2 12h1"/><path d="M18 12h1"/><path d="M12 2v1"/><path d="M12 21v1"/></svg>`;
        }
        return `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#003056" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg>`;
    }

    function getUploadIcon() {
        return `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#94A3B8" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>`;
    }

    function renderDocuments() {
        const container = document.getElementById('documentsContainer');
        if (!container) return;
        let html = '';
        for (const doc of documents) {
            if (doc.status === 'uploaded') {
                html += `
                    <div class="document-item">
                        <div class="document-label">
                            <span style="display: flex; align-items: center; gap: 10px;">
                                <span class="opacity-70">${getIcon(doc.id)}</span>
                                ${doc.name}
                            </span>
                            <span class="font-bold text-xs uppercase tracking-wider" style="color: #003056;">✓ Sudah Diunggah</span>
                        </div>
                        <div class="uploaded-card" onclick="viewDocument('${doc.name}', '${doc.imageUrl || '#'}')">
                            <div class="p-2 rounded-xl" style="background: rgba(255,255,255,0.2);">${getFileTextIcon()}</div>
                            <div class="uploaded-card-content">
                                <p class="text-sm font-bold text-white" style="letter-spacing: 0.025em;">${doc.originalName || `${doc.id.toUpperCase()}_SCAN.pdf`}</p>
                                <p class="click-text">Klik untuk melihat berkas</p>
                            </div>
                            <button class="ganti-button" onclick="event.stopPropagation(); handleGanti('${doc.id}')">Ganti</button>
                        </div>
                    </div>
                `;
            } else {
                html += `
                    <div class="document-item">
                        <div class="document-label">
                            <span style="display: flex; align-items: center; gap: 10px;">
                                <span class="opacity-70">${getIcon(doc.id)}</span>
                                ${doc.name}
                            </span>
                        </div>
                        <div class="upload-zone" onclick="handleUpload('${doc.id}')">
                            <div class="mb-3" style="color: #94A3B8;">${getUploadIcon()}</div>
                            <p class="text-sm font-bold uppercase tracking-wide" style="color: #003056;">Pilih atau Seret File Berkas</p>
                            <p class="upload-subtext">Format PDF / JPG, Maks 2MB</p>
                        </div>
                    </div>
                `;
            }
        }
        container.innerHTML = html;
    }

    function viewDocument(title, fileUrl) {
        if (!fileUrl || fileUrl === '#') {
            alert('Dokumen belum tersedia');
            return;
        }

        const container = document.getElementById('modalBodyContainer');
        if (!container) {
            alert('Preview tidak tersedia saat ini.');
            return;
        }

        document.getElementById('modalTitle').innerText = title;
        if (fileUrl.toLowerCase().endsWith('.pdf')) {
            container.innerHTML = `<iframe src="${fileUrl}" frameborder="0" style="width:100%;height:72vh;border:none;"></iframe>`;
        } else {
            container.innerHTML = `<img src="${fileUrl}" alt="Preview" style="max-width:100%;max-height:72vh;object-fit:contain;" onerror="this.onerror=null;this.src='https://placehold.co/600x800/003056/white?text=Preview+tidak+dapat+ditampilkan';">`;
        }

        document.getElementById('previewModal').style.display = 'flex';
    }

    function showImagePreview(url, name) {
        if (!url) {
            alert('Foto belum tersedia');
            return;
        }

        const container = document.getElementById('modalBodyContainer');
        if (!container) {
            alert('Preview tidak tersedia saat ini.');
            return;
        }

        document.getElementById('modalTitle').innerText = 'Foto - ' + name;
        container.innerHTML = `<img src="${url}" alt="Foto ${name}" style="max-width:100%;max-height:72vh;object-fit:contain;" onerror="this.onerror=null;this.src='https://placehold.co/600x800/003056/white?text=Foto+tidak+dapat+ditampilkan';">`;
        document.getElementById('previewModal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('previewModal').style.display = 'none';
    }

    function updateStats() {
        const total = documents.length;
        const completed = documents.filter(d => d.status === 'uploaded').length;
        const isComplete = completed === total;
        const statusBadge = document.getElementById('statusBadge');
        const statusText = document.getElementById('statusText');
        const pulseDot = document.querySelector('#statusBadge .pulse-dot');
        const saveBtn = document.getElementById('saveBtn');

        if (statusBadge) {
            statusBadge.classList.remove('status-badge-red', 'status-badge-green', 'status-badge-blue');
        }

        if (isSaved) {
            if (statusBadge) {
                statusBadge.classList.add('status-badge-blue');
                statusText.innerText = 'Status: Tersimpan';
            }
            if (pulseDot) pulseDot.style.backgroundColor = '#3b82f6';
        } else if (isComplete) {
            if (statusBadge) {
                statusBadge.classList.add('status-badge-green');
                statusText.innerText = 'Status: Lengkap';
            }
            if (pulseDot) pulseDot.style.backgroundColor = '#10b981';
        } else {
            if (statusBadge) {
                statusBadge.classList.add('status-badge-red');
                statusText.innerText = 'Status: Belum Lengkap';
            }
            if (pulseDot) pulseDot.style.backgroundColor = '#dc2626';
        }

        if (saveBtn) {
            if (isSaved) {
                saveBtn.disabled = true;
                saveBtn.classList.remove('btn-disabled', 'btn-primary');
                saveBtn.classList.add('btn-saved');
                saveBtn.innerText = '✓ Tersimpan';
            } else if (isComplete) {
                saveBtn.disabled = false;
                saveBtn.classList.remove('btn-disabled', 'btn-saved');
                saveBtn.classList.add('btn-primary');
                saveBtn.innerText = 'Simpan Seluruh Progress';
            } else {
                saveBtn.disabled = true;
                saveBtn.classList.remove('btn-primary', 'btn-saved');
                saveBtn.classList.add('btn-disabled');
                saveBtn.innerText = 'Simpan Seluruh Progress';
            }
        }
    }

    function handleUpload(id) {
        document.getElementById(`file_${id}`).click();
    }

    function handleGanti(id) {
        document.getElementById(`file_${id}`).click();
    }

    function submitBerkasUpload(fieldName) {
        const fileInput = document.getElementById(`file_${fieldName}`);
        if (fileInput.files.length > 0) {
            const formData = new FormData();
            formData.append(fieldName, fileInput.files[0]);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

            fetch('{{ route("siswa.profile.upload-berkas") }}', {
                method: 'POST',
                body: formData,
            })
            .then(response => {
                if (response.ok) {
                    location.reload();
                } else {
                    alert('Gagal upload. Coba lagi.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat upload.');
            });
        }
    }

    document.getElementById('saveBtn')?.addEventListener('click', function() {
        const completed = documents.filter(d => d.status === 'uploaded').length;
        const total = documents.length;
        if (completed === total) {
            alert('✅ Semua berkas sudah diunggah dan tersimpan!');
        } else {
            alert(`⚠️ Masih ada ${total - completed} berkas yang belum diunggah.`);
        }
    });

    renderDocuments();
    updateStats();
    const now = new Date();
    document.getElementById('lastUpdated').innerText = `Terakhir diperbarui: ${now.toLocaleDateString('id-ID')}, ${now.toLocaleTimeString('id-ID', { hour:'2-digit', minute:'2-digit' })}`;

    window.viewDocument = viewDocument;
    window.showImagePreview = showImagePreview;
    window.closeModal = closeModal;
    window.handleUpload = handleUpload;
    window.handleGanti = handleGanti;
    window.submitBerkasUpload = submitBerkasUpload;
</script>

<!-- Hidden file inputs untuk upload berkas -->
<input type="file" id="file_ktp_kia" accept=".pdf,.jpg,.jpeg,.png" style="display: none;" onchange="submitBerkasUpload('ktp_kia')">
<input type="file" id="file_surat_sehat" accept=".pdf,.jpg,.jpeg,.png" style="display: none;" onchange="submitBerkasUpload('surat_sehat')">
<input type="file" id="file_kartu_bpjs" accept=".pdf,.jpg,.jpeg,.png" style="display: none;" onchange="submitBerkasUpload('kartu_bpjs')">

@endsection
