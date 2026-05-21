<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class WaliKelasAndKakonslSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Wali Kelas 1 (XII SIJA 1)
        User::updateOrCreate(
            ['username' => 'wali_kelas_1'],
            [
                'name' => 'Wali Kelas XII SIJA 1',
                'role' => 'wali_kelas',
                'kelas_id' => 'XII SIJA 1',
                'password' => Hash::make('password123'),
            ]
        );

        // Create Wali Kelas 2 (XII SIJA 2)
        User::updateOrCreate(
            ['username' => 'wali_kelas_2'],
            [
                'name' => 'Wali Kelas XII SIJA 2',
                'role' => 'wali_kelas',
                'kelas_id' => 'XII SIJA 2',
                'password' => Hash::make('password123'),
            ]
        );

        // Create Kakonsli (Ketua Konsentrasi Keahlian) - can access both classes
        User::updateOrCreate(
            ['username' => 'kakonsli'],
            [
                'name' => 'Kakonsli SIJA',
                'role' => 'kakonsli',
                'kelas_id' => 'XII SIJA 1',
                'kelas_second' => 'XII SIJA 2',
                'password' => Hash::make('password123'),
            ]
        );
    }
}
