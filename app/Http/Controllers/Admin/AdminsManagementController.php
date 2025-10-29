<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminsManagementController extends Controller
{
    public function index()
    {
        $currentAdmin = Auth::guard('admin')->user();
        
        // Only super admin can access this
        if (!$currentAdmin->isSuperAdmin()) {
            return redirect()->route('admin.dashboard')
                           ->with('error', 'Unauthorized access.');
        }

        $admins = Admin::with(['role', 'assignedUsers'])
                      ->withCount('assignedUsers')
                      ->latest()
                      ->paginate(15);

        return view('admin.admins.index', compact('admins'));
    }

    public function show(Admin $admin)
    {
        $currentAdmin = Auth::guard('admin')->user();

        // Only super admin or self can view details
        if (!$currentAdmin->isSuperAdmin() && $currentAdmin->id !== $admin->id) {
            return redirect()->route('admin.dashboard')
                           ->with('error', 'Unauthorized access.');
        }

        $admin->load(['role', 'assignedUsers', 'wallet']);
        return view('admin.admins.show', compact('admin'));
    }

    public function updateRole(Request $request, Admin $admin)
    {
        $currentAdmin = Auth::guard('admin')->user();
        
        // Only super admin can update roles
        if (!$currentAdmin->isSuperAdmin()) {
            return back()->with('error', 'Unauthorized action.');
        }

        // Cannot change own role
        if ($admin->id === $currentAdmin->id) {
            return back()->with('error', 'Cannot change your own role.');
        }

        // Cannot modify super admin's role
        if ($admin->isSuperAdmin()) {
            return back()->with('error', 'Cannot modify super admin role.');
        }

        $request->validate([
            'role_id' => ['required', 'exists:roles,id']
        ]);

        // Cannot assign super admin role through this method
        $newRole = Role::findOrFail($request->role_id);
        if ($newRole->name === 'super') {
            return back()->with('error', 'Cannot assign super admin role.');
        }

        $admin->update(['role_id' => $request->role_id]);

        return back()->with('success', 'Admin role updated successfully.');
    }

    public function deactivate(Admin $admin)
    {
        $currentAdmin = Auth::guard('admin')->user();
        
        // Only super admin can deactivate admins
        if (!$currentAdmin->isSuperAdmin()) {
            return back()->with('error', 'Unauthorized action.');
        }

        // Cannot deactivate self
        if ($admin->id === $currentAdmin->id) {
            return back()->with('error', 'Cannot deactivate yourself.');
        }

        // Cannot deactivate other super admins
        if ($admin->isSuperAdmin()) {
            return back()->with('error', 'Cannot deactivate super admin.');
        }

        $admin->update(['is_active' => false]);

        return back()->with('success', 'Admin has been deactivated.');
    }

    public function activate(Admin $admin)
    {
        $currentAdmin = Auth::guard('admin')->user();
        
        // Only super admin can activate admins
        if (!$currentAdmin->isSuperAdmin()) {
            return back()->with('error', 'Unauthorized action.');
        }

        // Cannot modify super admin's status
        if ($admin->isSuperAdmin()) {
            return back()->with('error', 'Cannot modify super admin status.');
        }

        $admin->update(['is_active' => true]);

        return back()->with('success', 'Admin has been activated.');
    }
}