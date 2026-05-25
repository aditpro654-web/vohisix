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
    
    @if($siswas->count() > 0)
        <div style="margin-bottom:0.75rem;">
            <a href="{{ route('wali-kelas.siswas.export.pdf', request()->query()) }}" class="btn btn-secondary">Export PDF</a>
        </div>
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
