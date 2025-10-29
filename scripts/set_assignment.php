<?php
// Usage: php scripts/set_assignment.php <user_id> <admin_id>
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$userId = $argv[1] ?? null;
$adminId = $argv[2] ?? null;
if (!$userId || !$adminId) {
    echo "Usage: php scripts/set_assignment.php <user_id> <admin_id>\n";
    exit(1);
}

$user = User::find($userId);
if (!$user) {
    echo "User id={$userId} not found\n";
    exit(1);
}

$user->assigned_admin_id = $adminId;
$user->assigned_admin_date = now();
$user->save();

echo "Assigned user {$user->id} to admin {$adminId} at {$user->assigned_admin_date}\n";

exit(0);
