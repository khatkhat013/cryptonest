<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateConversionsTableAddCurrencyId extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('conversions', function (Blueprint $table) {
            // Add new columns
            $table->unsignedBigInteger('from_currency_id')->nullable()->after('user_id');
            $table->unsignedBigInteger('to_currency_id')->nullable()->after('from_currency_id');

            // Add foreign key constraints
            $table->foreign('from_currency_id')->references('id')->on('currencies');
            $table->foreign('to_currency_id')->references('id')->on('currencies');
            
            // Backfill data - convert existing coin columns to currency_id
            // We perform parameterized updates in two steps to avoid raw SQL and to be DB-agnostic.
            $conversions = DB::table('conversions')->select('id', 'from_coin', 'to_coin')->get();
            foreach ($conversions as $conv) {
                $fromCoin = strtolower($conv->from_coin ?? '');
                $toCoin = strtolower($conv->to_coin ?? '');

                $fromId = DB::table('currencies')->whereRaw('LOWER(symbol) = ?', [$fromCoin])->value('id');
                $toId = DB::table('currencies')->whereRaw('LOWER(symbol) = ?', [$toCoin])->value('id');

                DB::table('conversions')->where('id', $conv->id)->update([
                    'from_currency_id' => $fromId,
                    'to_currency_id' => $toId
                ]);
            }

            // Drop old columns
            $table->dropColumn(['from_coin', 'to_coin']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversions', function (Blueprint $table) {
            // Re-add old columns
            $table->string('from_coin')->nullable()->after('user_id');
            $table->string('to_coin')->nullable()->after('from_coin');

            // Backfill data - convert currency_id back to coin symbols
            $conversions = DB::table('conversions')->select('id', 'from_currency_id', 'to_currency_id')->get();
            foreach ($conversions as $conv) {
                $fromSym = DB::table('currencies')->where('id', $conv->from_currency_id)->value('symbol');
                $toSym = DB::table('currencies')->where('id', $conv->to_currency_id)->value('symbol');

                DB::table('conversions')->where('id', $conv->id)->update([
                    'from_coin' => $fromSym ? strtolower($fromSym) : null,
                    'to_coin' => $toSym ? strtolower($toSym) : null,
                ]);
            }

            // Drop new columns and their foreign key constraints
            $table->dropForeign(['from_currency_id']);
            $table->dropForeign(['to_currency_id']);
            $table->dropColumn(['from_currency_id', 'to_currency_id']);
        });
    }
}