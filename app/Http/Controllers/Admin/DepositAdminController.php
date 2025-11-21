<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Deposit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\ActionStatus;
use Illuminate\Support\Facades\Schema;

class DepositAdminController extends Controller
{
    public function index()
    {
        $admin = Auth::guard('admin')->user();

        $depositsQuery = Deposit::with(['user', 'actionStatus'])
            ->orderBy('created_at', 'desc');

        // load available action statuses to populate dropdowns
        $statuses = ActionStatus::orderBy('id')->get();

        // If not super admin, only show deposits belonging to users assigned to this admin
        // (We intentionally avoid showing deposits merely because deposit.admin_id equals the admin;
        // visibility is based on the user->assigned_admin_id relationship.)
        if (!$admin->isSuperAdmin()) {
            $depositsQuery->whereHas('user', function($uq) use ($admin) {
                $uq->where('assigned_admin_id', $admin->id);
            });
        }

        $deposits = $depositsQuery->paginate(20);

        // Debugging info: log current admin and resulting counts to help diagnose visibility issues
        try {
            \Log::info('DepositAdminController@index', [
                'admin_id' => $admin?->id,
                'is_super' => $admin?->isSuperAdmin(),
                'result_count' => $deposits->count(),
                'total' => $deposits->total()
            ]);
        } catch (\Throwable $e) {
            // swallow logging errors to avoid breaking page
        }

        return view('admin.deposits.index', compact('deposits', 'statuses'));
    }

