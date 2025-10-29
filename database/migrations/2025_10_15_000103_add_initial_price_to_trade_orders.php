<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('trade_orders')) return;
        if (!Schema::hasColumn('trade_orders', 'initial_price')) {
            Schema::table('trade_orders', function (Blueprint $table) {
                $table->decimal('initial_price', 28, 8)->nullable()->after('purchase_price');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('trade_orders')) return;
        if (Schema::hasColumn('trade_orders', 'initial_price')) {
            Schema::table('trade_orders', function (Blueprint $table) {
                $table->dropColumn('initial_price');
            });
        }
    }
};
