<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TradeOrder;
use App\Models\User;

class TradeOrderAdminController extends Controller
{
    /**
     * Check if current admin can manage this trade
     */
    private function canManageTrade(TradeOrder $trade): bool
    {
        $admin = Auth::guard('admin')->user();
        // Super admin can manage any trade, or if trade belongs to assigned user
        return $admin->isSuperAdmin() || ($trade->user && $trade->user->assigned_admin_id === $admin->id);
    }

    public function index(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        $query = TradeOrder::with('user')->latest();
        
        // Non-super-admin can only see trades for their assigned users
        if (!$admin->isSuperAdmin()) {
            $query->whereHas('user', function($q) use ($admin) {
                $q->where('assigned_admin_id', $admin->id);
            });
        }
        
        $trades = $query->paginate(20);
        return view('admin.trading', compact('trades'));
    }

    public function edit($id)
    {
        $trade = TradeOrder::with('user')->findOrFail($id);
        
        // Authorization check
        if (!$this->canManageTrade($trade)) {
            abort(403, 'You are not authorized to edit this trade.');
        }
        
        return view('admin.trade_edit', compact('trade'));
    }

    public function update(Request $request, $id)
    {
        $trade = TradeOrder::with('user')->findOrFail($id);
        
        // Authorization check
        if (!$this->canManageTrade($trade)) {
            abort(403, 'You are not authorized to update this trade.');
        }
        
        // Validate 'result' field to prevent invalid values
        $validated = $request->validate([
            'result' => 'nullable|in:win,lose,pending',
            // Add other fields as needed for stricter validation
        ]);

        $trade->fill($request->only(['symbol','direction','purchase_quantity','purchase_price','initial_price','final_price','price_range_percent','delivery_seconds','profit_amount','payout','result']));
        $trade->save();
        
        return redirect()->route('admin.trading.index')->with('success', 'Trade updated successfully.');
    }

    /**
     * Delete a trade order (admins only for assigned users; super-admins can delete any)
     */
    public function destroy($id)
    {
        $trade = TradeOrder::with('user')->findOrFail($id);
        
        // Authorization check
        if (!$this->canManageTrade($trade)) {
            abort(403, 'You are not authorized to delete this trade.');
        }

        $trade->delete();
        return redirect()->route('admin.trading.index')->with('success', 'Trade deleted successfully.');
    }
}
