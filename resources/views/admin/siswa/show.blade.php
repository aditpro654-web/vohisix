@extends('layouts.app')

@section('title', 'Detail Siswa')

@section('content')
<div class="page-header">
    <h1>Detail Siswa PKL</h1>
</div>

<div class="card max-w-600 mx-auto">
    <div class="card-header">
        <h2>{{ $siswa->nama }}</h2>
    </div>

    <div class="detail-block">
        <p><strong>Nama:</strong> {{ $siswa->nama }}</p>
        <p><strong>NIS:</strong> {{ $siswa->nis }}</p>
        <p><strong>Kelas:</strong> {{ $siswa->kelas }}</p>
        <p><strong>Dibuat:</strong> {{ $siswa->created_at->format('d M Y H:i') }}</p>
    </div>

    <div class="action-group">
        <a href="{{ route('admin.siswa.edit', $siswa->nis) }}" class="btn btn-primary">Edit</a>
        <a href="{{ route('admin.siswa.index') }}" class="btn btn-secondary">Kembali</a>
    </div>
</div>
@endsection
