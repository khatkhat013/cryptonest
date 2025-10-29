<?php
// Usage: php scripts/check_admin_role.php <email>
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Admin;

$email = $argv[1] ?? 'admin@example.com';
$admin = Admin::where('email', $email)->with('role')->first();
if (!$admin) {
    echo "Admin not found for email={$email}\n";
    exit(1);
}

echo "id={$admin->id}\n";
echo "email={$admin->email}\n";
echo "role_id={$admin->role_id}\n";
echo "role_name=".($admin->role?->name ?? '(none)')."\n";
echo "isSuperAdmin=".($admin->isSuperAdmin() ? 'true' : 'false')."\n";
exit(0);
