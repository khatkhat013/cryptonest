<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NetworkSeeder extends Seeder
{
    public function run(): void
    {
        $networks = [
            ['name' => 'TRC20', 'slug' => 'trc20'],
            ['name' => 'ERC20', 'slug' => 'erc20'],
            ['name' => 'BEP20', 'slug' => 'bep20'],
            ['name' => 'Solana', 'slug' => 'solana'],
            ['name' => 'Aptos', 'slug' => 'aptos'],
        ];

        foreach ($networks as $n) {
            DB::table('networks')->updateOrInsert(
                ['slug' => $n['slug']],
                ['name' => $n['name'], 'slug' => $n['slug'], 'updated_at' => now(), 'created_at' => now()]
            );
        }
    }
}
