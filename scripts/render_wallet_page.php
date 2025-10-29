<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Simulate visiting /wallet/btc for user with user_id '000001'
$user = App\Models\User::where('user_id', '000001')->first();
Auth::login($user);

$closure = include __DIR__ . '/../routes/web.php';
// routes file returns nothing; instead call the route logic directly is cumbersome.
// Instead render the view directly using view('wallet.detail', ...)

$validType = 'btc';
$address = null;
$initialBalance = 0;
$wallet = App\Models\UserWallet::where('user_id', $user->id)->whereRaw('UPPER(coin)=?', [strtoupper($validType)])->first();
if ($wallet) $initialBalance = $wallet->balance;

echo view('wallet.detail', ['type' => $validType, 'address' => $address, 'initialBalance' => $initialBalance])->render();
