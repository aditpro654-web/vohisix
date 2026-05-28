<?php

namespace App\Http\Controllers;

use App\Models\Dudi;
use App\Models\Booking;
use App\Models\Location;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class SiswaDudiController extends Controller
{
    /**
     * Display a listing of dudis
     */
    public function index(Request $request)
    {
        $siswa = Siswa::where('nis', Auth::user()->username)->firstOrFail();
        
        $search = $request->input('search');
        $provinsi = $request->input('provinsi');
        $kota = $request->input('kota');
        $jamBerangkat = $request->input('jam_berangkat');
        $jamPulang = $request->input('jam_pulang');
        $kuota = $request->input('kuota');
        $sort = $request->input('sort', 'default');
        
        $query = Dudi::query();
        if ($this->hasDudiStatusColumn()) {
            $query->where('status', 'active');
        }

        // Search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_dudi', 'like', "%$search%")
                  ->orWhere('bidang_usaha', 'like', "%$search%");
            });
        }

        // Lokasi filter
        if ($kota) {
            $query->where('kota', 'like', "%$kota%");
        } elseif ($provinsi) {
            $kabupatens = Location::where('provinsi', $provinsi)->pluck('kabupaten')->toArray();
            if (!empty($kabupatens)) {
                $query->whereIn('kota', $kabupatens);
            }
        }

        // Jam Berangkat filter
        if ($jamBerangkat && is_array($jamBerangkat)) {
            $query->where(function($q) use ($jamBerangkat) {
                foreach ($jamBerangkat as $jam) {
                    $q->orWhere('jam_masuk', 'like', "%$jam%");
                }
            });
        }

        // Jam Pulang filter
        if ($jamPulang && is_array($jamPulang)) {
            $query->where(function($q) use ($jamPulang) {
                foreach ($jamPulang as $jam) {
                    $q->orWhere('jam_pulang', 'like', "%$jam%");
                }
            });
        }

        // Kuota filter
        if ($kuota && $kuota != 'semua') {
            switch ($kuota) {
                case 'kecil':
                    $query->where('kuota', '<', 5);
                    break;
                case 'sedang':
                    $query->whereBetween('kuota', [5, 10]);
                    break;
                case 'besar':
                    $query->where('kuota', '>', 10);
                    break;
            }
        }

        // Sorting
        switch ($sort) {
            case 'nama-az':
                $query->orderBy('nama_dudi', 'asc');
                break;
            case 'nama-za':
                $query->orderBy('nama_dudi', 'desc');
                break;
            case 'kuota-banyak':
                $query->orderBy('kuota', 'desc');
                break;
            case 'kuota-sedikit':
                $query->orderBy('kuota', 'asc');
                break;
            case 'default':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $dudis = $query->paginate(12);

        $provinces = Location::select('provinsi')
            ->distinct()
            ->orderBy('provinsi')
            ->pluck('provinsi');

        $kabupatensByProvince = Location::orderBy('kabupaten')
            ->get()
            ->groupBy('provinsi')
            ->map(function ($items) {
                return $items->pluck('kabupaten')->toArray();
            })
            ->toArray();

        // Check booking aktif user
        $bookingAktif = Booking::where('nis', $siswa->nis)
                                ->whereIn('status', ['Direview', 'Diterima'])
                                ->first();

        // Add pendaftar count untuk setiap DUDI dan check status user
        $dudis->load('bookings');
        foreach ($dudis as $dudi) {
            $dudi->user_sudah_ajukan = Booking::where('nis', $siswa->nis)
                                              ->where('id_dudi', $dudi->id_dudi)
                                              ->exists();
        }

        return view('siswa.dudi.index', compact('dudis', 'search', 'provinsi', 'kota', 'jamBerangkat', 'jamPulang', 'kuota', 'sort', 'siswa', 'provinces', 'kabupatensByProvince', 'bookingAktif'));
    }

    private function hasDudiStatusColumn(): bool
    {
        return Schema::hasColumn('dudis', 'status');
    }

    /**
     * Ajukan PKL ke DUDI
     */
    public function ajukan(Request $request, Dudi $dudi)
    {
        $siswa = Siswa::where('nis', Auth::user()->username)->firstOrFail();
        $berkas = $siswa->berkas;

        // Check if berkas lengkap
        if (!$berkas || !$berkas->lengkap) {
            return back()->with('error', 'Berkas administrasi Anda belum lengkap. Silakan upload berkas terlebih dahulu.');
        }

        // Check if already booked to this DUDI
        $sudahAjukan = Booking::where('nis', $siswa->nis)
                               ->where('id_dudi', $dudi->id_dudi)
                               ->exists();

        if ($sudahAjukan) {
            return back()->with('error', 'Anda sudah mengajukan PKL ke DUDI ini');
        }

        // Check if sudah punya booking aktif (Direview atau Diterima)
        $bookingAktif = Booking::where('nis', $siswa->nis)
                                ->whereIn('status', ['Direview', 'Diterima'])
                                ->first();

        if ($bookingAktif) {
            return back()->with('error', 'Anda sudah memiliki pengajuan PKL yang aktif. Hanya bisa punya 1 pengajuan hingga ditolak atau diterima.');
        }

        // Check kuota DUDI
        $jumlahPendaftar = Booking::where('id_dudi', $dudi->id_dudi)
                                   ->whereIn('status', ['Direview', 'Diterima'])
                                   ->count();

        if ($jumlahPendaftar >= $dudi->kuota) {
            return back()->with('error', 'Kuota pendaftar untuk DUDI ini sudah penuh. Silakan pilih DUDI lain.');
        }

        if ($this->hasDudiStatusColumn() && $dudi->status !== 'active') {
            return back()->with('error', 'DUDI ini tidak aktif dan tidak dapat dipilih.');
        }

        Booking::create([
            'nis' => $siswa->nis,
            'id_dudi' => $dudi->id_dudi,
            'status' => 'Direview',
        ]);

        return back()->with('success', 'Pengajuan PKL ke ' . $dudi->nama_dudi . ' berhasil diterima. Tunggu konfirmasi dari pihak DUDI.');
    }
}
