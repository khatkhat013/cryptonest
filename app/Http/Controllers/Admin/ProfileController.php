<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class ProfileController extends Controller
{
    public function index()
    {
        $admin = Auth::guard('admin')->user();
        // Load admin wallets so admin can edit their addresses from profile
        $adminWallets = \App\Models\AdminWallet::where('admin_id', $admin->id)->with('currency')->get();
        return view('admin.profile', compact('admin', 'adminWallets'));
    }

    public function update(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:admins,email,' . $admin->id],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'telegram_username' => ['nullable', 'string', 'max:255'],
            'wallets' => ['sometimes', 'array'],
            'wallets.*.address' => ['nullable', 'string', 'max:255']
        ]);

        $admin->name = $data['name'];
        $admin->email = $data['email'];

        if (!empty($data['password'])) {
            $admin->password = Hash::make($data['password']);
        }

        $admin->save();

        // Update admin wallet addresses if any
        $wallets = $request->input('wallets', []);
        foreach ($wallets as $wid => $wdata) {
            if ($wid) {
                $update = [];
                if (isset($wdata['address'])) {
                    $update['address'] = $wdata['address'];
                }
                if (!empty($update)) {
                    \App\Models\AdminWallet::where('id', $wid)->where('admin_id', $admin->id)
                        ->update($update);
                }
            }
        }

        return back()->with('success', 'Profile updated successfully.');
    }
}
