<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AdminWallet;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Check if current admin can manage this user
     */
    private function canManageUser(User $user): bool
    {
        $admin = Auth::guard('admin')->user();
        // Super admin can manage any user, or if user is assigned to this admin
        return $admin->isSuperAdmin() || $user->assigned_admin_id === $admin->id;
    }

    public function index(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        $query = User::query();

        // Non-super-admin can only see their assigned users
        if (!$admin->isSuperAdmin()) {
            $query->where('assigned_admin_id', $admin->id);
        }

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(10);
        
        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        // Authorization check: only super admin or assigned admin can view this user
        if (!$this->canManageUser($user)) {
            abort(403, 'You are not authorized to view this user.');
        }

        // Eager-load relationships that exist on the User model
        $user->load([
            'assignedAdmin.role',
            'wallet'
        ]);

        // Get assignable admins (only for super admin)
        $admin = Auth::guard('admin')->user();
        $admins = $admin->isSuperAdmin() ? Admin::with('role')->get() : null;

        // Load admin wallets if user has an assigned admin
        $adminWallets = collect();
        if ($user->assignedAdmin) {
            $adminWallets = AdminWallet::where('admin_id', $user->assignedAdmin->id)
                ->with('currency')
                ->get();
        }

        return view('admin.users.show', compact('user', 'admins', 'adminWallets'));
    }

    public function toggleStatus(User $user)
    {
        // Authorization check
        if (!$this->canManageUser($user)) {
            abort(403, 'You are not authorized to modify this user.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        return back()->with('success', 'User status updated successfully');
    }

    /**
     * Toggle admin-enforced forced-loss flag for a user.
     * Only admins that can manage the user should be able to call this.
     */
    public function toggleForceLoss(User $user)
    {
        // Authorization check
        if (!$this->canManageUser($user)) {
            abort(403, 'You are not authorized to modify this user.');
        }

        $user->force_loss = !$user->force_loss;
        $user->save();

        $label = $user->force_loss ? 'enabled' : 'disabled';
        return back()->with('success', "Force-loss has been {$label} for user {$user->user_id}");
    }
}
