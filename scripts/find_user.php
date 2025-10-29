<?php
require __DIR__ . "/../vendor/autoload.php";
$app = require __DIR__ . "/../bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$user = User::where('name', 'MonMon')->first();
if (! $user) {
    echo "No user MonMon\n";
    exit(1);
}

echo "id={$user->id} name={$user->name} email={$user->email}\n";
$wallets = \App\Models\UserWallet::where('user_id', $user->id)->get();
foreach ($wallets as $w) {
    echo "wallet id={$w->id} coin={$w->coin} currency_id={$w->currency_id} balance={$w->balance}\n";
}

$deposits = \App\Models\Deposit::where('user_id', $user->id)->get();
foreach ($deposits as $d) {
    echo "deposit id={$d->id} coin={$d->coin} amount={$d->amount} status_id={$d->action_status_id} credited_at={$d->credited_at}\n";
}
