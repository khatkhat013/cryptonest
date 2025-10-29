<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Deposit;
use App\Services\WalletService;

// 1. Check USDC deposit status
$deposit = DB::table('deposits')
    ->where('coin', 'USDC')
    ->orderBy('created_at', 'desc')
    ->first();

echo "USDC Deposit Status:\n";
echo "ID: {$deposit->id}\n";
echo "Amount: {$deposit->amount}\n";
echo "Action Status ID: {$deposit->action_status_id}\n";
echo "Credited At: " . ($deposit->credited_at ?? 'NULL') . "\n\n";

// 2. Check USDC wallet
$wallet = DB::table('user_wallets')
    ->where('user_id', $deposit->user_id)
    ->where('coin', 'USDC')
    ->first();

echo "USDC Wallet Status:\n";
if ($wallet) {
    echo "Current Balance: {$wallet->balance}\n";
    echo "Last Updated: {$wallet->updated_at}\n";
} else {
    echo "No USDC wallet found\n";
}

// 3. Try crediting if needed
if ($deposit->action_status_id == 5 && !$deposit->credited_at) {
    echo "\nAttempting to credit deposit...\n";
    WalletService::creditDeposit(Deposit::find($deposit->id));
    
    // Check wallet again
    $wallet = DB::table('user_wallets')
        ->where('user_id', $deposit->user_id)
        ->where('coin', 'USDC')
        ->first();
    
    echo "\nAfter crediting attempt:\n";
    echo "Wallet Balance: " . ($wallet ? $wallet->balance : 'No wallet') . "\n";
    
    // Check if credited_at was updated
    $deposit = DB::table('deposits')->find($deposit->id);
    echo "Credited At: " . ($deposit->credited_at ?? 'NULL') . "\n";
}