@extends('layouts.app')

@section('title', 'Detail Booking')

@section('content')
<div class="page-header">
    <h1>Detail Booking PKL</h1>
</div>

<div class="card max-w-600 mx-auto">
    <div class="card-header">
        <h2>{{ $booking->siswa->nama }} - {{ $booking->dudi->nama_dudi }}</h2>
    </div>

    <div class="detail-block">
        <p><strong>Nama Siswa:</strong> {{ $booking->siswa->nama }}</p>
        <p><strong>NIS:</strong> {{ $booking->nis }}</p>
        <p><strong>Kelas:</strong> {{ $booking->siswa->kelas }}</p>
        <p><strong>DUDI:</strong> {{ $booking->dudi->nama_dudi }}</p>
        <p><strong>Bidang Usaha:</strong> {{ $booking->dudi->bidang_usaha }}</p>
        <p>
            <strong>Status:</strong>
            @if($booking->status === 'Diterima')
                <span class="status-pill accept">✓ Diterima</span>
            @elseif($booking->status === 'Ditolak')
                <span class="status-pill reject">✗ Ditolak</span>
            @else
                <span class="status-pill review">⏳ Direview</span>
            @endif
        </p>
        <p><strong>Tanggal Pengajuan:</strong> {{ $booking->created_at->format('d M Y H:i') }}</p>
    </div>

    <div class="action-group">
        <a href="{{ route('admin.booking.edit', $booking->id_booking) }}" class="btn btn-primary">Edit Status</a>
        <a href="{{ route('admin.booking.index') }}" class="btn btn-secondary">Kembali</a>
    </div>
</div>
@endsection
