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

    <form action="{{ route('admin.login.update', $user->id) }}" method="POST">
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
                    <option value="siswa" @if($user->role === 'siswa') selected @endif>Siswa</option>
                </select>
                @error('role')
                    <div class="form-error">{{ $message }}</div>
                @enderror
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
@endsection
