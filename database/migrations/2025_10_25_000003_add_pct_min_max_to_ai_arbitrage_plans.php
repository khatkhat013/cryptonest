<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('ai_arbitrage_plans')) return;

        Schema::table('ai_arbitrage_plans', function (Blueprint $table) {
            if (!Schema::hasColumn('ai_arbitrage_plans', 'pct_min')) {
                $table->decimal('pct_min', 8, 4)->nullable()->after('daily_revenue_percentage');
            }
            if (!Schema::hasColumn('ai_arbitrage_plans', 'pct_max')) {
                $table->decimal('pct_max', 8, 4)->nullable()->after('pct_min');
            }
        });

        // Backfill pct_min/pct_max based on plan definitions (keep in sync with routes/web.php)
        $planDefs = [
            'A' => ['min' => 500, 'max' => 2000, 'pct_min' => 1.60, 'pct_max' => 1.70],
            'B' => ['min' => 2001, 'max' => 10000, 'pct_min' => 2.00, 'pct_max' => 2.00],
            'C' => ['min' => 10001, 'max' => 50000, 'pct_min' => 2.45, 'pct_max' => 2.45],
            'D' => ['min' => 50001, 'max' => 200000, 'pct_min' => 3.05, 'pct_max' => 3.05],
            'E' => ['min' => 200001, 'max' => 500000, 'pct_min' => 4.50, 'pct_max' => 4.50],
            'VIP' => ['min' => 500001, 'max' => 3000000, 'pct_min' => 7.00, 'pct_max' => 7.00],
            'CN' => ['min' => 3000001, 'max' => 10000000, 'pct_min' => 8.25, 'pct_max' => 8.25],
        ];

        $rows = DB::table('ai_arbitrage_plans')->get();
        foreach ($rows as $row) {
            $plan = strtoupper(trim($row->plan_name ?? ''));
            $qty = floatval($row->quantity);
            if (isset($planDefs[$plan])) {
                $def = $planDefs[$plan];
                $minAmt = $def['min'];
                $maxAmt = $def['max'];
                $pmin = $def['pct_min'];
                $pmax = $def['pct_max'];

                if ($qty <= $minAmt) {
                    $dailyMin = $pmin;
                    $dailyMax = $pmin;
                } elseif ($qty >= $maxAmt) {
                    $dailyMin = $pmax;
                    $dailyMax = $pmax;
                } else {
                    // interpolate to find center; here pct_min/pct_max represent range; keep them as-is
                    $dailyMin = $pmin;
                    $dailyMax = $pmax;
                }
            } else {
                // fallback: use stored daily_revenue_percentage as both
                $dailyMin = floatval($row->daily_revenue_percentage ?: 0);
                $dailyMax = floatval($row->daily_revenue_percentage ?: 0);
            }

            DB::table('ai_arbitrage_plans')->where('id', $row->id)->update([
                'pct_min' => round($dailyMin, 4),
                'pct_max' => round($dailyMax, 4),
            ]);
        }
    }

    public function down()
    {
        if (!Schema::hasTable('ai_arbitrage_plans')) return;
        Schema::table('ai_arbitrage_plans', function (Blueprint $table) {
            if (Schema::hasColumn('ai_arbitrage_plans', 'pct_max')) {
                $table->dropColumn('pct_max');
            }
            if (Schema::hasColumn('ai_arbitrage_plans', 'pct_min')) {
                $table->dropColumn('pct_min');
            }
        });
    }
};
