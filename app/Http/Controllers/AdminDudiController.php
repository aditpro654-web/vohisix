<?php

namespace App\Http\Controllers;

use App\Models\Dudi;
use Illuminate\Http\Request;
use App\Http\Controllers\Traits\ExcelExportTrait;
use App\Http\Controllers\Traits\ExcelImportTrait;

class AdminDudiController extends Controller
{
    use ExcelImportTrait, ExcelExportTrait;
    /**
     * Display a listing of dudis
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $bidang = $request->input('bidang_usaha');
        $sortBy = $request->input('sort_by', 'newest');

        $dudis = Dudi::query();

        if ($search) {
            $dudis->where(function($q) use ($search) {
                $q->where('nama_dudi', 'like', "%$search%")
                  ->orWhere('bidang_usaha', 'like', "%$search%")
                  ->orWhere('alamat', 'like', "%$search%");
            });
        }

        if ($bidang) {
            $dudis->where('bidang_usaha', $bidang);
        }

        // Apply sorting
        switch ($sortBy) {
            case 'oldest':
                $dudis->orderBy('created_at', 'asc');
                break;
            case 'name_asc':
                $dudis->orderBy('nama_dudi', 'asc');
                break;
            case 'name_desc':
                $dudis->orderBy('nama_dudi', 'desc');
                break;
            default: // newest
                $dudis->orderBy('created_at', 'desc');
        }

        $dudis = $dudis->paginate(10);

        // Get statistics
        $totalDudi = Dudi::count();
        $allBidang = Dudi::distinct('bidang_usaha')->pluck('bidang_usaha')->filter()->sort();
        $totalKuota = Dudi::sum('kuota') ?? 0;
        $bukuTerdaftar = \App\Models\Booking::whereIn('status', ['Direview', 'Diterima'])->count();

        return view('admin.dudi.index', compact('dudis', 'search', 'bidang', 'sortBy', 'totalDudi', 'allBidang', 'totalKuota', 'bukuTerdaftar'));
    }

    /**
     * Show form for creating new dudi
     */
    public function create()
    {
        return view('admin.dudi.create');
    }

    /**
     * Store dudi in database
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_dudi' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'deskripsi' => 'nullable|string',
            'bidang_usaha' => 'nullable|string|max:255',
            'website' => 'nullable|string|max:255',
            'jumlah_pegawai' => 'nullable|string|max:255',
            'pembimbing_dudi' => 'nullable|string|max:255',
            'jam_masuk' => 'nullable|string|max:20',
            'jam_pulang' => 'nullable|string|max:20',
            'kota' => 'nullable|string|max:255',
            'kuota' => 'nullable|integer|min:0',
            'logo' => 'nullable|file|mimes:jpg,jpeg,png,webp,gif,bmp|max:2048',
        ]);

        // Store logo file if provided
        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('dudis', 'public');
        }

        // Set default kuota if not provided
        if (!isset($validated['kuota'])) {
            $validated['kuota'] = 5;
        }

        Dudi::create($validated);

        return redirect()->route('admin.dudi.index')->with('success', 'DUDI berhasil ditambahkan');
    }

    /**
     * Show dudi details
     */
    public function show(Dudi $dudi)
    {
        return view('admin.dudi.show', compact('dudi'));
    }

    /**
     * Show form for editing dudi
     */
    public function edit(Dudi $dudi)
    {
        return view('admin.dudi.edit', compact('dudi'));
    }

    /**
     * Update dudi in database
     */
    public function update(Request $request, Dudi $dudi)
    {
        $validated = $request->validate([
            'nama_dudi' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'deskripsi' => 'nullable|string',
            'bidang_usaha' => 'nullable|string|max:255',
            'website' => 'nullable|string|max:255',
            'jumlah_pegawai' => 'nullable|string|max:255',
            'pembimbing_dudi' => 'nullable|string|max:255',
            'jam_masuk' => 'nullable|string|max:20',
            'jam_pulang' => 'nullable|string|max:20',
            'kota' => 'nullable|string|max:255',
            'kuota' => 'nullable|integer|min:0',
            'logo' => 'nullable|file|mimes:jpg,jpeg,png,webp,gif,bmp|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            if ($dudi->logo && \Storage::disk('public')->exists($dudi->logo)) {
                \Storage::disk('public')->delete($dudi->logo);
            }
            $validated['logo'] = $request->file('logo')->store('dudis', 'public');
        }

        if (!isset($validated['kuota'])) {
            $validated['kuota'] = $dudi->kuota ?? 5;
        }

        $dudi->update($validated);

        return redirect()->route('admin.dudi.index')->with('success', 'DUDI berhasil diperbarui');
    }

