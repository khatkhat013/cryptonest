<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Deposit;
use App\Services\WalletService;

// First clear credited_at for test
DB::table('deposits')
    ->where('coin', 'USDC')
    ->update(['credited_at' => null]);

echo "Reset credited_at timestamp for USDC deposits\n\n";

// Get the deposit and try crediting
$deposit = DB::table('deposits')
    ->where('coin', 'USDC')
    ->where('action_status_id', 5)
    ->first();

if ($deposit) {
    echo "Found USDC deposit to credit:\n";
    echo "ID: {$deposit->id}\n";
    echo "Amount: {$deposit->amount} USDC\n";
    echo "Action Status: {$deposit->action_status_id}\n\n";
    
    // Get wallet balance before
    $before = DB::table('user_wallets')
        ->where('user_id', $deposit->user_id)
        ->where('coin', 'USDC')
        ->first();
    
    echo "Wallet before:\n";
    echo "Balance: " . ($before ? $before->balance : 'No wallet') . "\n\n";
    
    // Try crediting
    echo "Attempting to credit deposit...\n";
    WalletService::creditDeposit(Deposit::find($deposit->id));
    
    // Check results
    $after = DB::table('user_wallets')
        ->where('user_id', $deposit->user_id)
        ->where('coin', 'USDC')
        ->first();
    
    $deposit = DB::table('deposits')->find($deposit->id);
    
    echo "\nResults:\n";
    echo "New Balance: " . ($after ? $after->balance : 'No wallet') . "\n";
    echo "Credited At: " . ($deposit->credited_at ?? 'NULL') . "\n";
} else {
    echo "No USDC deposit found with action_status_id = 5\n";
}