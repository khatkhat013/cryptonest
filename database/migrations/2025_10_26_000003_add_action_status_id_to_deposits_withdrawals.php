<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class() extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('action_statuses')) {
            // action_statuses table should exist (seeded). If not, migration will still add the cols
        }

        Schema::table('deposits', function (Blueprint $table) {
            if (!Schema::hasColumn('deposits', 'action_status_id')) {
                $table->unsignedBigInteger('action_status_id')->nullable()->after('status');
            }
        });

        Schema::table('withdrawals', function (Blueprint $table) {
            if (!Schema::hasColumn('withdrawals', 'action_status_id')) {
                $table->unsignedBigInteger('action_status_id')->nullable()->after('status');
            }
        });

        // Backfill action_status_id from existing status strings where possible
        try {
            $map = DB::table('action_statuses')->pluck('id', 'name')->mapWithKeys(function($id, $name){
                return [strtolower($name) => $id];
            })->toArray();

            // helper to normalize old status strings to canonical action_status names
            $normalize = function($s) {
                $s = strtolower(trim((string)$s));
                $map = [
                    'completed' => 'complete',
                    'rejected' => 'reject',
                    'cancelled' => 'cancel',
                ];
                return $map[$s] ?? $s;
            };

            foreach (DB::table('deposits')->get() as $d) {
                $name = $normalize($d->status);
                if (isset($map[$name])) {
                    DB::table('deposits')->where('id', $d->id)->update(['action_status_id' => $map[$name]]);
                }
            }

            foreach (DB::table('withdrawals')->get() as $w) {
                $name = $normalize($w->status);
                if (isset($map[$name])) {
                    DB::table('withdrawals')->where('id', $w->id)->update(['action_status_id' => $map[$name]]);
                }
            }
        } catch (\Exception $e) {
            // ignore if action_statuses doesn't exist yet; seeder can be run afterwards
        }
    }

    public function down()
    {
        Schema::table('deposits', function (Blueprint $table) {
            if (Schema::hasColumn('deposits', 'action_status_id')) {
                $table->dropColumn('action_status_id');
            }
        });

        Schema::table('withdrawals', function (Blueprint $table) {
            if (Schema::hasColumn('withdrawals', 'action_status_id')) {
                $table->dropColumn('action_status_id');
            }
        });
    }
};
