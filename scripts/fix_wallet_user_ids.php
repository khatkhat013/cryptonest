<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$allWallets = App\Models\UserWallet::all();
$changes = [];
foreach ($allWallets as $w) {
    $current = $w->user_id;
    // If user_id matches some user's numeric id, assume already correct
    $userById = App\Models\User::find($current);
    if ($userById) continue; // already points to valid user.id

    // try matching by string user_id: find user where user.user_id = zero-padded string of current
    $padded = str_pad((string)$current, 6, '0', STR_PAD_LEFT);
    $userByUserId = App\Models\User::where('user_id', $padded)->first();
    if ($userByUserId) {
        $changes[] = ['wallet_id' => $w->id, 'from' => $current, 'to' => $userByUserId->id];
        $w->user_id = $userByUserId->id;
        $w->save();
        continue;
    }

    // try matching by string without padding
    $userByUserId2 = App\Models\User::where('user_id', (string)$current)->first();
    if ($userByUserId2) {
        $changes[] = ['wallet_id' => $w->id, 'from' => $current, 'to' => $userByUserId2->id];
        $w->user_id = $userByUserId2->id;
        $w->save();
        continue;
    }

    // no match found
    $changes[] = ['wallet_id' => $w->id, 'from' => $current, 'to' => null];
}

echo json_encode(['total_wallets' => $allWallets->count(), 'changes' => $changes], JSON_PRETTY_PRINT);
