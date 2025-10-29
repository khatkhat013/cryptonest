<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

$cols = Schema::getColumnListing('user_wallets');
echo "user_wallets table columns:\n";
foreach ($cols as $c) {
    echo " - $c\n";
}
