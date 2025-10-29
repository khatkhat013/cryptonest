<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Withdrawal;
use App\Models\UserWallet;

class WithdrawalController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'destination_address' => 'required|string|max:512',
            'amount' => 'required|numeric|min:0.00000001',
            'coin' => 'required|string|max:16'
        ]);

        $user = $request->user();

        $coin = strtolower($request->input('coin'));
        $amount = floatval($request->input('amount'));

        // Ensure user_wallets table has a wallet row for this user and coin
        $wallet = UserWallet::where('user_id', $user->id)
            ->whereRaw('LOWER(coin) = ?', [$coin])
            ->first();

        if (!$wallet) {
            return back()->with('error', 'Wallet not found for ' . strtoupper($coin));
        }

        // Require that the requested amount plus fee does not exceed available balance.
        // Withdrawal processing uses a 1% fee in the observer; enforce same rule here.
        $feeRate = 0.01; // 1%
        $fee = round($amount * $feeRate, 8);

        // Use float comparison here because balances are stored as decimal with limited precision.
        if (floatval($wallet->balance) < ($amount + $fee)) {
            // Do not create a withdrawal record; inform the user and abort with concise message.
            return back()->with('error', 'Insufficient balance for ' . strtoupper($coin) . '.');
        }

        // Wallet exists and has sufficient funds; create the withdrawal request.
        $withdrawal = Withdrawal::create([
            'user_id' => $user->id,
            'coin' => $coin,
            'destination_address' => $request->input('destination_address'),
            'amount' => $amount,
            'status' => 'pending'
        ]);

        return back()->with('success', 'Withdrawal request submitted.');
    }
}
