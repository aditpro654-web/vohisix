@extends('layouts.app')

@section('title', 'Data DUDI')

@section('content')
<div class="card">
    <div class="toolbar-panel">
        <div>
            <h2>Daftar DUDI</h2>
            <p class="form-helper">Gunakan pencarian dan filter untuk menemukan perusahaan mitra dengan cepat.</p>
        </div>
        <form action="{{ route('admin.dudi.index') }}" method="GET" class="toolbar-grid">
            <input type="text" name="search" placeholder="Cari Nama atau Bidang Industri..." value="{{ $search ?? '' }}" oninput="this.form.submit()" />
            <select name="bidang_usaha" onchange="this.form.submit()">
                <option value="">Semua Bidang</option>
                @foreach($allBidang as $bidangItem)
                    <option value="{{ $bidangItem }}" {{ ($bidang == $bidangItem) ? 'selected' : '' }}>{{ $bidangItem }}</option>
                @endforeach
            </select>
            <select name="status" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="active" {{ (request('status') ?? '') === 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="inactive" {{ (request('status') ?? '') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
            </select>
            <!-- live search: removed submit button -->
            <a href="{{ route('admin.dudi.export', request()->query()) }}" class="btn btn-secondary">Export</a>
            <a href="{{ route('admin.dudi.create') }}" class="btn btn-primary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Tambah DUDI Baru
            </a>
        </form>
    </div>

    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th class="w-70">No</th>
                    <th>Nama DUDI</th>
                    <th>Alamat</th>
                    <th class="w-120">Jam Berangkat</th>
                    <th class="w-120">Jam Pulang</th>
                    <th>Bidang Industri</th>
                    <th>Status</th>
                    <th class="w-120">Jumlah Pegawai</th>
                    <th>Website</th>
                    <th>No. Telp</th>
                    <th>Email</th>
                    <th class="w-120">Kuota</th>
                    <th>Penanggung Jawab</th>
                    <th class="w-180">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dudis as $dudi)
                    <tr>
                        <td>{{ ($dudis->currentPage() - 1) * $dudis->perPage() + $loop->iteration }}</td>
                        <td><strong>{{ $dudi->nama_dudi }}</strong></td>
                        <td>{{ $dudi->alamat }}</td>
                        <td>{{ $dudi->jam_masuk ?? '-' }}</td>
                        <td>{{ $dudi->jam_pulang ?? '-' }}</td>
                        <td>{{ $dudi->bidang_usaha }}</td>
                        <td>
                            @if(strtolower($dudi->status ?? '') === 'active')
                                <span class="status-pill accept">Aktif</span>
                            @else
                                <span class="status-pill reject">Nonaktif</span>
                            @endif
                        </td>
                        <td>{{ $dudi->jumlah_pegawai }}</td>
                        <td>
                            @if($dudi->website)
                                <a href="{{ preg_match('/^https?:\/\//', $dudi->website) ? $dudi->website : 'https://' . $dudi->website }}" target="_blank" rel="noreferrer">{{ $dudi->website }}</a>
                            @endif
                        </td>
                        <td>{{ $dudi->telepon }}</td>
                        <td>{{ $dudi->email }}</td>
                        <td class="text-center">
                            <strong>{{ $dudi->kuota ?? 0 }}</strong>
                        </td>
                        <td>{{ $dudi->pembimbing_dudi }}</td>
                        <td>
                            <div class="action-group">
                                <a href="{{ route('admin.dudi.edit', $dudi->id_dudi) }}" class="edit">Edit</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="13" class="no-data">Tidak ada data DUDI</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($dudis->total() > 0)
        <div class="pagination-container">
            <div class="pagination-info">
                Menampilkan {{ $dudis->firstItem() ?? 0 }} - {{ $dudis->lastItem() ?? 0 }} dari {{ $dudis->total() }} data
            </div>
            <div class="pagination-links">
                @if($dudis->onFirstPage())
                    <span class="disabled">← Sebelumnya</span>
                @else
                    <a href="{{ $dudis->appends(request()->except('page'))->previousPageUrl() }}">← Sebelumnya</a>
                @endif

                @foreach($dudis->getUrlRange(1, $dudis->lastPage()) as $page => $url)
                    @if($page == $dudis->currentPage())
                        <span class="active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}{{ request()->except('page') ? '&' . http_build_query(request()->except('page')) : '' }}">{{ $page }}</a>
                    @endif
                @endforeach

                @if($dudis->hasMorePages())
                    <a href="{{ $dudis->appends(request()->except('page'))->nextPageUrl() }}">Selanjutnya →</a>
                @else
                    <span class="disabled">Selanjutnya →</span>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection

