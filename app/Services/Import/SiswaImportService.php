<?php

namespace App\Services\Import;

use App\Models\Berkas;
use App\Models\Siswa;
use App\Models\User;
use App\Services\Import\ExcelImportService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SiswaImportService
{
    private ExcelImportService $parser;

    public function __construct(ExcelImportService $parser)
    {
        $this->parser = $parser;
    }

    public function parseFile($file): array
    {
        return $this->parser->parseFile($file);
    }

    public function previewRows(array $rows): array
    {
        $normalizedRows = collect($rows)->map(fn($row) => $this->parser->normalizeSiswaImportRow($row))->toArray();

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

        foreach ($normalizedRows as $index => $row) {
            $rowNumber = $index + 2;
            $validator = Validator::make($row, $this->rules());
            $errors = $validator->fails() ? $validator->errors()->all() : [];

            if (!empty($row['nis']) && isset($existingNis[$row['nis']])) {
                $errors[] = 'NIS sudah terdaftar.';
            }

            if (!empty($row['nis']) && ($rowCounts[$row['nis']] ?? 0) > 1) {
                $errors[] = 'NIS duplikat dalam file.';
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
            'headers' => ['No', 'NIS', 'Nama', 'Kelas', 'Valid', 'Errors'],
            'previewRows' => $results,
            'summary' => [
                'total' => count($results),
                'valid' => $validCount,
                'invalid' => $invalidCount,
            ],
        ];
    }

    public function importRows(array $previewRows): array
    {
        $validRows = collect($previewRows)->filter(fn($row) => $row['valid'])->pluck('data')->values();
        if ($validRows->isEmpty()) {
            return ['imported' => 0, 'skipped' => count($previewRows)];
        }

        $nisList = $validRows->pluck('nis')->unique()->values()->toArray();
        $existingNis = Siswa::whereIn('nis', $nisList)->pluck('nis')->toArray();

        $timestamp = now();
        $siswaRows = [];
        $userRows = [];
        $berkasRows = [];

        foreach ($validRows as $row) {
            if (in_array($row['nis'], $existingNis, true)) {
                continue;
            }

            $siswaRows[] = [
                'nis' => $row['nis'],
                'nama' => $row['nama'],
                'kelas' => $this->normalizeKelas($row['kelas']),
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
        ];
    }

    private function rules(): array
    {
        return [
            'nis' => ['required', 'string', 'max:255', 'regex:/^\d+$/'],
            'nama' => ['required', 'string', 'max:255'],
            'kelas' => ['required', Rule::in(['XII SIJA 1', 'XII SIJA 2', 'XII SIJA 3'])],
        ];
    }

    private function normalizeKelas(string $kelas): string
    {
        $kelas = trim(strtoupper($kelas));
        $kelas = preg_replace('/^(12|XIII)\s+SIJA/i', 'XII SIJA', $kelas);
        return preg_replace('/\s+/', ' ', $kelas);
    }
}
