<?php
// Usage: php scripts/list_users_for_admin.php <admin_id>
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Admin;
use App\Models\User;

$adminId = $argv[1] ?? null;
if (!$adminId) {
    echo "Usage: php scripts/list_users_for_admin.php <admin_id>\n";
    exit(1);
}

$admin = Admin::find($adminId);
if (!$admin) {
    echo "Admin id={$adminId} not found\n";
    exit(1);
}

echo "Admin: {$admin->id} | {$admin->name} | role={$admin->role?->name}\n";

// Query as controller does
$usersQuery = User::with(['assignedAdmin']);
if (!$admin->isSuperAdmin()) {
    $usersQuery->where('assigned_admin_id', $admin->id);
}
$users = $usersQuery->latest()->get();

echo "Users returned by query (count=" . $users->count() . "):\n";
foreach ($users as $u) {
    echo sprintf("%3s | user_id=%s | name=%s | assigned_admin_id=%s | assignedAdminName=%s\n", $u->id, $u->user_id, $u->name, $u->assigned_admin_id ?? 'NULL', $u->assignedAdmin?->name ?? 'NULL');
}

// Also print all users table for full picture
$all = User::with('assignedAdmin')->orderBy('id')->get();
echo "\nAll users in DB:\n";
foreach ($all as $u) {
    echo sprintf("%3s | user_id=%s | name=%s | assigned_admin_id=%s | assignedAdminName=%s\n", $u->id, $u->user_id, $u->name, $u->assigned_admin_id ?? 'NULL', $u->assignedAdmin?->name ?? 'NULL');
}

exit(0);
