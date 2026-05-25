<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Siswa;
use App\Models\Dudi;
use App\Http\Controllers\Traits\ExcelExportTrait;
use Illuminate\Http\Request;

class AdminBookingController extends Controller
{
    use ExcelExportTrait;
    /**
     * Display a listing of bookings
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $sortBy = $request->input('sort_by', 'newest');

        $bookings = Booking::with(['siswa', 'dudi']);

        if ($search) {
            $bookings->whereHas('siswa', function($query) use ($search) {
                $query->where('nama', 'like', "%$search%")
                      ->orWhere('nis', 'like', "%$search%")
                      ->orWhere('nomor_absen', 'like', "%$search%");
            });
        }

        if ($status && $status !== '') {
            $bookings->where('status', $status);
        }

        // Apply sorting
        switch ($sortBy) {
            case 'oldest':
                $bookings->orderBy('created_at', 'asc');
                break;
            case 'siswa_asc':
                $bookings->orderBy('nis', 'asc');
                break;
            case 'siswa_desc':
                $bookings->orderBy('nis', 'desc');
                break;
            default: // newest by nomor_absen
                $bookings->orderByRaw('(select nomor_absen from siswas where siswas.nis = bookings.nis) asc');
        }

        $bookings = $bookings->paginate(10);

        // Get statistics
        $totalBooking = Booking::count();
        $bookingDireview = Booking::where('status', 'Direview')->count();
        $bookingDiterima = Booking::where('status', 'Diterima')->count();
        $bookingDitolak = Booking::where('status', 'Ditolak')->count();

        return view('admin.booking.index', compact('bookings', 'search', 'status', 'sortBy', 'totalBooking', 'bookingDireview', 'bookingDiterima', 'bookingDitolak'));
    }

    /**
     * Show form for creating a new booking
     */
    public function create()
    {
        $siswas = Siswa::orderBy('nama')->get();
        $dudis = Dudi::orderBy('nama_dudi')->get();

        return view('admin.booking.create', compact('siswas', 'dudis'));
    }

    /**
     * Store a newly created booking in storage
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nis' => 'required|exists:siswas,nis',
            'id_dudi' => 'required|exists:dudis,id_dudi',
            'status' => 'required|in:Direview,Diterima,Ditolak',
        ]);

        $exists = Booking::where('nis', $validated['nis'])
            ->where('id_dudi', $validated['id_dudi'])
            ->exists();

        if ($exists) {
            return redirect()->route('admin.booking.index')->with('error', 'Booking untuk siswa dan DUDI yang sama sudah ada.');
        }

        Booking::create($validated);

        return redirect()->route('admin.booking.index')->with('success', 'Booking berhasil ditambahkan');
    }

    /**
     * Show booking details
     */
    public function show(Booking $booking)
    {
        return view('admin.booking.show', compact('booking'));
    }

    /**
     * Show form for editing booking status
     */
    public function edit(Booking $booking)
    {
        return view('admin.booking.edit', compact('booking'));
    }

    /**
     * Update booking status
     */
    public function update(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'status' => 'required|in:Direview,Diterima,Ditolak',
        ]);

        $booking->update($validated);

        return redirect()->route('admin.booking.index')->with('success', 'Status booking berhasil diperbarui');
    }

    /**
     * Delete booking
     */
    public function destroy(Booking $booking)
    {
        $booking->delete();

        return redirect()->route('admin.booking.index')->with('success', 'Booking berhasil dihapus');
    }

    public function export(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $bookings = Booking::with(['siswa', 'dudi']);
        if ($search) {
            $bookings->whereHas('siswa', function ($query) use ($search) {
                $query->where('nama', 'like', "%$search%")
                      ->orWhere('nis', 'like', "%$search%");
            });
        }
        if ($status && $status !== '') {
            $bookings->where('status', $status);
        }

        $bookings = $bookings->orderByRaw('(select nomor_absen from siswas where siswas.nis = bookings.nis) asc')->get();
        $rows = $bookings->map(function ($booking) {
            return [
                $booking->siswa?->nis,
                $booking->siswa?->nomor_absen,
                $booking->siswa?->nama,
                $booking->siswa?->kelas,
                $booking->dudi?->nama_dudi,
                $booking->status,
                $booking->created_at->format('d M Y H:i'),
            ];
        })->toArray();

        return $this->streamCsvDownload(
            'booking_export_' . now()->format('Y-m-d') . '.csv',
            ['NIS', 'Nomor Absen', 'Nama Siswa', 'Kelas', 'DUDI', 'Status', 'Tanggal'],
            $rows
        );
    }
}
