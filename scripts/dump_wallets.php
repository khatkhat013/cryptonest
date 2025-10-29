<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\UserWallet;

$user = User::where('user_id','000001')->first();
if (!$user) { echo "no user\n"; exit(1); }

$rows = UserWallet::where('user_id', $user->id)->get();
foreach ($rows as $r) {
    echo "id={$r->id} user_id={$r->user_id} coin={$r->coin} currency_id={$r->currency_id} balance={$r->balance}\n";
}

echo "done\n";
