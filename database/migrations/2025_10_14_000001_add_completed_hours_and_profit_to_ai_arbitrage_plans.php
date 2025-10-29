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
        // If the table doesn't exist yet (test environments), create a minimal table with common fields.
        if (!Schema::hasTable('ai_arbitrage_plans')) {
            Schema::create('ai_arbitrage_plans', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('plan_name')->nullable();
                $table->integer('duration_days')->default(1);
                $table->decimal('quantity', 16, 6)->default(0);
                $table->decimal('daily_revenue_percentage', 8, 4)->default(0);
                $table->decimal('total_profit', 16, 4)->default(0);
                $table->string('status')->default('active');
                $table->integer('completed_hours')->default(0);
                $table->decimal('profit', 10, 2)->default(0.00);
                $table->timestamps();
            });
            return;
        }

        // Table exists — safely add columns if they don't exist yet.
        Schema::table('ai_arbitrage_plans', function (Blueprint $table) {
            if (!Schema::hasColumn('ai_arbitrage_plans', 'completed_hours')) {
                $table->integer('completed_hours')->default(0);
            }
            if (!Schema::hasColumn('ai_arbitrage_plans', 'profit')) {
                $table->decimal('profit', 10, 2)->default(0.00);
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
            if (Schema::hasColumn('ai_arbitrage_plans', 'completed_hours')) {
                $table->dropColumn('completed_hours');
            }
            if (Schema::hasColumn('ai_arbitrage_plans', 'profit')) {
                $table->dropColumn('profit');
            }
        });
    }
};
