<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Remap admins.role_id according to new role id semantics:
        // current -> desired: 1(admin) -> 2(admin), 2(super) -> 3(super), 3(normal) -> 1(normal)
        DB::beginTransaction();
        try {
            DB::statement("UPDATE admins SET role_id = CASE role_id WHEN 1 THEN 2 WHEN 2 THEN 3 WHEN 3 THEN 1 ELSE role_id END");

            // Update roles metadata to match desired mapping
            DB::table('roles')->where('id', 1)->update([
                'name' => 'normal',
                'display_name' => 'Normal',
                'description' => 'Registered admin with no elevated permissions',
                'updated_at' => now(),
            ]);

            DB::table('roles')->where('id', 2)->update([
                'name' => 'admin',
                'display_name' => 'Admin',
                'description' => 'Administrator who can manage assigned users and their data',
                'updated_at' => now(),
            ]);

            DB::table('roles')->where('id', 3)->update([
                'name' => 'super',
                'display_name' => 'Super Admin',
                'description' => 'Administrator with full system access',
                'updated_at' => now(),
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert admins.role_id back to previous mapping (inverse of up):
        // desired -> original: 2->1, 3->2, 1->3
        DB::beginTransaction();
        try {
            DB::statement("UPDATE admins SET role_id = CASE role_id WHEN 2 THEN 1 WHEN 3 THEN 2 WHEN 1 THEN 3 ELSE role_id END");

            // Restore original roles metadata to match previous state
            DB::table('roles')->where('id', 1)->update([
                'name' => 'admin',
                'display_name' => 'Admin',
                'description' => 'Administrator who can manage assigned users and their own data',
                'updated_at' => now(),
            ]);

            DB::table('roles')->where('id', 2)->update([
                'name' => 'super',
                'display_name' => 'Super Admin',
                'description' => 'Administrator with full system access',
                'updated_at' => now(),
            ]);

            DB::table('roles')->where('id', 3)->update([
                'name' => 'normal',
                'display_name' => 'Normal',
                'description' => 'Registered admin with no elevated permissions',
                'updated_at' => now(),
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
};
