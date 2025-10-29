<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Deposit;
use App\Models\Withdrawal;
use App\Models\UserWallet;
use App\Models\Currency;
use App\Services\PriceService;
use Illuminate\Support\Facades\DB;


class FinancialRecordController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Aggregate wallet balances by normalized coin (case-insensitive) using a DB group-by
        $agg = [];
        $rows = DB::table('user_wallets')
            ->select(DB::raw('UPPER(TRIM(COALESCE(coin, ""))) as coin'), DB::raw('SUM(balance) as balance'), DB::raw('MAX(currency_id) as currency_id'))
            ->where('user_id', $user->id)
            ->whereRaw('TRIM(COALESCE(coin, "")) <> ""')
            ->groupBy(DB::raw('UPPER(TRIM(COALESCE(coin, "")))'))
            ->get();

        foreach ($rows as $r) {
            $coin = strtoupper(trim((string)$r->coin));
            if ($coin === '') continue;
            $agg[$coin] = [
                'coin' => $coin,
                'balance' => (float)$r->balance,
                'currency_id' => $r->currency_id,
            ];
        }

        // Determine primary wallet from aggregated set (prefer BTC)
        $primary = null;
        if (! empty($agg)) {
            $primary = $agg[array_key_first($agg)];
            if (isset($agg['BTC'])) $primary = $agg['BTC'];
        }

        $symbol = $primary ? strtolower($primary['coin']) : 'btc';
        $balance = $primary ? (float)$primary['balance'] : 0.0;

        // Compute USD values using aggregated balances
        $walletsData = [];
        $totalAllUsd = 0.0;
        foreach ($agg as $coin => $info) {
            $sym = strtolower($coin);
            $cacheKey = 'price_usd_' . $sym;
            $price = cache()->remember($cacheKey, 60, function () use ($sym) {
                return PriceService::getCryptoPrice($sym);
            });
            $bal = (float)$info['balance'];
            $usd = $price !== null ? ($price * $bal) : null;
            if ($usd !== null) $totalAllUsd += $usd;
            $walletsData[] = [
                'symbol' => strtoupper($coin),
                'balance' => $bal,
                'price' => $price,
                'usd' => $usd,
            ];
        }

    // Also prepare deposits/withdrawals. Show all deposit records for the user (do not filter by primary coin)
    $deposits = Deposit::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
    // Show all withdrawals for the user (do not filter by primary coin)
    $withdrawals = Withdrawal::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();

        return view('financial.record', [
            'symbol' => strtoupper($symbol),
            'balance' => $balance,
            'walletsData' => $walletsData,
            'totalAllUsd' => $totalAllUsd,
            'deposits' => $deposits,
            'withdrawals' => $withdrawals,
        ]);
    }
}
