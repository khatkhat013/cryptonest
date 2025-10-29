<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class AccrueArbitrageHourly extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'arbitrage:accrue-hourly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Accrue hourly profit for active AI arbitrage plans and log wallet transactions.';

    public function handle()
    {
        $now = Carbon::now();

        $plans = DB::table('ai_arbitrage_plans')
            ->where('status', 'active')
            ->get();

        $this->info('Processing ' . count($plans) . ' active plans');

        foreach ($plans as $plan) {
            try {
                DB::transaction(function() use ($plan, $now) {
                    $created = Carbon::parse($plan->created_at);
                    $hoursPassed = $created->diffInHours($now);

                    $durationDays = intval($plan->duration_days ?: 1);
                    $maxHours = $durationDays * 24;

                    $effectiveHours = min($hoursPassed, $maxHours);

                    $already = intval($plan->completed_hours ?: 0);
                    $newHours = $effectiveHours - $already;

                    if ($newHours <= 0) {
                        return; // nothing to accrue for this plan right now
                    }

                    $quantity = floatval($plan->quantity);
                    $dailyPct = floatval($plan->daily_revenue_percentage);

                    $dailyProfit = ($quantity * $dailyPct) / 100.0;
                    $hourlyProfit = $dailyProfit / 24.0;

                    $totalHourlyProfit = round($hourlyProfit * $newHours, 8);

                    // update or create user's USDT wallet row and credit profit
                    if (Schema::hasTable('user_wallets')) {
                        // try to find wallet
                        $w = DB::table('user_wallets')
                            ->where('user_id', $plan->user_id)
                            ->whereRaw('LOWER(coin) = ?', ['usdt'])
                            ->lockForUpdate()
                            ->first();

                        if (!$w) {
                            // create a USDT wallet row if missing
                            DB::table('user_wallets')->insert([
                                'user_id' => $plan->user_id,
                                'balance' => 0,
                                'coin' => 'usdt',
                                'created_at' => now(),
                                'updated_at' => now()
                            ]);

                            $w = DB::table('user_wallets')
                                ->where('user_id', $plan->user_id)
                                ->whereRaw('LOWER(coin) = ?', ['usdt'])
                                ->lockForUpdate()
                                ->first();
                        }

                        if ($w) {
                            $newBal = floatval($w->balance) + $totalHourlyProfit;
                            DB::table('user_wallets')->where('id', $w->id)->update(['balance' => $newBal, 'updated_at' => now()]);

                            // insert audit record
                            if (Schema::hasTable('user_wallet_transactions')) {
                                DB::table('user_wallet_transactions')->insert([
                                    'user_id' => $plan->user_id,
                                    'user_wallet_id' => $w->id,
                                    'coin' => 'usdt',
                                    'amount' => $totalHourlyProfit,
                                    'balance_after' => $newBal,
                                    'type' => 'credit',
                                    'subtype' => 'hourly_profit',
                                    'reference_model' => 'ai_arbitrage_plans',
                                    'reference_id' => $plan->id,
                                    'description' => 'Hourly profit accrual',
                                    'created_at' => now(),
                                    'updated_at' => now()
                                ]);
                            }

                            // update plan completed_hours and profit / total_profit
                            DB::table('ai_arbitrage_plans')->where('id', $plan->id)->update([
                                'completed_hours' => $already + $newHours,
                                'profit' => DB::raw('IFNULL(profit,0) + ' . $totalHourlyProfit),
                                'total_profit' => DB::raw('IFNULL(total_profit,0) + ' . $totalHourlyProfit),
                                'updated_at' => now()
                            ]);
                        }
                    }

                    // If plan reached its duration, mark it completed (do not auto-return principal here)
                    if ($effectiveHours >= $maxHours) {
                        DB::table('ai_arbitrage_plans')->where('id', $plan->id)->update([
                            'status' => 'completed',
                            'completed_at' => now(),
                            'updated_at' => now()
                        ]);

                        // log completion as a transaction row (optional) if there's any final profit portion (already logged above)
                    }
                });
            } catch (\Exception $e) {
                \Log::warning('AccrueArbitrageHourly error for plan ' . $plan->id . ': ' . $e->getMessage());
            }
        }

        $this->info('Arbitrage hourly accrual completed.');

        return 0;
    }
}
