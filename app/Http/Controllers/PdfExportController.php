<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class PdfExportController extends Controller
{
    public function siswa(Request $request)
    {
        $this->authorize('viewAny', Siswa::class);

        $status = $request->input('status'); // Direview, Diterima, Ditolak or empty (all)
        $kelas = $request->input('kelas'); // XII SIJA 1 / XII SIJA 2 or empty

        $query = Siswa::query()->with(['bookings' => function($q) { $q->orderBy('created_at', 'desc'); }, 'berkas']);

        if (!empty($kelas) && in_array($kelas, ['XII SIJA 1', 'XII SIJA 2'], true)) {
            $query->where('kelas', $kelas);
        }

        if (!empty($status) && in_array($status, ['Direview', 'Diterima', 'Ditolak'], true)) {
            $query->whereHas('bookings', function ($q) use ($status) {
                $q->where('status', $status);
            });
        }

        // If Wali Kelas or Kakonsli, restrict to their kelas
        $user = Auth::user();
        if ($user && in_array($user->role, ['wali_kelas', 'kakonsli'], true)) {
            if (!empty($user->kelas_id)) {
                $query->where('kelas', $user->kelas_id);
            }
        }

        $siswas = $query->orderBy('kelas')->orderBy('nomor_absen')->get();

        $total = $siswas->count();

        $logoUrl = null;
        if (Storage::disk('public')->exists('school_logo.png')) {
            $logoUrl = asset('storage/school_logo.png');
        }

        $data = [
            'title' => 'Laporan Data Siswa PKL',
            'generated_at' => now(),
            'filters' => [
                'status' => $status ?: 'Semua',
                'kelas' => $kelas ?: 'Semua',
            ],
            'siswas' => $siswas,
            'total' => $total,
            'logo' => $logoUrl,
        ];

        // Use barryvdh/laravel-dompdf facade if available
        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.siswa_report', $data)
                ->setPaper('a4', 'portrait')
                ->setOption('dpi', 150)
                ->setOption('isRemoteEnabled', true);

            return $pdf->stream('laporan_siswa_' . now()->format('Ymd_His') . '.pdf');
        }

        // Fallback: render HTML view
        return view('reports.siswa_report', $data);
    }
}
