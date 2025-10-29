<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Deposit;

// Create test deposit first
$deposit = Deposit::create([
    'user_id' => 2,
    'admin_id' => 1,
    'coin' => 'TEST2',
    'amount' => 5.00,
    'image_path' => 'test/image.png',
    'action_status_id' => 1
]);

echo "Created test deposit (ID: {$deposit->id})\n";
echo "Initial status: {$deposit->action_status_id}\n\n";

// Get initial wallet balance
$wallet = DB::table('user_wallets')
    ->where('user_id', 2)
    ->where('coin', 'TEST2')
    ->first();

echo "Initial wallet balance: " . ($wallet ? $wallet->balance : 'No wallet') . "\n\n";

// Simulate the controller's updateStatus action
echo "Simulating controller update...\n";
DB::transaction(function() use ($deposit) {
    $deposit->action_status_id = 5; // Set to complete
    $deposit->sent_address = '0x123...';
    $deposit->save();
});

// Wait a moment for the observer
sleep(1);

// Check final state
$wallet = DB::table('user_wallets')
    ->where('user_id', 2)
    ->where('coin', 'TEST2')
    ->first();

echo "\nFinal Results:\n";
echo "Wallet balance: " . ($wallet ? $wallet->balance : 'No wallet') . "\n";

// Check if deposit was updated
$deposit->refresh();
echo "Deposit status: {$deposit->action_status_id}\n";
echo "Credited at: " . ($deposit->credited_at ?? 'NULL') . "\n";