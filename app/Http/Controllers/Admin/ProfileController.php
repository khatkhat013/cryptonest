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
        $adminWallets = \App\Models\AdminWallet::where('admin_id', $admin->id)->with(['currency', 'network'])->get();

        // networks: all networks (global list)
        $networks = \App\Models\Network::orderBy('name')->get();

        return view('admin.profile', compact('admin', 'adminWallets', 'networks'));
    }

    public function update(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:admins,email,' . $admin->id],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'telegram_username' => ['nullable', 'string', 'max:255', 'unique:admins,telegram_username,' . $admin->id],
            'wallets' => ['sometimes', 'array'],
            'wallets.*.address' => ['nullable', 'string', 'max:255'],
            'wallets.*.network_id' => ['nullable', 'exists:networks,id']
        ], [
            'telegram_username.unique' => 'This Telegram username is already being used by another admin.',
        ]);


        $admin->name = $data['name'];
        $admin->email = $data['email'];
        if (array_key_exists('telegram_username', $data)) {
            $admin->telegram_username = $data['telegram_username'];
        }
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
                    if (isset($wdata['network_id'])) {
                        $update['network_id'] = $wdata['network_id'];
                        // keep legacy network string for backward compatibility
                        $net = \App\Models\Network::find($wdata['network_id']);
                        if ($net) {
                            $update['network'] = $net->name;
                        }
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
