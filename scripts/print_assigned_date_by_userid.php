<?php
// Usage: php scripts/print_assigned_date_by_userid.php <user_id_field>
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$userField = $argv[1] ?? null;
if (!$userField) {
    echo "Usage: php scripts/print_assigned_date_by_userid.php <user_id_field>\n";
    exit(1);
}

$user = User::where('user_id', $userField)->first();
if (!$user) {
    echo "No user found with user_id={$userField}\n";
    exit(1);
}

echo "User: id={$user->id} | user_id={$user->user_id} | name={$user->name}\n";
echo "assigned_admin_id={$user->assigned_admin_id}\n";
if ($user->assigned_admin_date) {
    echo "assigned_admin_date={$user->assigned_admin_date}\n";
} else {
    echo "assigned_admin_date: null\n";
}

exit(0);
