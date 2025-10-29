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
        if (!Schema::hasTable('user_wallet_transactions')) {
            return;
        }

        Schema::table('user_wallet_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('user_wallet_transactions', 'principal_amount')) {
                $table->decimal('principal_amount', 28, 8)->default(0)->after('amount');
            }
            if (!Schema::hasColumn('user_wallet_transactions', 'plan_profit')) {
                $table->decimal('plan_profit', 28, 8)->default(0)->after('principal_amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('user_wallet_transactions')) {
            return;
        }

        Schema::table('user_wallet_transactions', function (Blueprint $table) {
            if (Schema::hasColumn('user_wallet_transactions', 'plan_profit')) {
                $table->dropColumn('plan_profit');
            }
            if (Schema::hasColumn('user_wallet_transactions', 'principal_amount')) {
                $table->dropColumn('principal_amount');
            }
        });
    }
};
