<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        $currencies = [
            ['name' => 'Bitcoin', 'symbol' => 'BTC'],
            ['name' => 'Ethereum', 'symbol' => 'ETH'],
            ['name' => 'Tether', 'symbol' => 'USDT'],
            ['name' => 'Dogecoin', 'symbol' => 'DOGE'],
            ['name' => 'XRP', 'symbol' => 'XRP'],
            ['name' => 'PayPal USD', 'symbol' => 'PYUSD'],
            ['name' => 'USD Coin', 'symbol' => 'USDC'],
        ];

        foreach ($currencies as $currency) {
            // Use updateOrInsert so running the seeder multiple times won't create duplicates
            DB::table('currencies')->updateOrInsert([
                'symbol' => $currency['symbol']
            ], [
                'name' => $currency['name'],
                'symbol' => $currency['symbol'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}