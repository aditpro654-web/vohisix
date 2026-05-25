<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminSiswaStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nis' => 'required|numeric|unique:siswas,nis',
            'nomor_absen' => 'required|integer|min:1|unique:siswas,nomor_absen',
            'nama' => 'required|string|max:255',
            'kelas' => ['required', 'in:XII SIJA 1,XII SIJA 2,XII SIJA 3'],
            'foto' => 'nullable|file|mimes:jpg,jpeg,png,webp,gif,bmp,heic,heif|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'nis.required' => 'NIS wajib diisi.',
            'nis.numeric' => 'NIS harus berupa angka.',
            'nis.unique' => 'NIS sudah terdaftar.',
            'nomor_absen.required' => 'Nomor absen wajib diisi.',
            'nomor_absen.integer' => 'Nomor absen harus berupa angka.',
            'nomor_absen.min' => 'Nomor absen harus bernilai minimal 1.',
            'nomor_absen.unique' => 'Nomor absen sudah digunakan.',
            'nama.required' => 'Nama wajib diisi.',
            'nama.max' => 'Nama maksimal 255 karakter.',
            'kelas.required' => 'Kelas wajib dipilih.',
            'kelas.in' => 'Kelas tidak valid.',
            'foto.mimes' => 'Foto harus berformat gambar yang didukung.',
            'foto.max' => 'Ukuran foto maksimal 2MB.',
        ];
    }
}
