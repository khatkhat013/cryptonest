<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        if (!Schema::hasTable('trade_orders')) {
            return;
        }

        Schema::table('trade_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('trade_orders', 'final_price')) {
                $table->decimal('final_price', 28, 8)->nullable()->after('purchase_price');
            }
            if (!Schema::hasColumn('trade_orders', 'price_range_percent')) {
                $table->integer('price_range_percent')->nullable()->after('final_price');
            }
            if (!Schema::hasColumn('trade_orders', 'delivery_seconds')) {
                $table->integer('delivery_seconds')->nullable()->after('price_range_percent');
            }
            if (!Schema::hasColumn('trade_orders', 'profit_amount')) {
                $table->decimal('profit_amount', 28, 8)->nullable()->after('delivery_seconds');
            }
            if (!Schema::hasColumn('trade_orders', 'result')) {
                $table->enum('result', ['pending','win','lose','error'])->default('pending')->after('profit_amount');
            }
            if (!Schema::hasColumn('trade_orders', 'force_applied')) {
                $table->boolean('force_applied')->default(false)->after('result');
            }
            if (!Schema::hasColumn('trade_orders', 'meta')) {
                $table->json('meta')->nullable()->after('force_applied');
            }
        });
    }

    public function down()
    {
        if (!Schema::hasTable('trade_orders')) {
            return;
        }

        Schema::table('trade_orders', function (Blueprint $table) {
            // drop columns if they exist
            if (Schema::hasColumn('trade_orders', 'meta')) {
                $table->dropColumn('meta');
            }
            if (Schema::hasColumn('trade_orders', 'force_applied')) {
                $table->dropColumn('force_applied');
            }
            if (Schema::hasColumn('trade_orders', 'result')) {
                $table->dropColumn('result');
            }
            if (Schema::hasColumn('trade_orders', 'profit_amount')) {
                $table->dropColumn('profit_amount');
            }
            if (Schema::hasColumn('trade_orders', 'delivery_seconds')) {
                $table->dropColumn('delivery_seconds');
            }
            if (Schema::hasColumn('trade_orders', 'price_range_percent')) {
                $table->dropColumn('price_range_percent');
            }
            if (Schema::hasColumn('trade_orders', 'final_price')) {
                $table->dropColumn('final_price');
            }
        });
    }
};
