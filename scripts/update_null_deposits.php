<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Update deposit where action_status_id is null
DB::table('deposits')
    ->whereNull('action_status_id')
    ->update(['action_status_id' => 1]);

// Show current deposits and their statuses
$deposits = DB::table('deposits')
    ->select('deposits.*', 'action_statuses.name as status_name')
    ->leftJoin('action_statuses', 'deposits.action_status_id', '=', 'action_statuses.id')
    ->orderBy('deposits.created_at', 'desc')
    ->get();

echo "Current deposits:\n";
foreach ($deposits as $d) {
    echo "ID: {$d->id}, Coin: {$d->coin}, Status: {$d->status_name} (ID: {$d->action_status_id}), Created: {$d->created_at}\n";
}