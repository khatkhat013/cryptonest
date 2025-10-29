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
        if (Schema::hasTable('deposits') && Schema::hasColumn('deposits', 'status')) {
            Schema::table('deposits', function (Blueprint $table) {
                // drop column safely
                $table->dropColumn('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('deposits') && ! Schema::hasColumn('deposits', 'status')) {
            Schema::table('deposits', function (Blueprint $table) {
                $table->string('status')->default('pending');
            });
        }
    }
};
