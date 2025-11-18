<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('admin_wallets') && !Schema::hasColumn('admin_wallets', 'network')) {
            Schema::table('admin_wallets', function (Blueprint $table) {
                $table->string('network')->nullable()->after('address');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('admin_wallets') && Schema::hasColumn('admin_wallets', 'network')) {
            Schema::table('admin_wallets', function (Blueprint $table) {
                $table->dropColumn('network');
            });
        }
    }
};
