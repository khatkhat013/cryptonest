<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MarkTradeOrdersMigration extends Command
{
    protected $signature = 'migrations:mark-trade-orders';
    protected $description = 'Mark 2025_10_12_000021_create_trade_orders_table as migrated in the migrations table';

    public function handle()
    {
        $migrationName = '2025_10_12_000021_create_trade_orders_table.php';
        $exists = DB::table('migrations')->where('migration', $migrationName)->exists();
        if ($exists) {
            $this->info("Migration '{$migrationName}' already recorded.");
            return 0;
        }

        DB::table('migrations')->insert([
            'migration' => $migrationName,
            'batch' => 1,
        ]);

        $this->info("Inserted migration record for {$migrationName}");
        return 0;
    }
}
