@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<style>
    /* ========== GLOBAL STYLE ========== */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
        background: #f4f7fc;
        color: #1a2c3e;
    }

    :root {
        --navy: #0a2b44;
        --navy-light: #1e4a76;
        --navy-soft: #eef3fa;
        --shadow-card: 0 12px 30px rgba(0, 0, 0, 0.05);
        --border-radius-card: 1.75rem;
    }

    .bg-navy { background-color: #0a2b44; }
    .text-navy { color: #0a2b44; }

    /* Layout */
    .dashboard-container {
        max-width: 1440px;
        margin: 0 auto;
        padding: 2rem 2rem;
    }
    .grid {
        display: grid;
        gap: 1.75rem;
    }
    .grid-cols-1 { grid-template-columns: repeat(1, 1fr); }
    .grid-cols-2 { grid-template-columns: repeat(2, 1fr); }
    .grid-cols-4 { grid-template-columns: repeat(4, 1fr); }
    .grid-cols-12 { grid-template-columns: repeat(12, 1fr); }
    .col-span-8 { grid-column: span 8; }
    .col-span-4 { grid-column: span 4; }
    .col-span-6 { grid-column: span 6; }
    .flex { display: flex; }
    .flex-col { flex-direction: column; }
    .items-center { align-items: center; }
    .justify-between { justify-content: space-between; }
    .gap-2 { gap: 0.5rem; }
    .gap-3 { gap: 0.75rem; }
    .gap-4 { gap: 1rem; }

    /* Cards */
    .card-stat {
        background: white;
        padding: 1.5rem;
        border-radius: 1.5rem;
        border: 1px solid #eef2f6;
        transition: all 0.2s;
    }
    .card-stat:hover { transform: translateY(-2px); box-shadow: 0 12px 20px -10px rgba(0,0,0,0.05); }
    .chart-card {
        background: white;
        border-radius: 1.5rem;
        padding: 1.5rem;
        border: 1px solid #edf2f7;
        transition: all 0.2s;
    }
    .chart-card:hover { box-shadow: 0 12px 24px -12px rgba(0,0,0,0.08); }

    /* Hero */
    .hero {
        background: linear-gradient(135deg, #0a2b44 0%, #164a6b 100%);
        border-radius: 2rem;
        padding: 2rem;
        margin-bottom: 2rem;
        color: white;
        position: relative;
        overflow: hidden;
    }
    .hero h2 { font-size: 1.8rem; font-weight: 800; margin-bottom: 0.5rem; }
    .hero p { opacity: 0.85; max-width: 500px; }
    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(255,255,255,0.12);
        padding: 0.25rem 1rem;
        border-radius: 40px;
        font-size: 0.7rem;
        font-weight: 700;
        margin-bottom: 1rem;
    }

    /* Progress Bar */
    .progress-bar {
        height: 6px;
        background: #e2e8f0;
        border-radius: 10px;
        overflow: hidden;
    }
    .progress-fill { height: 100%; border-radius: 10px; background: #0a2b44; }

    /* Tooltip */
    .tooltip-custom {
        position: absolute;
        background: rgba(10, 43, 68, 0.92);
        backdrop-filter: blur(8px);
        color: white;
        padding: 0.5rem 0.9rem;
        border-radius: 40px;
        font-size: 0.75rem;
        font-weight: 600;
        pointer-events: none;
        z-index: 100;
        white-space: nowrap;
        border: 1px solid rgba(255,255,255,0.2);
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }

    /* Quick Button */
    .quick-btn {
        background: #f8fafc;
        border-radius: 1.2rem;
        padding: 1rem;
        text-align: center;
        transition: all 0.2s;
        cursor: pointer;
    }
    .quick-btn:hover { background: #0a2b44; color: white; }
    .quick-btn:hover svg { stroke: white; }

    /* Typography */
    .text-xs { font-size: 0.7rem; }
    .text-sm { font-size: 0.8rem; }
    .text-base { font-size: 0.9rem; }
    .text-lg { font-size: 1rem; }
    .text-xl { font-size: 1.2rem; }
    .text-2xl { font-size: 1.8rem; }
    .font-bold { font-weight: 700; }
    .font-black { font-weight: 800; }
    .uppercase { text-transform: uppercase; }
    .tracking-wide { letter-spacing: 0.02em; }
    .tracking-wider { letter-spacing: 0.05em; }

    @media (max-width: 1024px) {
        .grid-cols-4 { grid-template-columns: repeat(2, 1fr); }
        .grid-cols-12 { grid-template-columns: 1fr; }
        .col-span-8, .col-span-4, .col-span-6 { grid-column: span 1; }
        .dashboard-container { padding: 1rem; }
        .hero { padding: 1.5rem; }
    }
    @media (max-width: 640px) {
        .grid-cols-4 { grid-template-columns: 1fr; }
    }
    ::-webkit-scrollbar { width: 5px; }
    ::-webkit-scrollbar-track { background: #eef2f6; border-radius: 10px; }
    ::-webkit-scrollbar-thumb { background: #b9c7d4; border-radius: 10px; }
</style>

<div class="dashboard-container">
    <!-- Hero Section -->
    <div class="hero">
        <div class="hero-badge">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg>
            <span>SIJA Department Hub</span>
        </div>
        <h2>Selamat Datang, Administrator!</h2>
        <p>Sistem berjalan optimal. Ada <strong>{{ $bookingDireview }} pengajuan baru</strong> yang perlu diverifikasi.</p>
        <div class="flex gap-3 mt-5">
            <button class="bg-white text-navy px-5 py-2 rounded-2xl text-xs font-bold shadow-md hover:-translate-y-0.5 transition">Verifikasi Cepat</button>
            <button class="bg-white/10 hover:bg-white/20 px-5 py-2 rounded-2xl text-xs font-bold transition flex items-center gap-1">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 5 17 10"/><line x1="12" y1="5" x2="12" y2="15"/></svg>
                Download Laporan
            </button>
        </div>
        <svg width="140" height="140" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.03)" stroke-width="1" class="absolute -bottom-8 -right-8"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-4 gap-5 mb-8">
        <div class="card-stat">
            <div class="flex justify-between">
                <div class="p-2 bg-slate-50 rounded-xl text-navy">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">METRIC</span>
            </div>
            <p class="text-3xl font-black text-navy mt-2">{{ $totalSiswa }}</p>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Total Siswa</p>
        </div>
        <div class="card-stat">
            <div class="flex justify-between">
                <div class="p-2 bg-slate-50 rounded-xl text-navy">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                </div>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">METRIC</span>
            </div>
            <p class="text-3xl font-black text-navy mt-2">{{ $totalDudi }}</p>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Total DUDI</p>
        </div>
        <div class="card-stat">
            <div class="flex justify-between">
                <div class="p-2 bg-amber-50 rounded-xl text-amber-600">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">METRIC</span>
            </div>
            <p class="text-3xl font-black text-amber-600 mt-2">{{ $bookingDireview }}</p>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Pending Review</p>
        </div>
        <div class="card-stat">
            <div class="flex justify-between">
                <div class="p-2 bg-emerald-50 rounded-xl text-emerald-600">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">METRIC</span>
            </div>
            <p class="text-3xl font-black text-emerald-600 mt-2">{{ $bookingDiterima }}</p>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Penempatan Sukses</p>
        </div>
    </div>

    <!-- Row 1: Grafik Pendaftaran Siswa + Pie Kuota DUDI -->
    <div class="grid grid-cols-12 gap-6 mb-8">
        <div class="col-span-8">
            <div class="chart-card relative" id="areaChartWrapper">
                <div class="flex justify-between items-center mb-5">
                    <div><h3 class="text-lg font-black text-navy">📈 Grafik Pendaftaran Siswa</h3><p class="text-xs text-slate-400">Jumlah siswa mendaftar per bulan</p></div>
                    <div class="flex items-center gap-1 bg-slate-50 px-3 py-1 rounded-full">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                        <span class="text-[10px] font-bold text-navy">+18% vs bulan lalu</span>
                    </div>
                </div>
                <div style="height: 270px; width: 100%; position: relative;">
                    <canvas id="areaChartCanvas" width="800" height="270" style="width:100%; height:270px; display: block;"></canvas>
                    <div id="areaTooltip" class="tooltip-custom" style="display: none;"></div>
                </div>
            </div>
        </div>
        <div class="col-span-4">
            <div class="chart-card relative" id="kuotaWrapper">
                <h3 class="text-lg font-black text-navy mb-3">📊 Total Kuota DUDI</h3>
                <div style="height: 210px; width: 100%; position: relative;">
                    <canvas id="kuotaPieCanvas" width="350" height="210" style="width:100%; height:210px; display: block;"></canvas>
                    <div id="kuotaTooltip" class="tooltip-custom" style="display: none;"></div>
                </div>
                <div id="kuotaLegend" class="mt-4 space-y-2"></div>
            </div>
        </div>
    </div>

    <!-- Row 2: Aktivitas Terbaru & Progress Kelas -->
    <div class="grid grid-cols-12 gap-6 mb-8">
        <div class="col-span-6">
            <div class="chart-card">
                <div class="flex justify-between items-center mb-4"><h3 class="text-lg font-black text-navy">⏳ Aktivitas Terbaru</h3><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><path d="M22 12h-4l-3 9-4-18-3 9H2"/></svg></div>
                <div id="activityList" class="space-y-4"></div>
                <button class="w-full mt-5 py-2 border border-dashed border-slate-200 rounded-xl text-[10px] font-bold text-slate-400 hover:border-navy/40 hover:text-navy transition">Lihat Semua History</button>
            </div>
        </div>
        <div class="col-span-6">
            <div class="chart-card">
                <h3 class="text-lg font-black text-navy mb-4">📚 Progress Penempatan Kelas</h3>
                <div id="classProgressList" class="space-y-5"></div>
            </div>
        </div>
    </div>

    <!-- Row 3: Pie Status Booking + Pintasan Cepat -->
    <div class="grid grid-cols-12 gap-6">
        <div class="col-span-6">
            <div class="chart-card relative" id="statusWrapper">
                <h3 class="text-lg font-black text-navy mb-3">✅ Status Booking PKL</h3>
                <div style="height: 210px; width: 100%; position: relative;">
                    <canvas id="statusPieCanvas" width="350" height="210" style="width:100%; height:210px; display: block;"></canvas>
                    <div id="statusTooltip" class="tooltip-custom" style="display: none;"></div>
                </div>
                <div id="statusLegend" class="mt-4 flex justify-around text-center"></div>
            </div>
        </div>
        <div class="col-span-6">
            <div class="chart-card">
                <h3 class="text-lg font-black text-navy mb-4">⚡ Pintasan Cepat</h3>
                <div class="grid grid-cols-2 gap-4">
                    <a href="{{ route('admin.dudi.create') }}" class="quick-btn flex flex-col items-center gap-2">
                        <div class="bg-white p-2 rounded-xl shadow-sm"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></div>
                        <span class="text-[9px] font-black uppercase tracking-wider">Tambah DUDI</span>
                    </a>
                    <a href="{{ route('admin.booking.index') }}" class="quick-btn flex flex-col items-center gap-2">
                        <div class="bg-white p-2 rounded-xl shadow-sm"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg></div>
                        <span class="text-[9px] font-black uppercase tracking-wider">Rekap Nilai</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    (function() {
        // Data statis untuk grafik (bisa diganti dengan data dari server jika diperlukan)
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'];
        const pendaftaran = [4, 7, 5, 9, 6, 8];

        // Data kuota DUDI (contoh, bisa disesuaikan dengan data sebenarnya dari $dudi)
        const kuotaData = [
            { name: "PT Teknologi Bangsa", kuota: 5, color: "#1e3a5f" },
            { name: "Digital Creative Hub", kuota: 3, color: "#2c5282" },
            { name: "Indo Cloud Solutions", kuota: 8, color: "#0f2c44" }
        ];

        // Data status booking diambil dari server via blade
        const statusData = [
            { label: "Diterima", value: {{ $bookingDiterima }}, color: "#10b981" },
            { label: "Ditolak", value: {{ $bookingDitolak }}, color: "#ef4444" },
            { label: "Direview", value: {{ $bookingDireview }}, color: "#f59e0b" }
        ];

        // Aktivitas (contoh, bisa diambil dari database nantinya)
        const activities = [
            { user: "Aril Pratama", action: "Mengajukan booking ke", target: "PT. Teknologi Bangsa", time: "2 Jam yang lalu", icon: "clock" },
            { user: "Melvin", action: "Diterima di", target: "Digital Creative Hub", time: "4 Jam yang lalu", icon: "check" },
            { user: "Lintang", action: "Berkas diverifikasi oleh", target: "Bp. Hartono", time: "6 Jam yang lalu", icon: "file" }
        ];

        // Progress kelas (contoh, bisa dari data kelas)
        const classProgress = [
            { name: "XIII SIJA 1", progress: 85, color: "#0a2b44" },
            { name: "XIII SIJA 2", progress: 62, color: "#2c5282" }
        ];

        // Render aktivitas
        function renderActivities() {
            const container = document.getElementById('activityList');
            if (!container) return;
            container.innerHTML = activities.map(act => {
                let iconHtml = '';
                if (act.icon === 'clock') iconHtml = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>';
                else if (act.icon === 'check') iconHtml = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>';
                else iconHtml = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>';
                return `
                    <div class="flex gap-3 items-start group">
                        <div class="w-7 h-7 bg-slate-50 rounded-xl flex items-center justify-center text-slate-500 group-hover:bg-navy group-hover:text-white transition-colors">${iconHtml}</div>
                        <div><p class="text-xs font-bold text-navy">${act.user} <span class="text-slate-400 font-bold">${act.action}</span> ${act.target}</p><p class="text-[9px] text-slate-300 font-bold mt-0.5">${act.time}</p></div>
                    </div>
                `;
            }).join('');
        }

        function renderClassProgress() {
            const container = document.getElementById('classProgressList');
            if (!container) return;
            container.innerHTML = classProgress.map(c => `
                <div>
                    <div class="flex justify-between text-[11px] font-bold text-navy mb-1"><span>${c.name}</span><span>${c.progress}%</span></div>
                    <div class="progress-bar"><div class="progress-fill" style="width: ${c.progress}%; background: ${c.color};"></div></div>
                </div>
            `).join('');
        }

        // Area Chart
        let areaPoints = [];
        function drawAreaChart() {
            const canvas = document.getElementById('areaChartCanvas');
            if (!canvas) return;
            const w = canvas.clientWidth, h = canvas.clientHeight;
            canvas.width = w; canvas.height = h;
            if (w === 0) return;
            const ctx = canvas.getContext('2d');
            const stepX = w / (months.length - 1);
            const points = months.map((m, i) => ({
                x: i * stepX,
                y: h - (pendaftaran[i] / 12) * h,
                value: pendaftaran[i],
                month: m
            }));
            areaPoints = points;
            ctx.clearRect(0, 0, w, h);
            // area fill
            ctx.beginPath();
            ctx.moveTo(points[0].x, points[0].y);
            for (let i = 1; i < points.length; i++) ctx.lineTo(points[i].x, points[i].y);
            ctx.lineTo(w, h);
            ctx.lineTo(0, h);
            ctx.fillStyle = 'rgba(10,43,68,0.08)';
            ctx.fill();
            // line
            ctx.beginPath();
            ctx.moveTo(points[0].x, points[0].y);
            for (let i = 1; i < points.length; i++) ctx.lineTo(points[i].x, points[i].y);
            ctx.strokeStyle = '#0a2b44';
            ctx.lineWidth = 2.5;
            ctx.stroke();
            // points
            points.forEach(p => {
                ctx.beginPath();
                ctx.arc(p.x, p.y, 4, 0, 2 * Math.PI);
                ctx.fillStyle = '#0a2b44';
                ctx.fill();
            });
        }
        function attachAreaTooltip() {
            const canvas = document.getElementById('areaChartCanvas');
            const tooltip = document.getElementById('areaTooltip');
            if (!canvas) return;
            canvas.addEventListener('mousemove', (e) => {
                const rect = canvas.getBoundingClientRect();
                const mouseX = e.clientX - rect.left;
                const scaleX = canvas.width / rect.width;
                const canvasX = mouseX * scaleX;
                let closest = null, minDist = Infinity;
                areaPoints.forEach(p => {
                    const dist = Math.abs(p.x - canvasX);
                    if (dist < minDist) { minDist = dist; closest = p; }
                });
                if (closest && minDist < 30) {
                    tooltip.style.display = 'block';
                    tooltip.style.left = (rect.left + closest.x / scaleX) + 'px';
                    tooltip.style.top = (rect.top + closest.y / scaleX - 35) + 'px';
                    tooltip.innerHTML = `<strong>${closest.month}</strong><br>${closest.value} siswa mendaftar`;
                } else tooltip.style.display = 'none';
            });
            canvas.addEventListener('mouseleave', () => tooltip.style.display = 'none');
        }

        // Kuota Pie Chart
        let kuotaSegments = [];
        function drawKuotaPie() {
            const canvas = document.getElementById('kuotaPieCanvas');
            if (!canvas) return;
            const w = canvas.clientWidth, h = canvas.clientHeight;
            canvas.width = w; canvas.height = h;
            const ctx = canvas.getContext('2d');
            const total = kuotaData.reduce((s, i) => s + i.kuota, 0);
            let start = -Math.PI / 2;
            const segments = [];
            kuotaData.forEach(item => {
                const angle = (item.kuota / total) * 2 * Math.PI;
                const end = start + angle;
                ctx.beginPath();
                ctx.moveTo(w / 2, h / 2);
                ctx.arc(w / 2, h / 2, Math.min(w, h) / 2.5, start, end);
                ctx.fillStyle = item.color;
                ctx.fill();
                segments.push({ start, end, label: item.name, value: item.kuota });
                start = end;
            });
            kuotaSegments = segments;
            const legendDiv = document.getElementById('kuotaLegend');
            legendDiv.innerHTML = kuotaData.map(i => `
                <div class="flex justify-between items-center text-xs px-2 py-1.5 bg-slate-50 rounded-xl">
                    <div class="flex items-center gap-2"><div class="w-3 h-3 rounded-full" style="background:${i.color}"></div><span class="font-bold text-navy">${i.name}</span></div>
                    <span class="font-black text-navy">${i.kuota} kuota</span>
                </div>
            `).join('');
        }
        function attachKuotaTooltip() {
            const canvas = document.getElementById('kuotaPieCanvas');
            const tooltip = document.getElementById('kuotaTooltip');
            if (!canvas) return;
            function getSegment(mouseX, mouseY, w, h, segments) {
                const cx = w / 2, cy = h / 2;
                const dx = mouseX - cx, dy = mouseY - cy;
                const dist = Math.hypot(dx, dy);
                const radius = Math.min(w, h) / 2.5;
                if (dist > radius) return null;
                let angle = Math.atan2(dy, dx);
                if (angle < -Math.PI / 2) angle += 2 * Math.PI;
                for (let seg of segments) {
                    if (angle >= seg.start && angle <= seg.end) return seg;
                }
                return null;
            }
            canvas.addEventListener('mousemove', (e) => {
                const rect = canvas.getBoundingClientRect();
                const w = canvas.width, h = canvas.height;
                const mouseX = (e.clientX - rect.left) * (w / rect.width);
                const mouseY = (e.clientY - rect.top) * (h / rect.height);
                const seg = getSegment(mouseX, mouseY, w, h, kuotaSegments);
                if (seg) {
                    tooltip.style.display = 'block';
                    tooltip.style.left = e.clientX + 15 + 'px';
                    tooltip.style.top = e.clientY - 30 + 'px';
                    tooltip.innerHTML = `${seg.label}<br>Kuota: ${seg.value} slot`;
                } else tooltip.style.display = 'none';
            });
            canvas.addEventListener('mouseleave', () => tooltip.style.display = 'none');
        }

        // Status Pie Chart
        let statusSegments = [];
        function drawStatusPie() {
            const canvas = document.getElementById('statusPieCanvas');
            if (!canvas) return;
            const w = canvas.clientWidth, h = canvas.clientHeight;
            canvas.width = w; canvas.height = h;
            const ctx = canvas.getContext('2d');
            const total = statusData.reduce((s, i) => s + i.value, 0);
            if (total === 0) return;
            let start = -Math.PI / 2;
            const segments = [];
            statusData.forEach(item => {
                const angle = (item.value / total) * 2 * Math.PI;
                const end = start + angle;
                ctx.beginPath();
                ctx.moveTo(w / 2, h / 2);
                ctx.arc(w / 2, h / 2, Math.min(w, h) / 2.5, start, end);
                ctx.fillStyle = item.color;
                ctx.fill();
                segments.push({ start, end, label: item.label, value: item.value });
                start = end;
            });
            statusSegments = segments;
            const legendDiv = document.getElementById('statusLegend');
            legendDiv.innerHTML = statusData.map(i => `
                <div class="flex flex-col items-center gap-1"><div class="w-3 h-3 rounded-full" style="background:${i.color}"></div><span class="text-[9px] font-bold text-navy">${i.label}</span><span class="text-xs font-black">${i.value}</span></div>
            `).join('');
        }
        function attachStatusTooltip() {
            const canvas = document.getElementById('statusPieCanvas');
            const tooltip = document.getElementById('statusTooltip');
            if (!canvas) return;
            function getSegment(mouseX, mouseY, w, h, segments) {
                const cx = w / 2, cy = h / 2;
                const dx = mouseX - cx, dy = mouseY - cy;
                const dist = Math.hypot(dx, dy);
                const radius = Math.min(w, h) / 2.5;
                if (dist > radius) return null;
                let angle = Math.atan2(dy, dx);
                if (angle < -Math.PI / 2) angle += 2 * Math.PI;
                for (let seg of segments) {
                    if (angle >= seg.start && angle <= seg.end) return seg;
                }
                return null;
            }
            canvas.addEventListener('mousemove', (e) => {
                const rect = canvas.getBoundingClientRect();
                const w = canvas.width, h = canvas.height;
                const mouseX = (e.clientX - rect.left) * (w / rect.width);
                const mouseY = (e.clientY - rect.top) * (h / rect.height);
                const seg = getSegment(mouseX, mouseY, w, h, statusSegments);
                if (seg) {
                    tooltip.style.display = 'block';
                    tooltip.style.left = e.clientX + 15 + 'px';
                    tooltip.style.top = e.clientY - 30 + 'px';
                    tooltip.innerHTML = `${seg.label}<br>Jumlah: ${seg.value} booking`;
                } else tooltip.style.display = 'none';
            });
            canvas.addEventListener('mouseleave', () => tooltip.style.display = 'none');
        }

        // Initial render
        renderActivities();
        renderClassProgress();
        drawAreaChart();
        drawKuotaPie();
        drawStatusPie();
        attachAreaTooltip();
        attachKuotaTooltip();
        attachStatusTooltip();

        // Handle resize
        window.addEventListener('resize', () => {
            drawAreaChart();
            drawKuotaPie();
            drawStatusPie();
            attachAreaTooltip();
            attachKuotaTooltip();
            attachStatusTooltip();
        });
    })();
</script>
@endsection