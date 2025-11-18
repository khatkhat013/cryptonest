<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('networks')) {
            Schema::create('networks', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('admin_id')->nullable()->index();
                $table->string('name');
                $table->string('slug')->nullable();
                $table->timestamps();
            });

            // Seed common networks as global (admin_id = null)
            DB::table('networks')->insert([
                ['admin_id' => null, 'name' => 'TRC20', 'slug' => 'trc20', 'created_at' => now(), 'updated_at' => now()],
                ['admin_id' => null, 'name' => 'ERC20', 'slug' => 'erc20', 'created_at' => now(), 'updated_at' => now()],
                ['admin_id' => null, 'name' => 'BEP20', 'slug' => 'bep20', 'created_at' => now(), 'updated_at' => now()],
                ['admin_id' => null, 'name' => 'Solana', 'slug' => 'solana', 'created_at' => now(), 'updated_at' => now()],
                ['admin_id' => null, 'name' => 'Aptos', 'slug' => 'aptos', 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        if (Schema::hasTable('admin_wallets') && !Schema::hasColumn('admin_wallets', 'network_id')) {
            Schema::table('admin_wallets', function (Blueprint $table) {
                $table->unsignedBigInteger('network_id')->nullable()->after('address')->index();
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('admin_wallets') && Schema::hasColumn('admin_wallets', 'network_id')) {
            Schema::table('admin_wallets', function (Blueprint $table) {
                $table->dropColumn('network_id');
            });
        }

        if (Schema::hasTable('networks')) {
            Schema::dropIfExists('networks');
        }
    }
};
