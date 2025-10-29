<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCleanTables extends Migration
{
	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		// This migration is intentionally lightweight: it ensures the project
		// has the minimal expected tables when run in clean environments.
		// Avoid destructive operations here; more specific migrations handle
		// schema details elsewhere.

		if (! Schema::hasTable('action_statuses')) {
			Schema::create('action_statuses', function (Blueprint $table) {
				$table->id();
				$table->string('name')->unique();
				$table->timestamps();
			});
		}
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		// only drop if we created it and it's safe to drop
		if (Schema::hasTable('action_statuses')) {
			Schema::dropIfExists('action_statuses');
		}
	}
}
