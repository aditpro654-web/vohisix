<?php

namespace App\Services\Import;

use App\Imports\GenericHeadingRowImport;
use App\Http\Controllers\Traits\ExcelImportTrait;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ExcelImportService
{
    use ExcelImportTrait;

    public function parseFile(UploadedFile $file): array
    {
        if (class_exists(\Maatwebsite\Excel\Facades\Excel::class)) {
            $extension = strtolower($file->getClientOriginalExtension());
            if (in_array($extension, ['csv', 'txt'])) {
                $delimiter = $this->detectDelimiter($file->getRealPath());
                $sheets = \Maatwebsite\Excel\Facades\Excel::toArray(new GenericHeadingRowImport($delimiter), $file);
            } else {
                $sheets = \Maatwebsite\Excel\Facades\Excel::toArray(new GenericHeadingRowImport(), $file);
            }

            return $sheets[0] ?? [];
        }

        return $this->parseImportFile($file);
    }

    public function detectDelimiter(string $path): string
    {
        $handle = fopen($path, 'r');
        if ($handle === false) {
            return ';';
        }

        $firstLine = fgets($handle);
        fclose($handle);

        if ($firstLine === false) {
            return ';';
        }

        $semicolonCount = substr_count($firstLine, ';');
        $commaCount = substr_count($firstLine, ',');

        return $semicolonCount >= $commaCount ? ';' : ',';
    }

    public function normalizeRowKeys(array $row): array
    {
        return collect($row)
            ->mapWithKeys(function ($value, $key) {
                $normalizedKey = Str::of($key)
                    ->lower()
                    ->replace([' ', '\t', '\r', '\n'], '_')
                    ->replaceMatches('/[^a-z0-9_]+/', '_')
                    ->replaceMatches('/_+/', '_')
                    ->trim('_')
                    ->toString();

                return [$normalizedKey => trim((string) $value)];
            })
            ->toArray();
    }

    public function normalizeUserImportRow(array $row): array
    {
        $row = $this->normalizeRowKeys($row);

        $mapping = [
            'user_name' => 'username',
            'user' => 'username',
            'nama' => 'name',
            'full_name' => 'name',
            'kelas_utama' => 'kelas_id',
            'kelas utama' => 'kelas_id',
            'kelas_id' => 'kelas_id',
            'kelas_kedua' => 'kelas_second',
            'kelas kedua' => 'kelas_second',
            'kelas_second' => 'kelas_second',
            'nis' => 'username',
        ];

        $normalized = [];
        foreach ($row as $key => $value) {
            if (isset($mapping[$key])) {
                $normalized[$mapping[$key]] = $value;
                continue;
            }

            $normalized[$key] = $value;
        }

        return [
            'username' => $normalized['username'] ?? '',
            'name' => $normalized['name'] ?? '',
            'role' => strtolower($normalized['role'] ?? ''),
            'password' => $normalized['password'] ?? '',
            'kelas_id' => strtoupper($normalized['kelas_id'] ?? ''),
            'kelas_second' => strtoupper($normalized['kelas_second'] ?? ''),
        ];
    }

    public function normalizeSiswaImportRow(array $row): array
    {
        $row = $this->normalizeRowKeys($row);

        return [
            'nis' => $row['nis'] ?? $row['username'] ?? '',
            'nomor_absen' => $row['nomor_absen'] ?? $row['absen'] ?? '',
            'nama' => $row['nama'] ?? $row['name'] ?? '',
            'kelas' => strtoupper(trim($row['kelas'] ?? '')),
            'foto' => $row['foto'] ?? '',
        ];
    }
}
