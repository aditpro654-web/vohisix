@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="page-header">
    <h1>Edit User</h1>
    <p class="form-helper">Perbarui nama atau hak akses pengguna di sistem.</p>
</div>

<div class="form-card max-w-700 mx-auto">
    <div class="card-header">
        <h2>{{ $user->name }}</h2>
        <p class="form-helper">Username tidak dapat diubah, tetapi role dan nama pengguna bisa disesuaikan.</p>
    </div>

<form action="{{ route('admin.login.update', ['login' => $user->id]) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" value="{{ $user->username }}" disabled>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="name">Nama *</label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                @error('name')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="role">Role *</label>
                <select id="role" name="role" required>
                    <option value="">Pilih Role</option>
                    <option value="admin" @if($user->role === 'admin') selected @endif>Admin</option>
                    <option value="wali_kelas" @if($user->role === 'wali_kelas') selected @endif>Wali Kelas</option>
                    <option value="kakonsli" @if($user->role === 'kakonsli') selected @endif>Kakonsli</option>
                    <option value="siswa" @if($user->role === 'siswa') selected @endif>Siswa</option>
                </select>
                @error('role')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group" id="kelas_id_group">
                <label for="kelas_id">Kelas Utama</label>
                <select id="kelas_id" name="kelas_id">
                    <option value="">Tidak Ada</option>
                    <option value="XII SIJA 1" {{ old('kelas_id', $user->kelas_id) == 'XII SIJA 1' ? 'selected' : '' }}>XII SIJA 1</option>
                    <option value="XII SIJA 2" {{ old('kelas_id', $user->kelas_id) == 'XII SIJA 2' ? 'selected' : '' }}>XII SIJA 2</option>
                </select>
                @error('kelas_id')
                    <div class="form-error">{{ $message }}</div>
                @enderror
                <p class="form-helper">Diperlukan untuk role Wali Kelas saja.</p>
            </div>

            <div class="form-group" id="kelas_second_group">
                <label for="kelas_second">Kelas Kedua</label>
                <select id="kelas_second" name="kelas_second">
                    <option value="">Tidak Ada</option>
                    <option value="XII SIJA 1" {{ old('kelas_second', $user->kelas_second) == 'XII SIJA 1' ? 'selected' : '' }}>XII SIJA 1</option>
                    <option value="XII SIJA 2" {{ old('kelas_second', $user->kelas_second) == 'XII SIJA 2' ? 'selected' : '' }}>XII SIJA 2</option>
                </select>
                @error('kelas_second')
                    <div class="form-error">{{ $message }}</div>
                @enderror
                <p class="form-helper">Opsional jika diperlukan untuk pengaturan khusus.</p>
            </div>
        </div>

        <div class="form-group">
            <label for="password">Password (Kosongkan jika tidak diubah)</label>
            <input type="password" id="password" name="password">
            @error('password')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="{{ route('admin.login.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>

@section('scripts')
<script>
    function updateRoleFields() {
        const role = document.getElementById('role').value;
        const kelasIdGroup = document.getElementById('kelas_id_group');
        const kelasSecondGroup = document.getElementById('kelas_second_group');

        if (role === 'wali_kelas') {
            kelasIdGroup.style.display = '';
            kelasSecondGroup.style.display = 'none';
            document.getElementById('kelas_id').required = true;
            document.getElementById('kelas_second').required = false;
        } else {
            kelasIdGroup.style.display = 'none';
            kelasSecondGroup.style.display = 'none';
            document.getElementById('kelas_id').required = false;
            document.getElementById('kelas_second').required = false;
        }
    }

    document.getElementById('role').addEventListener('change', updateRoleFields);
    document.addEventListener('DOMContentLoaded', updateRoleFields);
</script>
@endsection
