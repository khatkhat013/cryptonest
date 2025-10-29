<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\Auth;
use App\Models\ActionStatus;
use Illuminate\Support\Facades\Schema;

class WithdrawalAdminController extends Controller
{
    public function index(Request $request)
    {
        // show latest withdrawals, paginated
        $admin = Auth::guard('admin')->user();

        $query = Withdrawal::with(['user', 'actionStatus'])->orderBy('created_at', 'desc');

        // If not super admin, only show withdrawals assigned to this admin or where the user is assigned to this admin
        if (!$admin->isSuperAdmin()) {
            $query->where(function($q) use ($admin) {
                // Only filter by admin_id if the column exists in the withdrawals table
                if (Schema::hasColumn('withdrawals', 'admin_id')) {
                    $q->where('admin_id', $admin->id)
                      ->orWhereHas('user', function($uq) use ($admin) {
                          $uq->where('assigned_admin_id', $admin->id);
                      });
                } else {
                    // Fallback: filter by user's assigned_admin_id only
                    $q->whereHas('user', function($uq) use ($admin) {
                        $uq->where('assigned_admin_id', $admin->id);
                    });
                }
            });
        }

        $withdrawals = $query->paginate(25);

        // Debugging info: log current admin and resulting counts to help diagnose visibility issues
        try {
            \Log::info('WithdrawalAdminController@index', [
                'admin_id' => $admin?->id,
                'is_super' => $admin?->isSuperAdmin(),
                'result_count' => $withdrawals->count(),
                'total' => $withdrawals->total()
            ]);
        } catch (\Throwable $e) {
            // ignore logging errors
        }

        return view('admin.withdrawals.index', compact('withdrawals'));
    }

    public function updateStatus(Request $request, Withdrawal $withdrawal)
    {
        // Only allow change if admin authenticated
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('admin.login');
        }

        // Authorization: normal admins can only update withdrawals assigned to them or withdrawals
        // where the user is assigned to this admin.
        $admin = Auth::guard('admin')->user();
        $userAssignedAdminId = optional($withdrawal->user)->assigned_admin_id;
        if (!$admin->isSuperAdmin() && $withdrawal->admin_id !== $admin->id && $userAssignedAdminId !== $admin->id) {
            return redirect()->route('withdraws.index')->with('error', 'Unauthorized to modify this withdrawal.');
        }

        // Accept either action_status_id (from modal) or status
        $actionStatusId = $request->input('action_status_id');
        $amount = $request->input('amount');

        if ($amount !== null) {
            // allow admin to adjust amount in modal
            $withdrawal->amount = floatval($amount);
        }

        if ($actionStatusId) {
            $status = ActionStatus::find($actionStatusId);
            if ($status) {
                $withdrawal->action_status_id = $status->id;
                $withdrawal->status = $status->name;
            }
        } else {
            // fallback to status string
            $new = $request->input('status');
            if ($new) $withdrawal->status = $new;
        }

        $withdrawal->save();

        // Return JSON for AJAX requests, otherwise redirect back with flash
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Withdrawal status updated.');
    }

    public function destroy(Request $request, Withdrawal $withdrawal)
    {
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('admin.login');
        }

        // Authorization: only super admins or assigned admin/user-assigned admin can delete
        $admin = Auth::guard('admin')->user();
        $userAssignedAdminId = optional($withdrawal->user)->assigned_admin_id;
        if (!$admin->isSuperAdmin() && $withdrawal->admin_id !== $admin->id && $userAssignedAdminId !== $admin->id) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
            return redirect()->route('withdraws.index')->with('error', 'Unauthorized to delete this withdrawal.');
        }

        try {
            $withdrawal->delete();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true]);
            }
            return back()->with('success', 'Withdrawal deleted.');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', 'Failed to delete withdrawal.');
        }
    }
}
