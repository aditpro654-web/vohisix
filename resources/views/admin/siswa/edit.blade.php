@extends('layouts.app')

@section('title', 'Edit Siswa')

@section('content')
<div class="page-header">
    <h1>Edit Data Siswa</h1>
    <p class="form-helper">Perbarui data siswa agar profil dan login tetap sinkron.</p>
</div>

<div class="form-card max-w-700 mx-auto">
    <div class="card-header">
        <h2>{{ $siswa->nama }}</h2>
        <p class="form-helper">Ubah NIS, nama, kelas, atau foto siswa.</p>
    </div>

    <form action="{{ route('admin.siswa.update', $siswa->nis) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="nis">NIS / Username Login *</label>
            <input type="text" id="nis" name="nis" value="{{ old('nis', $siswa->nis) }}" required>
            <span class="form-helper">Jika NIS diubah, username dan password siswa akan disesuaikan dengan NIS baru.</span>
            @error('nis')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="nomor_absen">Nomor Absen *</label>
            <input type="number" id="nomor_absen" name="nomor_absen" value="{{ old('nomor_absen', $siswa->nomor_absen) }}" required>
            @error('nomor_absen')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="nama">Nama Lengkap *</label>
            <input type="text" id="nama" name="nama" value="{{ old('nama', $siswa->nama) }}" required>
            @error('nama')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label>Kelas *</label>
            <div class="form-row">
                <label class="radio-option"><input type="radio" name="kelas" value="XII SIJA 1" {{ old('kelas', $siswa->kelas) == 'XII SIJA 1' ? 'checked' : '' }}> XII SIJA 1</label>
                <label class="radio-option"><input type="radio" name="kelas" value="XII SIJA 2" {{ old('kelas', $siswa->kelas) == 'XII SIJA 2' ? 'checked' : '' }}> XII SIJA 2</label>
                <label class="radio-option"><input type="radio" name="kelas" value="XII SIJA 3" {{ old('kelas', $siswa->kelas) == 'XII SIJA 3' ? 'checked' : '' }}> XII SIJA 3</label>
            </div>
            @error('kelas')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="foto">Foto Siswa (opsional)</label>
            <input type="file" id="foto" name="foto" accept="image/*">
            @if($siswa->foto)
                <div class="image-preview">
                    <img src="{{ asset('storage/'.$siswa->foto) }}" alt="Foto" class="preview-image">
                </div>
            @endif
            @error('foto')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="{{ route('admin.siswa.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
