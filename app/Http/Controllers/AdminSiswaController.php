<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminSiswaStoreRequest;
use App\Http\Requests\AdminSiswaImportPreviewRequest;
use App\Models\Siswa;
use App\Models\User;
use App\Models\Berkas;
use App\Services\Import\SiswaImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Traits\ExcelExportTrait;

class AdminSiswaController extends Controller
{
    use ExcelExportTrait;
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
    public function store(AdminSiswaStoreRequest $request)
    {
        $validated = $request->validated();
        $validated['kelas'] = $this->normalizeKelas($validated['kelas']);
        $password = $validated['nis'];

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('siswas', 'public');
        }

        Siswa::create([
            'nis' => $validated['nis'],
            'nama' => $validated['nama'],
            'kelas' => $validated['kelas'],
            'foto' => $fotoPath,
        ]);

        User::create([
            'username' => $validated['nis'],
            'name' => $validated['nama'],
            'role' => 'siswa',
            'password' => Hash::make($password),
        ]);

        Berkas::create([
            'nis' => $validated['nis'],
            'lengkap' => false,
        ]);

        return redirect()
            ->route('admin.siswa.index')
            ->with('success', 'Siswa berhasil ditambahkan')
            ->with('siswa_created', [
                'username' => $validated['nis'],
                'password' => $password,
                'nama' => $validated['nama'],
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

    public function importPreview(AdminSiswaImportPreviewRequest $request, SiswaImportService $importService)
    {
        $rows = $importService->parseFile($request->file('file'));
        if (empty($rows)) {
            return redirect()->route('admin.siswa.create')->with('error', 'File tidak dapat dibaca. Gunakan format CSV atau XLSX dengan header yang tepat.');
        }

        $preview = $importService->previewRows($rows);
        Session::put('admin_siswa_import_preview', $preview['previewRows']);

        return view('admin.siswa.create', [
            'previewRows' => $preview['previewRows'],
            'previewSummary' => $preview['summary'],
            'previewHeaders' => $preview['headers'],
            'previewMode' => true,
        ]);
    }

    public function import(Request $request, SiswaImportService $importService)
    {
        $previewRows = Session::get('admin_siswa_import_preview', []);
        if (empty($previewRows)) {
            return redirect()->route('admin.siswa.create')->with('error', 'Unggah file import terlebih dahulu untuk melihat preview dan mengonfirmasi data.');
        }

        $result = $importService->importRows($previewRows);
        Session::forget('admin_siswa_import_preview');

        $message = "{$result['imported']} siswa berhasil diimpor";
        if ($result['skipped'] > 0) {
            $message .= ", {$result['skipped']} baris dilewati.";
        }

        return redirect()->route('admin.siswa.index')->with('success', $message);
    }

    public function downloadImportTemplate()
    {
        return $this->streamCsvDownload(
            'siswa_import_template.csv',
            ['NIS', 'Nama', 'Kelas'],
            [
                ['1210001', 'Budi Santoso', 'XII SIJA 1'],
                ['1210002', 'Ani Putri', 'XII SIJA 2'],
                ['1210003', 'Candra Wijaya', 'XII SIJA 3'],
            ]
        );
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
