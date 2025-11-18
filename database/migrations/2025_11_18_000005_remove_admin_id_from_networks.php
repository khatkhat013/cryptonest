<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('networks') && Schema::hasColumn('networks', 'admin_id')) {
            Schema::table('networks', function (Blueprint $table) {
                // dropping the column will also drop its index on most DBs
                $table->dropColumn('admin_id');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('networks') && !Schema::hasColumn('networks', 'admin_id')) {
            Schema::table('networks', function (Blueprint $table) {
                $table->unsignedBigInteger('admin_id')->nullable()->index();
            });
        }
    }
};
