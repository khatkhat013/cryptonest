<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Recompute daily_revenue_percentage for existing Plan A rows based on new pct_min/pct_max.
     */
    public function up()
    {
        // Plan A config (keep in sync with routes/web.php)
        $minAmt = 500;
        $maxAmt = 2000;
        $pctMin = 1.60;
        $pctMax = 1.70;

        $rows = DB::table('ai_arbitrage_plans')->whereRaw('UPPER(plan_name) = ?', ['A'])->get();
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
            DB::table('ai_arbitrage_plans')->where('id', $row->id)->update(['daily_revenue_percentage' => $daily]);
        }
    }

    /**
     * Reverse the migrations.
     * This does not attempt to restore previous percentages; leave as-is.
     */
    public function down()
    {
        // no-op: we won't attempt to restore previous values automatically
    }
};
