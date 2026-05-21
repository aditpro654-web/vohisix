<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Siswa;
use App\Models\Booking;

echo "Wali users:\n";
foreach (User::whereIn('role', ['wali_kelas', 'kakonsli'])->get() as $u) {
    echo sprintf("%s: role=%s kelas_id=%s kelas_second=%s\n", $u->username, $u->role, $u->kelas_id ?? 'NULL', $u->kelas_second ?? 'NULL');
}

echo "\nDistinct siswa kelas:\n";
foreach (Siswa::select('kelas')->distinct()->pluck('kelas') as $k) {
    echo sprintf("[%s]\n", $k);
}
echo "\nSiswa count: " . Siswa::count() . "\n";
echo "Booking count: " . Booking::count() . "\n";
