<?php
require __DIR__ . "/../vendor/autoload.php";
$app = require __DIR__ . "/../bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\UserWallet;

$userId = $argv[1] ?? null;
$symbol = $argv[2] ?? null;

if (! $userId) {
    echo "Usage: php scripts/check_user_balance.php <user_id> [symbol]\n";
    exit(1);
}

if ($symbol) {
    $w = UserWallet::where('user_id', $userId)->whereRaw('UPPER(coin)=?', [strtoupper($symbol)])->first();
    if (! $w) {
        echo "No wallet for user {$userId} coin {$symbol}\n";
    } else {
        echo "Wallet id={$w->id} user_id={$w->user_id} coin={$w->coin} currency_id={$w->currency_id} balance={$w->balance}\n";
    }
} else {
    $rows = UserWallet::where('user_id', $userId)->get();
    foreach ($rows as $r) {
        echo "id={$r->id} coin={$r->coin} currency_id={$r->currency_id} balance={$r->balance}\n";
    }
}
