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
        $withdrawalsQuery = \App\Models\Withdrawal::with(['user', 'actionStatus'])->latest();
        $tradesQuery = \App\Models\TradeOrder::with('user')->latest();

        // Non-super admins only see activities for users assigned to them
        if ($admin && method_exists($admin, 'isSuperAdmin') && !$admin->isSuperAdmin()) {
            $depositsQuery->whereHas('user', function($q) use ($admin) {
                $q->where('assigned_admin_id', $admin->id);
            });
            $withdrawalsQuery->whereHas('user', function($q) use ($admin) {
                $q->where('assigned_admin_id', $admin->id);
            });
            $tradesQuery->whereHas('user', function($q) use ($admin) {
                $q->where('assigned_admin_id', $admin->id);
            });
        }

        $recentDeposits = $depositsQuery->limit(20)->get();
        $recentWithdrawals = $withdrawalsQuery->limit(20)->get();
        $recentTrades = $tradesQuery->limit(20)->get();

        // Merge into a single recent activities collection and sort by created_at desc
        $activities = collect();
        foreach ($recentDeposits as $d) {
            $activities->push((object)[
                'type' => 'deposit',
                'id' => $d->id,
                'tx_id' => $d->id ? sprintf('D%05d', $d->id) : null,
                'user' => $d->user,
                'amount' => $d->amount,
                'coin' => $d->coin,
                'status' => $d->actionStatus?->name ?? $d->status ?? 'Pending',
                'created_at' => $d->created_at,
            ]);
        }
        foreach ($recentWithdrawals as $w) {
            $activities->push((object)[
                'type' => 'withdrawal',
                'id' => $w->id,
                'tx_id' => $w->id ? sprintf('W%05d', $w->id) : null,
                'user' => $w->user,
                'amount' => $w->amount,
                'coin' => $w->coin,
                'status' => $w->actionStatus?->name ?? $w->status ?? 'Pending',
                'created_at' => $w->created_at,
            ]);
        }
        foreach ($recentTrades as $t) {
            $activities->push((object)[
                'type' => 'trade',
                'id' => $t->id,
                'tx_id' => $t->id ? sprintf('T%05d', $t->id) : null,
                'user' => $t->user,
                'amount' => $t->purchase_quantity,
                'coin' => $t->symbol,
                'status' => $t->result ?? 'open',
                'created_at' => $t->created_at,
            ]);
        }

        $recentActivities = $activities->sortByDesc('created_at')->values()->take(20);

        return view('admin.dashboard', ['recentActivities' => $recentActivities]);
    }
}
