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
        if (!Schema::hasTable('ai_arbitrage_plans')) {
            return;
        }

        Schema::table('ai_arbitrage_plans', function (Blueprint $table) {
            if (!Schema::hasColumn('ai_arbitrage_plans', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('ai_arbitrage_plans')) {
            return;
        }

        Schema::table('ai_arbitrage_plans', function (Blueprint $table) {
            if (Schema::hasColumn('ai_arbitrage_plans', 'completed_at')) {
                $table->dropColumn('completed_at');
            }
        });
    }
};
