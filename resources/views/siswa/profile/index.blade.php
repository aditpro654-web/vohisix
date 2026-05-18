@extends('layouts.app')

@section('title', 'Data Kelengkapan Berkas PKL')

@php
    $berkasCompleted = 0;
    if ($berkas && $berkas->ktp_kia) $berkasCompleted++;
    if ($berkas && $berkas->surat_sehat) $berkasCompleted++;
    if ($berkas && $berkas->kartu_bpjs) $berkasCompleted++;
    $berkasProgress = ($berkasCompleted / 3) * 100;
@endphp

@section('head')
<link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Outfit', ui-sans-serif, system-ui, sans-serif;
        background-color: #f8fafc;
    }

    .font-display {
        font-family: 'Quicksand', sans-serif;
    }

    @keyframes pulse-custom {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }

    .animate-pulse-custom {
        animation: pulse-custom 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    .container-main {
        max-width: 1200px;
        margin: 0 auto;
        padding: 48px 20px;
    }

    .header-section {
        display: flex;
        justify-content: flex-start;
        align-items: center;
        gap: 48px;
        margin-bottom: 64px;
        flex-wrap: wrap;
    }

    .header-content {
        flex: 0 0 auto;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #b91c1c;
        padding: 8px 16px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 16px;
    }

    .status-badge span {
        width: 8px;
        height: 8px;
        background: #dc2626;
        border-radius: 50%;
        animation: pulse-custom 2s infinite;
    }

    .progress-card {
        background: #003056;
        color: white;
        padding: 24px;
        border-radius: 16px;
        min-width: 220px;
        box-shadow: 0 4px 12px rgba(0, 48, 86, 0.15);
    }

    .progress-label {
        font-size: 0.625rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        opacity: 0.8;
        margin-bottom: 8px;
    }

    .progress-status {
        font-size: 0.875rem;
        font-weight: 600;
        margin-bottom: 16px;
    }

    .progress-bar {
        background: rgba(255, 255, 255, 0.2);
        height: 8px;
        border-radius: 4px;
        overflow: hidden;
        margin-bottom: 12px;
    }

    .progress-fill {
        background: white;
        height: 100%;
        border-radius: 4px;
        transition: width 1s ease-out;
        box-shadow: 0 0 8px rgba(255, 255, 255, 0.3);
    }

    .progress-text {
        font-size: 0.625rem;
        font-weight: 700;
        text-align: right;
        opacity: 0.8;
    }

    .content-wrapper {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 32px;
        margin-bottom: 60px;
    }

    .form-card {
        background: white;
        border: 1px solid #e8edf2;
        border-radius: 16px;
        padding: 32px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
    }

    .upload-section {
        margin-bottom: 40px;
    }

    .upload-section:last-child {
        margin-bottom: 0;
    }

    .upload-label {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
    }

    .upload-label-text {
        font-size: 0.875rem;
        font-weight: 700;
        color: #003056;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .upload-label-icon {
        width: 20px;
        height: 20px;
        opacity: 0.7;
    }

    .upload-status {
        font-size: 0.625rem;
        font-weight: 700;
        color: #003056;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .uploaded-card {
        background-color: #003056;
        border: 1px solid #003056;
        border-radius: 16px;
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 16px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin-bottom: 8px;
    }

    .uploaded-icon {
        background: rgba(255, 255, 255, 0.2);
        padding: 8px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .uploaded-icon svg {
        width: 18px;
        height: 18px;
        color: white;
        stroke: white;
    }

    .uploaded-content {
        flex: 1;
    }

    .uploaded-filename {
        font-size: 0.75rem;
        font-weight: 700;
        color: white;
        margin-bottom: 2px;
    }

    .uploaded-status-text {
        font-size: 0.625rem;
        color: rgba(255, 255, 255, 0.7);
    }

    .ganti-button {
        font-size: 0.625rem;
        font-weight: 700;
        color: rgba(255, 255, 255, 0.8);
        background: none;
        border: none;
        cursor: pointer;
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

    .upload-zone {
        border: 2px dashed #e2e8f0;
        border-radius: 16px;
        padding: 32px;
        text-align: center;
        background: #fafcfe;
        cursor: pointer;
        transition: all 0.2s;
    }

    .upload-zone:hover {
        border-color: #003056;
        background-color: #f5f8fc;
    }

    .upload-zone-icon {
        font-size: 24px;
        color: #94a3b8;
        margin-bottom: 12px;
        transition: color 0.2s;
    }

    .upload-zone:hover .upload-zone-icon {
        color: #003056;
    }

    .upload-zone-title {
        font-size: 0.75rem;
        font-weight: 700;
        color: #003056;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .upload-zone-subtitle {
        font-size: 0.625rem;
        color: #94a3b8;
        margin-top: 4px;
        font-weight: 600;
    }

    .upload-description {
        font-size: 0.625rem;
        color: #475569;
        margin-top: 8px;
        font-weight: 500;
    }

    .sidebar {
        background: white;
        border: 1px solid #e8edf2;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
        height: fit-content;
    }

    .sidebar-title {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.75rem;
        font-weight: 700;
        color: #003056;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 16px;
    }

    .sidebar-list {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .sidebar-list li {
        display: flex;
        gap: 12px;
        margin-bottom: 16px;
        align-items: flex-start;
    }

    .sidebar-list li:last-child {
        margin-bottom: 0;
    }

    .sidebar-list-dot {
        width: 6px;
        height: 6px;
        background: #003056;
        border-radius: 50%;
        margin-top: 6px;
        flex-shrink: 0;
    }

    .sidebar-list-text {
        font-size: 0.75rem;
        color: #475569;
        line-height: 1.6;
        font-weight: 500;
    }

    .sidebar-footer {
        margin-top: 24px;
        padding-top: 24px;
        border-top: 1px solid #e8edf2;
    }

    .save-button {
        width: 100%;
        padding: 16px;
        background-color: #003056;
        color: white;
        border: none;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.2s;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .save-button:hover {
        background-color: #002543;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 48, 86, 0.15);
    }

    .save-button:active {
        transform: translateY(0);
    }

    .save-button.disabled {
        background-color: #f1f5f9;
        color: #94a3b8;
        cursor: not-allowed;
    }

    .sidebar-footer-text {
        font-size: 0.625rem;
        text-align: center;
        color: #94a3b8;
        margin-top: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    @media (max-width: 1024px) {
        .content-wrapper {
            grid-template-columns: 1fr;
        }

        .header-section {
            flex-direction: column;
        }

        .header-content h1 {
            font-size: 1.875rem;
        }
    }

    @media (max-width: 640px) {
        .container-main {
            padding: 24px 16px;
        }

        .form-card {
            padding: 20px;
        }

        .header-content h1 {
            font-size: 1.5rem;
        }
    }
</style>
@endsection

@section('content')
<div class="container-main">
    <!-- Header Section -->
    <div class="header-section">
        <div class="header-content">
            <div class="status-badge">
                <span></span>
                Status: {{ ($berkasCompleted == 3) ? 'Selesai Dikumpulkan' : 'Belum Lengkap' }}
            </div>
        </div>

        <div class="progress-card">
            <div class="progress-label">Progress Berkas</div>
            <div class="progress-status">
                @if($berkasCompleted == 3)
                    Selesai Dikumpulkan
                @else
                    Sisa {{ 3 - $berkasCompleted }} Berkas Lagi
                @endif
            </div>
            <div class="progress-bar">
                <div class="progress-fill" style="width: {{ $berkasProgress }}%"></div>
            </div>
            <div class="progress-text">{{ $berkasCompleted }}/3 Berkas Berhasil</div>
        </div>
    </div>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <!-- Form Section -->
        <div class="form-card">
            <!-- KTP/KIA Upload -->
            <div class="upload-section">
                <div class="upload-label">
                    <span class="upload-label-text">
                        <svg class="upload-label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                            <line x1="1" y1="10" x2="23" y2="10"></line>
                        </svg>
                        Fotocopy KTP / KIA
                    </span>
                    @if($berkas && $berkas->ktp_kia)
                        <span class="upload-status">✓ Sudah Diunggah</span>
                    @endif
                </div>

                @if($berkas && $berkas->ktp_kia)
                    <div class="uploaded-card">
                        <div class="uploaded-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                            </svg>
                        </div>
                        <div class="uploaded-content">
                            <div class="uploaded-filename">KTP_KIA_SCAN.pdf</div>
                            <div class="uploaded-status-text">Berhasil diverifikasi sistem</div>
                        </div>
                        <button type="button" class="ganti-button">Ganti</button>
                    </div>
                @else
                    <div class="upload-zone">
                        <div class="upload-zone-icon">
                            <i class="fas fa-cloud-upload-alt"></i>
                        </div>
                        <div class="upload-zone-title">Pilih atau Seret File Berkas</div>
                        <div class="upload-zone-subtitle">Format PDF / JPG, Maks 2MB</div>
                    </div>
                @endif
                <p class="upload-description">Identitas diri resmi untuk keperluan asuransi dan administrasi industri.</p>
            </div>

            <!-- Surat Sehat Upload -->
            <div class="upload-section">
                <div class="upload-label">
                    <span class="upload-label-text">
                        <svg class="upload-label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                            <polyline points="9 12 11 14 15 10"></polyline>
                        </svg>
                        Surat Keterangan Sehat
                    </span>
                    @if($berkas && $berkas->surat_sehat)
                        <span class="upload-status">✓ Sudah Diunggah</span>
                    @endif
                </div>

                @if($berkas && $berkas->surat_sehat)
                    <div class="uploaded-card">
                        <div class="uploaded-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                            </svg>
                        </div>
                        <div class="uploaded-content">
                            <div class="uploaded-filename">SURAT_SEHAT_SCAN.pdf</div>
                            <div class="uploaded-status-text">Berhasil diverifikasi sistem</div>
                        </div>
                        <button type="button" class="ganti-button">Ganti</button>
                    </div>
                @else
                    <div class="upload-zone">
                        <div class="upload-zone-icon">
                            <i class="fas fa-cloud-upload-alt"></i>
                        </div>
                        <div class="upload-zone-title">Pilih atau Seret File Berkas</div>
                        <div class="upload-zone-subtitle">Format PDF / JPG, Maks 2MB</div>
                    </div>
                @endif
                <p class="upload-description">Dikeluarkan oleh UKS atau Puskesmas setempat.</p>
            </div>

            <!-- Kartu BPJS Upload -->
            <div class="upload-section">
                <div class="upload-label">
                    <span class="upload-label-text">
                        <svg class="upload-label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                            <polyline points="9 12 11 14 15 10"></polyline>
                        </svg>
                        Kartu BPJS Ketenagakerjaan
                    </span>
                    @if($berkas && $berkas->kartu_bpjs)
                        <span class="upload-status">✓ Sudah Diunggah</span>
                    @endif
                </div>

                @if($berkas && $berkas->kartu_bpjs)
                    <div class="uploaded-card">
                        <div class="uploaded-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                            </svg>
                        </div>
                        <div class="uploaded-content">
                            <div class="uploaded-filename">BPJS_SCAN.pdf</div>
                            <div class="uploaded-status-text">Berhasil diverifikasi sistem</div>
                        </div>
                        <button type="button" class="ganti-button">Ganti</button>
                    </div>
                @else
                    <div class="upload-zone">
                        <div class="upload-zone-icon">
                            <i class="fas fa-cloud-upload-alt"></i>
                        </div>
                        <div class="upload-zone-title">Pilih atau Seret File Berkas</div>
                        <div class="upload-zone-subtitle">Format PDF / JPG, Maks 2MB</div>
                    </div>
                @endif
                <p class="upload-description">Syarat wajib perlindungan keselamatan kerja selama PKL.</p>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-title">
                <i class="fas fa-circle-info"></i>
                Ketentuan Unggah
            </div>
            <ul class="sidebar-list">
                <li>
                    <div class="sidebar-list-dot"></div>
                    <span class="sidebar-list-text">Dokumen digital harus merupakan hasil scan (bukan foto) agar terbaca jelas.</span>
                </li>
                <li>
                    <div class="sidebar-list-dot"></div>
                    <span class="sidebar-list-text">Ukuran berkas dibatasi maksimal 2MB untuk efisiensi penyimpanan server.</span>
                </li>
                <li>
                    <div class="sidebar-list-dot"></div>
                    <span class="sidebar-list-text">Verifikasi manual oleh Hubin memerlukan waktu maksimal 2 hari kerja.</span>
                </li>
            </ul>

            <div class="sidebar-footer">
                <button class="save-button {{ $berkasCompleted == 3 ? '' : 'disabled' }}">
                    Simpan Seluruh Progress
                </button>
                <p class="sidebar-footer-text">Terakhir diperbarui: {{ now()->format('d F Y, H:i') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
