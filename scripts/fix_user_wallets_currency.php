<?php

require __DIR__ . "/../vendor/autoload.php";

$app = require __DIR__ . "/../bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\UserWallet;
use App\Models\Deposit;
use App\Models\Currency;
use Illuminate\Support\Facades\DB;

$rows = UserWallet::whereNull('currency_id')->orWhereNull('coin')->get();
if ($rows->isEmpty()) {
    echo "No user_wallets with null currency_id or coin\n";
    exit(0);
}

foreach ($rows as $w) {
    echo "Fixing wallet id={$w->id} user_id={$w->user_id}\n";

    // Try to find a recent deposit for this user with a non-empty coin
    $d = Deposit::where('user_id', $w->user_id)
        ->whereNotNull('coin')
        ->where('coin', '<>', '')
        ->orderBy('created_at', 'desc')
        ->first();

    $coin = null;
    $currencyId = null;

    if ($d) {
        $coin = strtoupper(trim($d->coin));
        $currency = Currency::whereRaw('UPPER(symbol)=?', [$coin])->first();
        if ($currency) {
            $currencyId = $currency->id;
        }
    }

    // If still no coin, try to infer from balance (fallback not ideal)
    if (! $coin) {
        echo "  No deposit coin found for user {$w->user_id}, skipping coin assignment\n";
    }

    $update = ['updated_at' => now()];
    if ($coin) {
        $update['coin'] = $coin;
    }
    if ($currencyId) {
        $update['currency_id'] = $currencyId;
    }

    if (count($update) > 1) {
        DB::table('user_wallets')->where('id', $w->id)->update($update);
        echo "  Updated wallet id={$w->id} with coin={$coin} currency_id={$currencyId}\n";
    } else {
        echo "  Nothing to update for wallet id={$w->id}\n";
    }
}

echo "Done.\n";
