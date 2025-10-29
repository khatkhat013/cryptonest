<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Conversion;
use Illuminate\Support\Facades\DB;
use App\Services\PriceService;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Fetch recent conversions (limit 50)
        $conversions = Conversion::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        $conversionItems = $conversions->map(function ($c) {
            $fromSym = $c->from_currency_id ? DB::table('currencies')->where('id', $c->from_currency_id)->value('symbol') : ($c->from_coin ?? null);
            $toSym = $c->to_currency_id ? DB::table('currencies')->where('id', $c->to_currency_id)->value('symbol') : ($c->to_coin ?? null);

            $fromSym = $fromSym ? strtoupper($fromSym) : null;
            $toSym = $toSym ? strtoupper($toSym) : null;

            $fromAmt = (float)$c->from_amount;
            $toAmt = (float)$c->to_amount;

            $fromUsd = null;
            $toUsd = null;
            try {
                if ($fromSym) {
                    $p = PriceService::getCryptoPrice(strtolower($fromSym));
                    if ($p !== null) $fromUsd = $p * $fromAmt;
                }
                if ($toSym) {
                    $p2 = PriceService::getCryptoPrice(strtolower($toSym));
                    if ($p2 !== null) $toUsd = $p2 * $toAmt;
                }
            } catch (\Throwable $e) {
                $fromUsd = null; $toUsd = null;
            }

            return [
                'id' => $c->id,
                'created_at' => $c->created_at ? $c->created_at->toDateTimeString() : null,
                'from_symbol' => $fromSym,
                'to_symbol' => $toSym,
                'from_amount' => $fromAmt,
                'to_amount' => $toAmt,
                'from_usd' => $fromUsd,
                'to_usd' => $toUsd,
                'status' => $c->status,
            ];
        })->toArray();

        // Mining records: use ai_arbitrage_plans as mining-like records if table exists
        $miningRecords = [];
        if (DB::getSchemaBuilder()->hasTable('ai_arbitrage_plans')) {
            $plans = DB::table('ai_arbitrage_plans')
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->get();

            foreach ($plans as $p) {
                $miningRecords[] = [
                    'id' => $p->id,
                    'created_at' => $p->created_at,
                    'plan_name' => $p->plan_name,
                    'quantity' => $p->quantity,
                    'status' => $p->status,
                ];
            }
        }

        return view('transaction.index', [
            'conversionItems' => $conversionItems,
            'miningRecords' => $miningRecords,
        ]);
    }
}
