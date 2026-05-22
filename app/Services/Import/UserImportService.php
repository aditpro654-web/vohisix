<?php

namespace App\Services\Import;

use App\Models\User;
use App\Services\Import\ExcelImportService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserImportService
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
        $results = [];
        $existingUsernames = User::whereIn('username', collect($rows)
            ->map(fn($row) => $this->parser->normalizeUserImportRow($row)['username'])
            ->filter()
            ->unique()
            ->values()
            ->toArray()
        )->pluck('username')
         ->mapWithKeys(fn($item) => [$item => true])
         ->toArray();

        $rowCounts = collect($rows)
            ->map(fn($row) => $this->parser->normalizeUserImportRow($row)['username'])
            ->filter()
            ->countBy()
            ->toArray();

        foreach ($rows as $index => $row) {
            $normalized = $this->parser->normalizeUserImportRow($row);
            $rowNumber = $index + 2;
            $validator = Validator::make($normalized, $this->rules($normalized), $this->messages());

            $errors = $validator->fails() ? $validator->errors()->all() : [];

            if (!empty($normalized['username']) && isset($existingUsernames[$normalized['username']])) {
                $errors[] = 'Username sudah terdaftar.';
            }

            if (!empty($normalized['username']) && ($rowCounts[$normalized['username']] ?? 0) > 1) {
                $errors[] = 'Username duplikat dalam file.';
            }

            $results[] = [
                'row_number' => $rowNumber,
                'data' => $normalized,
                'valid' => empty($errors),
                'errors' => array_values(array_unique($errors)),
            ];
        }

        $validCount = collect($results)->where('valid', true)->count();
        $invalidCount = count($results) - $validCount;

        return [
            'headers' => ['No', 'Username', 'Name', 'Role', 'Password', 'Kelas Utama', 'Kelas Kedua', 'Valid', 'Errors'],
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

        $usernames = $validRows->pluck('username')->unique()->values()->toArray();
        $existingUsernames = User::whereIn('username', $usernames)->pluck('username')->toArray();

        $insertRows = [];
        foreach ($validRows as $row) {
            if (in_array($row['username'], $existingUsernames, true)) {
                continue;
            }

            $password = $row['password'] ?: $row['username'];
            $insertRows[] = [
                'username' => $row['username'],
                'name' => $row['name'],
                'role' => $row['role'],
                'kelas_id' => $row['kelas_id'] ?: null,
                'kelas_second' => $row['kelas_second'] ?: null,
                'password' => Hash::make($password),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::transaction(function () use ($insertRows) {
            if (!empty($insertRows)) {
                User::insert($insertRows);
            }
        });

        return [
            'imported' => count($insertRows),
            'skipped' => count($previewRows) - count($insertRows),
        ];
    }

    private function rules(array $row): array
    {
        $rules = [
            'username' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'role' => ['required', Rule::in(['admin', 'siswa', 'wali_kelas', 'kakonsli'])],
            'password' => [Rule::requiredIf(fn () => ($row['role'] ?? '') !== 'siswa'), 'nullable', 'string', 'min:6'],
            'kelas_id' => [Rule::requiredIf(fn () => in_array($row['role'] ?? '', ['wali_kelas', 'kakonsli'], true)), Rule::in(['XII SIJA 1', 'XII SIJA 2'])],
            'kelas_second' => [Rule::requiredIf(fn () => ($row['role'] ?? '') === 'kakonsli'), Rule::in(['XII SIJA 1', 'XII SIJA 2'])],
        ];

        if (($row['role'] ?? '') === 'siswa') {
            $rules['username'][] = 'numeric';
        }

        return $rules;
    }

    private function messages(): array
    {
        return [
            'username.required' => 'Username wajib diisi.',
            'username.max' => 'Username maksimal 255 karakter.',
            'username.numeric' => 'Username siswa harus berupa NIS angka.',
            'name.required' => 'Nama wajib diisi.',
            'name.max' => 'Nama maksimal 255 karakter.',
            'role.required' => 'Role wajib dipilih.',
            'role.in' => 'Role tidak valid.',
            'password.required_if' => 'Password wajib diisi untuk role selain siswa.',
            'password.min' => 'Password minimal 6 karakter.',
            'kelas_id.required_if' => 'Kelas Utama wajib diisi untuk Wali Kelas atau Kakonsli.',
            'kelas_id.in' => 'Kelas Utama harus XII SIJA 1 atau XII SIJA 2.',
            'kelas_second.required_if' => 'Kelas Kedua wajib diisi untuk Kakonsli.',
            'kelas_second.in' => 'Kelas Kedua harus XII SIJA 1 atau XII SIJA 2.',
        ];
    }
}
