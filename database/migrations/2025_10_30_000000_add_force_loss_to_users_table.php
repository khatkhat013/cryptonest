<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('users', 'force_loss')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('force_loss')->default(false)->after('is_active');
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
        if (Schema::hasColumn('users', 'force_loss')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('force_loss');
            });
        }
    }
};
