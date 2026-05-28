@extends('layouts.app')

@section('title', 'Preview Laporan PDF Siswa')

@section('content')
<style>
    /* Print styling for A4 */
    @media print {
        body * {
            visibility: hidden;
        }
        .print-area, .print-area * {
            visibility: visible;
        }
        .print-area {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            margin: 0;
            padding: 20mm;
            background: white;
            box-shadow: none;
        }
        .no-print {
            display: none !important;
        }
        .btn, .btn-primary, .btn-secondary, a, button {
            display: none !important;
        }
    }
    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background-color: #f8fafc;
    }
    .pdf-sheet {
        background: white;
        box-shadow: 0 20px 25px -5px rgba(0,0,0,0.05);
        border-radius: 24px;
        border: 1px solid #e2e8f0;
    }
    .badge-diterima { background-color: #dcfce7; color: #166534; border-color: #bbf7d0; }
    .badge-ditolak { background-color: #fee2e2; color: #991b1b; border-color: #fecaca; }
    .badge-menunggu { background-color: #fef3c7; color: #92400e; border-color: #fde68a; }
    .badge-belum { background-color: #f1f5f9; color: #475569; border-color: #cbd5e1; }
</style>

<div class="container mx-auto px-4 py-8 max-w-7xl">
    <!-- Header dengan tombol aksi (no-print) -->
    <div class="no-print flex flex-wrap justify-between items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Preview Laporan PDF Siswa</h1>
            <p class="text-sm text-slate-500 mt-1">Pilih jenis laporan yang ingin dicetak, kemudian tekan tombol "Cetak PDF" untuk mengunduh dokumen dengan format resmi.</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ $downloadUrl }}" target="_blank" rel="noopener" class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow transition flex items-center gap-2">
                🖨️ Cetak PDF
            </a>
            <a href="{{ $autoDownloadUrl }}" target="_blank" rel="noopener" class="bg-blue-800 hover:bg-blue-900 text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow transition flex items-center gap-2">
                👁️ Preview + Unduh Otomatis
            </a>
            <a href="{{ route('admin.siswa.index') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 px-5 py-2.5 rounded-xl font-bold text-sm transition">
                Kembali
            </a>
        </div>
    </div>

    <!-- Statistik ringkas (mirip dashboard) -->
    @php
        $total = $siswas->count();
        $diterima = $siswas->filter(fn($s) => optional($s->bookings->first())->status === 'Diterima')->count();
        $ditolak = $siswas->filter(fn($s) => optional($s->bookings->first())->status === 'Ditolak')->count();
        $menunggu = $siswas->filter(fn($s) => optional($s->bookings->first())->status === 'Menunggu')->count();
        $belum = $total - ($diterima + $ditolak + $menunggu);
    @endphp
    <div class="no-print grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-xl p-4 text-center border border-slate-200 shadow-sm"><div class="text-xs text-slate-500">Total Siswa</div><div class="text-2xl font-black text-slate-800">{{ $total }}</div></div>
        <div class="bg-emerald-50 rounded-xl p-4 text-center border border-emerald-200"><div class="text-xs text-emerald-600">Diterima</div><div class="text-2xl font-black text-emerald-700">{{ $diterima }}</div></div>
        <div class="bg-rose-50 rounded-xl p-4 text-center border border-rose-200"><div class="text-xs text-rose-600">Ditolak</div><div class="text-2xl font-black text-rose-700">{{ $ditolak }}</div></div>
        <div class="bg-amber-50 rounded-xl p-4 text-center border border-amber-200"><div class="text-xs text-amber-600">Menunggu</div><div class="text-2xl font-black text-amber-700">{{ $menunggu }}</div></div>
        <div class="bg-slate-100 rounded-xl p-4 text-center border border-slate-200"><div class="text-xs text-slate-500">Belum Booking</div><div class="text-2xl font-black">{{ $belum }}</div></div>
    </div>

    <!-- AREA CETAK (PDF) yang akan muncul saat print -->
    <div class="print-area pdf-sheet bg-white p-8 md:p-10 mx-auto" style="max-width: 1100px;">
        <!-- Kop surat (sama seperti dashboard sebelumnya) -->
        <div class="border-b-4 border-slate-900 pb-4 flex items-start gap-5 mb-6">
            <div class="h-16 w-16 bg-blue-900 rounded-xl flex items-center justify-center text-white text-2xl font-black shadow-sm">SMK</div>
            <div class="flex-1 text-center">
                <h3 class="text-xs font-extrabold tracking-wider text-slate-600">PEMERINTAH PROVINSI DAERAH KHUSUS JAKARTA</h3>
                <h2 class="text-base font-extrabold text-blue-900 uppercase">DEPARTEMEN PENDIDIKAN & HUBUNGAN INDUSTRI</h2>
                <h1 class="text-lg font-black text-slate-900">{{ config('app.name', 'SMK NEGERI 1 INFORMATIKA UTAMA') }}</h1>
                <p class="text-[10px] text-gray-500 mt-1">Jalan Informatika Raya No. 42B, Jakarta. Telp: (021) 8877665 | Email: hubin@smk.sch.id</p>
            </div>
            <div class="h-14 w-14 border border-slate-300 rounded-lg flex flex-col items-center justify-center text-[8px] font-bold text-slate-500 bg-slate-50">
                <span class="text-lg">🏆</span>
                <span>AKREDITASI A</span>
            </div>
        </div>

        <!-- Judul laporan -->
        <div class="text-center mb-6">
            <h2 class="text-sm font-black uppercase tracking-wider border-b border-slate-300 inline-block pb-1 px-4">LAPORAN HASIL SELEKSI & BOOKING PKL</h2>
            <div class="text-[10px] font-mono text-slate-500 mt-2">Dicetak: {{ $generated_at->format('d M Y H:i') }} | Filter: {{ $filters['status'] }} / {{ $filters['kelas'] }}</div>
        </div>

        <!-- Tabel data siswa (rapi) -->
        <div class="overflow-x-auto border border-slate-200 rounded-xl mb-6">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-900 text-white text-[11px] uppercase font-bold">
                        <th class="px-4 py-3 text-center w-12">No</th>
                        <th class="px-4 py-3">NIS</th>
                        <th class="px-4 py-3">Nama Siswa</th>
                        <th class="px-4 py-3">Kelas</th>
                        <th class="px-4 py-3 text-center">Status PKL</th>
                        <th class="px-4 py-3">Perusahaan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($siswas as $index => $siswa)
                        @php
                            $booking = $siswa->bookings->first();
                            $status = $booking->status ?? 'Belum';
                            $dudiName = ($booking && $booking->dudi) ? $booking->dudi->nama_dudi : '-';
                            $badgeClass = match($status) {
                                'Diterima' => 'badge-diterima',
                                'Ditolak' => 'badge-ditolak',
                                'Menunggu' => 'badge-menunggu',
                                default => 'badge-belum'
                            };
                        @endphp
                        <tr class="border-b border-slate-100 {{ $index % 2 === 0 ? 'bg-white' : 'bg-slate-50/50' }}">
                            <td class="px-4 py-3 text-center font-mono text-slate-500">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 font-mono text-xs">{{ $siswa->nis }}</td>
                            <td class="px-4 py-3 font-semibold text-slate-800">{{ $siswa->nama }}</td>
                            <td class="px-4 py-3">{{ $siswa->kelas }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-block px-3 py-1 rounded-full text-[10px] font-bold border {{ $badgeClass }}">{{ $status }}</span>
                            </td>
                            <td class="px-4 py-3 text-slate-700">{{ $dudiName }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-12 text-slate-400">Tidak ada data siswa untuk ditampilkan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Catatan / rekomendasi (sama seperti dashboard) -->
        <div class="bg-slate-50 border border-dashed border-slate-300 rounded-xl p-4 mb-6 text-[11px] text-slate-700">
            <p class="font-bold text-slate-800 mb-1">📌 Catatan Resmi Panitia PKL:</p>
            <ul class="list-disc pl-5 space-y-1">
                <li>Keputusan ini berdasarkan hasil seleksi administratif dan wawancara mitra industri.</li>
                <li>Siswa dengan status <strong>Diterima</strong> wajib mengikuti pembekalan sebelum penempatan.</li>
                <li>Siswa <strong>Ditolak</strong> dapat mendaftar ulang pada gelombang berikutnya.</li>
                <li>Status <strong>Menunggu</strong> akan diumumkan lebih lanjut oleh tim Hubin.</li>
            </ul>
        </div>

        <!-- Tanda tangan (sesuai gaya dashboard) -->
        <div class="flex justify-between items-end pt-4 border-t border-slate-200 text-[11px]">
            <div class="text-center w-44">
                <p>Mengesahkan,</p>
                <p class="font-bold mt-1">Ketua Komite Hubin</p>
                <div class="h-12 flex justify-center items-center mt-2">
                    <span class="text-[8px] border border-emerald-500 rotate-12 px-2 py-0.5 rounded text-emerald-700">DIVERIFIKASI</span>
                </div>
                <p class="font-bold underline mt-1">Drs. Hendra Wijaya</p>
            </div>
            <div class="text-center w-56">
                <p>Jakarta, {{ $generated_at->format('d F Y') }}</p>
                <p class="font-bold">Kepala Sekolah</p>
                <div class="h-12 flex justify-center items-center mt-2">
                    <span class="text-[8px] border border-blue-400 rounded-full px-3 py-0.5 text-blue-700">SMK NEGERI 1</span>
                </div>
                <p class="font-bold underline mt-1">Dr. Eng. H. Rachmat Hidayat, M.T.</p>
                <p class="text-[9px] font-mono">NIP. 19740512 200212 1 003</p>
            </div>
        </div>
    </div>

    <!-- Footer no-print -->
    <div class="no-print mt-6 text-center text-xs text-slate-400">
        <p>Halaman preview ini menampilkan data sesuai filter yang dipilih. Klik "Cetak PDF" untuk membuat dokumen PDF yang akan terunduh ke komputer Anda.</p>
        <p class="mt-1">Dicetak: {{ $generated_at->format('d M Y H:i') }}</p>
    </div>
</div>

@if($autoDownload)
<script>
    document.addEventListener('DOMContentLoaded', function () {
        window.open('{{ $downloadUrl }}', '_blank');
    });
</script>
@endif
@endsection