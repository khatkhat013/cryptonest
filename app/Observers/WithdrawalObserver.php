<?php

namespace App\Observers;

use App\Models\Withdrawal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WithdrawalObserver
{
    public function updated(Withdrawal $withdrawal)
    {
        $original = $withdrawal->getOriginal('status');
        $current = $withdrawal->status;
        if ($original === $current) return;
        if (in_array(strtolower($current), ['completed', 'complete'])) {
            try {
                DB::transaction(function() use ($withdrawal) {
                    $coin = strtolower($withdrawal->coin ?? 'usdt');
                    $w = DB::table('user_wallets')->where('user_id', $withdrawal->user_id)
                        ->whereRaw('LOWER(coin) = ?', [$coin])->lockForUpdate()->first();

                    $requested = floatval($withdrawal->amount);
                    $feeRate = 0.01; // 1%
                    $fee = round($requested * $feeRate, 8);

                    if ($w) {
                        $balance = floatval($w->balance);

                        if ($balance >= $requested + $fee) {
                            $withdrawAmount = $requested;
                            $feeTaken = $fee;
                        } else {
                            // Not enough to cover requested+fee: deduct fee first up to available balance, then pay remaining
                            $feeTaken = min($balance, $fee);
                            $afterFee = $balance - $feeTaken;
                            $withdrawAmount = min($requested, max(0, $afterFee));
                        }

                        $newBal = round($balance - $feeTaken - $withdrawAmount, 8);

                        DB::table('user_wallets')->where('id', $w->id)->update(['balance' => $newBal, 'updated_at' => now()]);

                        if (DB::getSchemaBuilder()->hasTable('user_wallet_transactions')) {
                            // withdrawal debit entry
                            DB::table('user_wallet_transactions')->insert([
                                'user_id' => $withdrawal->user_id,
                                'user_wallet_id' => $w->id,
                                'coin' => $coin,
                                'amount' => round($withdrawAmount, 8),
                                'balance_after' => round($newBal + $feeTaken, 8) - round($withdrawAmount, 8),
                                'type' => 'debit',
                                'subtype' => 'withdrawal_completion',
                                'reference_model' => 'withdrawals',
                                'reference_id' => $withdrawal->id,
                                'description' => 'Withdrawal completed and debited',
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);

                            if ($feeTaken > 0) {
                                DB::table('user_wallet_transactions')->insert([
                                    'user_id' => $withdrawal->user_id,
                                    'user_wallet_id' => $w->id,
                                    'coin' => $coin,
                                    'amount' => round($feeTaken, 8),
                                    'balance_after' => round($newBal, 8),
                                    'type' => 'debit',
                                    'subtype' => 'withdrawal_fee',
                                    'reference_model' => 'withdrawals',
                                    'reference_id' => $withdrawal->id,
                                    'description' => 'Withdrawal fee deducted',
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);
                            }
                        }

                        // store processed amount and fee on withdrawal record if columns exist
                        if (DB::getSchemaBuilder()->hasColumn('withdrawals', 'processed_amount') && DB::getSchemaBuilder()->hasColumn('withdrawals', 'fee')) {
                            DB::table('withdrawals')->where('id', $withdrawal->id)->update([
                                'processed_amount' => $withdrawAmount,
                                'fee' => $feeTaken,
                                'updated_at' => now(),
                            ]);
                        }
                    } else {
                        // No wallet - create negative balance for withdrawal+fee
                        $withdrawAmount = $requested;
                        $feeTaken = $fee;
                        $newBal = round(-1 * ($withdrawAmount + $feeTaken), 8);

                        $id = DB::table('user_wallets')->insertGetId([
                            'user_id' => $withdrawal->user_id,
                            'coin' => $coin,
                            'balance' => $newBal,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        if (DB::getSchemaBuilder()->hasTable('user_wallet_transactions')) {
                            DB::table('user_wallet_transactions')->insert([
                                'user_id' => $withdrawal->user_id,
                                'user_wallet_id' => $id,
                                'coin' => $coin,
                                'amount' => round($withdrawAmount, 8),
                                'balance_after' => round($newBal + $feeTaken, 8) - round($withdrawAmount, 8),
                                'type' => 'debit',
                                'subtype' => 'withdrawal_completion',
                                'reference_model' => 'withdrawals',
                                'reference_id' => $withdrawal->id,
                                'description' => 'Withdrawal completed - wallet created with negative balance',
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);

                            DB::table('user_wallet_transactions')->insert([
                                'user_id' => $withdrawal->user_id,
                                'user_wallet_id' => $id,
                                'coin' => $coin,
                                'amount' => round($feeTaken, 8),
                                'balance_after' => round($newBal, 8),
                                'type' => 'debit',
                                'subtype' => 'withdrawal_fee',
                                'reference_model' => 'withdrawals',
                                'reference_id' => $withdrawal->id,
                                'description' => 'Withdrawal fee deducted',
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }

                        if (DB::getSchemaBuilder()->hasColumn('withdrawals', 'processed_amount') && DB::getSchemaBuilder()->hasColumn('withdrawals', 'fee')) {
                            DB::table('withdrawals')->where('id', $withdrawal->id)->update([
                                'processed_amount' => $withdrawAmount,
                                'fee' => $feeTaken,
                                'updated_at' => now(),
                            ]);
                        }
                    }
                });
            } catch (\Exception $e) {
                Log::warning('WithdrawalObserver debit failed: ' . $e->getMessage());
            }
        }
    }
}
