<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Deposit;
use App\Models\UserWallet;
use Illuminate\Support\Facades\DB;

// Get all uncredited ETH deposits that are marked as complete
$deposits = Deposit::where('coin', 'ETH')
    ->where('action_status_id', 5)
    ->whereNull('credited_at')
    ->get();

echo "Found " . $deposits->count() . " uncredited ETH deposits\n";

foreach ($deposits as $deposit) {
    echo "Processing deposit ID: " . $deposit->id . "\n";
    
    try {
        DB::beginTransaction();
        
        // Find or create user wallet
        $wallet = UserWallet::firstOrCreate(
            ['user_id' => $deposit->user_id, 'coin' => strtoupper($deposit->coin)],
            ['balance' => 0]
        );
        
        // Add deposit amount to wallet balance
        $wallet->balance += $deposit->amount;
        $wallet->save();
        
        // Mark deposit as credited
        $deposit->credited_at = now();
        $deposit->save();
        
        DB::commit();
        echo "Successfully credited " . $deposit->amount . " ETH to wallet ID: " . $wallet->id . "\n";
    } catch (\Exception $e) {
        DB::rollback();
        echo "Error processing deposit ID " . $deposit->id . ": " . $e->getMessage() . "\n";
    }
}