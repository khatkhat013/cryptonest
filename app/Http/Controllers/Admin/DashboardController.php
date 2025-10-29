<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Deposit;

class DashboardController extends Controller
{
    public function index()
    {
        $admin = Auth::guard('admin')->user();

    $depositsQuery = Deposit::with(['user', 'actionStatus'])->latest();

        // Non-super admins only see deposits for users assigned to them
        if ($admin && method_exists($admin, 'isSuperAdmin') && !$admin->isSuperAdmin()) {
            $depositsQuery->whereHas('user', function($q) use ($admin) {
                $q->where('assigned_admin_id', $admin->id);
            });
        }

        $recentDeposits = $depositsQuery->limit(20)->get();

        return view('admin.dashboard', ['recentDeposits' => $recentDeposits]);
    }
}
