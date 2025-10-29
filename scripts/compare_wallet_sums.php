<?php
// Usage: php compare_wallet_sums.php [userId|name]
require __DIR__ . "/../vendor/autoload.php";
$app = require __DIR__ . "/../bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\UserWallet;
use Illuminate\Support\Facades\DB;

$arg = $argv[1] ?? null;
if (! $arg) {
    echo "Provide a user id or name as first arg. Example: php compare_wallet_sums.php MonMon\n";
    exit(2);
}

$user = null;
if (is_numeric($arg)) {
    $user = User::find((int)$arg);
} else {
    $user = User::where('name', $arg)->orWhere('email', $arg)->first();
}

if (! $user) {
    echo "User not found for: {$arg}\n";
    exit(3);
}

echo "== User: id={$user->id} name={$user->name} email={$user->email}\n";

$rows = UserWallet::where('user_id', $user->id)->get();
if ($rows->isEmpty()) {
    echo "No wallet rows for this user.\n";
} else {
    echo "-- Raw wallet rows (as in DB):\n";
    foreach ($rows as $w) {
        printf("id=%d coin=%s currency_id=%s balance=%s\n", $w->id, $w->coin, $w->currency_id, $w->balance);
    }
}

// DB aggregation exactly like FinancialRecordController
$agg = DB::table('user_wallets')
    ->select(DB::raw('UPPER(TRIM(COALESCE(coin, ""))) as coin'), DB::raw('SUM(balance) as balance'))
    ->where('user_id', $user->id)
    ->whereRaw('TRIM(COALESCE(coin, "")) <> ""')
    ->groupBy(DB::raw('UPPER(TRIM(COALESCE(coin, "")))'))
    ->get();

if ($agg->isEmpty()) {
    echo "No aggregated rows (no coins).\n";
} else {
    echo "-- DB aggregated sums by coin: \n";
    foreach ($agg as $a) {
        printf("coin=%s sum=%s\n", $a->coin, $a->balance);
    }
}

// PHP-level sum by coin (sum raw rows exactly as DB would if grouping without UPPER/TRIM)
$phpAgg = [];
foreach ($rows as $w) {
    $coin = (string)$w->coin;
    if (trim($coin) === '') continue;
    if (! isset($phpAgg[$coin])) $phpAgg[$coin] = 0.0;
    $phpAgg[$coin] += (float)$w->balance;
}

if (! empty($phpAgg)) {
    echo "-- PHP aggregation by raw coin value (preserves case/whitespace):\n";
    foreach ($phpAgg as $c => $b) {
        printf("coin='%s' sum=%s\n", $c, $b);
    }
}

// Compare DB agg keys with PHP agg normalized keys
$normalizedPhp = [];
foreach ($phpAgg as $c => $b) {
    $k = strtoupper(trim($c));
    if ($k === '') continue;
    if (! isset($normalizedPhp[$k])) $normalizedPhp[$k] = 0.0;
    $normalizedPhp[$k] += $b;
}


echo "-- Comparison (DB agg vs PHP normalized agg):\n";
$allCoins = array_unique(array_merge($agg->pluck('coin')->map(fn($x)=>strtoupper($x))->toArray(), array_keys($normalizedPhp)));
foreach ($allCoins as $coin) {
    $dbRow = $agg->firstWhere('coin', $coin);
    $dbSum = $dbRow ? (float)$dbRow->balance : 0.0;
    $phpSum = $normalizedPhp[$coin] ?? 0.0;
    $ok = abs($dbSum - $phpSum) < 0.0000001 ? 'OK' : 'DIFFER';
    printf("coin=%s db=%s php_normalized=%s => %s\n", $coin, $dbSum, $phpSum, $ok);
}

exit(0);
