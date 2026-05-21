<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = App\Models\User::select('username','name','role')->get();
echo "Total users: " . $users->count() . "\n";
foreach ($users as $u) {
    echo sprintf("%s (%s) - %s\n", $u->username, $u->name, $u->role);
}
