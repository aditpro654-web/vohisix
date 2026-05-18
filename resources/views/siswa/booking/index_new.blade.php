@extends('layouts.app')

@section('title', 'Status Booking PKL')

@section('head')
<style>
/* ========== ANIMATIONS ========== */
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-fadeInUp {
    animation: fadeInUp 0.3s ease-out forwards;
}

/* ========== PAGE HEADER ========== */
.page-header {
    margin-bottom: 48px;
    text-align: center;
}

@media (min-width: 768px) {
    .page-header {
        text-align: left;
    }
}

.page-title {
    font-size: 2.5rem;
    font-weight: 900;
    letter-spacing: -0.025em;
    color: #003056;
    line-height: 1.1;
    margin-bottom: 16px;
}

@media (min-width: 768px) {
    .page-title {
        font-size: 3.75rem;
    }
}

.page-title-gradient {
    background: linear-gradient(135deg, #003056, #3b82f6);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
}

.page-subtitle {
    color: #64748b;
    max-width: 640px;
    margin: 0 auto;
    font-size: 1rem;
    font-weight: 500;
}

@media (min-width: 768px) {
    .page-subtitle {
        margin: 0;
    }
}

/* ========== STATS GRID ========== */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    margin-bottom: 48px;
}

.stat-card {
    padding: 20px;
    border-radius: 16px;
}

.stat-card-primary {
    background-color: #003056;
    color: white;
}

.stat-card-emerald {
    background-color: #10b981;
    color: white;
}

.stat-card-white {
    background-color: white;
    border: 1px solid #e2e8f0;
    color: #003056;
}

.stat-icon {
    width: 20px;
    height: 20px;
}

.stat-label {
    font-size: 10px;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-top: 12px;
    opacity: 0.8;
}

.stat-number {
    font-size: 2.25rem;
    font-weight: 900;
    margin-top: 4px;
}

.stat-card-white .stat-label {
    color: #94a3b8;
}

/* ========== TABS ========== */
.tabs-wrapper {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 16px;
}

.section-title {
    font-size: 1.25rem;
    font-weight: 900;
    color: #003056;
}

.tab-group {
    display: flex;
    gap: 4px;
    background-color: #f1f5f9;
    padding: 4px;
    border-radius: 12px;
}

.tab-btn {
    padding: 6px 16px;
    font-size: 10px;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
    background: transparent;
    color: #64748b;
}

.tab-btn.active {
    background-color: #003056;
    color: white;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}

/* ========== CARD LIST ========== */
.card-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.booking-card {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 20px;
    transition: box-shadow 0.2s;
    animation: fadeInUp 0.3s ease-out forwards;
    opacity: 0;
}

.booking-card:hover {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.card-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
}

.card-left {
    display: flex;
    align-items: center;
    gap: 16px;
}

.company-icon {
    width: 48px;
    height: 48px;
    background-color: #f8fafc;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #003056;
    font-size: 1.5rem;
    flex-shrink: 0;
}

.company-info {
    flex: 1;
    min-width: 0;
}

.company-name {
    font-weight: 700;
    color: #003056;
    font-size: 0.875rem;
    margin: 0;
}

.company-bidang {
    font-size: 10px;
    color: #94a3b8;
    margin-top: 2px;
    margin: 0;
}

.card-right {
    display: flex;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
}

