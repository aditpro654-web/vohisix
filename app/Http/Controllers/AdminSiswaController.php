<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\User;
use App\Models\Berkas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Traits\ExcelExportTrait;
use App\Http\Controllers\Traits\ExcelImportTrait;

class AdminSiswaController extends Controller
{
    use ExcelImportTrait, ExcelExportTrait;
    /**
     * Display a listing of siswas
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $kelas = $request->input('kelas');
        $sortBy = $request->input('sort_by', 'newest');

        $siswas = Siswa::with('berkas');

        if ($search) {
            $siswas->where(function($q) use ($search) {
                $q->where('nama', 'like', "%$search%")
                  ->orWhere('nis', 'like', "%$search%")
                  ->orWhere('kelas', 'like', "%$search%");
            });
        }

        if ($kelas) {
            $siswas->where('kelas', $kelas);
        }

        // Apply sorting
        switch ($sortBy) {
            case 'oldest':
                $siswas->orderBy('created_at', 'asc');
                break;
            case 'name_asc':
                $siswas->orderBy('nama', 'asc');
                break;
            case 'name_desc':
                $siswas->orderBy('nama', 'desc');
                break;
            default: // newest
                $siswas->orderBy('created_at', 'desc');
        }

        $siswas = $siswas->paginate(10);

        // Get statistics
        $totalSiswa = Siswa::count();
        $allKelas = Siswa::distinct('kelas')->pluck('kelas')->filter()->sort();
        $totalBerkas = Berkas::count();
        $totalBooking = \App\Models\Booking::count();

        return view('admin.siswa.index', compact('siswas', 'search', 'kelas', 'sortBy', 'totalSiswa', 'allKelas', 'totalBerkas', 'totalBooking'));
    }

    /**
     * Show form for creating new siswa
     */
    public function create()
    {
        return view('admin.siswa.create');
    }

    /**
     * Store siswa in database
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nis' => 'required|unique:siswas',
            'nama' => 'required|string|max:255',
            'kelas' => 'required|in:XII SIJA 1,XII SIJA 2,XII SIJA 3',
            // allow a broad set of common image file types; skip the `image` rule so unknown formats
            // (webp/heic/etc.) don't trigger "must be an image" errors
            'foto' => 'nullable|file|mimes:jpg,jpeg,png,webp,gif,bmp,heic,heif|max:2048',
            // password no longer provided here, it's defaulted in create view if needed
        ]);

        $validated['kelas'] = $this->normalizeKelas($validated['kelas']);

        // Generate password default to NIS if not specified (handled at view level now)
        $password = $validated['nis'];

        // Handle photo upload
        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('siswas', 'public');
        }

        // Buat siswa
        Siswa::create([
            'nis' => $validated['nis'],
            'nama' => $validated['nama'],
            'kelas' => $validated['kelas'],
            'foto' => $fotoPath,
        ]);

        // Buat user untuk login
        User::create([
            'username' => $validated['nis'],
            'name' => $validated['nama'],
            'role' => 'siswa',
            'password' => Hash::make($password),
        ]);

        // Buat berkas kosong
        Berkas::create([
            'nis' => $validated['nis'],
            'lengkap' => false,
        ]);

        // Return view dengan credentials
        return redirect()
            ->route('admin.siswa.index')
            ->with('success', 'Siswa berhasil ditambahkan')
            ->with('siswa_created', [
                'username' => $validated['nis'],
                'password' => $password,
                'nama' => $validated['nama']
            ]);
    }

    /**
     * Show siswa details
     */
    public function show(Siswa $siswa)
    {
        return view('admin.siswa.show', compact('siswa'));
    }

    /**
     * Show form for editing siswa
     */
    public function edit(Siswa $siswa)
    {
        return view('admin.siswa.edit', compact('siswa'));
    }

