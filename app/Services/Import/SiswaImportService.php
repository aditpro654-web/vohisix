<?php

namespace App\Services\Import;

use App\Models\Berkas;
use App\Models\Siswa;
use App\Models\User;
use App\Services\Import\ExcelImportService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use ZipArchive;

class SiswaImportService
{
    private const ALLOWED_IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png'];
    private const MAX_IMAGE_SIZE = 5 * 1024 * 1024; // 5 MB
    private const IMAGE_DIR = 'uploads';

    private ExcelImportService $parser;

    public function __construct(ExcelImportService $parser)
    {
        $this->parser = $parser;
    }

    public function parseFile($file): array
    {
        return $this->parser->parseFile($file);
    }

    public function previewRows(array $rows, ?string $zipPath = null): array
    {
        $normalizedRows = collect($rows)
            ->map(fn($row) => $this->parser->normalizeSiswaImportRow($row))
            ->toArray();

        $imageMap = $this->buildZipImageMap($zipPath);
        $existingNis = Siswa::whereIn('nis', collect($normalizedRows)
            ->pluck('nis')
            ->filter()
            ->unique()
            ->values()
            ->toArray()
        )->pluck('nis')
         ->mapWithKeys(fn($item) => [$item => true])
         ->toArray();

        $rowCounts = collect($normalizedRows)
            ->pluck('nis')
            ->filter()
            ->countBy()
            ->toArray();

        $results = [];
        $imageSummary = ['found' => 0, 'missing' => 0, 'invalid' => 0, 'warnings' => []];

        foreach ($normalizedRows as $index => $row) {
            $rowNumber = $index + 2;
            $validator = Validator::make($row, $this->rules());
            $errors = $validator->fails() ? $validator->errors()->all() : [];

            if (!empty($row['nis']) && isset($existingNis[$row['nis']])) {
                $errors[] = 'NIS sudah terdaftar.';
            }

            if (!empty($row['nomor_absen']) && Siswa::where('nomor_absen', $row['nomor_absen'])->exists()) {
                $errors[] = 'Nomor absen sudah digunakan.';
            }

            if (!empty($row['nis']) && ($rowCounts[$row['nis']] ?? 0) > 1) {
                $errors[] = 'NIS duplikat dalam file.';
            }

            $imageData = $this->resolveImageReference($row['foto'] ?? '', $imageMap, $zipPath);
            $row['image_status'] = $imageData['status'];
            $row['zip_image'] = $imageData['zip_image'];
            $row['image_warning'] = $imageData['warning'];

            if ($imageData['status'] === 'found') {
                $imageSummary['found']++;
            } elseif ($imageData['status'] === 'missing') {
                $imageSummary['missing']++;
                if ($imageData['warning']) {
                    $imageSummary['warnings'][] = $imageData['warning'];
                }
            } elseif ($imageData['status'] === 'invalid') {
                $imageSummary['invalid']++;
                if ($imageData['warning']) {
                    $imageSummary['warnings'][] = $imageData['warning'];
                }
            }

            $results[] = [
                'row_number' => $rowNumber,
                'data' => $row,
                'valid' => empty($errors),
                'errors' => array_values(array_unique($errors)),
            ];
        }

        $validCount = collect($results)->where('valid', true)->count();
        $invalidCount = count($results) - $validCount;

        return [
            'headers' => ['No', 'NIS', 'Nomor Absen', 'Nama', 'Kelas', 'Foto', 'Valid', 'Errors'],
            'previewRows' => $results,
            'summary' => [
                'total' => count($results),
                'valid' => $validCount,
                'invalid' => $invalidCount,
                'images' => [
                    'found' => $imageSummary['found'],
                    'missing' => $imageSummary['missing'],
                    'invalid' => $imageSummary['invalid'],
                    'warnings' => array_values(array_unique($imageSummary['warnings'])),
                ],
            ],
        ];
    }

