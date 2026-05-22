<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Siswa;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestSiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create or update a test siswa user
        $user = User::firstOrCreate(
            ['username' => 'siswa001'],
            [
                'name' => 'Siswa Test',
                'password' => Hash::make('password'),
                'role' => 'siswa',
            ]
        );

        // Create or update corresponding siswa record
        Siswa::firstOrCreate(
            ['nis' => 'siswa001'],
            [
                'nama' => 'Siswa Test',
                'kelas' => '12 TKJ 1',
            ]
        );

        // Create or update another test siswa
        $user2 = User::firstOrCreate(
            ['username' => 'siswa002'],
            [
                'name' => 'Siswa Kedua',
                'password' => Hash::make('password'),
                'role' => 'siswa',
            ]
        );

        Siswa::firstOrCreate(
            ['nis' => 'siswa002'],
            [
                'nama' => 'Siswa Kedua',
                'kelas' => '12 TKJ 2',
            ]
        );

        echo "✓ Test siswa data seeded successfully!\n";
        echo "  Username: siswa001, Password: password\n";
        echo "  Username: siswa002, Password: password\n";
    }
}
