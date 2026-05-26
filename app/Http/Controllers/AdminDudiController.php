<?php

namespace App\Http\Controllers;

use App\Models\Dudi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
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
        $status = $request->input('status');
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

        if ($status && $this->hasDudiStatusColumn() && in_array($status, ['active', 'inactive'])) {
            $dudis->where('status', $status);
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

        return view('admin.dudi.index', compact('dudis', 'search', 'bidang', 'status', 'sortBy', 'totalDudi', 'allBidang', 'totalKuota', 'bukuTerdaftar'));
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
            'status' => 'nullable|in:active,inactive',
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
        if ($this->hasDudiStatusColumn()) {
            if (!isset($validated['status'])) {
                $validated['status'] = 'active';
            }
        } else {
            unset($validated['status']);
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
            'status' => 'nullable|in:active,inactive',
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

        if ($this->hasDudiStatusColumn()) {
            if (!isset($validated['status'])) {
                $validated['status'] = $dudi->status ?? 'active';
            }
        } else {
            unset($validated['status']);
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
                $dudi->status,
                $dudi->logo ? asset('storage/'.$dudi->logo) : null,
            ];
        })->toArray();

        return $this->streamCsvDownload(
            'dudi_export_' . now()->format('Y-m-d') . '.csv',
            ['Nama DUDI', 'Alamat', 'Telepon', 'Email', 'Bidang Industri', 'Website', 'Jumlah Pegawai', 'Pembimbing', 'Jam Masuk', 'Jam Pulang', 'Kota', 'Kuota', 'Status', 'Logo URL'],
            $rows
        );
    }

    public function downloadImportTemplate()
    {
        return $this->streamCsvDownload(
            'dudi_import_template.csv',
            ['Nama DUDI', 'Alamat', 'Telepon', 'Email', 'Bidang Industri', 'Website', 'Jumlah Pegawai', 'Pembimbing', 'Jam Masuk', 'Jam Pulang', 'Kota', 'Kuota', 'Status', 'Logo'],
            [
                ['PT. Mitra Sukses', 'Jl. Merdeka No. 10', '081234567890', 'info@mitrasukses.co.id', 'Teknologi Informasi', 'www.mitrasukses.co.id', '100', 'Ibu Sari', '08:00', '16:00', 'Kota Malang', '10', 'active', 'mitrasukses.jpg'],
                ['CV. Harapan Bangsa', 'Jl. Kemerdekaan No. 22', '087654321098', 'contact@harapanbangsa.id', 'Manufaktur', 'www.harapanbangsa.id', '50', 'Bapak Agus', '09:00', '17:00', 'Kota Surabaya', '8', 'inactive', 'harapanbangsa.png'],
            ]
        );
    }

    public function previewImport(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx|max:5120',
            'zip' => 'nullable|file|mimes:zip|max:51200',
        ]);

        $zipPath = null;
        if ($request->hasFile('zip')) {
            $zipPath = $request->file('zip')->storeAs('imports/temp', uniqid('dudi_logos_') . '.zip');
            Session::put('admin_dudi_import_zip', $zipPath);
        } else {
            Session::forget('admin_dudi_import_zip');
        }

        $rows = $this->parseImportFile($request->file('file'));
        if (empty($rows)) {
            if ($zipPath) {
                Storage::disk('local')->delete($zipPath);
                Session::forget('admin_dudi_import_zip');
            }

            return redirect()->route('admin.dudi.create')->with('error', 'File tidak dapat dibaca. Gunakan format CSV atau XLSX dengan header yang tepat.');
        }

        $preview = $this->previewDudiRows($rows, $zipPath);
        Session::put('admin_dudi_import_preview', $preview['previewRows']);

        return view('admin.dudi.create', [
            'previewRows' => $preview['previewRows'],
            'previewSummary' => $preview['summary'],
            'previewHeaders' => $preview['headers'],
            'previewMode' => true,
            'zipUploaded' => $zipPath !== null,
        ]);
    }

    public function import(Request $request)
    {
        $previewRows = Session::get('admin_dudi_import_preview', []);
        $zipPath = Session::get('admin_dudi_import_zip');

        if (empty($previewRows)) {
            $validated = $request->validate([
                'file' => 'required|file|mimes:csv,txt,xlsx|max:5120',
                'zip' => 'nullable|file|mimes:zip|max:51200',
            ]);

            if ($request->hasFile('zip')) {
                $zipPath = $request->file('zip')->storeAs('imports/temp', uniqid('dudi_logos_') . '.zip');
            }

            $rows = $this->parseImportFile($request->file('file'));
            if (empty($rows)) {
                if ($zipPath) {
                    Storage::disk('local')->delete($zipPath);
                }
                return redirect()->route('admin.dudi.index')->with('error', 'File tidak dapat dibaca. Gunakan format CSV atau XLSX dengan header yang tepat.');
            }

            $previewRows = $this->previewDudiRows($rows, $zipPath)['previewRows'];
        }

        $result = $this->importDudiRows($previewRows, $zipPath);
        Session::forget(['admin_dudi_import_preview', 'admin_dudi_import_zip']);
        if ($zipPath) {
            Storage::disk('local')->delete($zipPath);
        }

        $message = "{$result['imported']} DUDI berhasil diimpor";
        if ($result['skipped'] > 0) {
            $message .= ", {$result['skipped']} baris dilewati karena nama DUDI kosong atau sudah ada";
        }
        if ($result['logosSaved'] > 0) {
            $message .= ", {$result['logosSaved']} logo disimpan";
        }
        if ($result['logosMissing'] > 0) {
            $message .= ", {$result['logosMissing']} logo tidak ditemukan";
        }
        if ($result['logosInvalid'] > 0) {
            $message .= ", {$result['logosInvalid']} logo tidak valid";
        }

        return redirect()->route('admin.dudi.index')->with('success', $message);
    }

    private function previewDudiRows(array $rows, ?string $zipPath = null): array
    {
        $zipMap = $this->buildZipImageMap($zipPath);
        $existingNames = Dudi::whereIn('nama_dudi', collect($rows)
            ->map(fn($row) => $this->normalizeDudiImportRow($row)['nama_dudi'])
            ->filter()
            ->unique()
            ->values()
            ->toArray()
        )->pluck('nama_dudi')
         ->mapWithKeys(fn($item) => [$item => true])
         ->toArray();

        $rowCounts = collect($rows)
            ->map(fn($row) => $this->normalizeDudiImportRow($row)['nama_dudi'])
            ->filter()
            ->countBy()
            ->toArray();

        $results = [];
        $logoSummary = ['found' => 0, 'missing' => 0, 'invalid' => 0, 'warnings' => []];

        foreach ($rows as $index => $row) {
            $normalized = $this->normalizeDudiImportRow($row);
            $rowNumber = $index + 2;
            $validator = \Validator::make($normalized, $this->dudiPreviewRules(), $this->dudiPreviewMessages());
            $errors = $validator->fails() ? $validator->errors()->all() : [];

            if (!empty($normalized['nama_dudi']) && ($rowCounts[$normalized['nama_dudi']] ?? 0) > 1) {
                $errors[] = 'Nama DUDI duplikat dalam file.';
            }

            $logoData = $this->resolveLogoReference($normalized['logo'] ?? '', $zipMap, $zipPath);
            $normalized['logo_status'] = $logoData['status'];
            $normalized['logo_warning'] = $logoData['warning'] ?? null;

            if ($logoData['status'] === 'found') {
                $logoSummary['found']++;
            } elseif ($logoData['status'] === 'missing') {
                $logoSummary['missing']++;
                if ($logoData['warning']) {
                    $logoSummary['warnings'][] = $logoData['warning'];
                }
            } elseif ($logoData['status'] === 'invalid') {
                $logoSummary['invalid']++;
                if ($logoData['warning']) {
                    $logoSummary['warnings'][] = $logoData['warning'];
                }
            }

            if (!empty($normalized['nama_dudi']) && isset($existingNames[$normalized['nama_dudi']])) {
                $normalized['existing'] = true;
            }

            $results[] = [
                'row_number' => $rowNumber,
                'data' => $normalized,
                'valid' => empty($errors),
                'errors' => array_values(array_unique($errors)),
            ];
        }

        $validCount = collect($results)->where('valid', true)->count();

        return [
            'headers' => ['No', 'Nama DUDI', 'Alamat', 'Telepon', 'Email', 'Bidang Usaha', 'Website', 'Jumlah Pegawai', 'Pembimbing', 'Jam Masuk', 'Jam Pulang', 'Kota', 'Kuota', 'Logo', 'Status', 'Status Logo', 'Peringatan', 'Valid', 'Errors'],
            'previewRows' => $results,
            'summary' => [
                'total' => count($results),
                'valid' => $validCount,
                'invalid' => count($results) - $validCount,
                'logos' => $logoSummary,
            ],
        ];
    }

    private function importDudiRows(array $previewRows, ?string $zipPath = null): array
    {
        $validRows = collect($previewRows)
            ->filter(fn($row) => $row['valid'])
            ->pluck('data')
            ->values();

        if ($validRows->isEmpty()) {
            return [
                'imported' => 0,
                'skipped' => count($previewRows),
                'logosSaved' => 0,
                'logosMissing' => 0,
                'logosInvalid' => 0,
            ];
        }

        $zipMap = $this->buildZipImageMap($zipPath);
        $imported = 0;
        $skipped = 0;
        $logosSaved = 0;
        $logosMissing = 0;
        $logosInvalid = 0;

        foreach ($validRows as $row) {
            $logoPath = null;
            if (!empty($row['logo'])) {
                $logoData = $this->resolveLogoReference($row['logo'], $zipMap, $zipPath);
                if ($logoData['status'] === 'found' && !empty($logoData['zip_name'])) {
                    $stored = $this->storeLogoFromZip($zipPath, $logoData['zip_name'], $row['nama_dudi']);
                    if ($stored) {
                        $logoPath = $stored;
                        $logosSaved++;
                    } else {
                        $logosInvalid++;
                    }
                } elseif ($logoData['status'] === 'storage_path') {
                    $logoPath = $logoData['storage_path'];
                    $logosSaved++;
                } else {
                    $logosMissing++;
                }
            }

            $data = [
                'alamat' => $row['alamat'] ?: null,
                'telepon' => $row['telepon'] ?: null,
                'email' => $row['email'] ?: null,
                'deskripsi' => $row['deskripsi'] ?: null,
                'bidang_usaha' => $row['bidang_usaha'] ?: null,
                'website' => $row['website'] ?: null,
                'jumlah_pegawai' => $row['jumlah_pegawai'] ?: null,
                'pembimbing_dudi' => $row['pembimbing_dudi'] ?: null,
                'jam_masuk' => $row['jam_masuk'] ?: null,
                'jam_pulang' => $row['jam_pulang'] ?: null,
                'kota' => $row['kota'] ?: null,
                'kuota' => is_numeric($row['kuota'] ?? null) ? (int) $row['kuota'] : 5,
                'logo' => $logoPath,
            ];
            if ($this->hasDudiStatusColumn()) {
                $data['status'] = $row['status'] ?? 'active';
            }

            $existing = Dudi::where('nama_dudi', $row['nama_dudi'])->first();
            if ($existing) {
                $existing->update($data);
                $skipped++;
                continue;
            }

            Dudi::create(array_merge(['nama_dudi' => $row['nama_dudi']], $data));
            $imported++;
        }

        return [
            'imported' => $imported,
            'skipped' => $skipped,
            'logosSaved' => $logosSaved,
            'logosMissing' => $logosMissing,
            'logosInvalid' => $logosInvalid,
        ];
    }

    private function normalizeDudiImportRow(array $row): array
    {
        $row = collect($row)->mapWithKeys(fn($value, $key) => [trim(strtolower($key)) => trim((string) $value)])->toArray();

        return [
            'nama_dudi' => $row['nama_dudi'] ?? $row['name'] ?? $row['dudi_name'] ?? $row['nama'] ?? '',
            'alamat' => $row['alamat'] ?? $row['address'] ?? null,
            'telepon' => $row['telepon'] ?? $row['phone'] ?? null,
            'email' => $row['email'] ?? null,
            'deskripsi' => $row['deskripsi'] ?? $row['description'] ?? null,
            'bidang_usaha' => $row['bidang_usaha'] ?? $row['bidang'] ?? null,
            'website' => $row['website'] ?? null,
            'jumlah_pegawai' => $row['jumlah_pegawai'] ?? $row['jumlah'] ?? null,
            'pembimbing_dudi' => $row['pembimbing_dudi'] ?? $row['pembimbing'] ?? null,
            'jam_masuk' => $row['jam_masuk'] ?? null,
            'jam_pulang' => $row['jam_pulang'] ?? null,
            'kota' => $row['kota'] ?? null,
            'kuota' => $row['kuota'] ?? null,
            'logo' => $row['logo'] ?? null,
        ];
            if ($this->hasDudiStatusColumn()) {
                $data['status'] = $row['status'] ?? 'active';
            }
    }

    private function dudiPreviewRules(): array
    {
        return [
            'nama_dudi' => ['required', 'string', 'max:255'],
            'alamat' => ['nullable', 'string', 'max:500'],
            'telepon' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
            'bidang_usaha' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'string', 'max:255'],
            'jumlah_pegawai' => ['nullable', 'string', 'max:255'],
            'pembimbing_dudi' => ['nullable', 'string', 'max:255'],
            'jam_masuk' => ['nullable', 'string', 'max:20'],
            'jam_pulang' => ['nullable', 'string', 'max:20'],
            'kota' => ['nullable', 'string', 'max:255'],
            'kuota' => ['nullable', 'integer', 'min:0'],
            'status' => ['nullable', 'in:active,inactive'],
            'logo' => ['nullable', 'string', 'max:255'],
        ];
    }

    private function hasDudiStatusColumn(): bool
    {
        return Schema::hasColumn('dudis', 'status');
    }

    private function dudiPreviewMessages(): array
    {
        return [
            'nama_dudi.required' => 'Nama DUDI wajib diisi.',
            'nama_dudi.max' => 'Nama DUDI maksimal 255 karakter.',
            'email.email' => 'Email tidak valid.',
            'email.max' => 'Email maksimal 255 karakter.',
            'kuota.integer' => 'Kuota harus berupa angka.',
            'kuota.min' => 'Kuota minimal 0.',
        ];
    }

    private function buildZipImageMap(?string $zipPath): array
    {
        if (!$zipPath || !Storage::disk('local')->exists($zipPath)) {
            return [];
        }

        $zip = new \ZipArchive();
        $map = [];
        if ($zip->open(Storage::disk('local')->path($zipPath)) !== true) {
            return [];
        }

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            if ($name === null || str_ends_with($name, '/')) {
                continue;
            }
            $basename = pathinfo($name, PATHINFO_BASENAME);
            $normalized = $this->normalizeImageKey($basename);
            if ($normalized === '') {
                continue;
            }
            $map[$normalized] = $name;
        }

        $zip->close();
        return $map;
    }

    private function resolveLogoReference(string $logo, array $zipMap, ?string $zipPath): array
    {
        $logo = trim($logo);
        if ($logo === '') {
            return ['status' => 'missing', 'zip_name' => null, 'warning' => 'Logo tidak ditemukan dalam file ZIP atau path penyimpanan.'];
        }

        if (filter_var($logo, FILTER_VALIDATE_URL)) {
            $storageSegment = '/storage/';
            $position = strpos($logo, $storageSegment);
            if ($position !== false) {
                $relativePath = substr($logo, $position + strlen($storageSegment));
                if (Storage::disk('public')->exists($relativePath)) {
                    return ['status' => 'storage_path', 'storage_path' => $relativePath, 'zip_name' => null, 'warning' => null];
                }
            }
            return ['status' => 'missing', 'zip_name' => null, 'warning' => 'Logo tidak ditemukan dalam file ZIP atau path penyimpanan.'];
        }

        if (Storage::disk('public')->exists($logo)) {
            return ['status' => 'storage_path', 'storage_path' => $logo, 'zip_name' => null, 'warning' => null];
        }

        if (empty($zipMap)) {
            return ['status' => 'missing', 'zip_name' => null, 'warning' => 'Logo tidak ditemukan dalam file ZIP atau path penyimpanan.'];
        }

        $normalized = $this->normalizeImageKey($logo);
        if (!isset($zipMap[$normalized])) {
            return ['status' => 'missing', 'zip_name' => null, 'warning' => 'Logo tidak ditemukan dalam file ZIP atau path penyimpanan.'];
        }

        return ['status' => 'found', 'zip_name' => $zipMap[$normalized], 'warning' => null];
    }

    private function storeLogoFromZip(string $zipPath, string $zipImageName, string $namaDudi): ?string
    {
        $zip = new \ZipArchive();
        if ($zip->open(Storage::disk('local')->path($zipPath)) !== true) {
            return null;
        }

        $contents = $zip->getFromName($zipImageName);
        $zip->close();

        if ($contents === false) {
            return null;
        }

        $extension = strtolower(pathinfo($zipImageName, PATHINFO_EXTENSION));
        if (!in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp'], true)) {
            return null;
        }

        $filename = $this->sanitizeFilename(pathinfo($zipImageName, PATHINFO_FILENAME));
        $targetName = sprintf('dudis/%s_%s.%s', $filename ?: 'logo', uniqid(strtolower(preg_replace('/[^a-z0-9]+/', '_', $namaDudi)) . '_', true), $extension);

        Storage::disk('public')->put($targetName, $contents);

        return $targetName;
    }

    private function normalizeImageKey(string $filename): string
    {
        $filename = trim(strtolower($filename));
        $filename = pathinfo($filename, PATHINFO_BASENAME);
        $filename = preg_replace('/[^a-z0-9\.\-_]+/', '_', $filename);
        $filename = preg_replace('/_+/', '_', $filename);
        return trim($filename, '_');
    }

    private function sanitizeFilename(string $filename): string
    {
        $filename = preg_replace('/[^a-z0-9\-_]+/', '_', strtolower($filename));
        return trim($filename, '_') ?: 'logo';
    }
}
