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
        Schema::table('admins', function (Blueprint $table) {
            $table->boolean('is_approved')->default(false)->after('role_id')->comment('Admin must be approved by site owner');
            $table->text('rejection_reason')->nullable()->after('is_approved')->comment('Reason if admin was rejected');
            $table->timestamp('approved_at')->nullable()->after('rejection_reason')->comment('When admin was approved');
            $table->unsignedBigInteger('approved_by')->nullable()->after('approved_at')->comment('Admin ID who approved this admin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn(['is_approved', 'rejection_reason', 'approved_at', 'approved_by']);
        });
    }
};
