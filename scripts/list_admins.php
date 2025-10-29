<?php
// Small helper to list admins and their roles. Run with: php scripts/list_admins.php
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Admin;

$admins = Admin::with('role')->get();
foreach ($admins as $a) {
    $role = $a->role ? $a->role->name : 'NULL';
    echo $a->id . " | " . $a->name . " | " . $a->email . " | " . $role . PHP_EOL;
}

exit(0);