.status-badge {
    padding: 4px 12px;
    border-radius: 9999px;
    font-size: 9px;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.status-accepted {
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
}

.status-rejected {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
}

.status-review {
    background: rgba(245, 158, 11, 0.1);
    color: #f59e0b;
}

.date-badge {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 10px;
    color: #94a3b8;
}

.action-btn {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    background-color: #f8fafc;
    display: flex;
    align-items: center;
    justify-content: center;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
    color: #003056;
    font-size: 1rem;
}

.action-btn:hover {
    background-color: #003056;
    color: white;
}

/* ========== EMPTY STATE ========== */
.empty-state {
    text-align: center;
    padding: 64px 32px;
    background: white;
    border: 2px dashed #e2e8f0;
    border-radius: 24px;
}

.empty-state-icon {
    font-size: 3rem;
    margin-bottom: 16px;
    color: #e2e8f0;
}

.empty-state h3 {
    color: #003056;
    font-size: 1.25rem;
    font-weight: 700;
    margin: 0 0 8px 0;
}

.empty-state p {
    color: #94a3b8;
    font-weight: 500;
    font-size: 0.875rem;
    margin: 0 0 24px 0;
}

.empty-state .btn {
    display: inline-block;
    background-color: #003056;
    color: white;
    padding: 12px 24px;
    border-radius: 12px;
    text-decoration: none;
    border: none;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.2s;
}

.empty-state .btn:hover {
    background-color: #002543;
    transform: translateY(-2px);
}

/* ========== RESPONSIVE ========== */
@media (max-width: 640px) {
    .stats-grid {
        grid-template-columns: 1fr;
        gap: 12px;
    }

    .stat-card {
        padding: 12px;
    }

    .stat-number {
        font-size: 1.5rem;
    }

    .page-title {
        font-size: 1.75rem;
    }

    .card-content {
        flex-direction: column;
        align-items: flex-start;
    }

    .card-right {
        width: 100%;
        justify-content: space-between;
    }

    .tabs-wrapper {
        flex-direction: column;
        align-items: flex-start;
    }

    .tab-group {
        width: 100%;
        justify-content: flex-start;
        overflow-x: auto;
    }
}
</style>
@endsection

@section('content')
<div class="container" style="max-width: 1024px; margin: 0 auto; padding: 48px 20px;">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            Status Booking <span class="page-title-gradient">PKL</span>
        </h1>
        <p class="page-subtitle">
            Pantau status pengajuan PKL Anda dengan kartu informasi yang mudah dibaca.
        </p>
    </div>

    @if($bookings->count() > 0)
        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card stat-card-primary">
                <svg class="stat-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="12" y1="13" x2="12" y2="17"/>
                    <line x1="10" y1="15" x2="14" y2="15"/>
                </svg>
                <p class="stat-label">Total Pengajuan</p>
                <p class="stat-number">{{ $bookings->total() }}</p>
            </div>

            <div class="stat-card stat-card-emerald">
                <svg class="stat-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="9 12 11 14 15 10"/>
                </svg>
                <p class="stat-label">Diterima</p>
                <p class="stat-number">{{ $bookings->where('status', 'Diterima')->count() }}</p>
            </div>

            <div class="stat-card stat-card-white">
                <svg class="stat-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="15" y1="9" x2="9" y2="15"/>
                    <line x1="9" y1="9" x2="15" y2="15"/>
                </svg>
                <p class="stat-label">Ditolak</p>
                <p class="stat-number">{{ $bookings->where('status', 'Ditolak')->count() }}</p>
            </div>
        </div>

        <!-- Tabs -->
        <div class="tabs-wrapper">
            <h2 class="section-title">Riwayat Pengajuan</h2>
            <div class="tab-group" id="tabGroup">
                <button class="tab-btn active" data-status="All">Semua</button>
                <button class="tab-btn" data-status="Diterima">Diterima</button>
                <button class="tab-btn" data-status="Ditolak">Ditolak</button>
                <button class="tab-btn" data-status="Direview">Direview</button>
            </div>
        </div>

        <!-- Card List -->
        <div class="card-list" id="cardList">
            @forelse($bookings as $booking)
                <div class="booking-card animate-fadeInUp" data-status="{{ $booking->status }}" style="animation-delay: {{ $loop->index * 0.05 }}s;">
                    <div class="card-content">
                        <div class="card-left">
                            <div class="company-icon">
                                @if($booking->dudi->logo)
                                    <img src="{{ asset('storage/' . $booking->dudi->logo) }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 12px;" alt="{{ $booking->dudi->nama_dudi }}">
                                @else
                                    <i class="fas fa-building"></i>
                                @endif
                            </div>
                            <div class="company-info">
                                <p class="company-name">{{ $booking->dudi->nama_dudi }}</p>
                                <p class="company-bidang">{{ $booking->dudi->bidang_usaha ?? 'Industri' }}</p>
                            </div>
                        </div>

                        <div class="card-right">
                            @if($booking->status === 'Diterima')
                                <span class="status-badge status-accepted">Diterima</span>
                            @elseif($booking->status === 'Ditolak')
                                <span class="status-badge status-rejected">Ditolak</span>
                            @else
                                <span class="status-badge status-review">Direview</span>
                            @endif

                            <div class="date-badge">
                                <i class="fas fa-calendar"></i>
                                <span>{{ $booking->created_at->format('d M Y') }}</span>
                            </div>

                            <button class="action-btn" title="Lihat detail">
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <h3>Tidak Ada Pengajuan</h3>
                    <p>Belum ada riwayat pengajuan PKL. Silakan ajukan PKL ke perusahaan pilihan Anda.</p>
                    <a href="{{ route('siswa.dudi.index') }}" class="btn">Cari Perusahaan</a>
                </div>
            @endforelse
        </div>

        @if($bookings->hasPages())
            <div style="margin-top: 48px; text-align: center;">
                <div style="display: flex; gap: 8px; justify-content: center; flex-wrap: wrap;">
                    @if($bookings->onFirstPage())
                        <span style="padding: 8px 12px; color: #94a3b8;">← Sebelumnya</span>
                    @else
                        <a href="{{ $bookings->previousPageUrl() }}" style="padding: 8px 12px; background: #003056; color: white; border-radius: 8px; text-decoration: none;">← Sebelumnya</a>
                    @endif

                    @foreach($bookings->getUrlRange(1, $bookings->lastPage()) as $page => $url)
                        @if($page == $bookings->currentPage())
                            <span style="padding: 8px 12px; background: #003056; color: white; border-radius: 8px; font-weight: 600;">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" style="padding: 8px 12px; background: #f1f5f9; color: #003056; border-radius: 8px; text-decoration: none;">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if($bookings->hasMorePages())
                        <a href="{{ $bookings->nextPageUrl() }}" style="padding: 8px 12px; background: #003056; color: white; border-radius: 8px; text-decoration: none;">Selanjutnya →</a>
                    @else
                        <span style="padding: 8px 12px; color: #94a3b8;">Selanjutnya →</span>
                    @endif
                </div>
            </div>
        @endif
    @else
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="fas fa-inbox"></i>
            </div>
            <h3>Belum Ada Pengajuan</h3>
            <p>Mulai pengajuan PKL Anda sekarang dan temukan perusahaan yang sempurna untuk belajar.</p>
            <a href="{{ route('siswa.dudi.index') }}" class="btn">Cari Perusahaan Sekarang</a>
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabBtns = document.querySelectorAll('.tab-btn');
    const bookingCards = document.querySelectorAll('.booking-card');

    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const status = this.dataset.status;

            // Update active tab
            tabBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            // Filter cards
            bookingCards.forEach((card, index) => {
                const cardStatus = card.dataset.status;
                if (status === 'All' || cardStatus === status) {
                    card.style.display = 'block';
                    setTimeout(() => card.classList.add('animate-fadeInUp'), 10);
                } else {
                    card.style.display = 'none';
                    card.classList.remove('animate-fadeInUp');
                }
            });
        });
    });
});
</script>
@endsection
