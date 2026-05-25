<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminSiswaImportPreviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => 'required|file|mimes:csv,txt,xlsx|max:5120',
            'zip' => 'nullable|file|mimes:zip|max:51200',
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'File import wajib diunggah.',
            'file.file' => 'File import tidak valid.',
            'file.mimes' => 'Gunakan file CSV, TXT, atau XLSX.',
            'file.max' => 'Ukuran file maksimal 5MB.',
            'zip.file' => 'File ZIP tidak valid.',
            'zip.mimes' => 'Gunakan file ZIP untuk gambar.',
            'zip.max' => 'Ukuran file ZIP maksimal 50MB.',
        ];
    }
}
