<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class AdminApprovalController extends Controller
{
    /**
     * Display list of all admins with approval status.
     * Only accessible to Site Owner (super admin).
     */
    public function index()
    {
        $admin = Auth::guard('admin')->user();
        
        // Only Site Owner (super admin) can manage approvals
        if (!$admin || !$admin->isSuperAdmin()) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Only the Site Owner can manage admin approvals.');
        }

        $admins = Admin::with('role')
            ->orderBy('id', 'desc')
            ->paginate(20);

        return view('admin.admin_approval', compact('admins'));
    }

    /**
     * Show approval details for a specific admin.
     */
    public function show(Admin $admin)
    {
        $currentAdmin = Auth::guard('admin')->user();
        
        // Only Site Owner can view approval details
        if (!$currentAdmin || !$currentAdmin->isSuperAdmin()) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Only the Site Owner can view admin approval details.');
        }

        return view('admin.admin_approval_show', compact('admin'));
    }

    /**
     * Approve a pending admin.
     */
    public function approve(Request $request, Admin $admin)
    {
        $currentAdmin = Auth::guard('admin')->user();
        
        // Only Site Owner can approve
        if (!$currentAdmin || !$currentAdmin->isSuperAdmin()) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Only the Site Owner can approve admins.');
        }

        // Check if already approved
        if ($admin->isApproved()) {
            return redirect()->route('admin.admin_approval.show', $admin)
                ->with('warning', 'This admin is already approved.');
        }

        // Update approval status
        $admin->update([
            'is_approved' => true,
            'rejection_reason' => null,
            'approved_at' => now(),
            'approved_by' => $currentAdmin->id,
        ]);

        // Log the action
        activity()
            ->causedBy($currentAdmin)
            ->performedOn($admin)
            ->log("Admin {$admin->name} approved for editing records");

        return redirect()->route('admin.admin_approval.show', $admin)
            ->with('success', "Admin {$admin->name} has been approved and can now edit records.");
    }

    /**
     * Reject a pending admin.
     */
    public function reject(Request $request, Admin $admin)
    {
        $currentAdmin = Auth::guard('admin')->user();
        
        // Only Site Owner can reject
        if (!$currentAdmin || !$currentAdmin->isSuperAdmin()) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Only the Site Owner can reject admins.');
        }

        // Validate input
        $request->validate([
            'rejection_reason' => 'required|string|min:5|max:255',
        ]);

        // Update rejection status
        $admin->update([
            'is_approved' => false,
            'rejection_reason' => $request->rejection_reason,
            'approved_at' => null,
            'approved_by' => null,
        ]);

        // Log the action
        activity()
            ->causedBy($currentAdmin)
            ->performedOn($admin)
            ->log("Admin {$admin->name} rejected - Reason: {$request->rejection_reason}");

        return redirect()->route('admin.admin_approval.show', $admin)
            ->with('success', "Admin {$admin->name} has been rejected and cannot edit records until approved.");
    }

    /**
     * Revoke approval from an admin.
     */
    public function revoke(Request $request, Admin $admin)
    {
        $currentAdmin = Auth::guard('admin')->user();
        
        // Only Site Owner can revoke
        if (!$currentAdmin || !$currentAdmin->isSuperAdmin()) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Only the Site Owner can revoke admin approvals.');
        }

        // Validate input
        $request->validate([
            'revocation_reason' => 'required|string|min:5|max:255',
        ]);

        // Update approval status to revoked
        $admin->update([
            'is_approved' => false,
            'rejection_reason' => 'Revoked: ' . $request->revocation_reason,
            'approved_at' => null,
            'approved_by' => null,
        ]);

        // Log the action
        activity()
            ->causedBy($currentAdmin)
            ->performedOn($admin)
            ->log("Admin {$admin->name} approval revoked - Reason: {$request->revocation_reason}");

        return redirect()->route('admin.admin_approval.index')
            ->with('success', "Admin {$admin->name} approval has been revoked.");
    }

    /**
     * Get approval status via JSON (for dashboard overview).
     */
    public function statusJson()
    {
        $currentAdmin = Auth::guard('admin')->user();
        
        if (!$currentAdmin || !$currentAdmin->isSuperAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $counts = [
            'total' => Admin::count(),
            'approved' => Admin::where('is_approved', true)->count(),
            'pending' => Admin::where('is_approved', false)
                ->whereNull('rejection_reason')
                ->count(),
            'rejected' => Admin::where('is_approved', false)
                ->whereNotNull('rejection_reason')
                ->count(),
        ];

        return response()->json($counts);
    }
}
