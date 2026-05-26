@extends('layouts.app')

@section('title', 'Daftar Siswa Kedua Kelas')

@section('content')
<div class="page-header">
    <h1>Daftar Siswa</h1>
    <p>Data siswa dari kedua kelas: <strong>{{ $kelas1 }}</strong> dan <strong>{{ $kelas2 }}</strong></p>
</div>

<div class="card">
    <div class="card-header">
        <h2>Data Siswa Kedua Kelas</h2>
    </div>
    
    <form action="{{ route('kakonsli.siswas') }}" method="GET" class="flex gap-3 items-center mb-4">
        <label for="status" class="text-sm">Filter status:</label>
        <select name="status" id="status" class="form-control">
            <option value="">Semua Status</option>
            <option value="Direview" {{ request('status') === 'Direview' ? 'selected' : '' }}>Direview</option>
            <option value="Diterima" {{ request('status') === 'Diterima' ? 'selected' : '' }}>Diterima</option>
            <option value="Ditolak" {{ request('status') === 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
        </select>
        <button type="submit" class="btn btn-primary">Terapkan</button>
        <a href="{{ route('kakonsli.siswas.export.pdf', request()->query()) }}" class="btn btn-secondary" target="_blank" rel="noopener">Export PDF</a>
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
                        <td>
                            <span class="badge" style="background-color: {{ $siswa->kelas === $kelas1 ? '#007bff' : '#17a2b8' }};">
                                {{ $siswa->kelas }}
                            </span>
                        </td>
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
            <p>Tidak ada siswa dalam kedua kelas ini</p>
        </div>
    @endif
</div>

<div style="margin-top: 20px;">
    <a href="{{ route('kakonsli.dashboard') }}" class="btn btn-secondary">← Kembali ke Dashboard</a>
</div>
@endsection
