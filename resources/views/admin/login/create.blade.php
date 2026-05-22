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
                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="wali_kelas" {{ old('role') == 'wali_kelas' ? 'selected' : '' }}>Wali Kelas</option>
                    <option value="kakonsli" {{ old('role') == 'kakonsli' ? 'selected' : '' }}>Kakonsli</option>
                    <option value="siswa" {{ old('role') == 'siswa' ? 'selected' : '' }}>Siswa</option>
                </select>
                @error('role')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group" id="kelas_id_group">
                <label for="kelas_id">Kelas Utama</label>
                <select id="kelas_id" name="kelas_id">
                    <option value="">Tidak Ada</option>
                    <option value="XII SIJA 1" {{ old('kelas_id') == 'XII SIJA 1' ? 'selected' : '' }}>XII SIJA 1</option>
                    <option value="XII SIJA 2" {{ old('kelas_id') == 'XII SIJA 2' ? 'selected' : '' }}>XII SIJA 2</option>
                </select>
                @error('kelas_id')
                    <div class="form-error">{{ $message }}</div>
                @enderror
                <p class="form-helper">Diperlukan untuk role Wali Kelas dan Kakonsli.</p>
            </div>

            <div class="form-group" id="kelas_second_group">
                <label for="kelas_second">Kelas Kedua</label>
                <select id="kelas_second" name="kelas_second">
                    <option value="">Tidak Ada</option>
                    <option value="XII SIJA 1" {{ old('kelas_second') == 'XII SIJA 1' ? 'selected' : '' }}>XII SIJA 1</option>
                    <option value="XII SIJA 2" {{ old('kelas_second') == 'XII SIJA 2' ? 'selected' : '' }}>XII SIJA 2</option>
                </select>
                @error('kelas_second')
                    <div class="form-error">{{ $message }}</div>
                @enderror
                <p class="form-helper">Hanya diperlukan untuk role Kakonsli.</p>
            </div>

            <div class="form-group" id="password_group">
                <label for="password">Password *</label>
                <input type="password" id="password" name="password" required>
                @error('password')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <p id="siswaPasswordNote" class="form-helper hidden">Untuk siswa, password otomatis sama dengan NIS setelah akun dibuat.</p>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Simpan User</button>
            <a href="{{ route('admin.login.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>

    <div class="toolbar-panel">
        <div>
            <h2>Impor Excel</h2>
            <p class="form-helper">Gunakan file Excel/CSV untuk membuat beberapa akun pengguna sekaligus. Lihat preview sebelum impor agar data lebih aman.</p>
        </div>
        <form action="{{ route('admin.login.import.preview') }}" method="POST" enctype="multipart/form-data" class="toolbar-grid">
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
            <button type="submit" class="btn btn-primary">Preview Import</button>
        </form>
        <a href="{{ route('admin.login.import.template') }}" class="btn btn-secondary" style="margin-top:0.75rem;">Download Template Import</a>

        @if(!empty($previewMode) && !empty($previewRows))
            <div class="preview-card">
                <div class="card-header">
                    <h2>Preview Import User</h2>
                    <p class="form-helper">Periksa dulu baris valid dan kesalahan sebelum mengonfirmasi import.</p>
                </div>
                <div class="preview-summary">
                    <p>Total baris: {{ $previewSummary['total'] }}</p>
                    <p>Valid: {{ $previewSummary['valid'] }}</p>
                    <p>Tidak valid: {{ $previewSummary['invalid'] }}</p>
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
                                    <td>{{ $row['data']['username'] }}</td>
                                    <td>{{ $row['data']['name'] }}</td>
                                    <td>{{ $row['data']['role'] }}</td>
                                    <td>{{ $row['data']['password'] }}</td>
                                    <td>{{ $row['data']['kelas_id'] }}</td>
                                    <td>{{ $row['data']['kelas_second'] }}</td>
                                    <td>{{ $row['valid'] ? 'Ya' : 'Tidak' }}</td>
                                    <td>{{ implode(', ', $row['errors']) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <form action="{{ route('admin.login.import') }}" method="POST" style="margin-top: 1rem;">
                    @csrf
                    <button type="submit" class="btn btn-success">Import Semua Baris Valid</button>
                </form>
            </div>
        @endif
    </div>
</div>

<script>
    function updateRoleFields() {
        const role = document.getElementById('role').value;
        const passwordGroup = document.getElementById('password_group');
        const kelasIdGroup = document.getElementById('kelas_id_group');
        const kelasSecondGroup = document.getElementById('kelas_second_group');
        const siswaPasswordNote = document.getElementById('siswaPasswordNote');

        if (role === 'siswa') {
            passwordGroup.style.display = 'none';
            kelasIdGroup.style.display = 'none';
            kelasSecondGroup.style.display = 'none';
            siswaPasswordNote.classList.remove('hidden');
            document.getElementById('password').required = false;
            document.getElementById('kelas_id').required = false;
            document.getElementById('kelas_second').required = false;
        } else {
            siswaPasswordNote.classList.add('hidden');
            passwordGroup.style.display = '';
            document.getElementById('password').required = true;

            if (role === 'wali_kelas') {
                kelasIdGroup.style.display = '';
                kelasSecondGroup.style.display = 'none';
                document.getElementById('kelas_id').required = true;
                document.getElementById('kelas_second').required = false;
            } else if (role === 'kakonsli') {
                kelasIdGroup.style.display = '';
                kelasSecondGroup.style.display = '';
                document.getElementById('kelas_id').required = true;
                document.getElementById('kelas_second').required = true;
            } else {
                kelasIdGroup.style.display = 'none';
                kelasSecondGroup.style.display = 'none';
                document.getElementById('kelas_id').required = false;
                document.getElementById('kelas_second').required = false;
            }
        }
    }

    document.getElementById('role').addEventListener('change', updateRoleFields);
    updateRoleFields();
</script>
@endsection