    /**
     * Delete dudi
     */
    public function destroy(Dudi $dudi)
    {
        $dudi->delete();

        return redirect()->route('admin.dudi.index')->with('success', 'DUDI berhasil dihapus');
    }

    public function export(Request $request)
    {
        $search = $request->input('search');
        $bidang = $request->input('bidang_usaha');

        $dudis = Dudi::query();
        if ($search) {
            $dudis->where(function ($q) use ($search) {
                $q->where('nama_dudi', 'like', "%$search%")
                  ->orWhere('bidang_usaha', 'like', "%$search%")
                  ->orWhere('alamat', 'like', "%$search%");
            });
        }
        if ($bidang) {
            $dudis->where('bidang_usaha', $bidang);
        }

        $dudis = $dudis->orderBy('created_at', 'desc')->get();
        $rows = $dudis->map(function ($dudi) {
            return [
                $dudi->nama_dudi,
                $dudi->alamat,
                $dudi->telepon,
                $dudi->email,
                $dudi->bidang_usaha,
                $dudi->website,
                $dudi->jumlah_pegawai,
                $dudi->pembimbing_dudi,
                $dudi->jam_masuk,
                $dudi->jam_pulang,
                $dudi->kota,
                $dudi->kuota,
                $dudi->logo ? asset('storage/'.$dudi->logo) : null,
            ];
        })->toArray();

        return $this->streamCsvDownload(
            'dudi_export_' . now()->format('Y-m-d') . '.csv',
            ['Nama DUDI', 'Alamat', 'Telepon', 'Email', 'Bidang Industri', 'Website', 'Jumlah Pegawai', 'Pembimbing', 'Jam Masuk', 'Jam Pulang', 'Kota', 'Kuota', 'Logo URL'],
            $rows
        );
    }

    public function import(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx|max:5120',
        ]);

        $rows = $this->parseImportFile($request->file('file'));
        if (empty($rows)) {
            return redirect()->route('admin.dudi.index')->with('error', 'File tidak dapat dibaca. Gunakan format CSV atau XLSX dengan header yang tepat.');
        }

        $imported = 0;
        $skipped = 0;
        foreach ($rows as $row) {
            $nama_dudi = $row['nama_dudi'] ?? null;
            if (!$nama_dudi) {
                $skipped++;
                continue;
            }

            $data = [
                'alamat' => $row['alamat'] ?? null,
                'telepon' => $row['telepon'] ?? null,
                'email' => $row['email'] ?? null,
                'deskripsi' => $row['deskripsi'] ?? null,
                'bidang_usaha' => $row['bidang_usaha'] ?? null,
                'website' => $row['website'] ?? null,
                'jumlah_pegawai' => $row['jumlah_pegawai'] ?? null,
                'pembimbing_dudi' => $row['pembimbing_dudi'] ?? null,
                'jam_masuk' => $row['jam_masuk'] ?? null,
                'jam_pulang' => $row['jam_pulang'] ?? null,
                'kota' => $row['kota'] ?? null,
                'kuota' => is_numeric($row['kuota'] ?? null) ? (int) $row['kuota'] : 5,
                'logo' => $row['logo'] ?? null,
            ];

            $existing = Dudi::where('nama_dudi', $nama_dudi)->first();
            if ($existing) {
                $existing->update($data);
                $skipped++;
                continue;
            }

            Dudi::create(array_merge(['nama_dudi' => $nama_dudi], $data));
            $imported++;
        }

        $message = "$imported DUDI berhasil diimpor";
        if ($skipped > 0) {
            $message .= ", $skipped baris dilewati karena nama DUDI kosong atau sudah ada";
        }

        return redirect()->route('admin.dudi.index')->with('success', $message);
    }
}
