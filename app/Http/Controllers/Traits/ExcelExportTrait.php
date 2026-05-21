<?php

namespace App\Http\Controllers\Traits;

trait ExcelExportTrait
{
    protected function streamCsvDownload(string $filename, array $headers, array $rows)
    {
        return response()->streamDownload(function () use ($headers, $rows) {
            $output = fopen('php://output', 'w');
            fwrite($output, "\xEF\xBB\xBF");
            fwrite($output, $this->csvLine($headers));

            foreach ($rows as $row) {
                fwrite($output, $this->csvLine($row));
            }

            fclose($output);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function csvLine(array $row): string
    {
        $escaped = array_map(function ($value) {
            $value = (string) $value;
            return '"' . str_replace('"', '""', $value) . '"';
        }, $row);

        return implode(';', $escaped) . "\r\n";
    }
}
