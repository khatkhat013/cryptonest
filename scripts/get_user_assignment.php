<?php
// Usage: php scripts/get_user_assignment.php <user_id>
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$userId = $argv[1] ?? null;
if (!$userId) {
    echo "Usage: php scripts/get_user_assignment.php <user_id>\n";
    exit(1);
}

$user = User::with('assignedAdmin')->find($userId);
if (!$user) {
    echo "User id={$userId} not found\n";
    exit(1);
}

echo "User id={$user->id} | user_id_field={$user->user_id} | name={$user->name}\n";
if ($user->assignedAdmin) {
    echo "Assigned admin id={$user->assignedAdmin->id} | name={$user->assignedAdmin->name} | telegram_username={$user->assignedAdmin->telegram_username}\n";
} else {
    echo "No assigned admin for this user. assigned_admin_id={$user->assigned_admin_id}\n";
}

exit(0);
