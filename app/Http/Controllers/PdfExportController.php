<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class PdfExportController extends Controller
{
    public function siswa(Request $request)
    {
        $report = $this->buildSiswaReportData($request);

        $logoUrl = null;
        if (Storage::disk('public')->exists('school_logo.png')) {
            $logoUrl = asset('storage/school_logo.png');
        }

        $data = [
            'title' => 'Laporan Data Siswa PKL',
            'generated_at' => now(),
            'filters' => [
                'status' => $report['status'] ?: 'Semua',
                'kelas' => $report['kelas'] ?: 'Semua',
            ],
            'siswas' => $report['siswas'],
            'total' => $report['siswas']->count(),
            'logo' => $logoUrl,
        ];

        if (class_exists(Pdf::class)) {
            $pdf = Pdf::loadView('reports.siswa_report', $data)
                ->setPaper('a4', 'portrait')
                ->setOption('dpi', 150)
                ->setOption('isRemoteEnabled', true);

            if ($request->boolean('download')) {
                return $pdf->download('laporan_siswa_' . now()->format('Ymd_His') . '.pdf');
            }

            return $pdf->stream('laporan_siswa_' . now()->format('Ymd_His') . '.pdf');
        }

        return view('reports.siswa_report', $data);
    }

    public function siswaPreview(Request $request)
    {
        $report = $this->buildSiswaReportData($request);

        $pdfRouteName = str_replace('.preview', '', $request->route()->getName());

        return view('reports.siswa_preview', [
            'title' => 'Preview Laporan Siswa PKL',
            'generated_at' => now(),
            'filters' => [
                'status' => $report['status'] ?: 'Semua',
                'kelas' => $report['kelas'] ?: 'Semua',
            ],
            'siswas' => $report['siswas'],
            'total' => $report['siswas']->count(),
            'downloadUrl' => route($pdfRouteName, array_merge($request->query(), ['download' => 1])),
            'autoDownloadUrl' => route($request->route()->getName(), array_merge($request->query(), ['auto_download' => 1])),
            'autoDownload' => $request->boolean('auto_download'),
        ]);
    }

    private function buildSiswaReportData(Request $request): array
    {
        $status = $request->input('status');
        $kelas = $request->input('kelas');

        $query = Siswa::query()->with([
            'bookings' => function ($q) {
                $q->orderBy('created_at', 'desc');
            },
            'bookings.dudi',
            'berkas',
        ]);

        if (!empty($kelas) && in_array($kelas, ['XII SIJA 1', 'XII SIJA 2'], true)) {
            $query->where('kelas', $kelas);
        }

        if (!empty($status) && in_array($status, ['Direview', 'Diterima', 'Ditolak'], true)) {
            $query->whereHas('bookings', function ($q) use ($status) {
                $q->where('status', $status);
            });
        }

        $user = Auth::user();
        if ($user) {
            if ($user->role === 'wali_kelas' && !empty($user->kelas_id)) {
                $query->where('kelas', $user->kelas_id);
            }

            if ($user->role === 'kakonsli') {
                $kelasIds = array_filter([$user->kelas_id, $user->kelas_second], fn ($value) => !empty($value));
                if (!empty($kelasIds)) {
                    $query->whereIn('kelas', $kelasIds);
                }
            }
        }

        return [
            'status' => $status,
            'kelas' => $kelas,
            'siswas' => $query->orderBy('kelas')->orderBy('nomor_absen')->get(),
        ];
    }
}
