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
            // Add columns only if they don't already exist (some environments already have these)
            if (!Schema::hasColumn('admins', 'is_approved')) {
                $table->boolean('is_approved')->default(false)->after('role_id')->comment('Admin must be approved by site owner');
            }

            if (!Schema::hasColumn('admins', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('is_approved')->comment('Reason if admin was rejected');
            }

            if (!Schema::hasColumn('admins', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('rejection_reason')->comment('When admin was approved');
            }

            if (!Schema::hasColumn('admins', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable()->after('approved_at')->comment('Admin ID who approved this admin');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            // Drop only if columns exist
            if (Schema::hasColumn('admins', 'is_approved')) {
                $table->dropColumn('is_approved');
            }
            if (Schema::hasColumn('admins', 'rejection_reason')) {
                $table->dropColumn('rejection_reason');
            }
            if (Schema::hasColumn('admins', 'approved_at')) {
                $table->dropColumn('approved_at');
            }
            if (Schema::hasColumn('admins', 'approved_by')) {
                $table->dropColumn('approved_by');
            }
        });
    }
};
