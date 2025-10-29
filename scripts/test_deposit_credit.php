<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Deposit;

// Test deposit for each coin
$coins = ['BTC', 'ETH', 'USDT', 'USDC'];
$testUserId = 2;

foreach ($coins as $coin) {
    echo "\nTesting {$coin} deposit:\n";
    echo "------------------------\n";
    
    // Check existing wallet
    $wallet = DB::table('user_wallets')
        ->where('user_id', $testUserId)
        ->where('coin', $coin)
        ->first();
        
    echo "Initial wallet balance: " . ($wallet ? $wallet->balance : "No wallet") . "\n";
    
    // Create new deposit
    $deposit = Deposit::create([
        'user_id' => $testUserId,
        'admin_id' => 1,
        'coin' => $coin,
        'amount' => 1.0,
        'image_path' => 'test/deposit.png',
        'action_status_id' => 1 // Start as pending
    ]);
    
    echo "Created deposit ID: {$deposit->id}\n";
    
    // Update to complete
    echo "Updating to complete status...\n";
    $deposit->update(['action_status_id' => 5]);
    
    // Check final wallet balance
    $wallet = DB::table('user_wallets')
        ->where('user_id', $testUserId)
        ->where('coin', $coin)
        ->first();
        
    echo "Final wallet balance: " . ($wallet ? $wallet->balance : "No wallet") . "\n";
    
    // Verify credited_at
    $deposit = DB::table('deposits')->find($deposit->id);
    echo "Credited at: " . ($deposit->credited_at ?? 'NULL') . "\n";
}