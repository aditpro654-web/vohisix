@extends('layouts.app')

@section('title', 'Detail DUDI')

@section('content')
<div class="page-header">
    <h1>Detail Perusahaan (DUDI)</h1>
</div>

<div class="card max-w-600 mx-auto">
    <div class="card-header">
        <h2>{{ $dudi->nama_dudi }}</h2>
    </div>

    <div class="detail-block">
        <p><strong>Nama DUDI:</strong> {{ $dudi->nama_dudi }}</p>
        <p><strong>Bidang Usaha:</strong> {{ $dudi->bidang_usaha }}</p>
        <p><strong>Alamat:</strong> {{ $dudi->alamat }}</p>
        <p><strong>Telepon:</strong> {{ $dudi->telepon }}</p>
        <p><strong>Email:</strong> {{ $dudi->email }}</p>
        <p><strong>Deskripsi:</strong> {{ $dudi->deskripsi }}</p>
        <?php $jumlahPendaftar = \App\Models\Booking::where('id_dudi', $dudi->id_dudi)->whereIn('status', ['Direview', 'Diterima'])->count(); ?>
        <p><strong>Kuota:</strong> {{ $dudi->kuota ?? 0 }} ({{ $jumlahPendaftar }} terdaftar)</p>
        <p><strong>Dibuat:</strong> {{ $dudi->created_at->format('d M Y H:i') }}</p>
    </div>

    <div class="action-group">
        <a href="{{ route('admin.dudi.edit', $dudi->id_dudi) }}" class="btn btn-primary">Edit</a>
        <a href="{{ route('admin.dudi.index') }}" class="btn btn-secondary">Kembali</a>
    </div>
</div>
@endsection
