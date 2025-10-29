<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserWallet;

$user = User::where('user_id', '000001')->first();
if (!$user) { echo "no user\n"; exit(1); }

// Ensure a BTC wallet with at least 1 exists
$btc = UserWallet::where('user_id', $user->id)->where('coin', 'btc')->first();
if (!$btc) {
    $btc = UserWallet::create(['user_id' => $user->id, 'coin' => 'btc', 'balance' => '100']);
}

// ensure a ETH wallet exists
$eth = UserWallet::where('user_id', $user->id)->where('coin', 'eth')->first();
if (!$eth) {
    $eth = UserWallet::create(['user_id' => $user->id, 'coin' => 'eth', 'balance' => '0']);
}

// Prepare fake request to ConversionController::store (it expects currency ids). We'll find currency ids by symbol
$fromCurrency = App\Models\Currency::whereRaw('UPPER(symbol)=?', ['BTC'])->first();
$toCurrency = App\Models\Currency::whereRaw('UPPER(symbol)=?', ['ETH'])->first();
if (!$fromCurrency || !$toCurrency) { echo "missing currencies\n"; exit(1); }

$req = Request::create('/wallet/convert', 'POST', [
    'from_currency_id' => $fromCurrency->id,
    'to_currency_id' => $toCurrency->id,
    'from_amount' => 1,
    'to_amount' => 2000 // pretend rate
]);
$req->setUserResolver(function() use ($user) { return $user; });

$ctrl = new App\Http\Controllers\ConversionController();
$resp = $ctrl->store($req);

echo "Status: " . $resp->getStatusCode() . "\n";
echo "Body: " . $resp->getContent() . "\n";

$btc = UserWallet::where('user_id', $user->id)->where('coin', 'btc')->first();
$eth = UserWallet::where('user_id', $user->id)->where('coin', 'eth')->first();

echo "Post balances BTC={$btc->balance} ETH={$eth->balance}\n";
