<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        body { font-family: Arial, sans-serif; color: #1f2937; background: #fff; margin: 0; padding: 0; }
        .page { padding: 28px 32px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
        .header .brand { display: flex; align-items: center; gap: 18px; }
        .header .brand img { width: 88px; height: auto; border-radius: 12px; }
        .header h1 { margin: 0; font-size: 24px; color: #0f172a; }
        .header .meta { text-align: right; font-size: 12px; color: #475569; }
        .summary { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; margin-bottom: 26px; }
        .summary-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; padding: 16px; }
        .summary-card span { display: block; font-size: 11px; text-transform: uppercase; letter-spacing: .08em; color: #64748b; margin-bottom: 8px; }
        .summary-card strong { display: block; font-size: 18px; color: #0f172a; }
        .table-wrap { width: 100%; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 11px; }
        th, td { padding: 12px 10px; border: 1px solid #e2e8f0; }
        th { background: #e0f2fe; color: #0f172a; text-transform: uppercase; font-size: 10px; letter-spacing: .05em; }
        tbody tr:nth-child(even) { background: #f8fafc; }
        .status-pill { display: inline-block; padding: 6px 10px; border-radius: 9999px; font-size: 10px; font-weight: 700; }
        .status-diterima { color: #047857; background: #d1fae5; }
        .status-ditolak { color: #991b1b; background: #fee2e2; }
        .status-direview { color: #92400e; background: #fef3c7; }
        .footer { margin-top: 20px; font-size: 11px; color: #475569; }
    </style>
</head>
<body>
    <div class="page">
        <div class="header">
            <div class="brand">
                @if($logo)
                    <img src="{{ $logo }}" alt="Logo Sekolah">
                @endif
                <div>
                    <h1>{{ $title }}</h1>
                    <div style="font-size: 12px; color: #475569;">Laporan PDF Siswa PKL</div>
                </div>
            </div>
            <div class="meta">
                <div>{{ $generated_at->format('d F Y') }}</div>
                <div>{{ $generated_at->format('H:i') }}</div>
            </div>
        </div>

        <div class="summary">
            <div class="summary-card">
                <span>Total Siswa</span>
                <strong>{{ $total }}</strong>
            </div>
            <div class="summary-card">
                <span>Filter Status</span>
                <strong>{{ $filters['status'] }}</strong>
            </div>
            <div class="summary-card">
                <span>Filter Kelas</span>
                <strong>{{ $filters['kelas'] }}</strong>
            </div>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIS</th>
                        <th>Nama</th>
                        <th>Kelas</th>
                        <th>Status PKL</th>
                        <th>Perusahaan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($siswas as $index => $siswa)
                        @php
                            $booking = $siswa->bookings->first();
                            $status = $booking->status ?? 'Belum';
                            $dudiName = $booking && $booking->dudi ? $booking->dudi->nama_dudi : '-';
                            $statusClass = $status === 'Diterima' ? 'status-diterima' : ($status === 'Ditolak' ? 'status-ditolak' : 'status-direview');
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $siswa->nis }}</td>
                            <td>{{ $siswa->nama }}</td>
                            <td>{{ $siswa->kelas }}</td>
                            <td><span class="status-pill {{ $statusClass }}">{{ $status }}</span></td>
                            <td>{{ $dudiName }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center; padding: 24px;">Tidak ada data siswa untuk ditampilkan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="footer">Dicetak: {{ $generated_at->format('d M Y H:i') }}</div>
    </div>
</body>
</html>