    public function updateStatus(Request $request, Deposit $deposit)
    {
        \Log::info('DepositAdminController::updateStatus called', ['deposit_id' => $deposit->id, 'request' => $request->all(), 'admin_id' => Auth::guard('admin')->id()]);

        $request->validate([
            'action_status_id' => 'required|exists:action_statuses,id',
            'amount' => 'nullable|numeric|min:0'
        ]);

        // Authorization: normal admins can only update deposits when the deposit's user is
        // assigned to this admin. Do not grant access solely because deposit.admin_id is set.
        $admin = Auth::guard('admin')->user();
        $userAssignedAdminId = optional($deposit->user)->assigned_admin_id;
        if (!$admin->isSuperAdmin() && $userAssignedAdminId !== $admin->id) {
            return redirect()->route('admin.deposits.index')->with('error', 'Unauthorized to modify this deposit.');
        }

        // determine numeric id for the 'complete' status dynamically
        $completeId = ActionStatus::where('name', 'complete')->value('id');

        try {
            DB::transaction(function() use ($deposit, $request, $completeId) {
                // Lock for update to prevent race conditions
                $wallet = DB::table('user_wallets')
                    ->where('user_id', $deposit->user_id)
                    ->where('coin', strtoupper($deposit->coin))
                    ->lockForUpdate()
                    ->first();

                // Update deposit amount if provided
                if ($request->filled('amount')) {
                    $deposit->amount = $request->input('amount');
                }

                // Update deposit status
                $oldStatusId = $deposit->action_status_id;
                $deposit->action_status_id = $request->action_status_id;
                // Ensure deposit.admin_id is set to this admin when they act on it (keeps permissions consistent)
                if (empty($deposit->admin_id)) {
                    $deposit->admin_id = Auth::guard('admin')->id();
                }
                $deposit->approved_by = Auth::guard('admin')->id();
                $deposit->approved_at = now();
                $deposit->save();

                \Log::info('Deposit status updated', ['deposit_id' => $deposit->id, 'old_status' => $oldStatusId, 'new_status' => $deposit->action_status_id]);

                // Only update wallet if status changed TO complete
                // AND hasn't been credited before
                if ($completeId && $request->action_status_id == $completeId && $oldStatusId != $completeId && !$deposit->credited_at) {
                    $coin = strtoupper($deposit->coin);
                    if (!$wallet) {
                        // Use atomic upsert to create the wallet row or increment if it already exists.
                        // This prevents creating duplicate rows and guarantees a single row is used.
                        $now = now();
                        $params = [
                            $deposit->user_id,
                            $coin,
                            $deposit->amount,
                            $now,
                            $now,
                            $deposit->amount,
                            $now,
                        ];

                        try {
                            DB::statement(
                                'INSERT INTO user_wallets (user_id, coin, balance, created_at, updated_at) VALUES (?, ?, ?, ?, ?) '
                                . 'ON DUPLICATE KEY UPDATE balance = balance + VALUES(balance), updated_at = VALUES(updated_at)',
                                $params
                            );
                        } catch (\Exception $e) {
                            // If the DB doesn't support the unique constraint or another error
                            // occurs, fall back to attempting an increment (best-effort).
                            DB::table('user_wallets')
                                ->where('user_id', $deposit->user_id)
                                ->where('coin', $coin)
                                ->increment('balance', $deposit->amount);
                        }
                    } else {
                        // Update existing wallet
                        DB::table('user_wallets')
                            ->where('id', $wallet->id)
                            ->update([
                                'balance' => $wallet->balance + $deposit->amount,
                                'updated_at' => now()
                            ]);
                    }

                    // Mark as credited
                    $deposit->credited_at = now();
                    $deposit->save();

                    // Log the credit
                    \Log::info('Deposit credited', [
                        'deposit_id' => $deposit->id,
                        'user_id' => $deposit->user_id,
                        'amount' => $deposit->amount,
                        'coin' => $deposit->coin,
                        'admin_id' => Auth::guard('admin')->id()
                    ]);

                    // If deposit references an admin who is currently a `normal` role,
                    // promote them to `admin` and approve their account. This supports
                    // the flow where a normal account pays and then is elevated.
                    try {
                        if (!empty($deposit->admin_id)) {
                            $adminModel = \App\Models\Admin::find($deposit->admin_id);
                            if ($adminModel) {
                                $roleName = optional($adminModel->role)->name;
                                // If role is "normal", promote to admin
                                if ($roleName === 'normal' || ($adminModel->role_id ?? null) === config('roles.normal_id')) {
                                    $adminModel->update([
                                        'role_id' => config('roles.admin_id'),
                                        'is_approved' => true,
                                        'rejection_reason' => null,
                                        'approved_at' => now(),
                                        'approved_by' => Auth::guard('admin')->id()
                                    ]);

                                    // Activity log and notify via Telegram if configured
                                    try {
                                        \App\Services\ActivityLogger::log(Auth::guard('admin')->user(), $adminModel, 'Promoted normal->admin after deposit credited');
                                    } catch (\Throwable $e) {
                                        \Log::error('Activity log failed when promoting admin', ['error' => $e->getMessage()]);
                                    }

                                    try {
                                        $notifyChat = config('services.telegram.channel_id') ?: env('TELEGRAM_CHANNEL_ID');
                                        if ($notifyChat) {
                                            $msg = "✅ Admin <b>{$adminModel->name}</b> (ID: {$adminModel->id}) was promoted to <b>admin</b> after a credited deposit.";
                                            \App\Services\TelegramService::sendMessage($notifyChat, $msg);
                                        }
                                    } catch (\Throwable $e) {
                                        \Log::error('Failed to send Telegram notify on admin promotion', ['error' => $e->getMessage()]);
                                    }
                                }
                            }
                        }
                    } catch (\Throwable $e) {
                        \Log::error('Error while attempting to promote admin after deposit credit', ['error' => $e->getMessage(), 'deposit_id' => $deposit->id]);
                    }
                }
            });

            // If this was an AJAX/fetch request, return JSON so the client-side handler
            // receives a clean success response; otherwise redirect back as before.
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Deposit status updated']);
            }

            return back()->with('success', 'Deposit status updated successfully');

        } catch (\Exception $e) {
            \Log::error('Failed to update deposit status', [
                'deposit_id' => $deposit->id,
                'error' => $e->getMessage()
            ]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Failed to update status', 'error' => $e->getMessage()], 500);
            }

            return back()->with('error', 'Failed to update status. Please try again.');
        }
    }

    /**
     * Delete a deposit. Only super admins or the assigned admin may delete.
     * Prevent deletion of deposits that have already been credited.
     */
    public function destroy(Deposit $deposit)
    {
        $admin = Auth::guard('admin')->user();

        // Allow delete if super admin, or if the deposit's user is assigned to this admin.
        // Do not allow deletion solely because deposit.admin_id equals this admin.
        $userAssignedAdminId = optional($deposit->user)->assigned_admin_id;
        if (!$admin->isSuperAdmin() && $userAssignedAdminId !== $admin->id) {
            return redirect()->route('admin.deposits.index')->with('error', 'Unauthorized to delete this deposit.');
        }

        if ($deposit->credited_at) {
            return redirect()->route('admin.deposits.index')->with('error', 'Cannot delete a deposit that has already been credited.');
        }

        try {
            $deposit->delete();
            \Log::info('Deposit deleted by admin', [
                'deposit_id' => $deposit->id,
                'admin_id' => $admin->id
            ]);
            return redirect()->route('admin.deposits.index')->with('success', 'Deposit deleted successfully.');
        } catch (\Exception $e) {
            \Log::error('Failed to delete deposit', ['deposit_id' => $deposit->id, 'error' => $e->getMessage()]);
            return redirect()->route('admin.deposits.index')->with('error', 'Failed to delete deposit.');
        }
    }
}
