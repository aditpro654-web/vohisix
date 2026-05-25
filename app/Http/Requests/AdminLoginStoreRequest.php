<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminLoginStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => [
                'required',
                'string',
                'max:255',
                'unique:users,username',
                $this->input('role') === 'siswa' ? 'numeric' : 'string',
            ],
            'name' => ['required', 'string', 'max:255'],
            'role' => ['required', Rule::in(['admin', 'siswa', 'wali_kelas', 'kakonsli'])],
            'password' => [
                Rule::requiredIf(fn () => $this->input('role') !== 'siswa'),
                'nullable',
                'string',
                'min:6',
            ],
            'kelas_id' => [
                Rule::requiredIf(fn () => in_array($this->input('role'), ['wali_kelas', 'kakonsli'], true)),
                Rule::in(['XII SIJA 1', 'XII SIJA 2']),
            ],
            'kelas_second' => [
                Rule::requiredIf(fn () => $this->input('role') === 'kakonsli'),
                Rule::in(['XII SIJA 1', 'XII SIJA 2']),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'username.numeric' => 'Username siswa harus berupa angka NIS.',
            'name.required' => 'Nama wajib diisi.',
            'role.required' => 'Role harus dipilih.',
            'password.required_if' => 'Password diperlukan untuk role selain siswa.',
            'password.min' => 'Password minimal 6 karakter.',
            'kelas_id.required_if' => 'Kelas Utama diperlukan untuk Wali Kelas atau Kakonsli.',
            'kelas_second.required_if' => 'Kelas Kedua diperlukan untuk Kakonsli.',
        ];
    }
}
