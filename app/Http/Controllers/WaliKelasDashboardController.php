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
        $kelas = $user->kelas_id ?? $user->kelas ?? null;

        // If user kelas is empty (migrations/seeds not applied), try to fallback to
        // the first available kelas from the siswa table so the page isn't empty.
        if (empty($kelas)) {
            $fallback = Siswa::select('kelas')->distinct()->orderBy('kelas')->pluck('kelas')->first();
            $kelas = $fallback ?? null;
        }

        if ($kelas) {
            $totalSiswa = Siswa::where('kelas', $kelas)->count();
            $totalBooking = Booking::whereHas('siswa', function($query) use ($kelas) {
                $query->where('kelas', $kelas);
            })->count();
            $totalDudi = Booking::whereHas('siswa', function($query) use ($kelas) {
                $query->where('kelas', $kelas);
            })->distinct('id_dudi')->count('id_dudi');
            
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
                ->orderByRaw('nomor_absen IS NULL, nomor_absen asc')
                ->get()
                ->map(function ($siswa) {
                    $latestBooking = $siswa->bookings->sortByDesc('created_at')->first();
                    $dudi = $latestBooking ? $latestBooking->dudi : null;

                    return [
                        'id' => $siswa->nis,
                        'nomor_absen' => $siswa->nomor_absen,
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
        } else {
            // No kelas could be determined; return empty sets to avoid SQL errors
            $totalSiswa = 0;
            $totalBooking = 0;
            $totalDudi = 0;
            $bookingDireview = 0;
            $bookingDiterima = 0;
            $bookingDitolak = 0;
            $siswas = collect([]);
        }

        return view('wali-kelas.dashboard', compact(
            'kelas',
            'totalSiswa',
            'totalBooking',
            'totalDudi',
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
        $kelas = $user->kelas_id ?? $user->kelas ?? null;

        if (empty($kelas)) {
            $kelas = Siswa::select('kelas')->distinct()->orderBy('kelas')->pluck('kelas')->first();
        }

        if ($kelas) {
            $siswas = Siswa::where('kelas', $kelas)
                ->orderByRaw('nomor_absen IS NULL, nomor_absen asc')
                ->paginate(15);
        } else {
            // no kelas determined — show all as fallback
            $siswas = Siswa::orderByRaw('nomor_absen IS NULL, nomor_absen asc')->paginate(15);
        }

        return view('wali-kelas.siswas', compact('siswas', 'kelas'));
    }

    /**
     * Show bookings for wali_kelas
     */
    public function bookings()
    {
        $user = auth()->user();
        $kelas = $user->kelas_id ?? $user->kelas ?? null;

        if (empty($kelas)) {
            $kelas = Siswa::select('kelas')->distinct()->orderBy('kelas')->pluck('kelas')->first();
        }

        if ($kelas) {
            $bookings = Booking::whereHas('siswa', function($query) use ($kelas) {
                $query->where('kelas', $kelas);
            })->with('siswa')->paginate(15);
        } else {
            // fallback to all bookings
            $bookings = Booking::with('siswa')->paginate(15);
        }

        return view('wali-kelas.bookings', compact('bookings', 'kelas'));
    }
}
