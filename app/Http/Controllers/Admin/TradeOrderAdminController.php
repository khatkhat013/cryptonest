<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TradeOrder;
use App\Models\User;

class TradeOrderAdminController extends Controller
{
    public function index(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        $query = TradeOrder::with('user')->latest();
        if ($admin && method_exists($admin, 'isSuperAdmin') && !$admin->isSuperAdmin()) {
            $query->whereHas('user', function($q) use ($admin) {
                $q->where('assigned_admin_id', $admin->id);
            });
        }
        $trades = $query->paginate(20);
        return view('admin.trading', compact('trades'));
    }

    public function edit($id)
    {
        $admin = Auth::guard('admin')->user();
        $trade = TradeOrder::with('user')->findOrFail($id);
        if ($admin && method_exists($admin, 'isSuperAdmin') && !$admin->isSuperAdmin()) {
            if (!$trade->user || $trade->user->assigned_admin_id !== $admin->id) {
                abort(403);
            }
        }
        return view('admin.trade_edit', compact('trade'));
    }

    public function update(Request $request, $id)
    {
        $admin = Auth::guard('admin')->user();
        $trade = TradeOrder::with('user')->findOrFail($id);
        if ($admin && method_exists($admin, 'isSuperAdmin') && !$admin->isSuperAdmin()) {
            if (!$trade->user || $trade->user->assigned_admin_id !== $admin->id) {
                abort(403);
            }
        }
        $trade->fill($request->only(['symbol','direction','purchase_quantity','purchase_price','initial_price','final_price','price_range_percent','delivery_seconds','profit_amount','payout','result']));
        $trade->save();
        return redirect()->route('admin.trading.index')->with('success', 'Trade updated successfully.');
    }

    /**
     * Delete a trade order (admins only for assigned users; super-admins can delete any)
     */
    public function destroy($id)
    {
        $admin = Auth::guard('admin')->user();
        $trade = TradeOrder::with('user')->findOrFail($id);
        if ($admin && method_exists($admin, 'isSuperAdmin') && !$admin->isSuperAdmin()) {
            if (!$trade->user || $trade->user->assigned_admin_id !== $admin->id) {
                abort(403);
            }
        }

        $trade->delete();
        return redirect()->route('admin.trading.index')->with('success', 'Trade deleted successfully.');
    }
}
