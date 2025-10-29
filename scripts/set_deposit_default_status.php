<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Set default value for existing null action_status_id to 1 (pending)
DB::table('deposits')
    ->whereNull('action_status_id')
    ->update(['action_status_id' => 1]);

// Verify column was updated
$nullCount = DB::table('deposits')
    ->whereNull('action_status_id')
    ->count();

echo "Remaining null action_status_id count: " . $nullCount . "\n";