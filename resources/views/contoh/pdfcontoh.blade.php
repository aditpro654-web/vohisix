@extends('layouts.app')

@section('title', 'Preview Laporan PDF Siswa')

@section('content')
<div class="card" style="padding: 24px; max-width: 1200px; margin: 0 auto;">
    <div style="display:flex; flex-wrap:wrap; justify-content:space-between; align-items:center; gap:16px; margin-bottom:24px;">
        <div>
            <h1 style="margin:0; font-size:1.75rem; color:#0f172a;">Preview Laporan PDF Siswa</h1>
            <p style="margin:8px 0 0; color:#475569; font-size:0.95rem; max-width:720px;">Pilih jenis laporan yang ingin dicetak, kemudian tekan tombol "Cetak PDF" untuk mengunduh dokumen dengan format resmi.</p>
        </div>
        <div style="display:flex; flex-wrap:wrap; gap:10px;">
            <a href="{{ $downloadUrl }}" target="_blank" rel="noopener" class="btn btn-primary">Cetak PDF</a>
            <a href="{{ $autoDownloadUrl }}" target="_blank" rel="noopener" class="btn btn-secondary">Preview + Unduh Otomatis</a>
            <a href="{{ route('admin.siswa.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </div>

    <div style="display:grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap:16px; margin-bottom:24px;">
        <div style="padding:18px; border-radius:18px; background:#f8fafc; border:1px solid #e2e8f0;">
            <div style="font-size:0.75rem; color:#64748b; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:8px;">Total siswa</div>
            <div style="font-size:1.5rem; font-weight:700; color:#0f172a;">{{ $total }}</div>
        </div>
        <div style="padding:18px; border-radius:18px; background:#f8fafc; border:1px solid #e2e8f0;">
            <div style="font-size:0.75rem; color:#64748b; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:8px;">Filter status</div>
            <div style="font-size:1rem; font-weight:700; color:#0f172a;">{{ $filters['status'] }}</div>
        </div>
        <div style="padding:18px; border-radius:18px; background:#f8fafc; border:1px solid #e2e8f0;">
            <div style="font-size:0.75rem; color:#64748b; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:8px;">Filter kelas</div>
            <div style="font-size:1rem; font-weight:700; color:#0f172a;">{{ $filters['kelas'] }}</div>
        </div>
    </div>

    <div style="overflow-x:auto; border-radius:16px; border:1px solid #e2e8f0;">
        <table style="width:100%; border-collapse:collapse; min-width:900px;">
            <thead>
                <tr style="background:#eef2ff; color:#0f172a; text-align:left;">
                    <th style="padding:14px 12px; border-bottom:1px solid #d1d5db;">No</th>
                    <th style="padding:14px 12px; border-bottom:1px solid #d1d5db;">NIS</th>
                    <th style="padding:14px 12px; border-bottom:1px solid #d1d5db;">Nama</th>
                    <th style="padding:14px 12px; border-bottom:1px solid #d1d5db;">Kelas</th>
                    <th style="padding:14px 12px; border-bottom:1px solid #d1d5db;">Status PKL</th>
                    <th style="padding:14px 12px; border-bottom:1px solid #d1d5db;">Perusahaan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($siswas as $index => $siswa)
                    @php
                        $booking = $siswa->bookings->first();
                        $status = $booking->status ?? 'Belum';
                        $dudiName = $booking && $booking->dudi ? $booking->dudi->nama_dudi : '-';
                        $statusColor = $status === 'Diterima' ? '#047857' : ($status === 'Ditolak' ? '#991b1b' : '#92400e');
                        $statusBg = $status === 'Diterima' ? '#d1fae5' : ($status === 'Ditolak' ? '#fee2e2' : '#fef3c7');
                    @endphp
                    <tr style="background: {{ $index % 2 === 0 ? '#ffffff' : '#f8fafc' }};">
                        <td style="padding:12px 12px; border-bottom:1px solid #e2e8f0;">{{ $index + 1 }}</td>
                        <td style="padding:12px 12px; border-bottom:1px solid #e2e8f0;">{{ $siswa->nis }}</td>
                        <td style="padding:12px 12px; border-bottom:1px solid #e2e8f0;">{{ $siswa->nama }}</td>
                        <td style="padding:12px 12px; border-bottom:1px solid #e2e8f0;">{{ $siswa->kelas }}</td>
                        <td style="padding:12px 12px; border-bottom:1px solid #e2e8f0;"><span style="display:inline-flex; align-items:center; gap:6px; padding:6px 10px; border-radius:9999px; font-size:0.75rem; font-weight:700; color:{{ $statusColor }}; background:{{ $statusBg }};">{{ $status }}</span></td>
                        <td style="padding:12px 12px; border-bottom:1px solid #e2e8f0;">{{ $dudiName }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="padding:40px 16px; text-align:center; color:#64748b;">Tidak ada data siswa untuk ditampilkan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:24px; display:flex; flex-wrap:wrap; gap:12px; justify-content:space-between; align-items:center; color:#475569;">
        <div style="max-width:760px; font-size:0.95rem; line-height:1.75;">
            Halaman preview ini menampilkan data sesuai filter yang dipilih. Klik "Cetak PDF" untuk membuat dokumen PDF yang akan terunduh ke komputer Anda.
        </div>
        <div style="font-size:0.85rem; color:#94a3b8;">Dicetak: {{ $generated_at->format('d M Y H:i') }}</div>
    </div>
</div>

@if($autoDownload)
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const iframe = document.createElement('iframe');
        iframe.style.display = 'none';
        iframe.src = '{{ $downloadUrl }}';
        document.body.appendChild(iframe);
    });
</script>
@endif
@endsection