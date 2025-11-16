<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use App\Models\Currency;

class AdminController extends Controller
{
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
        return view('admin.admins.create');
    }

    public function store(Request $request)
    {
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
        return view('admin.admins.show', compact('admin'));
    }

    public function edit(Admin $admin)
    {
        // Load admin wallets to allow editing addresses in the form
        $adminWallets = \App\Models\AdminWallet::where('admin_id', $admin->id)->with('currency')->get();
        $currencies = Currency::orderBy('symbol')->get();
        return view('admin.admins.edit', compact('admin', 'adminWallets', 'currencies'));
    }

    public function update(Request $request, Admin $admin)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins,email,' . $admin->id,
            'telegram_username' => ['nullable', 'string', 'max:255'],
            'role_id' => 'required|exists:roles,id'
            , 'wallets' => 'sometimes|array',
            'wallets.*.address' => 'nullable|string|max:255',
            'wallets.*.currency_id' => 'nullable|exists:currencies,id'
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
                        'address' => $address
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
}
