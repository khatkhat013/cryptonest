<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Try to find user with user_id '000001'
$user = App\Models\User::where('user_id', '000001')->first();

$result = [];
if ($user) {
    $result['user'] = $user->toArray();
    $result['numeric_id'] = $user->id;
    $result['wallets_by_string_user_id'] = App\Models\UserWallet::where('user_id', '000001')->get()->toArray();
    $result['wallets_by_numeric_user_id'] = App\Models\UserWallet::where('user_id', $user->id)->get()->toArray();
} else {
    $result['user'] = null;
    $result['wallets_by_string_user_id'] = App\Models\UserWallet::where('user_id', '000001')->get()->toArray();
    $result['wallets_by_numeric_user_id'] = App\Models\UserWallet::where('user_id', 1)->get()->toArray(); // sample fallback
}

echo json_encode($result, JSON_PRETTY_PRINT);
