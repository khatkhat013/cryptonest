<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Deposit;
use App\Services\WalletService;

// Get USDT deposit that needs crediting
$deposit = Deposit::where('coin', 'USDT')
    ->where('action_status_id', 5)
    ->whereNull('credited_at')
    ->first();

if ($deposit) {
    echo "Found uncredited USDT deposit (ID: {$deposit->id}). Attempting to credit...\n";
    
    // Get wallet balance before
    $wallet = DB::table('user_wallets')
        ->where('user_id', $deposit->user_id)
        ->where('coin', 'USDT')
        ->first();
    
    $balanceBefore = $wallet ? $wallet->balance : 0;
    echo "Wallet balance before: {$balanceBefore} USDT\n";
    
    // Credit the deposit
    WalletService::creditDeposit($deposit);
    
    // Get wallet balance after
    $wallet = DB::table('user_wallets')
        ->where('user_id', $deposit->user_id)
        ->where('coin', 'USDT')
        ->first();
    
    $balanceAfter = $wallet ? $wallet->balance : 0;
    echo "Wallet balance after: {$balanceAfter} USDT\n";
    
    // Check if credited_at was updated
    $deposit = Deposit::find($deposit->id);
    echo "Deposit credited_at: " . ($deposit->credited_at ?? 'NULL') . "\n";
} else {
    echo "No uncredited USDT deposits found.\n";
}