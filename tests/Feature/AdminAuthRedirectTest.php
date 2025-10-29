<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminAuthRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_admin_login_and_register()
    {
        $response = $this->get('/admin/login');
        $response->assertStatus(200);

        $response = $this->get('/admin/register');
        $response->assertStatus(200);
    }

    public function test_authenticated_admin_is_redirected_from_admin_auth_pages_to_dashboard()
    {
        $this->seed(\Database\Seeders\RoleSeeder::class);
        $admin = Admin::factory()->create();

        $response = $this->actingAs($admin, 'admin')->get('/admin/login');
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.dashboard'));

        $response = $this->actingAs($admin, 'admin')->get('/admin/register');
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.dashboard'));
    }
}
