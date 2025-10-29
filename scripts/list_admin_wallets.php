<?php
// Usage: php scripts/list_admin_wallets.php <admin_id>
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\AdminWallet;

$adminId = $argv[1] ?? null;
if (!$adminId) {
    echo "Usage: php scripts/list_admin_wallets.php <admin_id>\n";
    exit(1);
}

$wallets = AdminWallet::where('admin_id', $adminId)->with('currency')->get();
if ($wallets->isEmpty()) {
    echo "No wallets found for admin_id={$adminId}\n";
    exit(0);
}

foreach ($wallets as $w) {
    $symbol = $w->currency ? $w->currency->symbol : 'COIN';
    $amount = trim(rtrim(number_format((float)($w->coin_amount ?? 0), 8, '.', ''), '0'), '.') ?: '0';
    echo "{$w->id} | admin_id={$w->admin_id} | {$symbol} | {$w->address} | amount={$amount}\n";
}

exit(0);
