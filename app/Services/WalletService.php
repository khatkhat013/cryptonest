<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Deposit;

class WalletService
{
    /**
     * Credit a user's wallet for a completed deposit.
     * This method is idempotent: it will not credit if a transaction already
     * references the given deposit.
     */
    public static function creditDeposit(Deposit $deposit)
    {
        try {
            // If deposit already has a credited_at timestamp, skip (prevents double credit)
            if (isset($deposit->credited_at) && $deposit->credited_at) {
                return;
            }
            // If there is a transactions table and it already references this deposit,
            // assume credit was already applied.
            if (DB::getSchemaBuilder()->hasTable('user_wallet_transactions')) {
                $exists = DB::table('user_wallet_transactions')
                    ->where('reference_model', 'deposits')
                    ->where('reference_id', $deposit->id)
                    ->exists();
                if ($exists) return;
            }

            DB::transaction(function() use ($deposit) {
                $coin = strtoupper($deposit->coin ?? 'USDT');
                
                Log::info('WalletService::creditDeposit - Starting credit', [
                    'deposit_id' => $deposit->id,
                    'user_id' => $deposit->user_id,
                    'coin' => $coin,
                    'amount' => $deposit->amount
                ]);
                
                // Find or create wallet
                $w = DB::table('user_wallets')->where('user_id', $deposit->user_id)
                    ->where('coin', $coin)->lockForUpdate()->first();
                
                if (!$w) {
                    // Get currency_id for this coin
                    $currency = DB::table('currencies')->where('symbol', $coin)->first();
                    $currency_id = $currency ? $currency->id : null;
                    
                    // Insert new wallet
                    DB::table('user_wallets')->insert([
                        'user_id' => $deposit->user_id,
                        'currency_id' => $currency_id,
                        'coin' => $coin,
                        'balance' => 0,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    
                    // Get the newly created wallet
                    $w = DB::table('user_wallets')->where('user_id', $deposit->user_id)
                        ->where('coin', $coin)->lockForUpdate()->first();
                }

                if ($w) {
                    $credit = floatval($deposit->amount);
                    $oldBal = floatval($w->balance);
                    $newBal = $oldBal + $credit;
                    
                    Log::info('WalletService::creditDeposit - Updating balance', [
                        'wallet_id' => $w->id,
                        'old_balance' => $oldBal,
                        'credit_amount' => $credit,
                        'new_balance' => $newBal
                    ]);
                    
                    DB::table('user_wallets')->where('id', $w->id)->update(['balance' => $newBal, 'updated_at' => now()]);

                    if (DB::getSchemaBuilder()->hasTable('user_wallet_transactions')) {
                        DB::table('user_wallet_transactions')->insert([
                            'user_id' => $deposit->user_id,
                            'user_wallet_id' => $w->id,
                            'coin' => $coin,
                            'amount' => round($credit, 8),
                            'balance_after' => round($newBal, 8),
                            'type' => 'credit',
                            'subtype' => 'deposit_completion',
                            'reference_model' => 'deposits',
                            'reference_id' => $deposit->id,
                            'description' => 'Deposit approved and credited',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }

                    // mark deposit as credited
                    DB::table('deposits')
                        ->where('id', $deposit->id)
                        ->update([
                            'credited_at' => now(),
                            'updated_at' => now()
                        ]);
                } else {
                    // create a wallet if not exists
                    $id = DB::table('user_wallets')->insertGetId([
                        'user_id' => $deposit->user_id,
                        'coin' => $coin,
                        'balance' => floatval($deposit->amount),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    if (DB::getSchemaBuilder()->hasTable('user_wallet_transactions')) {
                        DB::table('user_wallet_transactions')->insert([
                            'user_id' => $deposit->user_id,
                            'user_wallet_id' => $id,
                            'coin' => $coin,
                            'amount' => round(floatval($deposit->amount), 8),
                            'balance_after' => round(floatval($deposit->amount), 8),
                            'type' => 'credit',
                            'subtype' => 'deposit_completion',
                            'reference_model' => 'deposits',
                            'reference_id' => $deposit->id,
                            'description' => 'Deposit approved and wallet created + credited',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }

                    // mark deposit as credited
                    DB::table('deposits')
                        ->where('id', $deposit->id)
                        ->update([
                            'credited_at' => now(),
                            'updated_at' => now()
                        ]);
                }
            });
        } catch (\Exception $e) {
            Log::warning('WalletService::creditDeposit failed: ' . $e->getMessage());
        }
    }
}
