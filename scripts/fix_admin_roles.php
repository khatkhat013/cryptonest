<?php
// Fix admin roles: set admin user (id=1) to role_id=1 (admin)
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Admin;

// Set admin with id=1 to role_id 1 (admin)
$admin = Admin::find(1);
if ($admin) {
    $admin->role_id = 1;
    $admin->save();
    echo "Updated admin id=1 to role_id=1\n";
} else {
    echo "Admin id=1 not found\n";
}

exit(0);
