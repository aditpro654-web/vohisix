@extends('layouts.app')

@section('title', 'Detail User')

@section('content')
<div class="page-header">
    <h1>Detail User</h1>
</div>

<div class="card max-w-600 mx-auto">
    <div class="card-header">
        <h2>{{ $user->name }}</h2>
    </div>

    <div class="detail-block">
        <p><strong>Username:</strong> {{ $user->username }}</p>
        <p><strong>Nama:</strong> {{ $user->name }}</p>
        <p>
            <strong>Role:</strong>
            @if($user->role === 'admin')
                <span class="badge-role admin">Admin</span>
            @else
                <span class="badge-role siswa">Siswa</span>
            @endif
        </p>
        <p><strong>Dibuat:</strong> {{ $user->created_at->format('d M Y H:i') }}</p>
    </div>

    <div class="action-group">
        <a href="{{ route('admin.login.edit', ['login' => $user->id]) }}" class="btn btn-primary">Edit</a>
        <a href="{{ route('admin.login.index') }}" class="btn btn-secondary">Kembali</a>
    </div>
</div>
@endsection
