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
        Schema::table('users', function (Blueprint $table) {
            // Add is_active column with default true
            $table->boolean('is_active')->default(true)->after('remember_token');
            
            // Add last_activity_at timestamp
            $table->timestamp('last_activity_at')->nullable()->after('is_active');
            
            // Add last_login_at timestamp
            $table->timestamp('last_login_at')->nullable()->after('last_activity_at');
            
            // Add last_login_ip
            $table->string('last_login_ip', 45)->nullable()->after('last_login_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'is_active',
                'last_activity_at',
                'last_login_at',
                'last_login_ip',
            ]);
        });
    }
};