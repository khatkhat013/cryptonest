<?php
// Usage: php compare_deposits_wallets.php [userId|name]
require __DIR__ . "/../vendor/autoload.php";
$app = require __DIR__ . "/../bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\DB;

$arg = $argv[1] ?? null;
if (! $arg) {
    echo "Provide a user id or name as first arg. Example: php compare_deposits_wallets.php MonMon\n";
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

// Aggregated wallet sums by coin
$wallets = DB::table('user_wallets')
    ->select(DB::raw('UPPER(TRIM(COALESCE(coin, ""))) as coin'), DB::raw('SUM(balance) as balance'))
    ->where('user_id', $user->id)
    ->whereRaw('TRIM(COALESCE(coin, "")) <> ""')
    ->groupBy(DB::raw('UPPER(TRIM(COALESCE(coin, "")))'))
    ->get();

echo "-- Wallets (aggregated):\n";
foreach ($wallets as $w) {
    printf("coin=%s balance=%s\n", $w->coin, $w->balance);
}

// Aggregated credited deposits by coin
$deposits = DB::table('deposits')
    ->select(DB::raw('UPPER(TRIM(COALESCE(coin, ""))) as coin'), DB::raw('SUM(amount) as amount'))
    ->where('user_id', $user->id)
    ->whereNotNull('credited_at')
    ->whereRaw('TRIM(COALESCE(coin, "")) <> ""')
    ->groupBy(DB::raw('UPPER(TRIM(COALESCE(coin, "")))'))
    ->get();

echo "-- Credited deposits (aggregated):\n";
foreach ($deposits as $d) {
    printf("coin=%s sum_amount=%s\n", $d->coin, $d->amount);
}

// Compare coins
$coins = array_unique(array_merge($wallets->pluck('coin')->map(fn($x)=>strtoupper($x))->toArray(), $deposits->pluck('coin')->map(fn($x)=>strtoupper($x))->toArray()));

if (empty($coins)) {
    echo "No coins found for user.\n";
    exit(0);
}

echo "-- Comparison (wallet vs credited deposits):\n";
foreach ($coins as $coin) {
    $w = $wallets->firstWhere('coin', $coin);
    $d = $deposits->firstWhere('coin', $coin);
    $wBal = $w ? (float)$w->balance : 0.0;
    $dSum = $d ? (float)$d->amount : 0.0;
    $diff = $wBal - $dSum;
    $status = abs($diff) < 0.0000001 ? 'MATCH' : 'DIFFER';
    printf("coin=%s wallet=%s deposits=%s diff=%s => %s\n", $coin, $wBal, $dSum, $diff, $status);
}

exit(0);
