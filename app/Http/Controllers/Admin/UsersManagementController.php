<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Admin;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UsersManagementController extends Controller
{
    /**
     * Check if admin can manage user
     */
    private function canManageUser(User $user): bool
    {
        $admin = Auth::guard('admin')->user();
        return $admin->isSuperAdmin() || $user->assigned_admin_id === $admin->id;
    }

    public function index()
    {
        $admin = Auth::guard('admin')->user();

        // Query builder for users with relationships
        $usersQuery = User::with(['assignedAdmin']);

        // If not super admin, only show assigned users
        if (!$admin->isSuperAdmin()) {
            $usersQuery->where('assigned_admin_id', $admin->id);
        } else {
            // Super admin can filter by admin_id to show users assigned to a specific admin
            $filterAdminId = request()->query('admin_id');
            if ($filterAdminId) {
                $usersQuery->where('assigned_admin_id', $filterAdminId);
            }
        }
        
        // Get users with pagination
        $users = $usersQuery->latest()
                           ->paginate(15, ['*'], 'users_page');

        // Get admins list for assignment (only for super admin)
        // Super admins should be able to assign users freely — include all admins (including super and self)
        if ($admin->isSuperAdmin()) {
            $admins = Admin::with('role')->get();
        } else {
            $admins = null;
        }
        
        return view('admin.users.index', compact('users', 'admins'));
    }

    public function show(User $user)
    {
        if (!$this->canManageUser($user)) {
            return back()->with('error', 'You are not authorized to view this user.');
        }

        // Eager-load assigned admin and related data to avoid N+1
        $user->load([
            'assignedAdmin.role',
            'transactions',
            'wallets'
        ]);

        // Prepare assignable admins for the Assign modal (only for super admin)
        // Super admins may assign to any admin, including super and themselves
        $admin = Auth::guard('admin')->user();
        if ($admin->isSuperAdmin()) {
            $admins = Admin::with('role')->get();
        } else {
            $admins = null;
        }

        // Also load admin wallets directly from admin_wallets table (by admin_id)
        $adminWallets = collect();
        if ($user->assignedAdmin) {
            $adminWallets = \App\Models\AdminWallet::where('admin_id', $user->assignedAdmin->id)
                ->with('currency')
                ->get();
        }

        return view('admin.users.show', compact('user', 'admins', 'adminWallets'));
    }

    public function assign(Request $request, User $user)
    {
        // Only super admin can assign users
        if (!Auth::guard('admin')->user()->isSuperAdmin()) {
            return back()->with('error', 'Only super admin can assign users to other admins.');
        }

        $request->validate([
            'admin_id' => ['required', 'exists:admins,id']
        ]);

        // Super admins are allowed to assign to themselves or any other admin.
        // Update assigned admin and record the assignment timestamp.
        // Use attribute assignment + save() (avoids mass-assignment issues if fillable lacks the date)
        $user->assigned_admin_id = $request->admin_id;
        $user->assigned_admin_date = now();
        $user->save();

        return back()->with('success', "User has been assigned to a new admin successfully.");
    }

    public function toggleStatus(Request $request, User $user)
    {
        if (!$this->canManageUser($user)) {
            return back()->with('error', 'You are not authorized to modify this user\'s status.');
        }

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "User has been {$status} successfully.");
    }
}