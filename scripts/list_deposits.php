<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Deposit;

$deps = Deposit::orderBy('id','desc')->take(30)->get();
foreach ($deps as $d) {
    echo "id={$d->id} user_id={$d->user_id} coin={$d->coin} amount={$d->amount} status_id={$d->action_status_id} credited_at={$d->credited_at}\n";
}
