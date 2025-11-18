<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('networks') || !Schema::hasTable('admin_wallets')) {
            return;
        }

        $trc = DB::table('networks')->whereRaw("UPPER(name) = 'TRC20'")->first();
        if (!$trc) {
            // If TRC20 doesn't exist for some reason, create it
            $trcId = DB::table('networks')->insertGetId(['admin_id' => null, 'name' => 'TRC20', 'slug' => 'trc20', 'created_at' => now(), 'updated_at' => now()]);
        } else {
            $trcId = $trc->id;
        }

        // Update rows without network_id to use TRC20 and set legacy network string when missing
        DB::table('admin_wallets')->whereNull('network_id')->update(['network_id' => $trcId]);
        DB::table('admin_wallets')->whereNull('network')->update(['network' => 'TRC20']);
    }

    public function down()
    {
        // no-op: do not remove defaults automatically
    }
};
