<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

function fmtAmount($n) {
    $s = number_format($n ?? 0, 8, '.', '');
    $s = rtrim(rtrim($s, '0'), '.');
    return $s === '' ? '0' : $s;
}

// find user 000001
$user = App\Models\User::where('user_id', '000001')->first();
if (!$user) { echo "User 000001 not found\n"; exit(1); }
$userId = $user->id;
$coin = 'BTC';
$wallet = App\Models\UserWallet::where('user_id', $userId)
            ->whereRaw('UPPER(coin) = ?', [$coin])
            ->first();
$balance = $wallet ? $wallet->balance : 0;
echo "User numeric id: {$userId}\n";
echo "Wallet found: "; var_export((bool)$wallet); echo "\n";
echo "Balance: " . fmtAmount($balance) . " {$coin}\n";
