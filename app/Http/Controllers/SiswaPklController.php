<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Berkas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SiswaPklController extends Controller
{
    /**
     * Display siswa profile and berkas upload form
     */
    public function index()
    {
        $siswa = Siswa::where('nis', Auth::user()->username)->firstOrFail();
        $berkas = Berkas::where('nis', $siswa->nis)->firstOrFail();

        return view('siswa.profile.index', compact('siswa', 'berkas'));
    }

    /**
     * Upload berkas
     */
    public function uploadBerkas(Request $request)
    {
        $validated = $request->validate([
            'ktp_kia' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'surat_sehat' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'kartu_bpjs' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $siswa = Siswa::where('nis', Auth::user()->username)->firstOrFail();
        $berkas = Berkas::where('nis', $siswa->nis)->firstOrFail();

        $files = [];

        foreach (['ktp_kia', 'surat_sehat', 'kartu_bpjs'] as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $filename = time() . '_' . $siswa->nis . '_' . $field . '.' . $file->getClientOriginalExtension();
                $file->storeAs('berkas', $filename, 'public');
                $files[$field] = $filename;
                $files[$field . '_name'] = $file->getClientOriginalName();
            }
        }

        $berkas->update($files);

        // Check if all berkas lengkap
        $allFilled = !empty($berkas->ktp_kia) && !empty($berkas->surat_sehat) && 
                     !empty($berkas->kartu_bpjs);
        
        if ($allFilled) {
            $berkas->update(['lengkap' => true]);
        }

        return redirect()->route('siswa.profile.index');
    }
}
