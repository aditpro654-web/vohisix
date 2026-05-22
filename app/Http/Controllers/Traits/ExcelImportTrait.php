<?php

namespace App\Http\Controllers\Traits;

use ZipArchive;

trait ExcelImportTrait
{
    /**
     * Parse uploaded CSV or XLSX file into an array of rows.
     * The first row is treated as header.
     * Returns an array of associative rows, or empty array if parsing fails.
     */
    protected function parseImportFile($uploadedFile): array
    {
        $extension = strtolower($uploadedFile->getClientOriginalExtension());
        if (in_array($extension, ['csv', 'txt'])) {
            return $this->parseCsvFile($uploadedFile->getRealPath());
        }

        if ($extension === 'xlsx') {
            return $this->parseXlsxFile($uploadedFile->getRealPath());
        }

        return [];
    }

    protected function parseCsvFile(string $path): array
    {
        $rows = [];
        if (($handle = fopen($path, 'r')) === false) {
            return [];
        }

        $delimiter = $this->detectCsvDelimiter($handle);
        rewind($handle);

        $headers = [];
        while (($line = fgetcsv($handle, 0, $delimiter)) !== false) {
            if (count(array_filter($line, fn($value) => $value !== null && trim($value) !== '')) === 0) {
                continue;
            }

            if (empty($headers)) {
                $headers = array_map([$this, 'normalizeImportHeader'], $line);
                continue;
            }

            $rows[] = array_combine($headers, array_map(fn($value) => trim($value), $line));
        }

        fclose($handle);

        return $rows;
    }

    protected function detectCsvDelimiter($handle): string
    {
        $line = fgets($handle);
        if ($line === false) {
            return ',';
        }

        $semicolonCount = substr_count($line, ';');
        $commaCount = substr_count($line, ',');

        return $semicolonCount >= $commaCount ? ';' : ',';
    }

    protected function parseXlsxFile(string $path): array
    {
        $zip = new ZipArchive();
        $rows = [];

        if ($zip->open($path) !== true) {
            return [];
        }

        $sharedStrings = [];
        if (($index = $zip->locateName('xl/sharedStrings.xml')) !== false) {
            $sharedData = $zip->getFromIndex($index);
            if ($sharedData !== false) {
                $xml = simplexml_load_string($sharedData);
                if ($xml) {
                    foreach ($xml->si as $si) {
                        if (isset($si->t)) {
                            $sharedStrings[] = (string) $si->t;
                        } else {
                            $text = '';
                            foreach ($si->r as $run) {
                                $text .= (string) $run->t;
                            }
                            $sharedStrings[] = $text;
                        }
                    }
                }
            }
        }

        if (($index = $zip->locateName('xl/worksheets/sheet1.xml')) === false) {
            $zip->close();
            return [];
        }

        $sheetData = $zip->getFromIndex($index);
        $zip->close();

        if ($sheetData === false) {
            return [];
        }

        $xml = simplexml_load_string($sheetData);
        if ($xml === false) {
            return [];
        }

        $header = [];
        foreach ($xml->sheetData->row as $rowIndex => $row) {
            $rowValues = [];
            foreach ($row->c as $cell) {
                $coord = (string) $cell['r'];
                $columnIndex = $this->xlsxColumnToIndex(preg_replace('/[0-9]/', '', $coord));
                $value = '';
                if (isset($cell->v)) {
                    $value = (string) $cell->v;
                    if ((string) $cell['t'] === 's' && isset($sharedStrings[(int) $value])) {
                        $value = $sharedStrings[(int) $value];
                    }
                }
                $rowValues[$columnIndex] = trim($value);
            }

            ksort($rowValues);
            $rowValues = array_values($rowValues);

            if (empty($header)) {
                $header = array_map([$this, 'normalizeImportHeader'], $rowValues);
                continue;
            }

            $rows[] = array_combine($header, $rowValues);
        }

        return $rows;
    }

    protected function xlsxColumnToIndex(string $column): int
    {
        $length = strlen($column);
        $index = 0;
        for ($i = 0; $i < $length; $i++) {
            $index *= 26;
            $index += ord($column[$i]) - ord('A') + 1;
        }

        return $index - 1;
    }

    protected function normalizeImportHeader($header): string
    {
        $normalized = trim(strtolower($header));
        $normalized = preg_replace('/[^a-z0-9]+/', '_', $normalized);
        $normalized = preg_replace('/_+/', '_', $normalized);
        return trim($normalized, '_');
    }
}
