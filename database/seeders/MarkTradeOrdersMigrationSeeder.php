<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MarkTradeOrdersMigrationSeeder extends Seeder
{
    public function run()
    {
        $migrationName = '2025_10_12_000021_create_trade_orders_table.php';

        // Check if already present
        $exists = DB::table('migrations')->where('migration', $migrationName)->exists();
        if ($exists) {
            $this->command->info("Migration '{$migrationName}' already recorded.");
            return;
        }

        // Insert with current timestamp and a high batch number so it's marked as run
        DB::table('migrations')->insert([
            'migration' => $migrationName,
            'batch' => 1,
        ]);

        $this->command->info("Inserted migration record for {$migrationName}");
    }
}
