<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

$cols = ['initial_price','purchase_price','final_price','profit_amount','payout','result'];
foreach ($cols as $c) {
    echo $c . ': ' . (Schema::hasColumn('trade_orders', $c) ? 'YES' : 'NO') . "\n";
}
