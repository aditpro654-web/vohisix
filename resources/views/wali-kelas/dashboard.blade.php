@extends('layouts.app')

@section('title', 'Dashboard Wali Kelas')

@section('head')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif;
        background-color: #f8fafc;
        color: #1e293b;
    }

    .hide-sidebar .hamburger-btn,
    .hide-sidebar .sidebar,
    .hide-sidebar .sidebar-overlay {
        display: none !important;
    }

    .hide-sidebar .main-content {
        margin-left: 0 !important;
    }

    .wali-dashboard {
        max-width: 1500px;
        margin: 0 auto;
        padding: 24px 24px 40px;
    }

    header.wali-header {
        background-color: #003056;
        color: white;
        border-radius: 24px;
        padding: 40px 32px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    .header-title {
        font-size: 1.875rem;
        font-weight: 800;
        letter-spacing: -0.025em;
        margin-bottom: 8px;
    }

    .header-subtitle {
        color: #cbd5e1;
        font-size: 0.875rem;
        font-weight: 500;
        margin-bottom: 24px;
    }

    .header-stats {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        justify-content: center;
    }

    .header-stat {
        flex: 1 1 140px;
        min-width: 140px;
        background-color: rgba(255, 255, 255, 0.1);
        padding: 16px 20px;
        border-radius: 16px;
        text-align: center;
    }

    .header-stat strong {
        display: block;
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 6px;
    }

    .header-stat span {
        display: block;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #cbd5e1;
        font-weight: 700;
    }

    .dashboard-card {
        background-color: white;
        border-radius: 24px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        margin-top: 32px;
    }

    .dashboard-toolbar {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        justify-content: space-between;
        align-items: center;
        padding: 24px;
        background-color: #003056;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .dashboard-toolbar-left,
    .dashboard-toolbar-right {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        align-items: center;
    }

    .toolbar-input,
    .toolbar-select,
    .toolbar-button,
    .custom-select {
        border-radius: 16px;
        padding: 10px 16px;
        font-size: 0.875rem;
        font-weight: 600;
        border: 1px solid rgba(255, 255, 255, 0.3);
        outline: none;
        color: white;
        background-color: rgba(255, 255, 255, 0.1);
    }

    .toolbar-input {
        min-width: 280px;
    }

    .toolbar-button {
        background-color: white;
        color: #003056;
        border: none;
        cursor: pointer;
        min-width: 140px;
        transition: all 0.2s ease;
    }

    .toolbar-button:hover {
        transform: translateY(-1px);
    }

    .custom-select {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%23FFFFFF' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3E%3C/svg%3E");
        background-position: right 14px center;
        background-repeat: no-repeat;
        background-size: 16px;
        padding-right: 44px;
        min-width: 160px;
    }

    .dashboard-table-wrapper {
        overflow-x: auto;
        background-color: white;
    }

    table.dashboard-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 640px;
    }

    table.dashboard-table th,
    table.dashboard-table td {
        padding: 16px 24px;
        text-align: left;
        vertical-align: middle;
        font-size: 0.875rem;
    }

    table.dashboard-table th {
        background-color: #003056;
        color: white;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        font-weight: 800;
    }

    table.dashboard-table tbody tr {
        border-bottom: 1px solid #f1f5f9;
    }

    .avatar-cell img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 4px 12px;
        border-radius: 9999px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .badge-lengkap {
        background-color: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }

    .badge-kurang {
        background-color: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    .badge-diterima {
        background-color: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }

    .badge-ditolak {
        background-color: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    .badge-direview {
        background-color: #fef3c7;
        color: #b45309;
        border: 1px solid #fde68a;
    }

    .detail-button {
        background-color: #003056;
        color: white;
        border: none;
        border-radius: 12px;
        padding: 8px 14px;
        font-size: 0.75rem;
        font-weight: 700;
        cursor: pointer;
        transition: background 0.2s ease;
    }

    .detail-button:hover {
        background-color: #002542;
    }

    .empty-state {
        padding: 80px 24px;
        text-align: center;
        color: #94a3b8;
    }

    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 100;
        padding: 16px;
    }

    .modal-card {
        width: 100%;
        max-width: 1100px;
        background: white;
        border-radius: 24px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }

    .modal-top {
        display: flex;
        flex-wrap: wrap;
        border-bottom: 1px solid #e2e8f0;
    }

    .modal-left,
    .modal-right {
        padding: 32px;
    }

    .modal-left {
        background-color: #003056;
        color: white;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    .modal-left img {
        width: 128px;
        height: 128px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid white;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        margin-bottom: 24px;
    }

    .modal-left h2 {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 4px;
    }

    .modal-left p {
        color: #bfdbfe;
        font-size: 0.875rem;
        margin-bottom: 24px;
    }

    .modal-fields {
        width: 100%;
        display: grid;
        gap: 12px;
        text-align: left;
    }

    .modal-field {
        display: flex;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        font-size: 0.8rem;
    }

    .modal-right {
        flex: 1;
    }

    .modal-tabs {
        display: flex;
        gap: 24px;
        padding-bottom: 16px;
    }

    .modal-tab {
        cursor: pointer;
        padding-bottom: 12px;
        font-size: 0.875rem;
        font-weight: 700;
        border: none;
        background: none;
        color: #94a3b8;
        transition: color 0.2s ease;
    }

    .modal-tab:hover {
        color: #003056;
    }

    .modal-tab.active {
        color: #003056;
        border-bottom: 2px solid #003056;
    }

    .modal-right-scroll {
        padding: 20px 0 0;
    }

    .document-card {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 14px;
        border-radius: 14px;
        background: #f8fafc;
        cursor: pointer;
    }

    .document-card:hover {
        background: #eef2ff;
    }

    .document-card span {
        font-weight: 700;
    }

    @media (max-width: 1024px) {
        .header-title {
            font-size: 1.5rem;
        }

        .dashboard-toolbar {
            padding: 20px;
        }

        .dashboard-table th,
        .dashboard-table td {
            padding: 14px 16px;
        }

        .modal-card {
            max-width: 100%;
        }

        .modal-top {
            flex-direction: column;
        }

        .modal-left,
        .modal-right {
            padding: 24px;
        }
    }

    @media (max-width: 640px) {
        .wali-dashboard {
            padding: 16px 12px 32px;
        }

        .header-stats {
            flex-direction: column;
        }

        .toolbar-input,
        .toolbar-select,
        .toolbar-button {
            width: 100%;
        }
    }
</style>
@endsection

@section('content')
<div class="wali-dashboard" id="root"></div>
@endsection

@push('scripts')
<script>
    // Data dari controller - gunakan variabel yang dikirim
    const SISWAS = @json($siswas);
    const KELAS_WALI = @json($kelas);
    const TOTAL_SISWA = @json($totalSiswa);
    const BOOKING_DITERIMA = @json($bookingDiterima);
    
    // Hitung berkas kurang dari data siswa
    const BERKAS_KURANG = SISWAS.filter(s => s.berkas === 'Kurang').length;

    // Debug
    console.log('SISWAS:', SISWAS);
    console.log('Jumlah data:', SISWAS.length);

    let searchQuery = '';
    let statusFilter = 'Semua Status';
    let selectedStudent = null;
    let activeTab = 'berkas';
    let filePreviewData = null;
    let currentPage = 1;
    const pageSize = 10;

    function getPaginatedData(filteredData) {
        const totalPages = Math.max(1, Math.ceil(filteredData.length / pageSize));
        if (currentPage > totalPages) {
            currentPage = totalPages;
        }
        const start = (currentPage - 1) * pageSize;
        return filteredData.slice(start, start + pageSize);
    }

    function getPaginationMarkup(totalItems) {
        if (!totalItems) {
            return '';
        }

        const totalPages = Math.max(1, Math.ceil(totalItems / pageSize));
        let markup = `
            <div class="pagination-container">
                <div class="pagination-info">Menampilkan halaman ${currentPage} dari ${totalPages} (${totalItems} data)</div>
                <div class="pagination-links">
        `;

        if (currentPage > 1) {
            markup += `<a href="#" data-page="${currentPage - 1}">← Sebelumnya</a>`;
        } else {
            markup += `<span class="disabled">← Sebelumnya</span>`;
        }

        for (let page = 1; page <= totalPages; page += 1) {
            if (page === currentPage) {
                markup += `<span class="active">${page}</span>`;
            } else {
                markup += `<a href="#" data-page="${page}">${page}</a>`;
            }
        }

        if (currentPage < totalPages) {
            markup += `<a href="#" data-page="${currentPage + 1}">Selanjutnya →</a>`;
        } else {
            markup += `<span class="disabled">Selanjutnya →</span>`;
        }

        markup += `</div></div>`;
        return markup;
    }

    function setPage(page) {
        currentPage = page;
        render();
    }

    function resetPagination() {
        currentPage = 1;
    }

    function getStatusClass(status) {
        if (status === 'Diterima') return 'badge-diterima';
        if (status === 'Ditolak') return 'badge-ditolak';
        return 'badge-direview';
    }

    function getBerkasClass(berkas) {
        return berkas === 'Lengkap' ? 'badge-lengkap' : 'badge-kurang';
    }

    function getFilteredData() {
        if (!SISWAS.length) return [];
        return SISWAS.filter(student => {
            const searchLower = searchQuery.toLowerCase();
            const matchSearch = student.nama.toLowerCase().includes(searchLower) ||
                student.nis.toLowerCase().includes(searchLower) ||
                (student.nomor_absen && student.nomor_absen.toString().includes(searchLower)) ||
                (student.perusahaan && student.perusahaan.toLowerCase().includes(searchLower));
            const matchStatus = statusFilter === 'Semua Status' || student.status_lamaran === statusFilter;
            return matchSearch && matchStatus;
        });
    }

    function formatDate() {
        const today = new Date();
        return {
            formattedDate: today.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }),
            formattedTime: today.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }),
        };
    }

    function showFilePreview(berkasType, studentName, statusFile) {
        filePreviewData = {
            title: `Preview - ${berkasType}`,
            content: `
                <div style="background: #f1f5f9; border-radius: 20px; padding: 30px; margin: 16px 0;">
                    <div style="font-size: 64px; margin-bottom: 16px;">📄</div>
                    <p style="font-weight: 700; color: #003056; margin-bottom: 8px;">${berkasType}</p>
                    <p style="font-size: 13px; color: #334155;">Nama file: ${berkasType.replace(/\s/g, '_')}_${studentName.replace(/\s/g, '')}.pdf</p>
                    <p style="font-size: 12px; color: #64748b; margin-top: 12px;">Status: ${statusFile}</p>
                    <div style="margin-top: 24px; padding: 16px; background: white; border-radius: 16px; border: 1px dashed #003056;">
                        ⚡ Preview dokumen (simulasi):<br>
                        <span style="font-size: 12px;">Berkas ${berkasType} milik ${studentName} akan ditampilkan di sini.</span>
                    </div>
                </div>
            `
        };
        render();
    }

    function showImagePreview(url, studentName) {
        filePreviewData = {
            title: `Foto - ${studentName}`,
            content: `<div style="display:flex; justify-content:center; align-items:center; padding:16px;"><img src="${url}" alt="${studentName}" style="max-width:90vw; max-height:80vh; border-radius:12px; box-shadow:0 10px 30px rgba(0,0,0,0.2);" onerror="this.src='https://placehold.co/600x600/003056/white?text=User'"></div>`
        };
        render();
    }

    function exportCSV() {
        const data = getFilteredData();
        if (!data.length) {
            alert('Tidak ada data untuk diekspor');
            return;
        }
        const headers = ['NIS', 'Nama', 'Kelas', 'Perusahaan', 'Status Lamaran', 'Berkas'];
        const rows = data.map(s => [s.nis, s.nama, s.kelas, s.perusahaan || '-', s.status_lamaran, s.berkas]);
        const csvBody = [headers.join(';'), ...rows.map(r => r.map(c => `"${String(c).replace(/"/g, '""')}"`).join(';'))].join('\r\n');
        const csvContent = '\uFEFF' + csvBody;
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `wali_kelas_export_${new Date().toISOString().slice(0,10)}.csv`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    }

    function closeFilePreview() {
        filePreviewData = null;
        render();
    }

    function render() {
        const filteredData = getFilteredData();
        const paginatedData = getPaginatedData(filteredData);
        const { formattedDate, formattedTime } = formatDate();
        const statusOptions = ['Semua Status', 'Diterima', 'Ditolak', 'Direview'];

        let html = `
            <header class="wali-header">
                <div>
                    <h1 class="header-title">Dashboard Wali Kelas</h1>
                    <p class="header-subtitle">Booking PKL SMKN 6 Malang • Kelas ${KELAS_WALI}</p>
                </div>
                <!-- header stats moved to navbar (compact recap) -->
            </header>

            <main class="dashboard-card">
                <div class="dashboard-toolbar">
                    <div class="dashboard-toolbar-left">
                        <input id="searchInput" class="toolbar-input" type="text" placeholder="Cari Siswa atau NIS..." value="${searchQuery.replace(/"/g, '&quot;')}">
                        <select id="statusSelect" class="toolbar-select custom-select">
                            ${statusOptions.map(s => `<option value="${s}" ${statusFilter === s ? 'selected' : ''}>${s}</option>`).join('')}
                        </select>
                    </div>
                    <div class="dashboard-toolbar-right">
                        <div style="background-color: rgba(255,255,255,0.1); border-radius: 16px; padding: 10px 16px; font-weight: 600; color: white; display: inline-flex; align-items: center; gap: 8px;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 4v16M4 12h16"/></svg>
                            Kelas: ${KELAS_WALI}
                        </div>
                        <button id="printBtn" class="toolbar-button">Export</button>
                    </div>
                </div>

                <div class="dashboard-table-wrapper">
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th style="text-align:center; width:70px;">No Absen</th>
                                <th>Foto</th>
                                <th>Nama Siswa</th>
                                <th>NIS</th>
                                <th>Berkas</th>
                                <th>Perusahaan Mitra</th>
                                <th>Bidang Industri</th>
                                <th style="text-align:center;">Status</th>
                                <th style="text-align:center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
        `;

        if (!filteredData.length) {
            html += `
                <tr>
                    <td colspan="9" style="padding: 80px 24px; text-align: center; color: #94a3b8;">
                        Data tidak ditemukan<br>
                        <small>(Total siswa di kelas: ${TOTAL_SISWA})</small>
                    </td>
                </tr>
            `;
        } else {
            paginatedData.forEach((student, index) => {
                const badgeClass = getStatusClass(student.status_lamaran);
                const berkasClass = getBerkasClass(student.berkas);
                const photo = student.foto || 'https://placehold.co/100x100/003056/white?text=User';

                html += `
                    <tr>
                        <td style="text-align:center; color:#94a3b8; font-size:0.75rem; font-weight:700;">${student.nomor_absen || '-'}</td>
                        <td class="avatar-cell"><img src="${photo}" alt="${student.nama}" onclick="showImagePreview('${photo}', '${student.nama}')" onerror="this.src='https://placehold.co/100x100/003056/white?text=User'"></td>
                        <td style="font-weight:700; color:#003056;">${student.nama}</td>
                        <td style="color:#64748b; font-size:0.75rem; font-weight:700;">${student.nis}</td>
                        <td><span class="badge ${berkasClass}">${student.berkas}</span></td>
                        <td style="font-weight:700; color:#334155; font-size:0.875rem;">${student.perusahaan || '-'}</td>
                        <td style="font-size:0.875rem; color:#94a3b8;">${student.bidang_industri || '-'}</td>
                        <td style="text-align:center;"><span class="badge ${badgeClass}">${student.status_lamaran}</span></td>
                        <td style="text-align:center;"><button class="detail-button" data-id="${student.id}">Detail</button></td>
                    </tr>
                `;
            });
        }

        html += `
                        </tbody>
                    </table>
                </div>
                ${getPaginationMarkup(filteredData.length)}
            </main>
        `;

        // MODAL DETAIL (sama seperti sebelumnya)
        if (selectedStudent) {
            const dudiName = selectedStudent.perusahaan || '-';
            html += `
                <div class="modal-overlay" onclick="closeModal(event)">
                    <div class="modal-card" onclick="event.stopPropagation()">
                        <div class="modal-top">
                            <div class="modal-left">
                                <img src="${selectedStudent.foto || 'https://placehold.co/100x100/003056/white?text=User'}" alt="${selectedStudent.nama}">
                                <h2>${selectedStudent.nama}</h2>
                                <p>${selectedStudent.nis}</p>
                                <div class="modal-fields">
                                    <div class="modal-field"><span>Status Lamaran</span><strong>${selectedStudent.status_lamaran}</strong></div>
                                    <div class="modal-field"><span>Kelengkapan Berkas</span><strong style="color:${selectedStudent.berkas==='Lengkap' ? '#86efac' : '#fca5a5'}">${selectedStudent.berkas}</strong></div>
                                </div>
                                <button class="detail-button" style="width:100%; margin-top:32px; background-color: rgba(255,255,255,0.12); border:1px solid rgba(255,255,255,0.18);" onclick="closeModal()">Tutup Detail</button>
                            </div>
                            <div class="modal-right">
                                <div class="modal-tabs">
                                    <button id="tabBerkasBtn" class="modal-tab ${activeTab === 'berkas' ? 'active' : ''}">Berkas Administrasi</button>
                                    <button id="tabPerusahaanBtn" class="modal-tab ${activeTab === 'perusahaan' ? 'active' : ''}">Data Perusahaan PKL</button>
                                </div>
                                <div class="modal-right-scroll">
            `;
            if (activeTab === 'berkas') {
                html += `
                    <div class="document-card" data-berkas="KTP/KIA" data-status="${selectedStudent.berkas_files.ktp.status}">
                        <span>📄 KTP / KIA</span>
                        <span class="badge ${selectedStudent.berkas_files.ktp.status === 'Selesai' ? 'badge-diterima' : 'badge-direview'}">${selectedStudent.berkas_files.ktp.status}</span>
                    </div>
                    <div class="document-card" data-berkas="Surat Sehat" data-status="${selectedStudent.berkas_files.sehat.status}">
                        <span>🏥 Surat Sehat</span>
                        <span class="badge ${selectedStudent.berkas_files.sehat.status === 'Selesai' ? 'badge-diterima' : 'badge-direview'}">${selectedStudent.berkas_files.sehat.status}</span>
                    </div>
                    <div class="document-card" data-berkas="BPJS Ketenagakerjaan" data-status="${selectedStudent.berkas_files.bpjs.status}">
                        <span>🛡️ BPJS Ketenagakerjaan</span>
                        <span class="badge ${selectedStudent.berkas_files.bpjs.status === 'Selesai' ? 'badge-diterima' : (selectedStudent.berkas_files.bpjs.status === 'Proses' ? 'badge-diterima' : 'badge-direview')}">${selectedStudent.berkas_files.bpjs.status}</span>
                    </div>
                `;
            } else {
                html += `
                    <div style="display:grid; gap:12px;">
                        <div style="padding:12px; background:#f8fafc; border-radius:12px;"><p style="font-size:0.7rem; color:#94a3b8; margin-bottom:2px;">Nama Perusahaan</p><p style="font-weight:700; font-size:0.8rem;">${dudiName}</p></div>
                        <div style="padding:12px; background:#f8fafc; border-radius:12px;"><p style="font-size:0.7rem; color:#94a3b8; margin-bottom:2px;">Bidang Industri</p><p style="font-weight:700; font-size:0.8rem;">${selectedStudent.bidang_industri}</p></div>
                        <div style="padding:12px; background:#f8fafc; border-radius:12px;"><p style="font-size:0.7rem; color:#94a3b8; margin-bottom:2px;">Jumlah Pegawai</p><p style="font-weight:700; font-size:0.8rem;">${selectedStudent.jumlah_pegawai}</p></div>
                        <div style="padding:12px; background:#f8fafc; border-radius:12px;"><p style="font-size:0.7rem; color:#94a3b8; margin-bottom:2px;">Website</p><p style="font-weight:700; font-size:0.8rem;">${selectedStudent.website}</p></div>
                        <div style="padding:12px; background:#f8fafc; border-radius:12px;"><p style="font-size:0.7rem; color:#94a3b8; margin-bottom:2px;">Penanggung Jawab</p><p style="font-weight:700; font-size:0.8rem;">${selectedStudent.penanggung_jawab || '-'}</p></div>
                        <div style="padding:12px; background:#f8fafc; border-radius:12px;"><p style="font-size:0.7rem; color:#94a3b8; margin-bottom:2px;">Kontak</p><p style="font-weight:700; font-size:0.8rem;">${selectedStudent.telepon} | ${selectedStudent.email}</p></div>
                        <div style="padding:12px; background:#f8fafc; border-radius:12px;"><p style="font-size:0.7rem; color:#94a3b8; margin-bottom:2px;">Alamat</p><p style="font-weight:700; font-size:0.8rem;">${selectedStudent.alamat}</p></div>
                        <div style="display:grid; grid-template-columns: repeat(3, minmax(0,1fr)); gap:10px;">
                            <div style="padding:8px; background:#f8fafc; border-radius:12px; text-align:center;"><p style="font-size:9px; color:#94a3b8;">Jam Masuk</p><p style="font-weight:700; font-size:0.75rem;">${selectedStudent.jam_berangkat}</p></div>
                            <div style="padding:8px; background:#f8fafc; border-radius:12px; text-align:center;"><p style="font-size:9px; color:#94a3b8;">Jam Pulang</p><p style="font-weight:700; font-size:0.75rem;">${selectedStudent.jam_pulang}</p></div>
                            <div style="padding:8px; background:#f8fafc; border-radius:12px; text-align:center;"><p style="font-size:9px; color:#94a3b8;">Kuota</p><p style="font-weight:700; font-size:0.75rem;">${selectedStudent.kuota}</p></div>
                        </div>
                    </div>
                `;
            }
            html += `
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        if (filePreviewData) {
            html += `
                <div class="modal-overlay" onclick="closeFilePreview()">
                    <div style="background: white; border-radius: 24px; padding: 24px; max-width: 600px; width: 90%;" onclick="event.stopPropagation()">
                        <h3 style="font-size:1.25rem; font-weight:800; color:#003056; margin-bottom:12px;">${filePreviewData.title}</h3>
                        ${filePreviewData.content}
                        <button onclick="closeFilePreview()" style="margin-top:24px; background:#003056; color:white; border:none; padding:10px 24px; border-radius:40px; font-weight:700; cursor:pointer;">Tutup</button>
                    </div>
                </div>
            `;
        }

        const root = document.getElementById('root');
        if (!root) return;
        root.innerHTML = html;

        // Event listeners
        document.getElementById('searchInput')?.addEventListener('input', (e) => { searchQuery = e.target.value; resetPagination(); render(); });
        document.getElementById('statusSelect')?.addEventListener('change', (e) => { statusFilter = e.target.value; resetPagination(); render(); });
        document.getElementById('printBtn')?.addEventListener('click', () => exportCSV());

        document.querySelectorAll('.detail-button').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.getAttribute('data-id');
                selectedStudent = SISWAS.find(item => String(item.id) === String(id));
                activeTab = 'berkas';
                render();
            });
        });

        document.querySelectorAll('.pagination-links a[data-page]').forEach(btn => {
            btn.addEventListener('click', (event) => {
                event.preventDefault();
                setPage(Number(btn.getAttribute('data-page')) || 1);
            });
        });

        if (selectedStudent) {
            const tabBerkasBtn = document.getElementById('tabBerkasBtn');
            const tabPerusahaanBtn = document.getElementById('tabPerusahaanBtn');
            tabBerkasBtn?.addEventListener('click', () => { activeTab = 'berkas'; render(); });
            tabPerusahaanBtn?.addEventListener('click', () => { activeTab = 'perusahaan'; render(); });
            document.querySelectorAll('.document-card').forEach(el => {
                el.addEventListener('click', () => {
                    const berkasName = el.getAttribute('data-berkas');
                    const statusVal = el.getAttribute('data-status');
                    showFilePreview(berkasName, selectedStudent.nama, statusVal);
                });
            });
        }
    }

    window.showImagePreview = showImagePreview;
    window.closeModal = function(event) {
        if (event && event.target !== event.currentTarget) return;
        selectedStudent = null;
        render();
    };
    window.closeFilePreview = closeFilePreview;

    render();
</script>
@endpush