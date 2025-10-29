<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Deposit;

// Clear log file
@unlink(storage_path('logs/laravel.log'));

// Create test deposit
$deposit = Deposit::create([
    'user_id' => 2,
    'admin_id' => 1,
    'coin' => 'TEST',
    'amount' => 1.00,
    'image_path' => 'test/image.png',
    'action_status_id' => 1
]);

echo "Created test deposit (ID: {$deposit->id})\n";

// Get initial wallet balance
$wallet = DB::table('user_wallets')
    ->where('user_id', 2)
    ->where('coin', 'TEST')
    ->first();

echo "Initial wallet balance: " . ($wallet ? $wallet->balance : 'No wallet') . "\n";

// Update status to complete
echo "Updating status to complete (5)...\n";
$deposit->action_status_id = 5;
$deposit->save();

// Sleep briefly to allow observer to work
sleep(1);

// Check final state
$wallet = DB::table('user_wallets')
    ->where('user_id', 2)
    ->where('coin', 'TEST')
    ->first();

echo "Final wallet balance: " . ($wallet ? $wallet->balance : 'No wallet') . "\n";

// Check if deposit was credited
$deposit->refresh();
echo "Deposit credited_at: " . ($deposit->credited_at ?? 'NULL') . "\n";

// Check logs
echo "\nLog entries:\n";
if (file_exists(storage_path('logs/laravel.log'))) {
    echo file_get_contents(storage_path('logs/laravel.log')) . "\n";
} else {
    echo "No log file found\n";
}