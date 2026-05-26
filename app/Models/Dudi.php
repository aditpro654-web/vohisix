<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dudi extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_dudi';

    protected $fillable = [
        'nama_dudi',
        'alamat',
        'telepon',
        'email',
        'deskripsi',
        'bidang_usaha',
        'website',
        'jumlah_pegawai',
        'logo',
        'pembimbing_dudi',
        'jam_masuk',
        'jam_pulang',
        'kota',
        'kuota',
        'status',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'id_dudi', 'id_dudi');
    }
}
