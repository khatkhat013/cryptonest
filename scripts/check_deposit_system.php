<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Deposit;

// Check latest deposits
$deposits = DB::table('deposits')
    ->select('deposits.*', 'action_statuses.name as status_name')
    ->leftJoin('action_statuses', 'deposits.action_status_id', '=', 'action_statuses.id')
    ->orderBy('deposits.created_at', 'desc')
    ->limit(5)
    ->get();

echo "Latest Deposits:\n";
echo "----------------------------------------\n";
foreach ($deposits as $d) {
    echo "ID: {$d->id}\n";
    echo "Coin: {$d->coin}\n";
    echo "Amount: {$d->amount}\n";
    echo "Status ID: {$d->action_status_id}\n";
    echo "Status Name: {$d->status_name}\n";
    echo "Credited At: " . ($d->credited_at ?? 'NULL') . "\n";
    echo "Created At: {$d->created_at}\n";
    echo "Updated At: {$d->updated_at}\n";
    echo "----------------------------------------\n";
}

// Check wallet balances
$wallets = DB::table('user_wallets')
    ->orderBy('coin')
    ->get();

echo "\nCurrent Wallet Balances:\n";
echo "----------------------------------------\n";
foreach ($wallets as $w) {
    echo "User ID: {$w->user_id}\n";
    echo "Coin: {$w->coin}\n";
    echo "Balance: {$w->balance}\n";
    echo "Updated At: {$w->updated_at}\n";
    echo "----------------------------------------\n";
}

// Check if deposit observer is registered
$providers = glob(base_path('app/Providers/*.php'));
foreach ($providers as $provider) {
    $content = file_get_contents($provider);
    if (strpos($content, 'DepositObserver') !== false) {
        echo "\nFound DepositObserver registration in: " . basename($provider) . "\n";
        echo "----------------------------------------\n";
    }
}