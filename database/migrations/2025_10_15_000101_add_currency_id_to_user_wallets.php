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
        if (!Schema::hasTable('user_wallets')) {
            return;
        }

        // add column if missing
        if (!Schema::hasColumn('user_wallets', 'currency_id')) {
            Schema::table('user_wallets', function (Blueprint $table) {
                $table->unsignedBigInteger('currency_id')->nullable()->after('user_id');
            });

            // backfill currency_id from coin symbol (if currencies table exists)
            if (Schema::hasTable('currencies')) {
                $wallets = DB::table('user_wallets')->select('id', 'coin')->get();
                foreach ($wallets as $w) {
                    if (!$w->coin) continue;
                    $sym = strtolower($w->coin);
                    $currencyId = DB::table('currencies')->whereRaw('LOWER(symbol) = ?', [$sym])->value('id');
                    if ($currencyId) {
                        DB::table('user_wallets')->where('id', $w->id)->update(['currency_id' => $currencyId]);
                    }
                }
            }

            // try to add FK and unique constraint adjustments
            Schema::table('user_wallets', function (Blueprint $table) {
                // Some MySQL setups have other tables with foreign keys that reference
                // the user_wallets(user_id,coin) index. MySQL prevents dropping an
                // index which is used by a foreign key constraint (error 1553).
                // To be safe, look up any foreign keys that reference user_wallets
                // and drop them first. This query is MySQL-specific and wrapped in
                // try/catch so it won't break other drivers (SQLite, etc.).
                try {
                    $driver = DB::getDriverName();
                } catch (\Throwable $ex) {
                    $driver = null;
                }

                if ($driver === 'mysql') {
                    try {
                        $database = config('database.connections.' . config('database.default') . '.database');
                        $refs = DB::select(
                            "SELECT CONSTRAINT_NAME as constraint_name, TABLE_NAME as table_name FROM information_schema.KEY_COLUMN_USAGE WHERE REFERENCED_TABLE_NAME = 'user_wallets' AND REFERENCED_TABLE_SCHEMA = ?",
                            [$database]
                        );

                        foreach ($refs as $ref) {
                            try {
                                Schema::table($ref->table_name, function (Blueprint $t) use ($ref) {
                                    // drop foreign key by constraint name on the referencing table
                                    $t->dropForeign($ref->constraint_name);
                                });
                            } catch (\Exception $e) {
                                // ignore individual failures
                            }
                        }
                    } catch (\Exception $e) {
                        // ignore information_schema lookup failures
                    }
                }

                // drop unique on user_id+coin if exists — Laravel doesn't provide a direct check, ignore errors
                try {
                    $table->dropUnique(['user_id', 'coin']);
                } catch (\Exception $e) {
                    // ignore if not present
                }

                // ensure index/unique on user_id + currency_id
                try {
                    $table->unique(['user_id', 'currency_id']);
                } catch (\Exception $e) {
                    // ignore
                }

                try {
                    $table->foreign('currency_id')->references('id')->on('currencies');
                } catch (\Exception $e) {
                    // ignore if DB doesn't support or already exists
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('user_wallets')) {
            return;
        }

        if (Schema::hasColumn('user_wallets', 'currency_id')) {
            Schema::table('user_wallets', function (Blueprint $table) {
                try {
                    $table->dropForeign(['currency_id']);
                } catch (\Exception $e) {}
                try {
                    $table->dropUnique(['user_id', 'currency_id']);
                } catch (\Exception $e) {}
                try {
                    $table->unique(['user_id', 'coin']);
                } catch (\Exception $e) {}

                $table->dropColumn('currency_id');
            });
        }
    }
};
