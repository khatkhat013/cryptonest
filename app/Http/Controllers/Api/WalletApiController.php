<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserWallet;

class WalletApiController extends Controller
{
    // GET /api/wallet/balance/{coin}
    public function balance(Request $request, $coin)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['balance' => '0.00'], 200);
        }

        $coin = strtolower(trim($coin));

        // ensure table exists to avoid exceptions when migrations haven't run
        if (!\Illuminate\Support\Facades\Schema::hasTable('user_wallets')) {
            return response()->json(['balance' => '0.00'], 200);
        }

        $wallet = UserWallet::where('user_id', $user->id)->where('coin', $coin)->first();
        $balance = $wallet ? (string) $wallet->balance : '0.00';

        return response()->json(['balance' => $balance], 200);
    }
}
