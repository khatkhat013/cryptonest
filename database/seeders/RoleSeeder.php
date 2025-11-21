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
        // Default roles (1=normal, 2=admin, 3=super)
        $roles = [
                [
                    'id' => config('roles.normal_id', 1),
                    'name' => 'normal',
                    'display_name' => 'Normal',
                    'description' => 'Registered admin with no elevated permissions'
                ],
                [
                    'id' => config('roles.admin_id', 2),
                    'name' => 'admin',
                    'display_name' => 'Admin',
                    'description' => 'Administrator who can manage assigned users and their data'
                ],
                [
                    'id' => config('roles.super_id', 3),
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