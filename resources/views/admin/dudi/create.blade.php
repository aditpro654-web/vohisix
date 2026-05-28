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
            <div class="form-group">
                <label for="status">Status DUDI</label>
                <select id="status" name="status">
                    <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ old('status', 'active') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
                @error('status')
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

    <form action="{{ route('admin.dudi.import.preview') }}" method="POST" enctype="multipart/form-data">
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
            <button type="submit" class="btn btn-primary">Preview Import</button>
            <a href="{{ route('admin.dudi.import.template') }}" class="btn btn-secondary">Download Template Import</a>
            <a href="{{ route('admin.dudi.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </form>

    @if(!empty($previewMode) && !empty($previewRows))
        <div class="preview-card" style="margin-top:1.5rem;">
            <div class="card-header">
                <h2>Preview Import DUDI</h2>
                <p class="form-helper">Periksa data valid dan kesalahan sebelum melakukan import.</p>
            </div>
            <div class="preview-summary">
                <p>Total baris: {{ $previewSummary['total'] }}</p>
                <p>Valid: {{ $previewSummary['valid'] }}</p>
                <p>Tidak valid: {{ $previewSummary['invalid'] }}</p>
                @if(isset($previewSummary['logos']))
                    <p>Logo ditemukan: {{ $previewSummary['logos']['found'] }}</p>
                    <p>Logo hilang: {{ $previewSummary['logos']['missing'] }}</p>
                    <p>Logo tidak valid: {{ $previewSummary['logos']['invalid'] }}</p>
                    @if(!empty($previewSummary['logos']['warnings']))
                        <div class="alert alert-warning" style="margin-top:1rem; padding:0.75rem; background:#fff3cd; border:1px solid #ffeeba; color:#856404;">
                            <strong>Peringatan logo:</strong>
                            <ul style="margin:0.5rem 0 0 1.25rem;">
                                @foreach($previewSummary['logos']['warnings'] as $warning)
                                    <li>{{ $warning }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                @endif
            </div>
            <div class="table-card">
                <table>
                    <thead>
                        <tr>
                            @foreach($previewHeaders as $header)
                                <th>{{ $header }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($previewRows as $row)
                            <tr class="{{ $row['valid'] ? '' : 'invalid-row' }}">
                                <td>{{ $row['row_number'] }}</td>
                                <td>{{ $row['data']['nama_dudi'] }}</td>
                                <td>{{ $row['data']['alamat'] }}</td>
                                <td>{{ $row['data']['telepon'] }}</td>
                                <td>{{ $row['data']['email'] }}</td>
                                <td>{{ $row['data']['bidang_usaha'] }}</td>
                                <td>{{ $row['data']['website'] }}</td>
                                <td>{{ $row['data']['jumlah_pegawai'] }}</td>
                                <td>{{ $row['data']['pembimbing_dudi'] }}</td>
                                <td>{{ $row['data']['jam_masuk'] }}</td>
                                <td>{{ $row['data']['jam_pulang'] }}</td>
                                <td>{{ $row['data']['kota'] }}</td>
                                <td>{{ $row['data']['kuota'] }}</td>
                                <td>{{ $row['data']['logo'] }}</td>
                                <td>{{ ucfirst($row['data']['status'] ?? 'active') }}</td>
                                <td>{{ ucfirst($row['data']['logo_status'] ?? 'missing') }}</td>
                                <td>{{ $row['data']['logo_warning'] ?? '-' }}</td>
                                <td>{{ $row['valid'] ? 'Ya' : 'Tidak' }}</td>
                                <td>{{ implode(', ', $row['errors']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <form action="{{ route('admin.dudi.import') }}" method="POST" style="margin-top: 1rem;">
                @csrf
                <button type="submit" class="btn btn-success">Import Semua Baris Valid</button>
            </form>
        </div>
    @endif
</div>
@endsection
