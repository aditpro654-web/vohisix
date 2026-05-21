@extends('layouts.app')

@section('title', 'Manajemen Login')

@section('content')
<div class="page-hero">
    <div class="section-title">
        <div>
            <h1>Manajemen Login</h1>
            <p>Kelola akun user Admin dan Siswa dengan tampilan yang rapi dan modern.</p>
        </div>
    </div>
    <div class="hero-stats">
        <div class="hero-stat"><strong>{{ $totalUser }}</strong><span>Total User</span></div>
        <div class="hero-stat"><strong>{{ $totalAdmin }}</strong><span>Admin</span></div>
        <div class="hero-stat"><strong>{{ $totalSiswa }}</strong><span>Siswa</span></div>
    </div>
</div>

<div class="card">
    <div class="toolbar-panel">
        <div class="toolbar-panel-header">
            <div>
                <h2>Daftar User</h2>
                <p class="form-helper">Cari dan filter akun siswa maupun admin dengan antarmuka yang lebih bersih.</p>
            </div>
            <a href="{{ route('admin.login.create') }}" class="btn btn-primary">+ Tambah User</a>
        </div>
        <form action="{{ route('admin.login.index') }}" method="GET" class="toolbar-grid">
            <input type="text" name="search" placeholder="Cari username atau nama..." value="{{ $search ?? '' }}" />
            <select name="role">
                <option value="">Semua Role</option>
                <option value="admin" {{ ($role ?? '') == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="wali_kelas" {{ ($role ?? '') == 'wali_kelas' ? 'selected' : '' }}>Wali Kelas</option>
                <option value="kakonsli" {{ ($role ?? '') == 'kakonsli' ? 'selected' : '' }}>Kakonsli</option>
                <option value="siswa" {{ ($role ?? '') == 'siswa' ? 'selected' : '' }}>Siswa</option>
            </select>
            <select name="sort_by">
                <option value="newest" {{ ($sortBy ?? '') == 'newest' ? 'selected' : '' }}>Terbaru</option>
                <option value="oldest" {{ ($sortBy ?? '') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                <option value="name_asc" {{ ($sortBy ?? '') == 'name_asc' ? 'selected' : '' }}>Nama A-Z</option>
                <option value="name_desc" {{ ($sortBy ?? '') == 'name_desc' ? 'selected' : '' }}>Nama Z-A</option>
            </select>
            <button type="submit" class="btn btn-primary">Cari</button>
            <a href="{{ route('admin.login.export', request()->query()) }}" class="btn btn-secondary">Export</a>
        </form>
    </div>

    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th class="w-70">No</th>
                    <th>Username</th>
                    <th>Nama</th>
                    <th>Role</th>
                    <th class="w-180">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>{{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}</td>
                        <td><strong>{{ $user->username }}</strong></td>
                        <td>{{ $user->name }}</td>
                        <td>
                            @if($user->role === 'admin')
                                <span class="badge-role admin">Admin</span>
                            @elseif($user->role === 'wali_kelas')
                                <span class="badge-role wali">Wali Kelas</span>
                            @elseif($user->role === 'kakonsli')
                                <span class="badge-role kakonsli">Kakonsli</span>
                            @else
                                <span class="badge-role siswa">Siswa</span>
                            @endif
                        </td>
                        <td>
                            <div class="action-group">
                                <a href="{{ route('admin.login.edit', $user->id) }}" class="edit">Edit</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="no-data">Tidak ada data user</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
