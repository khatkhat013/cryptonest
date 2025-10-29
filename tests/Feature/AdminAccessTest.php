<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_admin_login_when_accessing_admin_dashboard()
    {
        $response = $this->get('/admin/dashboard');

        $response->assertStatus(302);
        $response->assertRedirect(route('admin.login'));
    }

    public function test_authenticated_admin_can_access_admin_dashboard()
    {
        // Ensure roles exist (RoleSeeder creates default roles)
        $this->seed(\Database\Seeders\RoleSeeder::class);

        $admin = Admin::factory()->create();

        $response = $this->actingAs($admin, 'admin')->get('/admin/dashboard');

        $response->assertStatus(200);
    }
}
