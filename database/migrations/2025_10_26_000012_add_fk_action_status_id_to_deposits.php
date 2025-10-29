<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('deposits')) return;

        // Only attempt to add the foreign key if the column exists and the
        // referenced table exists.
        if (! Schema::hasColumn('deposits', 'action_status_id')) return;
        if (! Schema::hasTable('action_statuses')) return;

        // Add foreign key if it doesn't already exist. Different DBs handle
        // FK listing differently; attempt to create and ignore errors.
        try {
            Schema::table('deposits', function (Blueprint $table) {
                $table->foreign('action_status_id', 'deposits_action_status_id_foreign')
                    ->references('id')->on('action_statuses')
                    ->onDelete('set null');
            });
        } catch (\Exception $e) {
            // ignore - likely the FK already exists or the DB does not support
            // adding FKs at runtime (e.g., sqlite in some modes).
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('deposits')) return;
        if (! Schema::hasColumn('deposits', 'action_status_id')) return;

        try {
            Schema::table('deposits', function (Blueprint $table) {
                $sm = DB::getDoctrineSchemaManager();
                $foreignKeys = array_map(function($fk) { return $fk->getName(); }, $sm->listTableForeignKeys('deposits'));
                if (in_array('deposits_action_status_id_foreign', $foreignKeys)) {
                    $table->dropForeign('deposits_action_status_id_foreign');
                }
            });
        } catch (\Exception $e) {
            // ignore
        }
    }
};
