<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create default roles and currencies first
        $this->call([
            RoleSeeder::class,
            NetworkSeeder::class,
            CurrencySeeder::class,
        ]);

        // Create test users (idempotent)
        User::updateOrCreate([
            'email' => 'test@example.com'
        ], [
            'name' => 'Test User',
            'user_id' => '000000',
            'password' => Hash::make('password'),
            'email_verified_at' => now()
        ]);

        User::updateOrCreate([
            'email' => 'monmon@example.com'
        ], [
            'name' => 'MonMon',
            'user_id' => '000001',
            'password' => Hash::make('password'),
            'email_verified_at' => now()
        ]);

        // Create test admin (idempotent)
        \App\Models\Admin::updateOrCreate([
            'email' => 'admin@example.com'
        ], [
            'name' => 'Admin User',
            'phone' => '09123456789',
            'password' => Hash::make('password'),
            'telegram_username' => 'testadmin',
            'role_id' => 2 // Admin role
        ]);

        // Ensure every user has wallet rows (balance = 0)
        $this->call([UserWalletsSeeder::class]);
    }
}
