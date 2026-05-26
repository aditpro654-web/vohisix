<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Berkas extends Model
{
    use HasFactory;

    protected $fillable = [
        'nis',
        'ktp_kia',
        'ktp_kia_name',
        'surat_sehat',
        'surat_sehat_name',
        'kartu_bpjs',
        'kartu_bpjs_name',
        'surat_balasan',
        'buku_tabungan',
        'lengkap',
    ];

    protected $casts = [
        'lengkap' => 'boolean',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'nis', 'nis');
    }
}
