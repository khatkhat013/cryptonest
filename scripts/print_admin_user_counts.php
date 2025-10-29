<?php
// Usage: php scripts/print_admin_user_counts.php
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Admin;

$admins = Admin::withCount('assignedUsers')->orderBy('id')->get();

foreach ($admins as $a) {
    echo sprintf("%3s | %-20s | assigned_users_count=%d\n", $a->id, $a->name, $a->assigned_users_count ?? 0);
}

exit(0);
