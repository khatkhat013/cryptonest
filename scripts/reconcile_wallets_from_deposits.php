<?php
require __DIR__ . "/../vendor/autoload.php";
$app = require __DIR__ . "/../bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\UserWallet;
use App\Models\Deposit;
use App\Models\Currency;
use Illuminate\Support\Facades\DB;

// Optional user id arg
$userArg = $argv[1] ?? null;

// Build list of user_ids to process
$userIds = [];
if ($userArg) {
    $userIds = [(int)$userArg];
} else {
    $userIds = Deposit::distinct()->pluck('user_id')->toArray();
}

foreach ($userIds as $uid) {
    echo "Reconciling wallets for user {$uid}\n";

    // Collect coins from deposits (only credited deposits)
    $coins = Deposit::where('user_id', $uid)->whereNotNull('credited_at')->distinct()->pluck(DB::raw('UPPER(coin) as coin'))->toArray();

    foreach ($coins as $coin) {
        $coinNorm = strtoupper(trim($coin));
        if ($coinNorm === '') continue;

        $sum = Deposit::where('user_id', $uid)->whereRaw('UPPER(coin)=?', [$coinNorm])->whereNotNull('credited_at')->sum('amount');
        $sum = (float)$sum;

        if ($sum === 0.0) {
            echo "  coin {$coinNorm} sum is zero, skipping\n";
            continue;
        }

        // Find currency id if possible
        $currency = Currency::whereRaw('UPPER(symbol)=?', [$coinNorm])->first();
        $currencyId = $currency ? $currency->id : null;

        // Update or create wallet with reconciled balance
        $existing = UserWallet::where('user_id', $uid)->whereRaw('UPPER(coin)=?', [$coinNorm])->first();
        if ($existing) {
            DB::table('user_wallets')->where('id', $existing->id)->update([
                'balance' => $sum,
                'currency_id' => $currencyId,
                'coin' => $coinNorm,
                'updated_at' => now(),
            ]);
            echo "  updated wallet id={$existing->id} coin={$coinNorm} balance={$sum}\n";
        } else {
            DB::table('user_wallets')->insert([
                'user_id' => $uid,
                'coin' => $coinNorm,
                'currency_id' => $currencyId,
                'balance' => $sum,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            echo "  created wallet coin={$coinNorm} balance={$sum}\n";
        }
    }
}

echo "Done.\n";
