<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\UserWallet;
use App\Models\User;

$all = UserWallet::all();
$invalid = [];
foreach ($all as $w) {
    $uid = $w->user_id;
    $user = User::find($uid);
    if (!$user) {
        $invalid[] = ['wallet_id' => $w->id, 'user_id' => $uid, 'coin' => $w->coin, 'balance' => $w->balance];
    }
}

echo json_encode(['total' => $all->count(), 'invalid' => $invalid], JSON_PRETTY_PRINT) . PHP_EOL;
