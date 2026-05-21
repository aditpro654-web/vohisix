<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Siswa;
use App\Models\Dudi;
use App\Models\Berkas;

class BookingQuotaTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function siswa_cant_register_when_dudi_quota_is_full()
    {
        // Buat DUDI dengan kuota 2
        $dudi = Dudi::create([
            'nama_dudi' => 'Test DUDI',
            'alamat' => 'Jl Test',
            'telepon' => '081234',
            'email' => 'test@example.com',
            'deskripsi' => 'Deskripsi',
            'bidang_usaha' => 'Testing',
            'kuota' => 2,
        ]);

        // Buat 3 siswa dengan user masing-masing
        $siswaData = [
            ['nis' => '9001', 'nama' => 'Satu'],
            ['nis' => '9002', 'nama' => 'Dua'],
            ['nis' => '9003', 'nama' => 'Tiga'],
        ];

        foreach ($siswaData as $sd) {
            Siswa::create([
                'nis' => $sd['nis'],
                'nama' => $sd['nama'],
                'kelas' => 'XII SIJA 1',
            ]);

            User::create([
                'username' => $sd['nis'],
                'name' => $sd['nama'],
                'role' => 'siswa',
                'password' => bcrypt($sd['nis']),
            ]);

            Berkas::create([
                'nis' => $sd['nis'],
                'lengkap' => true,
            ]);
        }

        // Dua siswa pertama berhasil mendaftar
        $first = User::where('username', '9001')->first();
        $this->actingAs($first)->post(route('siswa.dudi.ajukan', $dudi->id_dudi))
             ->assertSessionHas('success');

        $second = User::where('username', '9002')->first();
        $this->actingAs($second)->post(route('siswa.dudi.ajukan', $dudi->id_dudi))
             ->assertSessionHas('success');

        // Ketiga siswa harus ditolak karena kuota penuh
        $third = User::where('username', '9003')->first();
        $this->actingAs($third)->post(route('siswa.dudi.ajukan', $dudi->id_dudi))
             ->assertSessionHas('error');
    }
}
