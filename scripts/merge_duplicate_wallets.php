<?php
require __DIR__ . "/../vendor/autoload.php";
$app = require __DIR__ . "/../bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\UserWallet;
use Illuminate\Support\Facades\DB;

// Find duplicate wallets per user by coin (case-insensitive)
$groups = DB::table('user_wallets')
    ->selectRaw('user_id, UPPER(COALESCE(coin, "")) as coin_upper, count(*) as cnt')
    ->groupBy('user_id', 'coin_upper')
    ->having('cnt', '>', 1)
    ->get();

if ($groups->isEmpty()) {
    echo "No duplicate wallets found.\n";
    exit(0);
}

foreach ($groups as $g) {
    echo "Merging duplicates for user_id={$g->user_id} coin={$g->coin_upper} (count={$g->cnt})\n";

    $rows = UserWallet::where('user_id', $g->user_id)
        ->whereRaw('UPPER(COALESCE(coin, "")) = ?', [$g->coin_upper])
        ->orderBy('id')
        ->get();

    if ($rows->count() < 2) continue;

    $base = $rows->first();
    $sumBalance = 0.0;
    $currencyId = $base->currency_id;

    foreach ($rows as $r) {
        $sumBalance += (float)$r->balance;
        if (empty($currencyId) && ! empty($r->currency_id)) {
            $currencyId = $r->currency_id;
        }
    }

    // Update base row
    DB::table('user_wallets')->where('id', $base->id)->update([
        'balance' => $sumBalance,
        'currency_id' => $currencyId,
        'coin' => $g->coin_upper,
        'updated_at' => now(),
    ]);

    // Delete other rows
    $others = $rows->slice(1);
    foreach ($others as $o) {
        DB::table('user_wallets')->where('id', $o->id)->delete();
        echo "  removed wallet id={$o->id}\n";
    }

    echo "  merged into id={$base->id} balance={$sumBalance} currency_id={$currencyId}\n";
}

echo "Done.\n";
