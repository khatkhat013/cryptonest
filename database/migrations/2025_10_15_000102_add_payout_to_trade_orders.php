<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('trade_orders')) return;

        if (!Schema::hasColumn('trade_orders', 'payout')) {
            Schema::table('trade_orders', function (Blueprint $table) {
                $table->decimal('payout', 28, 8)->nullable()->after('profit_amount');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('trade_orders')) return;

        if (Schema::hasColumn('trade_orders', 'payout')) {
            Schema::table('trade_orders', function (Blueprint $table) {
                $table->dropColumn('payout');
            });
        }
    }
};
