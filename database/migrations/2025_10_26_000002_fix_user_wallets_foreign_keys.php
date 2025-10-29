<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixUserWalletsForeignKeys extends Migration
{
	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		if (!Schema::hasTable('user_wallets')) {
			return;
		}

		// Attempt to ensure the expected foreign keys exist for MySQL and other DBs.
		// Wrap operations in try/catch to keep migration resilient across drivers.
		try {
			Schema::table('user_wallets', function (Blueprint $table) {
				if (Schema::hasColumn('user_wallets', 'user_id')) {
					try {
						$table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
					} catch (\Exception $e) {
						// ignore if already exists or not supported
					}
				}

				if (Schema::hasColumn('user_wallets', 'currency_id')) {
					try {
						$table->foreign('currency_id')->references('id')->on('currencies')->onDelete('set null');
					} catch (\Exception $e) {
						// ignore if already exists or not supported
					}
				}
			});
		} catch (\Exception $e) {
			// swallow to avoid hard failure on environments where doctrine/schema manager
			// or specific operations may not be available (e.g., in-memory sqlite)
		}
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		if (!Schema::hasTable('user_wallets')) {
			return;
		}

		try {
			Schema::table('user_wallets', function (Blueprint $table) {
				try { $table->dropForeign(['user_id']); } catch (\Exception $e) {}
				try { $table->dropForeign(['currency_id']); } catch (\Exception $e) {}
			});
		} catch (\Exception $e) {
			// ignore
		}
	}
}
