<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Booking;

class WaliKelasDashboardController extends Controller
{
    /**
     * Show wali kelas dashboard
     */
    public function index()
    {
        $user = auth()->user();
        $kelas = $user->kelas_id;

        $totalSiswa = Siswa::where('kelas', $kelas)->count();
        $totalBooking = Booking::whereHas('siswa', function($query) use ($kelas) {
            $query->where('kelas', $kelas);
        })->count();
        
        $bookingDireview = Booking::whereHas('siswa', function($query) use ($kelas) {
            $query->where('kelas', $kelas);
        })->where('status', 'Direview')->count();
        
        $bookingDiterima = Booking::whereHas('siswa', function($query) use ($kelas) {
            $query->where('kelas', $kelas);
        })->where('status', 'Diterima')->count();
        
        $bookingDitolak = Booking::whereHas('siswa', function($query) use ($kelas) {
            $query->where('kelas', $kelas);
        })->where('status', 'Ditolak')->count();

        $siswas = Siswa::with(['berkas', 'bookings.dudi'])
            ->where('kelas', $kelas)
            ->get()
            ->map(function ($siswa) {
                $latestBooking = $siswa->bookings->sortByDesc('created_at')->first();
                $dudi = $latestBooking ? $latestBooking->dudi : null;

                return [
                    'id' => $siswa->nis,
                    'nama' => $siswa->nama,
                    'nis' => $siswa->nis,
                    'kelas' => $siswa->kelas,
                    'foto' => $siswa->foto ? asset('storage/' . $siswa->foto) : null,
                    'status_lamaran' => $latestBooking ? $latestBooking->status : 'Belum',
                    'berkas' => optional($siswa->berkas)->lengkap ? 'Lengkap' : 'Kurang',
                    'berkas_files' => [
                        'ktp' => [
                            'status' => optional($siswa->berkas)->ktp_kia ? 'Selesai' : 'Belum',
                            'date' => optional(optional($siswa->berkas)->updated_at)->format('d M Y') ?: '-',
                        ],
                        'sehat' => [
                            'status' => optional($siswa->berkas)->surat_sehat ? 'Selesai' : 'Belum',
                            'date' => optional(optional($siswa->berkas)->updated_at)->format('d M Y') ?: '-',
                        ],
                        'bpjs' => [
                            'status' => optional($siswa->berkas)->kartu_bpjs ? 'Selesai' : 'Belum',
                            'date' => optional(optional($siswa->berkas)->updated_at)->format('d M Y') ?: '-',
                        ],
                    ],
                    'perusahaan' => $dudi?->nama_dudi ?? '-',
                    'perusahaan_id' => $dudi?->id_dudi ?? null,
                    'bidang_industri' => $dudi?->bidang_usaha ?? '-',
                    'alamat' => $dudi?->alamat ?? '-',
                    'jam_berangkat' => $dudi?->jam_masuk ?? '-',
                    'jam_pulang' => $dudi?->jam_pulang ?? '-',
                    'jumlah_pegawai' => $dudi?->jumlah_pegawai ?? '-',
                    'website' => $dudi?->website ?? '-',
                    'telepon' => $dudi?->telepon ?? '-',
                    'email' => $dudi?->email ?? '-',
                    'penanggung_jawab' => $dudi?->pembimbing_dudi ?? '-',
                    'kuota' => $dudi?->kuota ?? 0,
                    'terdaftar' => $dudi ? Booking::where('id_dudi', $dudi->id_dudi)->count() : 0,
                ];
            });

        return view('wali-kelas.dashboard', compact(
            'kelas',
            'totalSiswa',
            'totalBooking',
            'bookingDireview',
            'bookingDiterima',
            'bookingDitolak',
            'siswas'
        ));
    }

    /**
     * Show siswa list for wali_kelas
     */
    public function siswas()
    {
        $user = auth()->user();
        $kelas = $user->kelas_id;
        $siswas = Siswa::where('kelas', $kelas)->paginate(15);

        return view('wali-kelas.siswas', compact('siswas', 'kelas'));
    }

    /**
     * Show bookings for wali_kelas
     */
    public function bookings()
    {
        $user = auth()->user();
        $kelas = $user->kelas_id;
        $bookings = Booking::whereHas('siswa', function($query) use ($kelas) {
            $query->where('kelas', $kelas);
        })->with('siswa')->paginate(15);

        return view('wali-kelas.bookings', compact('bookings', 'kelas'));
    }
}
