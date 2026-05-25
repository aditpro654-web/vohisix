<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;

    protected $primaryKey = 'nis';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'nis',
        'nomor_absen',
        'nama',
        'kelas',
        'foto',
    ];

    public function berkas()
    {
        return $this->hasOne(Berkas::class, 'nis', 'nis');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'nis', 'nis');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'nis', 'username');
    }
}
