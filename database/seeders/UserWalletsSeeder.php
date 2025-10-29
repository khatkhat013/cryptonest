<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Currency;
use Carbon\Carbon;

class UserWalletsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * This will ensure that every existing user has a wallet row for each
     * available currency. The wallet rows are created with balance = 0 and
     * are created idempotently using updateOrInsert.
     */
    public function run()
    {
        $now = Carbon::now();

        $users = User::all();
        $currencies = Currency::all();

        if ($users->isEmpty() || $currencies->isEmpty()) {
            Log::info('UserWalletsSeeder: no users or currencies found, skipping.');
            return;
        }

        foreach ($users as $user) {
            foreach ($currencies as $currency) {
                $coin = strtoupper(trim($currency->symbol ?? '')) ?: null;

                // Use updateOrInsert to avoid creating duplicates if the seeder is run multiple times.
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

        Log::info('UserWalletsSeeder: ensured wallets for ' . $users->count() . ' users and ' . $currencies->count() . ' currencies.');
    }
}
