<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Currency;
use App\Services\TelegramService;
use App\Services\ActivityLogger;

class AdminController extends Controller
{
    /**
     * Check if current admin can manage this admin
     */
    private function canManageAdmin(Admin $admin): bool
    {
        $currentAdmin = Auth::guard('admin')->user();
        // Only super admin can manage other admins, or an admin can view/edit their own profile
        // fallback to role_id check in case role relation isn't loaded for the current admin
        $isSuper = $currentAdmin->isSuperAdmin() || ($currentAdmin->role_id ?? null) === config('roles.super_id', 3);
        return $isSuper || $currentAdmin->id === $admin->id;
    }

    public function index()
    {
        // include users count to display "Total Users" per admin without N+1
        // ensure withCount is applied after select so the count column is included
        $admins = Admin::select('id', 'name', 'email', 'role_id', 'telegram_username', 'created_at')
            ->with('role')
            ->withCount('assignedUsers')
            ->latest()
            ->paginate(10);
            
        return view('admin.admins.index', compact('admins'));
    }

    public function create()
    {
        $currentAdmin = Auth::guard('admin')->user();
        // Only super admin can create new admins
        if (!$currentAdmin->isSuperAdmin()) {
            abort(403, 'Only super admin can create new admins.');
        }
        return view('admin.admins.create');
    }

