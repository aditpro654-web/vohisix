@extends('layouts.app')

@section('title', 'Daftar Siswa Kelas ' . $kelas)

@section('content')
<div class="page-header">
    <h1>Daftar Siswa</h1>
    <p>Kelas: <strong>{{ $kelas }}</strong></p>
</div>

<div class="card">
    <div class="card-header">
        <h2>Data Siswa Kelas {{ $kelas }}</h2>
    </div>
    
    <form action="{{ route('wali-kelas.siswas') }}" method="GET" class="flex gap-3 items-center mb-4">
        <label for="status" class="text-sm">Filter status:</label>
        <select name="status" id="status" class="form-control">
            <option value="">Semua Status</option>
            <option value="Direview" {{ request('status') === 'Direview' ? 'selected' : '' }}>Direview</option>
            <option value="Diterima" {{ request('status') === 'Diterima' ? 'selected' : '' }}>Diterima</option>
            <option value="Ditolak" {{ request('status') === 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
        </select>
        <button type="submit" class="btn btn-primary">Terapkan</button>
        <a href="{{ route('wali-kelas.siswas.export.pdf', request()->query()) }}" class="btn btn-secondary" target="_blank" rel="noopener">Export PDF</a>
    </form>
    
    @if($siswas->count() > 0)
        <table class="table">
            <thead>
                <tr>
                    <th>NIS</th>
                    <th>Nama</th>
                    <th>Kelas</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($siswas as $siswa)
                    <tr>
                        <td>{{ $siswa->nis }}</td>
                        <td>{{ $siswa->nama }}</td>
                        <td>{{ $siswa->kelas }}</td>
                        <td>
                            <a href="#" class="btn btn-sm btn-info">Detail</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        <div style="margin-top: 20px;">
            {{ $siswas->links() }}
        </div>
    @else
        <div style="padding: 20px; text-align: center; color: #666;">
            <p>Tidak ada siswa dalam kelas ini</p>
        </div>
    @endif
</div>

<div style="margin-top: 20px;">
    <a href="{{ route('wali-kelas.dashboard') }}" class="btn btn-secondary">← Kembali ke Dashboard</a>
</div>
@endsection
