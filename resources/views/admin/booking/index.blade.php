@extends('layouts.app')

@section('title', 'Booking PKL')

@section('content')
<div class="booking-hero">
    <div class="section-title">
        <div>
            <h1>Manajemen Booking PKL</h1>
            <p>Review dan update status pengajuan PKL siswa dengan tampilan yang lebih segar dan konsisten.</p>
        </div>
        <a href="{{ route('admin.booking.create') }}" class="btn btn-primary">+ Tambah Booking</a>
    </div>
    <div class="hero-stats">
        <div class="hero-stat"><strong>{{ $totalBooking }}</strong><span>Total Booking</span></div>
        <div class="hero-stat"><strong>{{ $bookingDireview }}</strong><span>Direview</span></div>
        <div class="hero-stat"><strong>{{ $bookingDiterima }}</strong><span>Diterima</span></div>
        <div class="hero-stat"><strong>{{ $bookingDitolak }}</strong><span>Ditolak</span></div>
    </div>
</div>

<div class="card">
    <div class="toolbar-panel">
        <div>
            <h2>Daftar Booking</h2>
            <p class="form-helper">Filter, cari, dan kelola semua pengajuan PKL siswa di satu tempat.</p>
        </div>
        <form action="{{ route('admin.booking.index') }}" method="GET" class="toolbar-grid">
            <input type="text" name="search" placeholder="Cari nama atau NIS siswa..." value="{{ $search }}" />
            <select name="status">
                <option value="">Semua Status</option>
                <option value="Direview" {{ $status == 'Direview' ? 'selected' : '' }}>Direview</option>
                <option value="Diterima" {{ $status == 'Diterima' ? 'selected' : '' }}>Diterima</option>
                <option value="Ditolak" {{ $status == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
            </select>
            <select name="sort_by">
                <option value="newest" {{ $sortBy == 'newest' ? 'selected' : '' }}>Terbaru</option>
                <option value="oldest" {{ $sortBy == 'oldest' ? 'selected' : '' }}>Terlama</option>
                <option value="siswa_asc" {{ $sortBy == 'siswa_asc' ? 'selected' : '' }}>NIS A-Z</option>
                <option value="siswa_desc" {{ $sortBy == 'siswa_desc' ? 'selected' : '' }}>NIS Z-A</option>
            </select>
            <button type="submit">Cari</button>
        </form>
    </div>

    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th class="w-70">No</th>
                    <th>Nama Siswa</th>
                    <th>NIS</th>
                    <th>DUDI</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th class="w-180">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $key => $booking)
                    <tr>
                        <td>{{ ($bookings->currentPage() - 1) * $bookings->perPage() + $loop->iteration }}</td>
                        <td><strong>{{ $booking->siswa->nama }}</strong></td>
                        <td>{{ $booking->nis }}</td>
                        <td>{{ $booking->dudi->nama_dudi }}</td>
                        <td>
                            @if($booking->status === 'Diterima')
                                <span class="status-pill accept">✓ Diterima</span>
                            @elseif($booking->status === 'Ditolak')
                                <span class="status-pill reject">✗ Ditolak</span>
                            @else
                                <span class="status-pill review">⏳ Direview</span>
                            @endif
                        </td>
                        <td>{{ $booking->created_at->format('d M Y') }}</td>
                        <td>
                            <div class="action-group">
                                <a href="{{ route('admin.booking.edit', $booking->id_booking) }}" class="edit">Edit</a>
                                <form action="{{ route('admin.booking.destroy', $booking->id_booking) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus?');" class="inline-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="delete">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="no-data">Tidak ada data booking</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($bookings->hasPages())
        <div class="pagination">
            @if($bookings->onFirstPage())
                <span>← Sebelumnya</span>
            @else
                <a href="{{ $bookings->previousPageUrl() }}{{ !empty($search) ? '&search='.$search : '' }}{{ !empty($status) ? '&status='.$status : '' }}{{ $sortBy != 'newest' ? '&sort_by='.$sortBy : '' }}">← Sebelumnya</a>
            @endif

            @foreach($bookings->getUrlRange(1, $bookings->lastPage()) as $page => $url)
                @if($page == $bookings->currentPage())
                    <span class="active"><span>{{ $page }}</span></span>
                @else
                    <a href="{{ $url }}{{ !empty($search) ? '&search='.$search : '' }}{{ !empty($status) ? '&status='.$status : '' }}{{ $sortBy != 'newest' ? '&sort_by='.$sortBy : '' }}">{{ $page }}</a>
                @endif
            @endforeach

            @if($bookings->hasMorePages())
                <a href="{{ $bookings->nextPageUrl() }}{{ !empty($search) ? '&search='.$search : '' }}{{ !empty($status) ? '&status='.$status : '' }}{{ $sortBy != 'newest' ? '&sort_by='.$sortBy : '' }}">Selanjutnya →</a>
            @else
                <span>Selanjutnya →</span>
            @endif
        </div>
    @endif
</div>
@endsection

