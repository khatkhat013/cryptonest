<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class ArbitrageController extends Controller
{
    public function custodyOrder()
    {
        $user = request()->user();
        
        // Get current date for comparison
        $now = Carbon::now();

        // Finalize any plans that have reached their duration: credit principal + profit back to user's USDT wallet
        try {
            $duePlans = DB::table('ai_arbitrage_plans')
                ->where('user_id', $user->id)
                ->where('status', 'active')
                ->whereRaw('DATE_ADD(created_at, INTERVAL duration_days DAY) <= ?', [$now])
                ->get();

            foreach ($duePlans as $p) {
                DB::transaction(function() use ($p) {
                    // compute profit: simple calculation quantity * daily_percentage * duration_days
                    $quantity = floatval($p->quantity);
                    $dailyPct = floatval($p->daily_revenue_percentage);
                    $days = intval($p->duration_days ?: 1);
                    $profit = ($quantity * $dailyPct / 100.0) * $days;

                    // update plan record: set profit (earned) and total_profit (principal + profit), mark completed
                    $totalProfit = $quantity + $profit;
                    // Use Eastern Time (America/New_York) with microsecond precision when writing completion time
                    $edtNow = Carbon::now('America/New_York')->format('Y-m-d H:i:s.u');
                    DB::table('ai_arbitrage_plans')->where('id', $p->id)->update([
                        'profit' => round($profit, 8),
                        'total_profit' => round($totalProfit, 8),
                        'status' => 'completed',
                        'completed_at' => $edtNow,
                        'updated_at' => $edtNow
                    ]);

                    // Credit user's USDT wallet: return principal (quantity) + profit
                    if (Schema::hasTable('user_wallets')) {
                        $w = DB::table('user_wallets')->where('user_id', $p->user_id)->whereRaw('LOWER(coin) = ?', ['usdt'])->lockForUpdate()->first();
                            if ($w) {
                            // total to credit = principal + profit
                            $creditAmount = $quantity + $profit;
                            $newBal = floatval($w->balance) + $creditAmount;
                            // update wallet with EDT microsecond timestamp
                            DB::table('user_wallets')->where('id', $w->id)->update(['balance' => $newBal, 'updated_at' => $edtNow]);

                            // Insert a single combined audit transaction row with principal and profit columns
                            if (Schema::hasTable('user_wallet_transactions')) {
                                DB::table('user_wallet_transactions')->insert([
                                    'user_id' => $p->user_id,
                                    'user_wallet_id' => $w->id,
                                    'coin' => 'usdt',
                                    'amount' => round($creditAmount, 8),
                                    'principal_amount' => round($quantity, 8),
                                    'plan_profit' => round($profit, 8),
                                    'balance_after' => round($newBal, 8),
                                    'type' => 'credit',
                                    'subtype' => 'plan_completion',
                                    'reference_model' => 'ai_arbitrage_plans',
                                    'reference_id' => $p->id,
                                    'description' => 'Arbitrage plan principal+profit returned on completion',
                                    'created_at' => $edtNow,
                                    'updated_at' => $edtNow
                                ]);
                            }
                        }

                    }
                });
            }
        } catch (\Exception $e) {
            // Log and continue — do not block page render if crediting fails
            \Log::warning('Error finalizing arbitrage plans: ' . $e->getMessage());
        }

        // Get active plans (not ended yet) and calculate expected daily profit
        $activePlans = DB::table('ai_arbitrage_plans')
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->where(function($query) use ($now) {
                $query->whereRaw('DATE_ADD(created_at, INTERVAL duration_days DAY) > ?', [$now]);
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($plan) use ($now) {
                // Calculate hours passed since plan creation
                $created = Carbon::parse($plan->created_at);
                $hoursPassed = $created->diffInHours($now);
                $completedDays = floor($hoursPassed / 24);

                // Calculate completed hours (total hours passed and hours into current day)
                $completedHoursIntoDay = $hoursPassed % 24;

                // Determine the daily percentage to use. Prefer stored center pct, fall back to pct_min/pct_max center or config.
                $dailyPct = 0.0;
                if (isset($plan->daily_revenue_percentage) && is_numeric($plan->daily_revenue_percentage) && floatval($plan->daily_revenue_percentage) > 0) {
                    $dailyPct = floatval($plan->daily_revenue_percentage);
                } elseif (isset($plan->pct_min) && isset($plan->pct_max) && is_numeric($plan->pct_min) && is_numeric($plan->pct_max)) {
                    $dailyPct = (floatval($plan->pct_min) + floatval($plan->pct_max)) / 2.0;
                } else {
                    // fallback to config definitions based on plan name and quantity
                    try {
                        $defs = config('arbitrage.plans', []);
                        $pname = strtoupper(trim($plan->plan_name ?? 'A'));
                        if (isset($defs[$pname])) {
                            $def = $defs[$pname];
                            if (isset($def['pct_min']) && isset($def['pct_max'])) {
                                $dailyPct = (floatval($def['pct_min']) + floatval($def['pct_max'])) / 2.0;
                            }
                        }
                    } catch (\Exception $e) {
                        // ignore and keep dailyPct = 0
                    }
                }

                // Calculate daily profit (one-day profit) and per-hour profit
                $dailyProfit = ($plan->quantity * $dailyPct) / 100.0;
                $hourlyProfit = $dailyProfit / 24.0;

                // Cumulative profit = profit from full days + profit from the partial current day (completed hours)
                $cumulativeProfit = ($dailyProfit * $completedDays) + ($hourlyProfit * $completedHoursIntoDay);
                
                // Update completed hours and profit in database
                // persist completed hours and the incremental profit (rounded to 8 decimals) and timestamp in EDT
                $edtNow = Carbon::now('America/New_York')->format('Y-m-d H:i:s.u');
                DB::table('ai_arbitrage_plans')
                    ->where('id', $plan->id)
                    ->update([
                        // store total elapsed hours for clarity
                        'completed_hours' => $hoursPassed,
                        // store cumulative profit so far (including partial day hours)
                        'profit' => round($cumulativeProfit, 8),
                        'updated_at' => $edtNow
                    ]);

                // Show cumulative profit based on elapsed hours (includes partial day earnings)
                $plan->expected_daily_profit = $cumulativeProfit;
                // also expose per-hour amount for UI if desired
                $plan->expected_hourly_profit = $hourlyProfit;
                
                return $plan;
            });

        // Get completed plans
        $completedPlans = DB::table('ai_arbitrage_plans')
            ->where('user_id', $user->id)
            ->where(function($query) use ($now) {
                $query->whereRaw('DATE_ADD(created_at, INTERVAL duration_days DAY) <= ?', [$now])
                    ->orWhere('status', '!=', 'active');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('arbitrage.custody-order', [
            'activePlans' => $activePlans,
            'completedPlans' => $completedPlans
        ]);
    }
}