<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// 1. Add default value constraint to action_status_id
Schema::table('deposits', function (Blueprint $table) {
    // First remove any existing default constraint
    DB::statement('ALTER TABLE deposits ALTER COLUMN action_status_id DROP DEFAULT');
    
    // Then add the new default constraint
    DB::statement('ALTER TABLE deposits ALTER COLUMN action_status_id SET DEFAULT 1');
});

echo "Added default value (1) to action_status_id\n";

// 2. Update existing NULL values to 1
DB::table('deposits')
    ->whereNull('action_status_id')
    ->update(['action_status_id' => 1]);

echo "Updated existing NULL action_status_id to 1\n";

// 3. Verify changes
$nullCount = DB::table('deposits')
    ->whereNull('action_status_id')
    ->count();

echo "Remaining NULL action_status_id count: " . $nullCount . "\n";

// 4. Show sample of recent deposits
$deposits = DB::table('deposits')
    ->orderBy('created_at', 'desc')
    ->limit(5)
    ->get(['id', 'coin', 'amount', 'action_status_id', 'created_at']);

echo "\nRecent deposits:\n";
foreach ($deposits as $d) {
    echo "ID: {$d->id}, Coin: {$d->coin}, Status: {$d->action_status_id}, Created: {$d->created_at}\n";
}