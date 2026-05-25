@extends('layouts.app')

@section('title', 'Tambah DUDI')

@section('content')
<div class="page-header">
    <h1>Tambah DUDI Baru</h1>
    <p class="form-helper">Daftarkan perusahaan mitra PKL dengan informasi lengkap dan kontak yang jelas.</p>
</div>

<div class="form-card max-w-860 mx-auto">
    <div class="card-header">
        <h2>Form Pendaftaran DUDI</h2>
        <p class="form-helper">Lengkapi detail DUDI untuk proses booking siswa.</p>
    </div>

    <form action="{{ route('admin.dudi.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-row">
            <div class="form-group">
                <label for="nama_dudi">Nama DUDI *</label>
                <input type="text" id="nama_dudi" name="nama_dudi" value="{{ old('nama_dudi') }}" required>
                @error('nama_dudi')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="bidang_usaha">Bidang Usaha</label>
                <input type="text" id="bidang_usaha" name="bidang_usaha" value="{{ old('bidang_usaha') }}" placeholder="Contoh: Teknologi Informasi">
                @error('bidang_usaha')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="website">Website</label>
                <input type="text" id="website" name="website" value="{{ old('website') }}" placeholder="Contoh: www.perusahaan.co.id">
                @error('website')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="jumlah_pegawai">Jumlah Pegawai</label>
                <input type="text" id="jumlah_pegawai" name="jumlah_pegawai" value="{{ old('jumlah_pegawai') }}" placeholder="Contoh: 20 – 250 pegawai">
                @error('jumlah_pegawai')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="pembimbing_dudi">Penanggung Jawab</label>
                <input type="text" id="pembimbing_dudi" name="pembimbing_dudi" value="{{ old('pembimbing_dudi') }}" placeholder="Contoh: Ir. Budi Santoso">
                @error('pembimbing_dudi')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="kota">Kota</label>
                <input type="text" id="kota" name="kota" value="{{ old('kota') }}" placeholder="Contoh: Kota Malang">
                @error('kota')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="form-group">
            <label for="logo">Logo DUDI (opsional)</label>
            <input type="file" id="logo" name="logo" accept="image/*">
            @error('logo')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="jam_masuk">Jam Masuk</label>
                <input type="text" id="jam_masuk" name="jam_masuk" value="{{ old('jam_masuk') }}" placeholder="Contoh: 07.00">
                @error('jam_masuk')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="jam_pulang">Jam Pulang</label>
                <input type="text" id="jam_pulang" name="jam_pulang" value="{{ old('jam_pulang') }}" placeholder="Contoh: 16.00">
                @error('jam_pulang')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="kuota">Kuota Pendaftar</label>
                <input type="number" id="kuota" name="kuota" value="{{ old('kuota', 5) }}" min="0">
                @error('kuota')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group"></div>
        </div>

        <div class="form-group">
            <label for="alamat">Alamat</label>
            <textarea id="alamat" name="alamat" rows="3">{{ old('alamat') }}</textarea>
            @error('alamat')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="telepon">Telepon</label>
                <input type="text" id="telepon" name="telepon" value="{{ old('telepon') }}">
                @error('telepon')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}">
                @error('email')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="form-group">
            <label for="deskripsi">Deskripsi</label>
            <textarea id="deskripsi" name="deskripsi" rows="4">{{ old('deskripsi') }}</textarea>
            @error('deskripsi')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Simpan DUDI</button>
            <a href="{{ route('admin.dudi.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>

<div class="form-card">
    <div class="card-header">
        <h2>Import Data DUDI</h2>
        <p class="form-helper">Unggah file CSV untuk menambahkan banyak DUDI sekaligus.</p>
    </div>

    <form action="{{ route('admin.dudi.import') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label for="csv_file">Pilih File CSV/XLSX</label>
            <input type="file" id="csv_file" name="file" accept=".csv,.xlsx" required>
            @error('file')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="zip_file">Pilih File ZIP Logo (opsional)</label>
            <input type="file" id="zip_file" name="zip" accept=".zip">
            @error('zip')
                <div class="form-error">{{ $message }}</div>
            @enderror
            <p class="form-helper" style="margin-top:0.5rem;">Jika ingin memuat logo, sertakan nama file logo sesuai kolom Logo di CSV.</p>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Import Data</button>
            <a href="{{ route('admin.dudi.import.template') }}" class="btn btn-secondary">Download Template Import</a>
            <a href="{{ route('admin.dudi.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </form>
</div>
@endsection
