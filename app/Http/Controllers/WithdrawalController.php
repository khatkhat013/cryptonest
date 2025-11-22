<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWithdrawalRequest;
use App\Models\Withdrawal;
use App\Models\UserWallet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WithdrawalController extends Controller
{
    public function store(StoreWithdrawalRequest $request)
    {
        Log::info('=== WITHDRAWAL STORE CALLED ===', [
            'user_id' => $request->user()->id ?? null,
            'all_input' => $request->all(),
            'coin' => $request->coin,
            'destination_address' => substr($request->destination_address, 0, 30),
            'amount' => $request->amount
        ]);
        
        // Validated data is automatically escaped and filtered via FormRequest
        $validated = $request->validated();
        
        Log::info('Validation passed', ['validated_data' => $validated]);

        $user = $request->user();

        $coin = strtolower($validated['coin']);
        $amount = floatval($validated['amount']);
        $destination = trim($validated['destination_address']);

        Log::info('Processing withdrawal', ['user_id' => $user->id, 'coin' => $coin, 'amount' => $amount]);

        // Ensure user_wallets table has a wallet row for this user and coin
        $wallet = UserWallet::where('user_id', $user->id)
            ->whereRaw('LOWER(coin) = ?', [$coin])
            ->first();

        if (!$wallet) {
            Log::warning('Wallet not found', ['user_id' => $user->id, 'coin' => $coin]);
            return back()->with('error', 'Wallet not found for ' . strtoupper($coin));
        }

        Log::info('Wallet found', ['wallet_id' => $wallet->id, 'balance' => $wallet->balance]);

        // Require that the requested amount plus fee does not exceed available balance.
        // Withdrawal processing uses a 1% fee in the observer; enforce same rule here.
        $feeRate = 0.01; // 1%
        $fee = round($amount * $feeRate, 8);
        
        Log::info('Checking balance', ['amount' => $amount, 'fee' => $fee, 'total_required' => $amount + $fee, 'available' => $wallet->balance]);

        // Use float comparison here because balances are stored as decimal with limited precision.
        if (floatval($wallet->balance) < ($amount + $fee)) {
            // Do not create a withdrawal record; inform the user and abort with concise message.
            Log::warning('Insufficient balance', ['required' => $amount + $fee, 'available' => $wallet->balance]);
            return back()->with('error', 'Insufficient balance for ' . strtoupper($coin) . '.');
        }

        Log::info('Balance check passed, creating withdrawal');

        // Wallet exists and has sufficient funds; create the withdrawal request.
        $withdrawal = Withdrawal::create([
            'user_id' => $user->id,
            'coin' => $coin,
            'destination_address' => $destination,
            'amount' => $amount,
            'status' => 'pending'
        ]);

        Log::info('Withdrawal created successfully', ['withdrawal_id' => $withdrawal->id, 'user_id' => $user->id]);

        return back()->with('success', 'Withdrawal request submitted.');
    }
}
