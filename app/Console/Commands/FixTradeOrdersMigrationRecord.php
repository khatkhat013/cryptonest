<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixTradeOrdersMigrationRecord extends Command
{
    protected $signature = 'migrations:fix-trade-orders';
    protected $description = 'Fix migration record for trade_orders: remove incorrect entries and insert canonical migration name';

    public function handle()
    {
        $rawLike = '%trade_orders%';
        $rows = DB::table('migrations')->where('migration', 'like', $rawLike)->get();

        if ($rows->isNotEmpty()) {
            foreach ($rows as $r) {
                $this->info("Deleting migration row: {$r->migration}");
                DB::table('migrations')->where('id', $r->id)->delete();
            }
        }

        $migrationName = '2025_10_12_000021_create_trade_orders_table';
        $exists = DB::table('migrations')->where('migration', $migrationName)->exists();
        if (!$exists) {
            // choose batch 1 to match existing migrations
            DB::table('migrations')->insert([
                'migration' => $migrationName,
                'batch' => 1,
            ]);
            $this->info("Inserted canonical migration record: {$migrationName}");
        } else {
            $this->info("Canonical migration record already exists: {$migrationName}");
        }

        return 0;
    }
}
