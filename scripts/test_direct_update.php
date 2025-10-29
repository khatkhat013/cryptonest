<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Create a test deposit directly in DB
$depositId = DB::table('deposits')->insertGetId([
    'user_id' => 2,
    'admin_id' => 1,
    'coin' => 'TEST3',
    'amount' => 7.5,
    'image_path' => 'test/image.png',
    'action_status_id' => 1,
    'created_at' => now(),
    'updated_at' => now()
]);

echo "Created test deposit (ID: {$depositId})\n";

// Get initial wallet state
$wallet = DB::table('user_wallets')
    ->where('user_id', 2)
    ->where('coin', 'TEST3')
    ->first();

echo "Initial wallet balance: " . ($wallet ? $wallet->balance : 'No wallet') . "\n\n";

// Update deposit status directly in DB
echo "Updating deposit status to complete (5)...\n";
DB::table('deposits')
    ->where('id', $depositId)
    ->update([
        'action_status_id' => 5,
        'updated_at' => now()
    ]);

// Run the update command
echo "\nRunning update command...\n";
$kernel->call('deposit:update-balance');

// Check final state
$wallet = DB::table('user_wallets')
    ->where('user_id', 2)
    ->where('coin', 'TEST3')
    ->first();

echo "\nFinal Results:\n";
echo "Wallet balance: " . ($wallet ? $wallet->balance : 'No wallet') . "\n";

// Check deposit credited status
$deposit = DB::table('deposits')->find($depositId);
echo "Credited at: " . ($deposit->credited_at ?? 'NULL') . "\n";