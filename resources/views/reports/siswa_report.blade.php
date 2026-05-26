<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    <style>
        @page { margin: 36pt 36pt; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #222; }
        .header { display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:20px; }
        .logo { width:72px; height:72px; object-fit:contain; margin-right:12px; }
        .brand { display:flex; align-items:center; gap:12px; }
        .title-wrapper { flex:1; }
        .title { font-size:18px; font-weight:700; margin-bottom:6px; }
        .subtitle { font-size:12px; color:#555; margin-bottom:2px; }
        .meta { font-size:11px; color:#444; line-height:1.5; }
        .info-cards { display:flex; gap:10px; margin-top:18px; margin-bottom:18px; }
        .card { flex:1; background:#f8f9fb; border:1px solid #e2e8f0; border-radius:6px; padding:10px 12px; }
        .card strong { display:block; font-size:13px; margin-bottom:4px; color:#1f2937; }
        .card span { display:block; font-size:12px; color:#4b5563; }
        .table-wrapper { width:100%; overflow-x:auto; }
        table { width:100%; border-collapse:collapse; margin-top:12px; font-size:11px; }
        th, td { border:1px solid #d1d5db; padding:10px 12px; vertical-align:middle; }
        th { background:#eef2ff; font-weight:700; color:#1f2937; text-align:left; }
        tbody tr:nth-child(odd) { background:#ffffff; }
        tbody tr:nth-child(even) { background:#f9fafb; }
        .status { padding:4px 8px; border-radius:4px; font-size:10px; font-weight:700; display:inline-block; }
        .status-direview { background:#fef3c7; color:#92400e; }
        .status-diterima { background:#d1fae5; color:#047857; }
        .status-ditolak { background:#fee2e2; color:#991b1b; }
        .footer { position: fixed; bottom: 12pt; left: 36pt; right: 36pt; text-align:center; font-size:10px; color:#555; }
        .summary { margin-top:16px; font-weight:700; }
        .page-number { font-size:10px; color:#6b7280; }
        .small { font-size:10px; color:#6b7280; }
        .text-muted { color:#6b7280; }
        .highlight { color:#1d4ed8; }
        .table-small { font-size:10px; }
        .nowrap { white-space:nowrap; }
    </style>
</head>
<body>
    <div class="header">
        <div class="brand">
            @if(!empty($logo))
                <img class="logo" src="{{ $logo }}" alt="Logo Sekolah">
            @endif
            <div class="title-wrapper">
                <div class="title">{{ $title }}</div>
                <div class="subtitle">Laporan data siswa PKL lengkap dengan status lamaran dan perusahaan tujuan.</div>
                <div class="meta">Dicetak: {{ $generated_at->format('d M Y H:i') }}<br>Filter: Status={{ $filters['status'] }}, Kelas={{ $filters['kelas'] }}</div>
            </div>
        </div>
    </div>

    <div class="info-cards">
        <div class="card">
            <strong>Total Siswa</strong>
            <span>{{ $total }} siswa</span>
        </div>
        <div class="card">
            <strong>Status</strong>
            <span>{{ $filters['status'] }}</span>
        </div>
        <div class="card">
            <strong>Kelas</strong>
            <span>{{ $filters['kelas'] }}</span>
        </div>
    </div>

    <div class="table-wrapper">
        <table class="table-small">
        <thead>
            <tr>
                <th style="width:40px">No</th>
                <th>NIS</th>
                <th>Nama</th>
                <th>Kelas</th>
                <th>Status PKL</th>
                <th>DUDI (Terakhir)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($siswas as $i => $siswa)
                @php
                    $lastBooking = $siswa->bookings->first();
                    $status = $lastBooking->status ?? 'Tidak Ada';
                    $dudiName = $lastBooking ? ($lastBooking->dudi->nama_dudi ?? '-') : '-';
                @endphp
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $siswa->nis }}</td>
                    <td>{{ $siswa->nama }}</td>
                    <td>{{ $siswa->kelas }}</td>
                    <td>
                        @php
                            $statusClass = match($status) {
                                'Diterima' => 'status-diterima',
                                'Ditolak' => 'status-ditolak',
                                'Direview' => 'status-direview',
                                default => 'status-direview',
                            };
                        @endphp
                        <span class="status {{ $statusClass }}">{{ $status }}</span>
                    </td>
                    <td>{{ $dudiName }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">Total data: <span class="highlight">{{ $total }}</span></div>

    <div class="footer">
        Sistem PKL &mdash; Halaman <span class="page"></span>
    </div>

    @if(class_exists('\Barryvdh\DomPDF\Facade\Pdf'))
        <script type="text/php">
            if (isset($pdf)) {
                $x = 520;
                $y = 820;
                $text = "Halaman {PAGE_NUM} / {PAGE_COUNT}";
                $font = $fontMetrics->getFont("DejaVu Sans");
                $size = 9;
                $pdf->page_text($x, $y, $text, $font, $size, array(0,0,0));
            }
        </script>
    @endif
</body>
</html>
