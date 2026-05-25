<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Hash;

// Create admin
App\Models\User::updateOrCreate(
    ['username' => 'gwadmin'],
    ['name' => 'Administrator', 'role' => 'admin', 'password' => Hash::make('acm')]
);

// sample siswa
$siswas = [
    ['nis' => '001', 'nama' => 'Ari Pratama', 'kelas' => 'XII SIJA 1'],
    ['nis' => '002', 'nama' => 'Budi Santoso', 'kelas' => 'XII SIJA 1'],
    ['nis' => '003', 'nama' => 'Citra Dewi', 'kelas' => 'XII SIJA 2'],
    ['nis' => '004', 'nama' => 'Dina Kusuma', 'kelas' => 'XII SIJA 2'],
    ['nis' => '005', 'nama' => 'Eka Wijaya', 'kelas' => 'XII SIJA 2'],
];

foreach ($siswas as $s) {
    App\Models\Siswa::updateOrCreate(['nis' => $s['nis']], $s);
    App\Models\User::updateOrCreate(
        ['username' => $s['nis']],
        ['name' => $s['nama'], 'role' => 'siswa', 'password' => Hash::make($s['nis'])]
    );
    App\Models\Berkas::updateOrCreate(['nis' => $s['nis']], ['lengkap' => false]);
}

echo "Seeding minimal selesai\n";
