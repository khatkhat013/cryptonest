<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('ai_arbitrage_plans')) {
            return;
        }

        // Only run raw alter if column exists. Use a raw ALTER to set microsecond precision
        try {
            $hasColumn = Schema::hasColumn('ai_arbitrage_plans', 'completed_at');
            if ($hasColumn) {
                // MySQL/Postgres syntax differs; attempt MySQL style first
                DB::statement("ALTER TABLE `ai_arbitrage_plans` MODIFY COLUMN `completed_at` TIMESTAMP(6) NULL AFTER `status`");
            }
        } catch (\Exception $e) {
            // If altering fails, log and continue — application will still write string timestamps
            \Log::warning('Could not modify ai_arbitrage_plans.completed_at precision: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('ai_arbitrage_plans')) {
            return;
        }

        try {
            $hasColumn = Schema::hasColumn('ai_arbitrage_plans', 'completed_at');
            if ($hasColumn) {
                DB::statement("ALTER TABLE `ai_arbitrage_plans` MODIFY COLUMN `completed_at` TIMESTAMP NULL AFTER `status`");
            }
        } catch (\Exception $e) {
            \Log::warning('Could not revert ai_arbitrage_plans.completed_at precision change: ' . $e->getMessage());
        }
    }
};
