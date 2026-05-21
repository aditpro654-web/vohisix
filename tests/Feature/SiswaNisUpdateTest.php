<?php
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Siswa;
use App\Models\Berkas;

class SiswaNisUpdateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function changing_sis_updates_user_username_and_password()
    {
        // Buat siswa dengan NIS 001
        $siswa = Siswa::create([
            'nis' => '001',
            'nama' => 'Test Student',
            'kelas' => 'XII SIJA 1'
        ]);

        User::create([
            'username' => '001',
            'name' => 'Test Student',
            'role' => 'siswa',
            'password' => bcrypt('001'),
        ]);

        Berkas::create([
            'nis' => '001',
            'lengkap' => false,
        ]);

        // Expect login dengan 001/001 berhasil
        $this->assertTrue(
            \Illuminate\Support\Facades\Hash::check('001', User::where('username', '001')->first()->password)
        );

        // Admin login dan ubah NIS siswa dari 001 ke 9999
        $admin = User::create([
            'username' => 'admin_test',
            'name' => 'Admin',
            'role' => 'admin',
            'password' => bcrypt('password'),
        ]);

        $this->actingAs($admin)
            ->put(route('admin.siswa.update', $siswa->nis), [
                'nis' => '9999',
                'nama' => 'Test Student Updated',
                'kelas' => 'XII SIJA 1'
            ]);

        // Verify User username changed
        $this->assertNull(User::where('username', '001')->first());
        $this->assertNotNull(User::where('username', '9999')->first());

        // Verify password reset ke NIS baru
        $user = User::where('username', '9999')->first();
        $this->assertTrue(
            \Illuminate\Support\Facades\Hash::check('9999', $user->password)
        );

        // Verify Berkas NIS updated
        $this->assertNotNull(Berkas::where('nis', '9999')->first());
        $this->assertNull(Berkas::where('nis', '001')->first());
    }
}
