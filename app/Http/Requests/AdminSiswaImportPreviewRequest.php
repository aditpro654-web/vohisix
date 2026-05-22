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
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'File import wajib diunggah.',
            'file.file' => 'File import tidak valid.',
            'file.mimes' => 'Gunakan file CSV, TXT, atau XLSX.',
            'file.max' => 'Ukuran file maksimal 5MB.',
        ];
    }
}
