<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration will:
     *  - Normalize `coin` to UPPER(TRIM(...)) and set empty to NULL
     *  - Merge duplicate rows per (user_id, coin) by summing balances
     *  - Add a UNIQUE index on (user_id, coin)
     */
    public function up(): void
    {
        // Normalize coin values (upper-case, trimmed). Set empty string to NULL so NULLs are allowed.
        DB::statement("UPDATE user_wallets SET coin = UPPER(TRIM(COALESCE(coin, '')))");
        DB::statement("UPDATE user_wallets SET coin = NULL WHERE coin = ''");

        // Merge duplicates for rows that share the same user_id and coin (non-null coin)
        $groups = DB::table('user_wallets')
            ->select(DB::raw('user_id, coin, COUNT(*) as cnt'))
            ->whereNotNull('coin')
            ->groupBy('user_id', 'coin')
            ->having('cnt', '>', 1)
            ->get();

        foreach ($groups as $g) {
            DB::transaction(function () use ($g) {
                $rows = DB::table('user_wallets')
                    ->where('user_id', $g->user_id)
                    ->where('coin', $g->coin)
                    ->orderBy('id')
                    ->get();

                if ($rows->count() < 2) return;

                $base = $rows->first();
                $sum = 0.0;
                $currencyId = $base->currency_id;
                foreach ($rows as $r) {
                    $sum += (float)$r->balance;
                    if (empty($currencyId) && ! empty($r->currency_id)) $currencyId = $r->currency_id;
                }

                // Update base row
                DB::table('user_wallets')->where('id', $base->id)->update([
                    'balance' => $sum,
                    'currency_id' => $currencyId,
                    'coin' => $g->coin,
                    'updated_at' => now(),
                ]);

                // Delete the duplicates (excluding base)
                $others = $rows->slice(1);
                foreach ($others as $o) {
                    DB::table('user_wallets')->where('id', $o->id)->delete();
                }
            });
        }

        // Finally add the unique index on user_id + coin
        Schema::table('user_wallets', function (Blueprint $table) {
            $table->unique(['user_id', 'coin'], 'user_wallets_user_id_coin_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_wallets', function (Blueprint $table) {
            $table->dropUnique('user_wallets_user_id_coin_unique');
        });
    }
};
