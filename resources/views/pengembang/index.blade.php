@extends('layouts.app')

@section('title', 'Pengembang')

@section('content')
<style>
    :root {
        --primary: #003056;
        --primary-light: #00457d;
        --secondary: #f8fafc;
        --gray-100: #f1f5f9;
        --gray-300: #cbd5e1;
        --gray-600: #475569;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif;
        background-color: #f8fafc;
    }

    .card {
        background-color: white;
        border-radius: 24px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.02);
        overflow: hidden;
        border: 1px solid #e2e8f0;
        margin-bottom: 32px;
    }

    .toolbar-panel {
        background-color: var(--primary);
        padding: 24px 32px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .toolbar-panel h2 {
        color: white;
        font-size: 1.5rem;
        font-weight: 700;
        letter-spacing: -0.02em;
        margin-bottom: 8px;
    }

    .form-helper {
        color: #cbd5e1;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .info-panel {
        background: linear-gradient(135deg, #eef2ff 0%, #f8fafc 100%);
        margin: 24px 32px;
        padding: 20px 28px;
        border-radius: 20px;
        border-left: 5px solid var(--primary);
    }

    .lead {
        font-size: 0.95rem;
        color: #1e293b;
        line-height: 1.5;
        font-weight: 500;
    }

    .grid-columns {
        display: grid;
        grid-template-columns: repeat(1, 1fr);
        gap: 24px;
        padding: 0 32px 32px 32px;
    }

    @media (min-width: 768px) {
        .grid-columns {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (min-width: 1024px) {
        .grid-columns {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    .profile-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        transition: all 0.25s ease;
        border: 1px solid #e2e8f0;
        display: flex;
        flex-direction: column;
    }

    .profile-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 30px -12px rgba(0, 48, 86, 0.15);
        border-color: #cbd5e1;
    }

    .profile-card-header {
        background-color: var(--primary);
        padding: 24px 20px 20px 20px;
        text-align: center;
        color: white;
    }

    .profile-image {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid white;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.2);
        margin-bottom: 16px;
    }

    .profile-card-header h3 {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 6px;
    }

    .tag {
        background-color: rgba(255, 255, 255, 0.2);
        padding: 4px 12px;
        border-radius: 30px;
        font-size: 0.7rem;
        font-weight: 600;
        display: inline-block;
        backdrop-filter: blur(2px);
    }

    .profile-card-body {
        padding: 20px;
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 12px;
        background-color: #ffffff;
    }

    .profile-card-body p {
        font-size: 0.8rem;
        color: #334155;
        margin: 0;
        display: flex;
        align-items: baseline;
        flex-wrap: wrap;
        gap: 6px;
    }

    .profile-card-body strong {
        color: #0f172a;
        font-weight: 700;
        min-width: 85px;
        font-size: 0.75rem;
    }

    .profile-card-body a {
        color: var(--primary);
        text-decoration: none;
        font-weight: 600;
        transition: color 0.2s;
        word-break: break-all;
    }

    .profile-card-body a:hover {
        color: var(--primary-light);
        text-decoration: underline;
    }

    .profile-motto {
        margin-top: 8px;
        font-style: italic;
        background-color: #f8fafc;
        padding: 12px;
        border-radius: 16px;
        font-size: 0.75rem;
        color: #475569;
        border-left: 3px solid var(--primary);
    }

    /* Ikon media sosial kecil (opsional) */
    .social-icon {
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
</style>

<div class="card">
    <div class="toolbar-panel">
        <div>
            <h2>👥 Tim Pengembang</h2>
            <p class="form-helper">Halaman informatif untuk menampilkan identitas pengembang proyek dan kontribusi tim.</p>
        </div>
    </div>

    <div class="info-panel">
        <p class="lead">✨ Selamat datang di halaman pengembang. Di sini kami menampilkan tim yang mendesain, mengembangkan, dan menguji sistem PKL ini untuk memastikan pengalaman yang profesional.</p>
    </div>

    <div class="grid-columns">
        <!-- Card 1 - Fikri Rahman -->
        <div class="profile-card">
            <div class="profile-card-header">
                <img src="https://ui-avatars.com/api/?background=0D8F81&color=fff&rounded=true&size=120&bold=true&name=Fikri+Rahman" alt="Foto Fikri Rahman" class="profile-image" />
                <h3>Fikri Rahman</h3>
                <span class="tag">Absen 12 • XI TKJ 1</span>
            </div>
            <div class="profile-card-body">
                <p><strong>📍 Alamat:</strong> Jl. Gubeng Kertajaya No. 7, Surabaya</p>
                <p><strong>📷 Instagram:</strong> <a href="https://instagram.com/fikrirahman" target="_blank" rel="noreferrer">@fikrirahman</a></p>
                <p><strong>📱 WhatsApp:</strong> <a href="https://wa.me/6281234567890" target="_blank" rel="noreferrer">0812-3456-7890</a></p>
                <p class="profile-motto">💡 "Kerja cerdas dan kolaborasi adalah fondasi solusi digital yang berkelanjutan."</p>
            </div>
        </div>

        <!-- Card 2 - Aulia Putri -->
        <div class="profile-card">
            <div class="profile-card-header">
                <img src="https://ui-avatars.com/api/?background=9C27B0&color=fff&rounded=true&size=120&bold=true&name=Aulia+Putri" alt="Foto Aulia Putri" class="profile-image" />
                <h3>Aulia Putri</h3>
                <span class="tag">Absen 07 • XI Multimedia</span>
            </div>
            <div class="profile-card-body">
                <p><strong>📍 Alamat:</strong> Jl. Ijen No. 15, Malang</p>
                <p><strong>📷 Instagram:</strong> <a href="https://instagram.com/auliaputri" target="_blank" rel="noreferrer">@auliaputri</a></p>
                <p><strong>📱 WhatsApp:</strong> <a href="https://wa.me/6282345678901" target="_blank" rel="noreferrer">0823-4567-8901</a></p>
                <p class="profile-motto">🎨 "Selesaikan setiap tantangan dengan ketelitian, kreativitas, dan tetap profesional."</p>
            </div>
        </div>

        <!-- Card 3 - Bima Santoso -->
        <div class="profile-card">
            <div class="profile-card-header">
                <img src="https://ui-avatars.com/api/?background=1976D2&color=fff&rounded=true&size=120&bold=true&name=Bima+Santoso" alt="Foto Bima Santoso" class="profile-image" />
                <h3>Bima Santoso</h3>
                <span class="tag">Absen 21 • XI RPL</span>
            </div>
            <div class="profile-card-body">
                <p><strong>📍 Alamat:</strong> Jl. Dago Atas No. 4, Bandung</p>
                <p><strong>📷 Instagram:</strong> <a href="https://instagram.com/bimasantoso" target="_blank" rel="noreferrer">@bimasantoso</a></p>
                <p><strong>📱 WhatsApp:</strong> <a href="https://wa.me/6283456789012" target="_blank" rel="noreferrer">0834-5678-9012</a></p>
                <p class="profile-motto">🚀 "Inovasi dimulai dari tekad, disiplin, dan kemauan untuk belajar setiap hari."</p>
            </div>
        </div>
    </div>
</div>
@endsection