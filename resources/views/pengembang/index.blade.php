@extends('layouts.app')

@section('title', 'Pengembang')

@section('content')
<div class="card">
    <div class="toolbar-panel">
        <div>
            <h2>Tim Pengembang</h2>
            <p class="form-helper">Halaman informatif untuk menampilkan identitas pengembang proyek dan kontribusi tim.</p>
        </div>
    </div>

    <div class="info-panel">
        <p class="lead">Selamat datang di halaman pengembang. Di sini kami menampilkan tim yang mendesain, mengembangkan, dan menguji sistem PKL ini untuk memastikan pengalaman yang profesional.</p>
    </div>

    <div class="grid-columns grid-columns-3 gap-24">
        <div class="profile-card">
            <div class="profile-card-header">
                <img src="https://via.placeholder.com/320x240?text=Fikri+Rahman" alt="Foto Fikri Rahman" class="profile-image" />
                <div>
                    <h3>Fikri Rahman</h3>
                    <span class="tag">Absen 12 • XI TKJ 1</span>
                </div>
            </div>
            <div class="profile-card-body">
                <p><strong>Alamat:</strong> Jl. Gubeng Kertajaya No. 7, Surabaya</p>
                <p><strong>Instagram:</strong> <a href="https://instagram.com/fikrirahman" target="_blank" rel="noreferrer">@fikrirahman</a></p>
                <p><strong>WA:</strong> <a href="https://wa.me/6281234567890" target="_blank" rel="noreferrer">0812-3456-7890</a></p>
                <p class="profile-motto">"Kerja cerdas dan kolaborasi adalah fondasi solusi digital yang berkelanjutan."</p>
            </div>
        </div>

        <div class="profile-card">
            <div class="profile-card-header">
                <img src="https://via.placeholder.com/320x240?text=Aulia+Putri" alt="Foto Aulia Putri" class="profile-image" />
                <div>
                    <h3>Aulia Putri</h3>
                    <span class="tag">Absen 07 • XI Multimedia</span>
                </div>
            </div>
            <div class="profile-card-body">
                <p><strong>Alamat:</strong> Jl. Ijen No. 15, Malang</p>
                <p><strong>Instagram:</strong> <a href="https://instagram.com/auliaputri" target="_blank" rel="noreferrer">@auliaputri</a></p>
                <p><strong>WA:</strong> <a href="https://wa.me/6282345678901" target="_blank" rel="noreferrer">0823-4567-8901</a></p>
                <p class="profile-motto">"Selesaikan setiap tantangan dengan ketelitian, kreativitas, dan tetap profesional."</p>
            </div>
        </div>

        <div class="profile-card">
            <div class="profile-card-header">
                <img src="https://via.placeholder.com/320x240?text=Bima+Santoso" alt="Foto Bima Santoso" class="profile-image" />
                <div>
                    <h3>Bima Santoso</h3>
                    <span class="tag">Absen 21 • XI RPL</span>
                </div>
            </div>
            <div class="profile-card-body">
                <p><strong>Alamat:</strong> Jl. Dago Atas No. 4, Bandung</p>
                <p><strong>Instagram:</strong> <a href="https://instagram.com/bimasantoso" target="_blank" rel="noreferrer">@bimasantoso</a></p>
                <p><strong>WA:</strong> <a href="https://wa.me/6283456789012" target="_blank" rel="noreferrer">0834-5678-9012</a></p>
                <p class="profile-motto">"Inovasi dimulai dari tekad, disiplin, dan kemauan untuk belajar setiap hari."</p>
            </div>
        </div>
    </div>
</div>
@endsection
