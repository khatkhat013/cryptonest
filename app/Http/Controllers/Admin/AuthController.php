<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AdminWallet;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Try to locate the admin explicitly and authenticate against its hashed password
        $admin = Admin::where('email', $credentials['email'])->first();
        if ($admin && \Illuminate\Support\Facades\Hash::check($credentials['password'], $admin->password)) {
            Auth::guard('admin')->login($admin, $request->filled('remember'));
            // Log successful admin login for debugging
            \Illuminate\Support\Facades\Log::info('Admin logged in', ['admin_id' => $admin->id, 'email' => $admin->email]);
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'));
        }

        // Fallback: attempt with the guard (keeps backwards compatibility)
        if (Auth::guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showRegisterForm()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }
        $currencies = Currency::where('is_active', true)->get();
        return view('admin.auth.register', compact('currencies'));
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:admins,email'],
            'phone' => ['required', 'string', 'max:20', 'unique:admins,phone'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'telegram_username' => ['required', 'string', 'max:100', 'unique:admins,telegram_username'],
            'wallet_addresses' => ['required', 'array'],
            'wallet_addresses.*' => ['required', 'string', 'max:255']
        ]);

        // Create admin with normal role (ID: 1)
        $admin = Admin::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
            'telegram_username' => $data['telegram_username'],
            'role_id' => config('roles.normal_id', 1)
        ]);

        // Ensure currencies table exists
        if (!Schema::hasTable('currencies')) {
            Log::error('Currencies table missing when registering admin; cannot persist wallets');
            // proceed but inform user
            return back()->withErrors(['wallet_addresses' => 'Currency configuration is missing. Please contact the system administrator.']);
        }

        // Create or update wallets for each provided currency address.
        // Accept keys as currency ID (int) or currency symbol (e.g. BTC)
        foreach ($data['wallet_addresses'] as $key => $address) {
            $currency = null;
            if (is_numeric($key)) {
                $currency = Currency::find((int) $key);
            }

            if (!$currency) {
                // try looking up by symbol (case-insensitive)
                $symbol = strtoupper(trim((string) $key));
                $currency = Currency::whereRaw('UPPER(symbol) = ?', [$symbol])->first();
            }

            if (!$currency) {
                // if we still can't resolve, skip but log for debugging
                Log::warning("Could not resolve currency for admin wallet key: {$key}");
                continue;
            }

            AdminWallet::updateOrCreate(
                ['admin_id' => $admin->id, 'currency_id' => $currency->id],
                ['address' => $address]
            );
        }

        Auth::guard('admin')->login($admin);

        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('admin.login');
    }
}