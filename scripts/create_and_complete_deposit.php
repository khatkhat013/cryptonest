<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\ActionStatus;
use App\Models\Deposit;

// pick an existing user
$user = DB::table('users')->first();
if (!$user) { echo "No users found\n"; exit(1); }

$completeId = ActionStatus::where('name', 'complete')->value('id');
if (!$completeId) { echo "No complete status id\n"; exit(1); }

// create deposit
$id = DB::table('deposits')->insertGetId([
    'user_id' => $user->id,
    'admin_id' => null,
    'coin' => 'usdt',
    'amount' => '12.34',
    'sent_address' => 'testaddr',
    'created_at' => now(),
    'updated_at' => now(),
    'action_status_id' => ActionStatus::where('name','pending')->value('id') ?? 1,
]);

echo "Created deposit id $id\n";

// Now mark complete via model to fire observer
$deposit = Deposit::find($id);
$deposit->action_status_id = $completeId;
$deposit->save();

echo "Marked complete.\n";
$wallet = DB::table('user_wallets')->where('user_id', $user->id)->where('coin','USDT')->first();
if ($wallet) {
    echo "Wallet exists with balance: {$wallet->balance}\n";
} else {
    echo "No wallet for USDT found for user.\n";
}
