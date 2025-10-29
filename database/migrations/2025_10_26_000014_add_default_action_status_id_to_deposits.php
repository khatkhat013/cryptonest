<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Attempt to set a default of 1 while keeping the column nullable
        // because the foreign key uses ON DELETE SET NULL. Use a driver
        // specific statement for MySQL and fall back to a schema change.
        try {
            $driver = Schema::getConnection()->getDriverName();
        } catch (\Exception $e) {
            $driver = null;
        }

        if ($driver === 'mysql') {
            try {
                \Illuminate\Support\Facades\DB::statement("ALTER TABLE `deposits` MODIFY `action_status_id` BIGINT UNSIGNED NULL DEFAULT 1");
            } catch (\Exception $e) {
                // ignore and try the schema-based approach below
            }
        }

        try {
            Schema::table('deposits', function (Blueprint $table) {
                // keep column nullable to satisfy the existing FK ON DELETE SET NULL
                $table->unsignedBigInteger('action_status_id')->nullable()->default(1)->change();
            });
        } catch (\Exception $e) {
            // ignore failures (e.g., doctrine/dbal missing); the MySQL raw
            // statement should have handled the common case.
        }
    }

    public function down()
    {
        try {
            $driver = Schema::getConnection()->getDriverName();
        } catch (\Exception $e) {
            $driver = null;
        }

        if ($driver === 'mysql') {
            try {
                \Illuminate\Support\Facades\DB::statement("ALTER TABLE `deposits` MODIFY `action_status_id` BIGINT UNSIGNED NULL DEFAULT NULL");
            } catch (\Exception $e) {
                // ignore
            }
        }

        try {
            Schema::table('deposits', function (Blueprint $table) {
                $table->unsignedBigInteger('action_status_id')->nullable()->default(null)->change();
            });
        } catch (\Exception $e) {
            // ignore
        }
    }
};