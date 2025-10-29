<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateCleanTablesWithData extends Migration
{
	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		// Seed minimal data for a clean environment. Non-destructive and
		// wrapped in try/catch to avoid breaking on re-run or partial installs.
		try {
			if (Schema::hasTable('action_statuses')) {
				$exists = DB::table('action_statuses')->count();
				if ($exists === 0) {
					DB::table('action_statuses')->insert([
						['name' => 'pending', 'created_at' => now(), 'updated_at' => now()],
						['name' => 'cancel', 'created_at' => now(), 'updated_at' => now()],
						['name' => 'reject', 'created_at' => now(), 'updated_at' => now()],
						['name' => 'frozen', 'created_at' => now(), 'updated_at' => now()],
						['name' => 'complete', 'created_at' => now(), 'updated_at' => now()],
					]);
				}
			}
		} catch (\Exception $e) {
			// ignore seeding errors in migration context
		}
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		// no destructive rollback for seed data
	}
}

