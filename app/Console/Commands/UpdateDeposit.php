<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Deposit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateDeposit extends Command
{
    protected $signature = 'deposit:update-balance';
    protected $description = 'Update user wallet balance for completed deposits';

    public function handle()
    {
        $this->info('Looking for completed deposits without credited_at...');
        
        try {
            // Get all completed deposits that haven't been credited
            $deposits = Deposit::where('action_status_id', 5)
                ->whereNull('credited_at')
                ->get();

            $this->info("Found {$deposits->count()} deposits to process.");

            foreach ($deposits as $deposit) {
                DB::transaction(function() use ($deposit) {
                    // Get or create user wallet
                    $wallet = DB::table('user_wallets')
                        ->where('user_id', $deposit->user_id)
                        ->where('coin', strtoupper($deposit->coin))
                        ->lockForUpdate()
                        ->first();

                    if (!$wallet) {
                        // Get currency_id for this coin
                        $currency = DB::table('currencies')
                            ->where('symbol', strtoupper($deposit->coin))
                            ->first();

                        // Create new wallet
                        $walletId = DB::table('user_wallets')->insertGetId([
                            'user_id' => $deposit->user_id,
                            'currency_id' => $currency ? $currency->id : null,
                            'coin' => strtoupper($deposit->coin),
                            'balance' => $deposit->amount,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);

                        $this->info("Created new wallet (ID: {$walletId}) for {$deposit->coin}");
                    } else {
                        // Update existing wallet balance
                        $newBalance = $wallet->balance + $deposit->amount;
                        
                        DB::table('user_wallets')
                            ->where('id', $wallet->id)
                            ->update([
                                'balance' => $newBalance,
                                'updated_at' => now()
                            ]);

                        $this->info("Updated wallet balance: {$wallet->balance} -> {$newBalance} {$deposit->coin}");
                    }

                    // Mark deposit as credited
                    DB::table('deposits')
                        ->where('id', $deposit->id)
                        ->update([
                            'credited_at' => now(),
                            'updated_at' => now()
                        ]);

                    $this->info("Marked deposit {$deposit->id} as credited");
                });
            }

            $this->info('Completed processing deposits.');

        } catch (\Exception $e) {
            $this->error('Error processing deposits: ' . $e->getMessage());
            Log::error('Deposit update failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}