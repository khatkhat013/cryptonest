<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        // Only run if the user_wallets and currencies tables exist
        if (! Schema::hasTable('user_wallets') || ! Schema::hasTable('currencies')) {
            return;
        }

        $now = Carbon::now();

        $currencies = DB::table('currencies')->get();
        if ($currencies->isEmpty()) {
            return;
        }

        foreach ($currencies as $currency) {
            $coin = strtoupper(trim($currency->symbol ?? '')) ?: null;

            DB::table('user_wallets')->updateOrInsert(
                [
                    'user_id' => $user->id,
                    'coin' => $coin,
                ],
                [
                    'user_id' => $user->id,
                    'currency_id' => $currency->id,
                    'coin' => $coin,
                    'balance' => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}
