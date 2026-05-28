@extends('layouts.app')

@section('title', 'Booking PKL')

@section('content')
<div class="card">
    <div class="toolbar-panel">
        <div>
            <h2>Daftar Booking</h2>
            <p class="form-helper">Filter, cari, dan kelola semua pengajuan PKL siswa di satu tempat.</p>
        </div>
        <form action="{{ route('admin.booking.index') }}" method="GET" class="toolbar-grid">
            <input type="text" name="search" placeholder="Cari nama atau NIS siswa..." value="{{ $search }}" oninput="this.form.submit()" />
            <select name="status" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="Direview" {{ $status == 'Direview' ? 'selected' : '' }}>Direview</option>
                <option value="Diterima" {{ $status == 'Diterima' ? 'selected' : '' }}>Diterima</option>
                <option value="Ditolak" {{ $status == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
            </select>
            <select name="sort_by" onchange="this.form.submit()">
                <option value="newest" {{ $sortBy == 'newest' ? 'selected' : '' }}>Terbaru</option>
                <option value="oldest" {{ $sortBy == 'oldest' ? 'selected' : '' }}>Terlama</option>
                <option value="siswa_asc" {{ $sortBy == 'siswa_asc' ? 'selected' : '' }}>NIS A-Z</option>
                <option value="siswa_desc" {{ $sortBy == 'siswa_desc' ? 'selected' : '' }}>NIS Z-A</option>
            </select>
            <!-- live search: removed submit button -->
            <a href="{{ route('admin.booking.export', request()->query()) }}" class="btn btn-secondary">Export</a>
        </form>
    </div>

    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th class="w-70">No Absen</th>
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
                        <td>{{ $booking->siswa?->nomor_absen ?? '-' }}</td>
                        <td><strong>{{ $booking->siswa?->nama }}</strong></td>
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

    @if($bookings->total() > 0)
        <div class="pagination-container">
            <div class="pagination-info">
                Menampilkan {{ $bookings->firstItem() ?? 0 }} - {{ $bookings->lastItem() ?? 0 }} dari {{ $bookings->total() }} data
            </div>
            <div class="pagination-links">
                @if($bookings->onFirstPage())
                    <span class="disabled">← Sebelumnya</span>
                @else
                    <a href="{{ $bookings->appends(request()->except('page'))->previousPageUrl() }}">← Sebelumnya</a>
                @endif

                @foreach($bookings->getUrlRange(1, $bookings->lastPage()) as $page => $url)
                    @if($page == $bookings->currentPage())
                        <span class="active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}{{ request()->except('page') ? '&' . http_build_query(request()->except('page')) : '' }}">{{ $page }}</a>
                    @endif
                @endforeach

                @if($bookings->hasMorePages())
                    <a href="{{ $bookings->appends(request()->except('page'))->nextPageUrl() }}">Selanjutnya →</a>
                @else
                    <span class="disabled">Selanjutnya →</span>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection

