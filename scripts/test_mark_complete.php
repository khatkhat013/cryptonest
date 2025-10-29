<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Deposit;
use App\Models\ActionStatus;
use Illuminate\Support\Facades\DB;

$depositId = $argv[1] ?? 2;
$completeId = ActionStatus::where('name', 'complete')->value('id');
if (!$completeId) {
    echo "No complete status id found\n"; exit(1);
}

$deposit = Deposit::find($depositId);
if (!$deposit) { echo "Deposit not found\n"; exit(1); }

echo "Before: action_status_id={$deposit->action_status_id}, credited_at={$deposit->credited_at}\n";
$deposit->action_status_id = $completeId;
$deposit->save();

$deposit = Deposit::find($depositId);
echo "After: action_status_id={$deposit->action_status_id}, credited_at={$deposit->credited_at}\n";
$wallet = DB::table('user_wallets')->where('user_id', $deposit->user_id)->where('coin', strtoupper($deposit->coin))->first();
if ($wallet) {
    echo "Wallet balance: {$wallet->balance}\n";
} else {
    echo "No wallet created for user\n";
}
