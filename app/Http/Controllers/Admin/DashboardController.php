<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Deposit;
use App\Models\PlanPrice;

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

    $recentDeposits = $depositsQuery->limit(15)->get();
    $recentWithdrawals = $withdrawalsQuery->limit(15)->get();
    $recentTrades = $tradesQuery->limit(15)->get();

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

    $recentActivities = $activities->sortByDesc('created_at')->values()->take(15);

        // Summary counts (total + "new" in last 24 hours)
        $depositsCount = $depositsQuery->count();
        $depositsNew = $depositsQuery->where('created_at', '>=', now()->subDay())->count();

        $withdrawalsCount = $withdrawalsQuery->count();
        $withdrawalsNew = $withdrawalsQuery->where('created_at', '>=', now()->subDay())->count();

        $tradesCount = $tradesQuery->count();
        $tradesNew = $tradesQuery->where('created_at', '>=', now()->subDay())->count();

        // AI Arbitrage plans - use query builder to allow joining users for admin filtering
        $aiArbBase = \Illuminate\Support\Facades\DB::table('ai_arbitrage_plans as p')
            ->select('p.id', 'p.created_at')
            ->leftJoin('users as u', 'p.user_id', '=', 'u.id');

        if ($admin && method_exists($admin, 'isSuperAdmin') && !$admin->isSuperAdmin()) {
            $aiArbBase->where('u.assigned_admin_id', $admin->id);
        }

        $aiArbCount = (clone $aiArbBase)->count();
        $aiArbNew = (clone $aiArbBase)->where('p.created_at', '>=', now()->subDay())->count();

        // Plan inquiries (recent)
        $planPricesQuery = PlanPrice::with('admin')->latest();
        if ($admin && method_exists($admin, 'isSuperAdmin') && !$admin->isSuperAdmin()) {
            $planPricesQuery->where('admin_id', $admin->id);
        }
        $recentPlanInquiries = $planPricesQuery->limit(10)->get();

        return view('admin.dashboard', [
                        'recentPlanInquiries' => $recentPlanInquiries,
            'recentActivities' => $recentActivities,
            'depositsCount' => $depositsCount,
            'depositsNew' => $depositsNew,
            'withdrawalsCount' => $withdrawalsCount,
            'withdrawalsNew' => $withdrawalsNew,
            'tradesCount' => $tradesCount,
            'tradesNew' => $tradesNew,
            'aiArbCount' => $aiArbCount,
            'aiArbNew' => $aiArbNew,
        ]);
    }
}
