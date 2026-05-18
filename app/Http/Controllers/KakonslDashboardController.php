<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Booking;

class KakonslDashboardController extends Controller
{
    /**
     * Show kakonsli dashboard - can see data from both classes
     */
    public function index()
    {
        $user = auth()->user();
        $kelas1 = $user->kelas_id;
        $kelas2 = $user->kelas_second;

        $totalSiswa = Siswa::whereIn('kelas', [$kelas1, $kelas2])->count();
        $totalBooking = Booking::whereHas('siswa', function($query) use ($kelas1, $kelas2) {
            $query->whereIn('kelas', [$kelas1, $kelas2]);
        })->count();
        
        $bookingDireview = Booking::whereHas('siswa', function($query) use ($kelas1, $kelas2) {
            $query->whereIn('kelas', [$kelas1, $kelas2]);
        })->where('status', 'Direview')->count();
        
        $bookingDiterima = Booking::whereHas('siswa', function($query) use ($kelas1, $kelas2) {
            $query->whereIn('kelas', [$kelas1, $kelas2]);
        })->where('status', 'Diterima')->count();
        
        $bookingDitolak = Booking::whereHas('siswa', function($query) use ($kelas1, $kelas2) {
            $query->whereIn('kelas', [$kelas1, $kelas2]);
        })->where('status', 'Ditolak')->count();

        $kelasOptions = Siswa::whereIn('kelas', [$kelas1, $kelas2])
            ->select('kelas')
            ->distinct()
            ->orderBy('kelas')
            ->pluck('kelas')
            ->values()
            ->all();

        $siswas = Siswa::with(['berkas', 'bookings.dudi'])
            ->whereIn('kelas', [$kelas1, $kelas2])
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

        return view('kakonsli.dashboard', compact(
            'kelas1',
            'kelas2',
            'totalSiswa',
            'totalBooking',
            'bookingDireview',
            'bookingDiterima',
            'bookingDitolak',
            'siswas',
            'kelasOptions'
        ));
    }

    /**
     * Show siswa list for kakonsli - can see both classes
     */
    public function siswas()
    {
        $user = auth()->user();
        $kelas1 = $user->kelas_id;
        $kelas2 = $user->kelas_second;
        $siswas = Siswa::whereIn('kelas', [$kelas1, $kelas2])->paginate(15);

        return view('kakonsli.siswas', compact('siswas', 'kelas1', 'kelas2'));
    }

    /**
     * Show bookings for kakonsli - can see both classes
     */
    public function bookings()
    {
        $user = auth()->user();
        $kelas1 = $user->kelas_id;
        $kelas2 = $user->kelas_second;
        $bookings = Booking::whereHas('siswa', function($query) use ($kelas1, $kelas2) {
            $query->whereIn('kelas', [$kelas1, $kelas2]);
        })->with('siswa')->paginate(15);

        return view('kakonsli.bookings', compact('bookings', 'kelas1', 'kelas2'));
    }
}
