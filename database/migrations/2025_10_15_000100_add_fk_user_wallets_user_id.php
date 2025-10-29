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
        if (!Schema::hasTable('user_wallets') || !Schema::hasTable('users')) {
            return;
        }

        // Try to add the foreign key. Some drivers (sqlite in memory) or older
        // configurations may not support changing columns; wrap in try/catch to
        // avoid hard failures during migrate.
        try {
            Schema::table('user_wallets', function (Blueprint $table) {
                if (!Schema::hasColumn('user_wallets', 'user_id')) return;
                // Attempt to add foreign key; if it already exists, an exception may be thrown
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        } catch (\Exception $e) {
            // swallow exception to keep migration from failing on drivers that
            // don't support the operation in this environment
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('user_wallets')) return;
        Schema::table('user_wallets', function (Blueprint $table) {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $foreignKeys = array_map(function($fk) { return $fk->getName(); }, $sm->listTableForeignKeys('user_wallets'));
            if (in_array('user_wallets_user_id_foreign', $foreignKeys)) {
                $table->dropForeign('user_wallets_user_id_foreign');
            }
        });
    }
};
