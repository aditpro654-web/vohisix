@extends('layouts.app')

@section('title', 'Tambah Booking')

@section('content')
<div class="page-header">
    <h1>Tambah Booking PKL</h1>
    <p class="form-helper">Buat pengajuan booking baru untuk siswa yang akan mengikuti PKL.</p>
</div>

<div class="form-card max-w-700 m-auto">
    <div class="card-header">
        <h2>Form Tambah Booking</h2>
        <p class="form-helper">Pilih siswa dan DUDI untuk membuat data booking baru.</p>
    </div>

    <form action="{{ route('admin.booking.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="nis">Siswa *</label>
            <select id="nis" name="nis" required>
                <option value="">Pilih Siswa</option>
                @foreach($siswas as $siswa)
                    <option value="{{ $siswa->nis }}" @if(old('nis') == $siswa->nis) selected @endif>{{ $siswa->nama }} ({{ $siswa->nis }} - {{ $siswa->kelas }})</option>
                @endforeach
            </select>
            @error('nis')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="id_dudi">DUDI *</label>
            <select id="id_dudi" name="id_dudi" required>
                <option value="">Pilih DUDI</option>
                @foreach($dudis as $dudi)
                    <option value="{{ $dudi->id_dudi }}" @if(old('id_dudi') == $dudi->id_dudi) selected @endif>{{ $dudi->nama_dudi }} @if($dudi->kota) - {{ $dudi->kota }} @endif</option>
                @endforeach
            </select>
            @error('id_dudi')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="status">Status *</label>
            <select id="status" name="status" required>
                <option value="">Pilih Status</option>
                <option value="Direview" @if(old('status') == 'Direview') selected @endif>Direview</option>
                <option value="Diterima" @if(old('status') == 'Diterima') selected @endif>Diterima</option>
                <option value="Ditolak" @if(old('status') == 'Ditolak') selected @endif>Ditolak</option>
            </select>
            @error('status')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Simpan Booking</button>
            <a href="{{ route('admin.booking.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
