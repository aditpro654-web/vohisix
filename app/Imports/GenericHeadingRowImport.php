<?php

namespace App\Imports;

use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class GenericHeadingRowImport implements ToArray, WithHeadingRow, WithCustomCsvSettings
{
    private string $delimiter;

    public function __construct(string $delimiter = ';')
    {
        $this->delimiter = $delimiter;
    }

    public function array(array $array)
    {
        return $array;
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => $this->delimiter,
            'input_encoding' => 'UTF-8',
        ];
    }
}
