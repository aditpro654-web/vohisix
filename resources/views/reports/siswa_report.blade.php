<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    <style>
        @page { margin: 36pt 36pt; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #222; }
        .header { display:flex; align-items:center; justify-content: space-between; margin-bottom:18px; }
        .logo { width:72px; height:72px; object-fit:contain; margin-right:12px; }
        .title { font-size:18px; font-weight:700; margin-bottom:2px; }
        .meta { font-size:11px; color:#444; margin-top:6px; line-height:1.4; }
        .table-wrapper { overflow-x: auto; }
        table { width:100%; border-collapse: collapse; margin-top:12px; font-size:11px; }
        th, td { border:1px solid #ddd; padding:8px 10px; vertical-align: middle; }
        th { background:#f5f5f5; font-weight:700; font-size:11px; text-align:left; }
        tbody tr:nth-child(odd) { background:#fbfbfb; }
        tbody tr:hover { background:#f0f4f8; }
        .text-muted { color:#666; }
        .footer { position: fixed; bottom: 12pt; left: 36pt; right: 36pt; text-align: center; font-size:10px; color:#666; }
        .summary { margin-top:16px; font-weight:700; }
        .summary span { display:inline-block; margin-right:1.25rem; }
        .filters { margin-top:4px; }
    </style>
</head>
<body>
    <div class="header">
        @if(!empty($logo))
            <img class="logo" src="{{ $logo }}" alt="Logo Sekolah">
        @endif
        <div>
            <div class="title">{{ $title }}</div>
            <div class="meta">Dicetak: {{ $generated_at->format('d M Y H:i') }} &nbsp; | &nbsp; Filter: Status={{ $filters['status'] }}, Kelas={{ $filters['kelas'] }}</div>
        </div>
    </div>

    <table>
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
                    <td>{{ $status }}</td>
                    <td>{{ $dudiName }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top:12px; font-weight:700;">Total data: {{ $total }}</div>

    <div class="footer">
        Sistem PKL &mdash; Halaman <span class="page"></span>
    </div>

    @if(class_exists('\Barryvdh\DomPDF\Facade\Pdf'))
        <script type="text/php">
            if (isset($pdf)) {
                $x = 520; $y = 820; $text = "Halaman {PAGE_NUM} / {PAGE_COUNT}";
                $font = $fontMetrics->getFont("DejaVu Sans");
                $size = 9;
                $pdf->page_text($x, $y, $text, $font, $size, array(0,0,0));
            }
        </script>
    @endif
</body>
</html>
