<?php

namespace App\Observers;

use App\Models\Deposit;
use App\Models\UserWallet;
use App\Models\Currency;
use Illuminate\Support\Facades\DB;

class DepositCreditingObserver
{
    /**
     * Handle the Deposit "updated" event.
     */
    public function updated(Deposit $deposit): void
    {
        // Only act when action_status_id actually changed
        if (! $deposit->isDirty('action_status_id')) {
            return;
        }

        // Only credit when new status is 'complete' and not already credited
        $status = $deposit->actionStatus;
        if (! $status || strtolower($status->name) !== 'complete') {
            return;
        }

        // If already credited (credited_at set), skip
        if ($deposit->credited_at) {
            return;
        }

        DB::transaction(function () use ($deposit) {
            $coin = strtoupper(trim($deposit->coin ?? ''));

            // Find a currency row matching the coin symbol (case-insensitive)
            $currency = null;
            if ($coin !== '') {
                $currency = Currency::whereRaw('UPPER(symbol) = ?', [$coin])->first();
            }

            // Use a MySQL named lock per user+coin to avoid duplicate inserts when multiple processes
            // try to credit the same deposit concurrently. This is reliable even when there's no
            // unique DB constraint.
            $lockNameRaw = 'user_wallet_' . $deposit->user_id . '_' . ($coin ?: '');
            // MySQL GET_LOCK key has a 64-char limit; use md5 to be safe
            $lockName = substr('uw_' . md5($lockNameRaw), 0, 64);

            $got = DB::select('SELECT GET_LOCK(?, 10) as l', [$lockName]);
            $haveLock = $got && isset($got[0]->l) && (int)$got[0]->l === 1;
            // Re-check deposit credited_at in DB in case another process already credited it
            $freshCredited = DB::table('deposits')->where('id', $deposit->id)->value('credited_at');
            if ($freshCredited) {
                if ($haveLock) {
                    DB::select('SELECT RELEASE_LOCK(?)', [$lockName]);
                }
                return;
            }
            if (! $haveLock) {
                // Could not obtain lock quickly; bail out to avoid blocking indefinitely.
                // Another process is likely handling it — re-check and update if necessary.
                $wallet = UserWallet::where('user_id', $deposit->user_id)
                    ->whereRaw('UPPER(coin) = ?', [$coin])
                    ->first();
                if ($wallet) {
                    \Illuminate\Support\Facades\DB::table('user_wallets')->where('id', $wallet->id)->update([
                        'balance' => \DB::raw('balance + ' . $deposit->amount),
                        'updated_at' => now(),
                    ]);
                } else {
                    // Before inserting, re-check deposit credited_at to avoid double-credit
                    $freshCredited2 = DB::table('deposits')->where('id', $deposit->id)->value('credited_at');
                    if ($freshCredited2) {
                        return;
                    }
                    // If still not found, attempt a single insert (best-effort)
                    $insert = [
                        'user_id' => $deposit->user_id,
                        'coin' => $coin ?: null,
                        'balance' => $deposit->amount,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    if ($currency) $insert['currency_id'] = $currency->id;
                    \Illuminate\Support\Facades\DB::table('user_wallets')->insert($insert);
                }
            } else {
                try {
                    // With the named lock held, re-check and insert or update deterministically
                    $wallet = UserWallet::where('user_id', $deposit->user_id)
                        ->whereRaw('UPPER(coin) = ?', [$coin])
                        ->first();

                    if (! $wallet) {
                        // Use an atomic upsert so that if another process inserts concurrently
                        // we don't end up with duplicate rows. This requires a UNIQUE index
                        // on (user_id, coin). The statement will insert a new wallet row or
                        // increment balance if the row already exists.
                        $now = now();
                        $params = [
                            $deposit->user_id,
                            $coin ?: null,
                            $currency ? $currency->id : null,
                            $deposit->amount,
                            $now,
                            $now,
                            $deposit->amount,
                            $now,
                        ];

                        // Note: if your DB does not have a UNIQUE(user_id, coin) index,
                        // this will still run but will not merge duplicates. Ensure the
                        // migration to add the unique index is applied.
                        \Illuminate\Support\Facades\DB::statement(
                            'INSERT INTO user_wallets (user_id, coin, currency_id, balance, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?) '
                            . 'ON DUPLICATE KEY UPDATE balance = balance + VALUES(balance), updated_at = VALUES(updated_at)',
                            $params
                        );
                    } else {
                        \Illuminate\Support\Facades\DB::table('user_wallets')->where('id', $wallet->id)->update([
                            'balance' => \DB::raw('balance + ' . $deposit->amount),
                            'updated_at' => now(),
                        ]);
                    }
                } finally {
                    // Always release the lock
                    DB::select('SELECT RELEASE_LOCK(?)', [$lockName]);
                }
            }

            // Mark deposit as credited using direct query to avoid firing observer recursion
            \Illuminate\Support\Facades\DB::table('deposits')->where('id', $deposit->id)->update([
                'credited_at' => now(),
                'updated_at' => now(),
            ]);
        });
    }
}