    public function importRows(array $previewRows, ?string $zipPath = null): array
    {
        $validRows = collect($previewRows)
            ->filter(fn($row) => $row['valid'])
            ->pluck('data')
            ->values();

        if ($validRows->isEmpty()) {
            return [
                'imported' => 0,
                'skipped' => count($previewRows),
                'images_processed' => 0,
                'images_missing' => 0,
                'images_invalid' => 0,
            ];
        }

        $nisList = $validRows->pluck('nis')->unique()->values()->toArray();
        $existingNis = Siswa::whereIn('nis', $nisList)->pluck('nis')->toArray();

        $zipMap = $this->buildZipImageMap($zipPath);
        $timestamp = now();
        $siswaRows = [];
        $userRows = [];
        $berkasRows = [];
        $imagesProcessed = 0;
        $imagesMissing = 0;
        $imagesInvalid = 0;

        foreach ($validRows as $row) {
            if (in_array($row['nis'], $existingNis, true)) {
                continue;
            }

            $fotoPath = null;
            if (!empty($row['foto'])) {
                $imageData = $this->resolveImageReference($row['foto'], $zipMap, $zipPath);
                if ($imageData['status'] === 'found' && $zipPath) {
                    $stored = $this->storeImageFromZip($zipPath, $imageData['zip_image'], $row['nis']);
                    if ($stored) {
                        $fotoPath = $stored;
                        $imagesProcessed++;
                    } else {
                        $imagesInvalid++;
                    }
                } else {
                    $imagesMissing++;
                }
            } else {
                $imagesMissing++;
            }

            $siswaRows[] = [
                'nis' => $row['nis'],
                'nomor_absen' => $row['nomor_absen'] ? intval($row['nomor_absen']) : null,
                'nama' => $row['nama'],
                'kelas' => $this->normalizeKelas($row['kelas']),
                'foto' => $fotoPath,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];

            $userRows[] = [
                'username' => $row['nis'],
                'name' => $row['nama'],
                'role' => 'siswa',
                'password' => Hash::make($row['nis']),
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];

            $berkasRows[] = [
                'nis' => $row['nis'],
                'lengkap' => false,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }

        DB::transaction(function () use ($siswaRows, $userRows, $berkasRows) {
            if (!empty($siswaRows)) {
                Siswa::insert($siswaRows);
            }
            if (!empty($userRows)) {
                User::insert($userRows);
            }
            if (!empty($berkasRows)) {
                Berkas::insert($berkasRows);
            }
        });

        return [
            'imported' => count($siswaRows),
            'skipped' => count($previewRows) - count($siswaRows),
            'images_processed' => $imagesProcessed,
            'images_missing' => $imagesMissing,
            'images_invalid' => $imagesInvalid,
        ];
    }

    private function rules(): array
    {
        return [
            'nis' => ['required', 'string', 'max:255', 'regex:/^\d+$/'],
            'nomor_absen' => ['required', 'integer', 'min:1'],
            'nama' => ['required', 'string', 'max:255'],
            'kelas' => ['required', Rule::in(['XII SIJA 1', 'XII SIJA 2'])],
            'foto' => ['nullable', 'string', 'max:255'],
        ];
    }

    private function resolveImageReference(string $filename, array $imageMap, ?string $zipPath): array
    {
        $filename = trim($filename);
        if ($filename === '') {
            return ['status' => 'none', 'zip_image' => null, 'warning' => 'Tidak ada nama gambar pada kolom foto.'];
        }

        $normalizedKey = $this->normalizeImageKey($filename);
        if (empty($imageMap)) {
            return ['status' => 'missing', 'zip_image' => null, 'warning' => 'File ZIP tidak tersedia; gambar default akan digunakan.'];
        }

        if (!isset($imageMap[$normalizedKey])) {
            return ['status' => 'missing', 'zip_image' => null, 'warning' => "Gambar '{$filename}' tidak ditemukan dalam ZIP; default akan digunakan."];
        }

        $zipImageName = $imageMap[$normalizedKey];
        $extension = strtolower(pathinfo($zipImageName, PATHINFO_EXTENSION));
        if (!in_array($extension, self::ALLOWED_IMAGE_EXTENSIONS, true)) {
            return ['status' => 'invalid', 'zip_image' => $zipImageName, 'warning' => "File '{$zipImageName}' bukan tipe gambar yang diizinkan."];
        }

        if (!$zipPath || !Storage::disk('local')->exists($zipPath)) {
            return ['status' => 'missing', 'zip_image' => null, 'warning' => 'File ZIP tidak tersedia saat pemrosesan gambar.'];
        }

        $zip = new ZipArchive();
        if ($zip->open(Storage::disk('local')->path($zipPath)) !== true) {
            return ['status' => 'missing', 'zip_image' => null, 'warning' => 'ZIP tidak dapat dibuka.'];
        }

        $stat = $zip->statName($zipImageName);
        $zip->close();
        if ($stat === false || !isset($stat['size'])) {
            return ['status' => 'missing', 'zip_image' => null, 'warning' => "Ukuran file '{$zipImageName}' tidak dapat dibaca."];
        }

        if ($stat['size'] > self::MAX_IMAGE_SIZE) {
            return ['status' => 'invalid', 'zip_image' => $zipImageName, 'warning' => "Ukuran gambar '{$zipImageName}' melebihi batas 5MB."];
        }

        return ['status' => 'found', 'zip_image' => $zipImageName, 'warning' => null];
    }

    private function buildZipImageMap(?string $zipPath): array
    {
        if (!$zipPath || !Storage::disk('local')->exists($zipPath)) {
            return [];
        }

        $zip = new ZipArchive();
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

    private function storeImageFromZip(string $zipPath, string $zipImageName, string $nis): ?string
    {
        $zip = new ZipArchive();
        if ($zip->open(Storage::disk('local')->path($zipPath)) !== true) {
            return null;
        }

        $contents = $zip->getFromName($zipImageName);
        $zip->close();

        if ($contents === false) {
            return null;
        }

        $extension = strtolower(pathinfo($zipImageName, PATHINFO_EXTENSION));
        if (!in_array($extension, self::ALLOWED_IMAGE_EXTENSIONS, true)) {
            return null;
        }

        $filename = $this->sanitizeImageFilename(pathinfo($zipImageName, PATHINFO_FILENAME));
        $targetName = sprintf('%s/%s_%s.%s', self::IMAGE_DIR, $filename, uniqid($nis . '_', true), $extension);

        Storage::disk('public')->put($targetName, $contents);

        return $targetName;
    }

    private function sanitizeImageFilename(string $name): string
    {
        return Str::slug(preg_replace('/\.[^.]+$/', '', $name)) ?: 'image';
    }

    private function normalizeImageKey(string $filename): string
    {
        $filename = trim(strtolower($filename));
        $filename = pathinfo($filename, PATHINFO_BASENAME);
        $filename = preg_replace('/[^a-z0-9\.\-_]+/', '_', $filename);
        $filename = preg_replace('/_+/', '_', $filename);
        return trim($filename, '_');
    }

    private function normalizeKelas(string $kelas): string
    {
        $kelas = trim(strtoupper($kelas));
        $kelas = preg_replace('/^(12|XIII)\s+SIJA/i', 'XII SIJA', $kelas);
        return preg_replace('/\s+/', ' ', $kelas);
    }
}