    /**
     * Update siswa in database
     */
    public function update(Request $request, Siswa $siswa)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'kelas' => 'required|in:XII SIJA 1,XII SIJA 2,XII SIJA 3',
            'nis' => 'nullable|string|unique:siswas,nis,' . $siswa->nis . ',nis',
            'foto' => 'nullable|file|mimes:jpg,jpeg,png,webp,gif,bmp,heic,heif|max:2048',
        ]);

        $validated['kelas'] = $this->normalizeKelas($validated['kelas']);

        // Handle new photo upload if present
        if ($request->hasFile('foto')) {
            // delete old photo if exists
            if ($siswa->foto && \Storage::disk('public')->exists($siswa->foto)) {
                \Storage::disk('public')->delete($siswa->foto);
            }
            $validated['foto'] = $request->file('foto')->store('siswas', 'public');
        }

        // Jika NIS berubah, update juga di User dan Berkas
        $nisLama = $siswa->nis;
        if (isset($validated['nis']) && $validated['nis'] !== $nisLama) {
            $nisBaru = $validated['nis'];
            
            // Gunakan transaction dan disable FK checks sementara
            \DB::beginTransaction();
            try {
                \DB::statement('SET FOREIGN_KEY_CHECKS=0');
                
                // Update semua tabel sesuai urutan yang aman
                User::where('username', $nisLama)->update([
                    'username' => $nisBaru,
                    'password' => Hash::make($nisBaru),
                ]);
                
                Berkas::where('nis', $nisLama)->update(['nis' => $nisBaru]);
                $siswa->update($validated);
                
                \DB::statement('SET FOREIGN_KEY_CHECKS=1');
                \DB::commit();
                
                return redirect()->route('admin.siswa.index')->with('success', 'Siswa berhasil diperbarui. Username dan password siswa berubah mengikuti NIS baru.');
            } catch (\Exception $e) {
                \DB::rollBack();
                \DB::statement('SET FOREIGN_KEY_CHECKS=1');
                return back()->with('error', 'Gagal mengubah NIS: ' . $e->getMessage());
            }
        }
        
        $siswa->update($validated);

        return redirect()->route('admin.siswa.index')->with('success', 'Siswa berhasil diperbarui');
    }

    /**
     * Delete siswa
     */
    public function destroy(Siswa $siswa)
    {
        // Hapus user terkait
        User::where('username', $siswa->nis)->delete();

        $siswa->delete();

        return redirect()->route('admin.siswa.index')->with('success', 'Siswa berhasil dihapus');
    }

    public function import(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx|max:5120',
        ]);

        $rows = $this->parseImportFile($request->file('file'));
        if (empty($rows)) {
            return redirect()->route('admin.siswa.index')->with('error', 'File tidak dapat dibaca. Gunakan format CSV atau XLSX dengan header yang tepat.');
        }

        $imported = 0;
        $skipped = 0;
        foreach ($rows as $row) {
            $nis = $row['nis'] ?? null;
            $nama = $row['nama'] ?? null;
            $kelasValue = $row['kelas'] ?? null;

            if (!$nis || !$nama || !$kelasValue) {
                $skipped++;
                continue;
            }

            $existing = Siswa::find($nis);
            if ($existing) {
                $skipped++;
                continue;
            }

            $kelas = $this->normalizeKelas($kelasValue);
            if (!$kelas || !in_array($kelas, ['XII SIJA 1', 'XII SIJA 2', 'XII SIJA 3'], true)) {
                $skipped++;
                continue;
            }

            Siswa::create([
                'nis' => $nis,
                'nama' => $nama,
                'kelas' => $kelas,
            ]);

            User::create([
                'username' => $nis,
                'name' => $nama,
                'role' => 'siswa',
                'password' => Hash::make($nis),
            ]);

            Berkas::create([
                'nis' => $nis,
                'lengkap' => false,
            ]);

            $imported++;
        }

        $message = "$imported siswa berhasil diimpor";
        if ($skipped > 0) {
            $message .= ", $skipped baris dilewati karena data tidak lengkap atau NIS sudah ada";
        }

        return redirect()->route('admin.siswa.index')->with('success', $message);
    }

    public function export(Request $request)
    {
        $search = $request->input('search');
        $kelas = $request->input('kelas');

        $siswas = Siswa::with('berkas');
        if ($search) {
            $siswas->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%$search%")
                  ->orWhere('nis', 'like', "%$search%")
                  ->orWhere('kelas', 'like', "%$search%");
            });
        }
        if ($kelas) {
            $siswas->where('kelas', $kelas);
        }

        $siswas = $siswas->orderBy('created_at', 'desc')->get();
        $rows = $siswas->map(function ($siswa) {
            $berkas = $siswa->berkas;
            return [
                $siswa->nis,
                $siswa->nama,
                $siswa->kelas,
                $berkas?->ktp_kia ? 'Selesai' : 'Belum',
                $berkas?->surat_sehat ? 'Selesai' : 'Belum',
                $berkas?->kartu_bpjs ? 'Selesai' : 'Belum',
            ];
        })->toArray();

        return $this->streamCsvDownload(
            'siswa_export_' . now()->format('Y-m-d') . '.csv',
            ['NIS', 'Nama', 'Kelas', 'KTP/KIA', 'Surat Sehat', 'BPJS'],
            $rows
        );
    }

    private function normalizeKelas(?string $kelas): ?string
    {
        if (!$kelas) {
            return null;
        }

        $kelas = trim(strtoupper($kelas));
        $kelas = preg_replace('/^(12|XIII)\s+SIJA/i', 'XII SIJA', $kelas);
        $kelas = preg_replace('/\s+/', ' ', $kelas);

        return $kelas;
    }
}
