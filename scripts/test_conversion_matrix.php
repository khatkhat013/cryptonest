<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserWallet;
use App\Models\Currency;

$user = User::where('user_id', '000001')->first();
if (!$user) { echo "no user\n"; exit(1); }

// Helper to ensure wallet exists
function ensureWallet($userId, $coin, $balance = '0') {
    $w = UserWallet::where('user_id', $userId)->where('coin', $coin)->first();
    if (!$w) $w = UserWallet::create(['user_id' => $userId, 'coin' => $coin, 'balance' => $balance]);
    return $w;
}

ensureWallet($user->id, 'btc', '100');
ensureWallet($user->id, 'eth', '0');
ensureWallet($user->id, 'doge', '0');
ensureWallet($user->id, 'xrp', '0');

$pairs = [
    ['from' => 'BTC', 'to' => 'ETH', 'from_amt' => 1, 'to_amt' => 2000],
    ['from' => 'BTC', 'to' => 'DOGE', 'from_amt' => 1, 'to_amt' => 30000],
    ['from' => 'ETH', 'to' => 'XRP', 'from_amt' => 1, 'to_amt' => 5000],
];

foreach ($pairs as $p) {
    $fromC = Currency::whereRaw('UPPER(symbol)=?', [$p['from']])->first();
    $toC = Currency::whereRaw('UPPER(symbol)=?', [$p['to']])->first();
    if (!$fromC || !$toC) { echo "Missing currency pair {$p['from']}->{$p['to']}\n"; continue; }

    $req = Request::create('/wallet/convert', 'POST', [
        'from_currency_id' => $fromC->id,
        'to_currency_id' => $toC->id,
        'from_amount' => $p['from_amt'],
        'to_amount' => $p['to_amt']
    ]);
    $req->setUserResolver(function() use ($user) { return $user; });

    $ctrl = new App\Http\Controllers\ConversionController();
    $resp = $ctrl->store($req);

    echo "=== {$p['from']} -> {$p['to']} ===\n";
    echo "Status: " . $resp->getStatusCode() . "\n";
    echo "Body: " . $resp->getContent() . "\n";

    $fromW = UserWallet::where('user_id', $user->id)->where('coin', strtolower($p['from']))->first();
    $toW = UserWallet::where('user_id', $user->id)->where('coin', strtolower($p['to']))->first();
    echo "Post balances {$p['from']}={$fromW->balance} {$p['to']}={$toW->balance}\n\n";
}

echo "Done.\n";
