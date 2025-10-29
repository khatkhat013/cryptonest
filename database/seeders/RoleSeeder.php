<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Default roles
        $roles = [
            [
                'id' => 1,
                'name' => 'admin',
                'display_name' => 'Admin',
                'description' => 'Regular administrator with limited access'
            ],
            [
                'id' => 2,
                'name' => 'super',
                'display_name' => 'Super Admin',
                'description' => 'Administrator with full system access'
            ]
        ];

        foreach ($roles as $role) {
            // Use updateOrInsert so seeding is idempotent and won't fail on duplicate runs
            DB::table('roles')->updateOrInsert(
                ['name' => $role['name']],
                [
                    'display_name' => $role['display_name'] ?? null,
                    'description' => $role['description'] ?? null,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}