@extends('layouts.app')

@section('title', 'Tambah Siswa')

@section('content')
<div class="page-header">
    <h1>Tambah Siswa Baru</h1>
    <p class="form-helper">Tambahkan siswa baru dengan data lengkap untuk login dan booking PKL.</p>
</div>

<div class="form-card">
    <div class="card-header">
        <h2>Form Pendaftaran Siswa</h2>
        <p class="form-helper">Masukkan NIS, nama, kelas, dan unggah foto jika tersedia.</p>
    </div>

    <form action="{{ route('admin.siswa.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label for="nis">NIS *</label>
            <input type="text" id="nis" name="nis" value="{{ old('nis') }}" required>
            @error('nis')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="nama">Nama Lengkap *</label>
            <input type="text" id="nama" name="nama" value="{{ old('nama') }}" required>
            @error('nama')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label>Kelas *</label>
            <div class="form-row">
                <label class="radio-option"><input type="radio" name="kelas" value="XII SIJA 1" {{ old('kelas') == 'XII SIJA 1' ? 'checked' : '' }}> XII SIJA 1</label>
                <label class="radio-option"><input type="radio" name="kelas" value="XII SIJA 2" {{ old('kelas') == 'XII SIJA 2' ? 'checked' : '' }}> XII SIJA 2</label>
                <label class="radio-option"><input type="radio" name="kelas" value="XII SIJA 3" {{ old('kelas') == 'XII SIJA 3' ? 'checked' : '' }}> XII SIJA 3</label>
            </div>
            @error('kelas')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="foto">Foto Siswa (opsional)</label>
            <div class="input-icon-group">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="7 10 12 5 17 10"></polyline>
                    <path d="M12 5v12"></path>
                </svg>
                <label for="foto" class="upload-file-label">Pilih Foto</label>
            </div>
            <input id="foto" type="file" name="foto" accept="image/*" class="sr-only">
            @error('foto')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Simpan Siswa</button>
            <a href="{{ route('admin.siswa.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>

<div class="form-card">
    <div class="card-header">
        <h2>Import Data Siswa</h2>
        <p class="form-helper">Unggah file CSV untuk menambahkan banyak siswa sekaligus.</p>
    </div>

    <form action="{{ route('admin.siswa.import') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label for="csv_file">Pilih File CSV</label>
            <input type="file" id="csv_file" name="file" accept=".csv,.xlsx" required>
            @error('file')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Import Data</button>
            <a href="{{ route('admin.siswa.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </form>
</div>
@endsection
