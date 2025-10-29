<?php
// Usage: php scripts/check_user_by_userid.php <user_id_field>
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\AdminWallet;

$userField = $argv[1] ?? null;
if (!$userField) {
    echo "Usage: php scripts/check_user_by_userid.php <user_id_field>\n";
    exit(1);
}

$user = User::with('assignedAdmin')->where('user_id', $userField)->first();
if (!$user) {
    echo "No user found with user_id={$userField}\n";
    exit(1);
}

echo "Found user: id={$user->id} | user_id_field={$user->user_id} | name={$user->name}\n";
if ($user->assignedAdmin) {
    $admin = $user->assignedAdmin;
    echo "Assigned admin: id={$admin->id} | name={$admin->name} | telegram_username={$admin->telegram_username} | email={$admin->email}\n";

    $wallets = AdminWallet::where('admin_id', $admin->id)->with('currency')->get();
    if ($wallets->isEmpty()) {
        echo "Assigned admin has NO wallets (admin_id={$admin->id})\n";
    } else {
        echo "Admin wallets:\n";
        foreach ($wallets as $w) {
            $symbol = $w->currency ? $w->currency->symbol : 'COIN';
            $amount = trim(rtrim(number_format((float)($w->coin_amount ?? 0), 8, '.', ''), '0'), '.') ?: '0';
            echo "  - id={$w->id} | {$symbol} | {$w->address} | amount={$amount}\n";
        }
    }
} else {
    echo "User has no assigned admin. assigned_admin_id={$user->assigned_admin_id}\n";
}

exit(0);
