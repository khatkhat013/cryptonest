<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private $superAdmin;
    private $regularAdmin;
    private $anotherAdmin;
    private $assignedUser;
    private $unassignedUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        $superRole = Role::create(['name' => 'super', 'display_name' => 'Super Admin']);
        $adminRole = Role::create(['name' => 'admin', 'display_name' => 'Admin']);

        // Create admins
        $this->superAdmin = Admin::create([
            'name' => 'Super Admin',
            'email' => 'super@example.com',
            'password' => bcrypt('password123'),
            'role_id' => $superRole->id
        ]);

        $this->regularAdmin = Admin::create([
            'name' => 'Regular Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
            'role_id' => $adminRole->id
        ]);

        $this->anotherAdmin = Admin::create([
            'name' => 'Another Admin',
            'email' => 'another@example.com',
            'password' => bcrypt('password123'),
            'role_id' => $adminRole->id
        ]);

        // Create users
        $this->assignedUser = User::create([
            'name' => 'Assigned User',
            'email' => 'assigned@example.com',
            'password' => bcrypt('password123'),
            'assigned_admin_id' => $this->regularAdmin->id
        ]);

        $this->unassignedUser = User::create([
            'name' => 'Unassigned User',
            'email' => 'unassigned@example.com',
            'password' => bcrypt('password123'),
            'assigned_admin_id' => $this->anotherAdmin->id
        ]);
    }

    /**
     * Test that non-super-admin cannot view users not assigned to them
     */
    public function test_regular_admin_cannot_view_unassigned_user()
    {
        $this->actingAs($this->regularAdmin, 'admin');

        $response = $this->get(route('admin.users.show', $this->unassignedUser->id));

        $response->assertStatus(403);
    }

    /**
     * Test that regular admin can view their assigned users
     */
    public function test_regular_admin_can_view_assigned_user()
    {
        $this->actingAs($this->regularAdmin, 'admin');

        $response = $this->get(route('admin.users.show', $this->assignedUser->id));

        $response->assertStatus(200);
    }

    /**
     * Test that super admin can view any user
     */
    public function test_super_admin_can_view_any_user()
    {
        $this->actingAs($this->superAdmin, 'admin');

        $response = $this->get(route('admin.users.show', $this->unassignedUser->id));

        $response->assertStatus(200);
    }

    /**
     * Test that non-super-admin cannot modify user status of unassigned user
     */
    public function test_regular_admin_cannot_toggle_unassigned_user_status()
    {
        $this->actingAs($this->regularAdmin, 'admin');

        $response = $this->post(route('admin.users.toggle-status', $this->unassignedUser->id));

        $response->assertStatus(403);
    }

    /**
     * Test that regular admin can modify assigned user status
     */
    public function test_regular_admin_can_toggle_assigned_user_status()
    {
        $this->actingAs($this->regularAdmin, 'admin');

        $response = $this->post(route('admin.users.toggle-status', $this->assignedUser->id));

        $response->assertRedirect();
        $this->assertTrue($this->assignedUser->fresh()->is_active !== $this->assignedUser->is_active);
    }

    /**
     * Test that non-super-admin cannot view other admins' profiles (except their own)
     */
    public function test_regular_admin_cannot_view_other_admin_profile()
    {
        $this->actingAs($this->regularAdmin, 'admin');

        $response = $this->get(route('admin.admins.show', $this->anotherAdmin->id));

        $response->assertStatus(403);
    }

    /**
     * Test that admin can view their own profile
     */
    public function test_admin_can_view_own_profile()
    {
        $this->actingAs($this->regularAdmin, 'admin');

        $response = $this->get(route('admin.admins.show', $this->regularAdmin->id));

        $response->assertStatus(200);
    }

    /**
     * Test that super admin can view any admin's profile
     */
    public function test_super_admin_can_view_any_admin_profile()
    {
        $this->actingAs($this->superAdmin, 'admin');

        $response = $this->get(route('admin.admins.show', $this->regularAdmin->id));

        $response->assertStatus(200);
    }

    /**
     * Test that non-super-admin cannot access admin list
     */
    public function test_regular_admin_cannot_access_admin_list()
    {
        $this->actingAs($this->regularAdmin, 'admin');

        $response = $this->get('/admin/admins');

        $response->assertStatus(403);
    }

    /**
     * Test that super admin can access admin list
     */
    public function test_super_admin_can_access_admin_list()
    {
        $this->actingAs($this->superAdmin, 'admin');

        $response = $this->get('/admin/admins');

        $response->assertStatus(200);
    }
}
