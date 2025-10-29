<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Check action_statuses table
$statuses = DB::table('action_statuses')->get();
echo "Current action_statuses:\n";
foreach ($statuses as $status) {
    echo "{$status->id}: {$status->name}\n";
}

// Run seeder if empty
if ($statuses->isEmpty()) {
    echo "\nNo statuses found. Running seeder...\n";
    $seeder = new \Database\Seeders\ActionStatusSeeder();
    $seeder->run();
    
    // Check again
    $statuses = DB::table('action_statuses')->get();
    echo "\nAfter seeding:\n";
    foreach ($statuses as $status) {
        echo "{$status->id}: {$status->name}\n";
    }
}