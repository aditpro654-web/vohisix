@extends('layouts.app')

@section('title', 'Tambah User')

@section('content')
<div class="page-header">
    <h1>Tambah User Baru</h1>
    <p class="form-helper">Buat akun baru untuk administrator atau siswa dengan hak akses sesuai.</p>
</div>

<div class="form-card">
    <div class="card-header">
        <h2>Form Pendaftaran User</h2>
        <p class="form-helper">Gunakan username unik dan password aman untuk setiap akun.</p>
    </div>

    <form action="{{ route('admin.login.store') }}" method="POST">
        @csrf

        <div class="form-row">
            <div class="form-group">
                <label for="username">Username *</label>
                <input type="text" id="username" name="username" value="{{ old('username') }}" required>
                @error('username')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="name">Nama *</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required>
                @error('name')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="role">Role *</label>
                <select id="role" name="role" required>
                    <option value="">Pilih Role</option>
                    <option value="admin">Admin</option>
                    <option value="siswa">Siswa</option>
                </select>
                @error('role')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Password *</label>
                <input type="password" id="password" name="password" required>
                @error('password')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Simpan User</button>
            <a href="{{ route('admin.login.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>

    <div class="toolbar-panel">
        <div>
            <h2>Impor Excel</h2>
            <p class="form-helper">Gunakan file Excel/CSV untuk membuat beberapa akun pengguna sekaligus.</p>
        </div>
        <form action="{{ route('admin.login.import') }}" method="POST" enctype="multipart/form-data" class="toolbar-grid">
            @csrf
            <label for="import_file" class="upload-file-label">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2"></path>
                    <polyline points="7 9 12 4 17 9"></polyline>
                    <line x1="12" y1="4" x2="12" y2="16"></line>
                </svg>
                Pilih File
            </label>
            <input id="import_file" type="file" name="file" accept=".csv,.xlsx" class="sr-only" required>
            <button type="submit" class="btn btn-primary">Unggah</button>
        </form>
    </div>
</div>
@endsection
