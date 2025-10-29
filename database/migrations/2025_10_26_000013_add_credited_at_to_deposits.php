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
        if (Schema::hasTable('deposits') && ! Schema::hasColumn('deposits', 'credited_at')) {
            Schema::table('deposits', function (Blueprint $table) {
                $table->timestamp('credited_at')->nullable()->after('action_status_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('deposits') && Schema::hasColumn('deposits', 'credited_at')) {
            Schema::table('deposits', function (Blueprint $table) {
                $table->dropColumn('credited_at');
            });
        }
    }
};
