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
        if (Schema::hasTable('deposits') && ! Schema::hasColumn('deposits', 'sent_address')) {
            Schema::table('deposits', function (Blueprint $table) {
                $table->string('sent_address')->nullable()->after('image_path');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('deposits') && Schema::hasColumn('deposits', 'sent_address')) {
            Schema::table('deposits', function (Blueprint $table) {
                $table->dropColumn('sent_address');
            });
        }
    }
};