    public function store(Request $request)
    {
        $currentAdmin = Auth::guard('admin')->user();
        // Only super admin can create new admins
        if (!$currentAdmin->isSuperAdmin()) {
            abort(403, 'Only super admin can create new admins.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id'
        ]);

        Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id
        ]);

        return redirect()->route('admin.admins.index')
            ->with('success', 'Admin created successfully');
    }

    public function show(Admin $admin)
    {
        // Authorization check
        if (!$this->canManageAdmin($admin)) {
            abort(403, 'You are not authorized to view this admin.');
        }
        return view('admin.admins.show', compact('admin'));
    }

    public function edit(Admin $admin)
    {
        // Authorization check
        if (!$this->canManageAdmin($admin)) {
            abort(403, 'You are not authorized to edit this admin.');
        }

        // Load admin wallets to allow editing addresses in the form
        $adminWallets = \App\Models\AdminWallet::where('admin_id', $admin->id)->with(['currency', 'network'])->get();
        $currencies = Currency::orderBy('symbol')->get();

        // Networks available: global list
        $networks = \App\Models\Network::orderBy('name')->get();

        return view('admin.admins.edit', compact('admin', 'adminWallets', 'currencies', 'networks'));
    }

    public function update(Request $request, Admin $admin)
    {
        // Authorization check
        if (!$this->canManageAdmin($admin)) {
            abort(403, 'You are not authorized to update this admin.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins,email,' . $admin->id,
            'telegram_username' => ['nullable', 'string', 'max:255'],
            'role_id' => 'required|exists:roles,id'
            , 'wallets' => 'sometimes|array',
            'wallets.*.address' => 'nullable|string|max:255',
            'wallets.*.currency_id' => 'nullable|exists:currencies,id',
            'wallets.*.network' => 'nullable|string|max:50',
            'wallets.*.network_id' => 'nullable|exists:networks,id'
        ]);

        $admin->update([
            'name' => $request->name,
            'email' => $request->email,
            'telegram_username' => $request->telegram_username,
            'role_id' => $request->role_id
        ]);

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'required|string|min:8|confirmed'
            ]);
            
            $admin->update([
                'password' => Hash::make($request->password)
            ]);
        }

        // Update admin wallet addresses if present and create new ones for submitted empty slots
        $wallets = $request->input('wallets', []);
        foreach ($wallets as $wid => $wdata) {
            // New wallet entries are submitted with a key like "new_{$currencyId}"
            if (is_string($wid) && strpos($wid, 'new_') === 0) {
                $currencyId = $wdata['currency_id'] ?? null;
                $address = $wdata['address'] ?? null;
                if (!empty($address) && $currencyId) {
                    \App\Models\AdminWallet::create([
                        'admin_id' => $admin->id,
                        'currency_id' => $currencyId,
                        'address' => $address,
                        'network' => $wdata['network'] ?? null,
                        'network_id' => $wdata['network_id'] ?? null,
                    ]);
                }
            } else {
                // existing wallet by id
                $update = [];
                if (isset($wdata['address'])) {
                    $update['address'] = $wdata['address'];
                }
                if (isset($wdata['currency_id'])) {
                    $update['currency_id'] = $wdata['currency_id'];
                }
                if (!empty($update)) {
                    if (isset($wdata['network'])) {
                        $update['network'] = $wdata['network'];
                    }
                    if (isset($wdata['network_id'])) {
                        $update['network_id'] = $wdata['network_id'];
                    }
                    \App\Models\AdminWallet::where('id', $wid)->where('admin_id', $admin->id)
                        ->update($update);
                }
            }
        }

        return redirect()->route('admin.admins.index')
            ->with('success', 'Admin updated successfully');
    }

    public function destroy(Admin $admin)
    {
        if ($admin->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account');
        }

        $admin->delete();
        return redirect()->route('admin.admins.index')
            ->with('success', 'Admin deleted successfully');
    }

    /**
     * Activate (approve) an admin. Only super admin may perform this.
     */
    public function activate(Admin $admin)
    {
        $current = Auth::guard('admin')->user();
        $isSuper = $current && ($current->isSuperAdmin() || ($current->role_id ?? null) === config('roles.super_id', 3));
        if (!$isSuper) {
            abort(403, 'Only Site Owner can activate admins.');
        }

        if ($admin->isApproved()) {
            return back()->with('warning', 'Admin is already active');
        }

        $admin->update([
            'is_approved' => true,
            'rejection_reason' => null,
            'approved_at' => now(),
            'approved_by' => $current->id,
        ]);

        ActivityLogger::log($current, $admin, 'Activated admin via Admin Management');

        // Notify via Telegram channel if configured
        try {
            $notifyChat = config('services.telegram.channel_id') ?: env('TELEGRAM_CHANNEL_ID');
            if ($notifyChat) {
                $msg = "✅ Admin <b>{$admin->name}</b> (ID: {$admin->id}) has been activated by Site Owner <b>{$current->name}</b>.";
                TelegramService::sendMessage($notifyChat, $msg);
            }
        } catch (\Throwable $e) {
            \Log::error('Failed to send Telegram notify on admin activation', ['error' => $e->getMessage()]);
        }

        return back()->with('success', 'Admin activated successfully');
    }

    /**
     * Deactivate (revoke) an admin. Only super admin may perform this.
     */
    public function deactivate(Request $request, Admin $admin)
    {
        $current = Auth::guard('admin')->user();
        $isSuper = $current && ($current->isSuperAdmin() || ($current->role_id ?? null) === config('roles.super_id', 3));
        if (!$isSuper) {
            abort(403, 'Only Site Owner can deactivate admins.');
        }

        if (!$admin->isApproved()) {
            return back()->with('warning', 'Admin is already inactive');
        }

        $reason = $request->input('reason', 'Deactivated by Site Owner');

        $admin->update([
            'is_approved' => false,
            'rejection_reason' => $reason,
            'approved_at' => null,
            'approved_by' => null,
        ]);

        ActivityLogger::log($current, $admin, 'Deactivated admin via Admin Management - Reason: ' . $reason);

        // Notify via Telegram channel if configured
        try {
            $notifyChat = config('services.telegram.channel_id') ?: env('TELEGRAM_CHANNEL_ID');
            if ($notifyChat) {
                $msg = "⚠️ Admin <b>{$admin->name}</b> (ID: {$admin->id}) has been deactivated by Site Owner <b>{$current->name}</b>. Reason: {$reason}";
                TelegramService::sendMessage($notifyChat, $msg);
            }
        } catch (\Throwable $e) {
            \Log::error('Failed to send Telegram notify on admin deactivation', ['error' => $e->getMessage()]);
        }

        return back()->with('success', 'Admin deactivated successfully');
    }

    /**
     * Toggle approval state: if approved -> deactivate, else approve.
     */
    public function toggleApproval(Request $request, Admin $admin)
    {
        $current = Auth::guard('admin')->user();
        $isSuper = $current && ($current->isSuperAdmin() || ($current->role_id ?? null) === config('roles.super_id', 3));
        if (!$isSuper) {
            abort(403, 'Only Site Owner can toggle admin approvals.');
        }

        if ($admin->isApproved()) {
            // Deactivate
            $reason = $request->input('reason', 'Deactivated by Site Owner (toggle)');
            $admin->update([
                'is_approved' => false,
                'rejection_reason' => $reason,
                'approved_at' => null,
                'approved_by' => null,
            ]);

            ActivityLogger::log($current, $admin, 'Deactivated admin via toggle - Reason: ' . $reason);

            try {
                $notifyChat = config('services.telegram.channel_id') ?: env('TELEGRAM_CHANNEL_ID');
                if ($notifyChat) {
                    $msg = "⚠️ Admin <b>{$admin->name}</b> (ID: {$admin->id}) was deactivated by <b>{$current->name}</b> (toggle). Reason: {$reason}";
                    TelegramService::sendMessage($notifyChat, $msg);
                }
            } catch (\Throwable $e) {
                \Log::error('Failed to send Telegram notify on admin toggle-deactivate', ['error' => $e->getMessage()]);
            }

            return back()->with('success', 'Admin deactivated successfully');
        } else {
            // Activate
            $admin->update([
                'is_approved' => true,
                'rejection_reason' => null,
                'approved_at' => now(),
                'approved_by' => $current->id,
            ]);

            ActivityLogger::log($current, $admin, 'Activated admin via toggle');

            try {
                $notifyChat = config('services.telegram.channel_id') ?: env('TELEGRAM_CHANNEL_ID');
                if ($notifyChat) {
                    $msg = "✅ Admin <b>{$admin->name}</b> (ID: {$admin->id}) was activated by <b>{$current->name}</b> (toggle).";
                    TelegramService::sendMessage($notifyChat, $msg);
                }
            } catch (\Throwable $e) {
                \Log::error('Failed to send Telegram notify on admin toggle-activate', ['error' => $e->getMessage()]);
            }

            return back()->with('success', 'Admin activated successfully');
        }
    }
}
