<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWithdrawalRequest;
use App\Models\Withdrawal;
use App\Models\UserWallet;
use Illuminate\Support\Facades\DB;

class WithdrawalController extends Controller
{
    public function store(StoreWithdrawalRequest $request)
    {
        // Validated data is automatically escaped and filtered via FormRequest
        $validated = $request->validated();

        $user = $request->user();

        $coin = strtolower($validated['coin']);
        $amount = floatval($validated['amount']);
        $destination = trim($validated['destination_address']);

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
            'destination_address' => $destination,
            'amount' => $amount,
            'status' => 'pending'
        ]);

        return back()->with('success', 'Withdrawal request submitted.');
    }
}
