<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\TradeOrder;
use App\Models\User;

$user = User::where('user_id', '000001')->first();
if (!$user) { echo "no user\n"; exit(1); }
$userId = $user->id;

$stats = [
    'totalTrades' => TradeOrder::where('user_id', $userId)->whereIn('result', ['win', 'lose'])->count(),
    'winningTrades' => TradeOrder::where('user_id', $userId)->where('result', 'win')->count(),
    'losingTrades' => TradeOrder::where('user_id', $userId)->where('result', 'lose')->count(),
    'totalProfit' => TradeOrder::where('user_id', $userId)->where('result', 'win')->sum('profit_amount'),
    'totalPayout' => TradeOrder::where('user_id', $userId)->where('result', 'win')->sum('payout')
];

print_r($stats);
