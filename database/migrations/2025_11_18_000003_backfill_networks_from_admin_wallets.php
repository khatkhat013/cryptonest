<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('admin_wallets') || !Schema::hasTable('networks')) {
            return;
        }

        $rows = DB::table('admin_wallets')->selectRaw('DISTINCT network')->whereNotNull('network')->pluck('network')->filter()->unique();
        foreach ($rows as $name) {
            $slug = strtolower(preg_replace('/[^a-z0-9]+/', '-', $name));
            $existing = DB::table('networks')->where('slug', $slug)->orWhere('name', $name)->first();
            if (!$existing) {
                $id = DB::table('networks')->insertGetId(['admin_id' => null, 'name' => $name, 'slug' => $slug, 'created_at' => now(), 'updated_at' => now()]);
            } else {
                $id = $existing->id;
            }

            // Update admin_wallets to reference the new network_id where missing
            DB::table('admin_wallets')->where('network', $name)->whereNull('network_id')->update(['network_id' => $id]);
        }
    }

    public function down()
    {
        // noop: do not remove networks created from legacy values
    }
};
