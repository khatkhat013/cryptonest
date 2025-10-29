<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Plan B config (keep in sync with config/arbitrage.php)
        $minAmt = 2001;
        $maxAmt = 10000;
        $pctMin = 1.90;
        $pctMax = 2.10;

        // Update existing plan B rows: set pct_min/pct_max and recompute daily_revenue_percentage
        $rows = DB::table('ai_arbitrage_plans')->whereRaw('UPPER(plan_name) = ?', ['B'])->get();
        foreach ($rows as $row) {
            $qty = floatval($row->quantity);
            if ($qty <= $minAmt) {
                $daily = $pctMin;
            } elseif ($qty >= $maxAmt) {
                $daily = $pctMax;
            } else {
                $ratio = ($qty - $minAmt) / max(1, ($maxAmt - $minAmt));
                $daily = $pctMin + $ratio * ($pctMax - $pctMin);
            }
            $daily = round($daily, 4);
            DB::table('ai_arbitrage_plans')->where('id', $row->id)->update([
                'pct_min' => round($pctMin,4),
                'pct_max' => round($pctMax,4),
                'daily_revenue_percentage' => $daily,
            ]);
        }
    }

    public function down()
    {
        // no-op: leave values as-is
    }
};
