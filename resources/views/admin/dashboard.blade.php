@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<div class="page-header">
    <h1>Dashboard Admin</h1>
    <p>Selamat datang di halaman administrasi Website Booking PKL</p>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Total Siswa</div>
        <div class="stat-number">{{ $totalSiswa }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total DUDI</div>
        <div class="stat-number">{{ $totalDudi }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total Booking</div>
        <div class="stat-number">{{ $totalBooking }}</div>
    </div>
</div>

<div class="stats-grid">
    <div class="status-card review">
        <div class="stat-label">Booking Direview</div>
        <div class="stat-number status-number review">{{ $bookingDireview }}</div>
    </div>
    <div class="status-card accept">
        <div class="stat-label">Booking Diterima</div>
        <div class="stat-number status-number accept">{{ $bookingDiterima }}</div>
    </div>
    <div class="status-card reject">
        <div class="stat-label">Booking Ditolak</div>
        <div class="stat-number status-number reject">{{ $bookingDitolak }}</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>Menu Utama</h2>
    </div>
    <div class="dashboard-grid">
        <a href="{{ route('admin.siswa.index') }}" class="btn btn-primary">👥 Kelola Siswa</a>
        <a href="{{ route('admin.dudi.index') }}" class="btn btn-primary">🏢 Kelola DUDI</a>
        <a href="{{ route('admin.booking.index') }}" class="btn btn-primary">📋 Kelola Booking</a>
        <a href="{{ route('admin.login.index') }}" class="btn btn-primary">🔐 Manajemen Login</a>
    </div>
</div>
@endsection
