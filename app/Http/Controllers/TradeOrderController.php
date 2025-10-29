<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TradeOrder;
use App\Services\TradeService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\UserWallet;

class TradeOrderController extends Controller
{
    protected $tradeService;

    public function __construct(TradeService $tradeService)
    {
        $this->tradeService = $tradeService;
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'symbol' => 'required|string',
            'direction' => 'required|in:up,down',
            'delivery_seconds' => 'required|integer|min:1',
            'price_range_percent' => 'nullable|integer',
            'purchase_quantity' => 'required|numeric|min:1',
            'purchase_price' => 'required|numeric|min:0'
        ]);

        $order = TradeOrder::create([
            'user_id' => Auth::id(),
            'symbol' => strtoupper($data['symbol']),
            'direction' => $data['direction'],
            'purchase_quantity' => $data['purchase_quantity'],
            'purchase_price' => $data['purchase_price'],
            'price_range_percent' => $data['price_range_percent'] ?? null,
            'delivery_seconds' => $data['delivery_seconds'],
            'result' => 'pending'
        ]);

        // Try to fetch a server-authoritative current price and persist it as initial_price.
        // Prefer Coinbase, optionally Binance (USE_BINANCE env), fallback to CoinGecko.
        $symbol = strtoupper($order->symbol);
        $initialPrice = null;
        try {
            $resp = Http::get("https://api.coinbase.com/v2/prices/{$symbol}-USD/spot");
            if ($resp->ok()) {
                $j = $resp->json();
                if (isset($j['data']['amount'])) $initialPrice = (float)$j['data']['amount'];
            }
        } catch (\Exception $e) {}

        if ($initialPrice === null && env('USE_BINANCE', false)) {
            try {
                $pair = $symbol . 'USDT';
                $resp = Http::get("https://api.binance.com/api/v3/ticker/price", ['symbol'=>$pair]);
                if ($resp->ok()) {
                    $j = $resp->json();
                    if (isset($j['price'])) $initialPrice = (float)$j['price'];
                }
            } catch (\Exception $e) {}
        }

        if ($initialPrice === null) {
            try {
                $map = ['BTC'=>'bitcoin','ETH'=>'ethereum','BNB'=>'binancecoin','TRX'=>'tron','XRP'=>'ripple','DOGE'=>'dogecoin'];
                $s = strtolower($order->symbol);
                if (isset($map[$s])) {
                    $resp = Http::get('https://api.coingecko.com/api/v3/simple/price', ['ids'=>$map[$s],'vs_currencies'=>'usd']);
                    if ($resp->ok()) {
                        $j = $resp->json();
                        if (isset($j[$map[$s]]['usd'])) $initialPrice = (float)$j[$map[$s]]['usd'];
                    }
                }
            } catch (\Exception $e) {}
        }

        if ($initialPrice !== null) {
            $order->initial_price = $initialPrice;
            // if client price is missing or zero, set purchase_price to server price to avoid bad data
            if (empty($order->purchase_price) || $order->purchase_price == 0) {
                $order->purchase_price = $initialPrice;
            }
            $order->save();
        }

        return response()->json(['id' => $order->id, 'initial_price' => $initialPrice]);
    }

    public function finalize(Request $r, $id)
    {
        $order = TradeOrder::findOrFail($id);
        if ($order->result !== 'pending') {
            return response()->json(['status' => 'already_finalized', 'result' => $order->result]);
        }

        // fetch final price for crypto: prefer Coinbase, optionally Binance (for VPS), fallback to CoinGecko
        $symbol = strtoupper($order->symbol);
        $finalPrice = null;
        
        // Try to get real market price from different sources
        try {
            $resp = Http::get("https://api.coinbase.com/v2/prices/{$symbol}-USD/spot");
            if ($resp->ok()) {
                $j = $resp->json();
                if (isset($j['data']['amount'])) $finalPrice = (float)$j['data']['amount'];
            }
        } catch (\Exception $e) {}

        if ($finalPrice === null && env('USE_BINANCE', false)) {
            try {
                $pair = $symbol . 'USDT';
                $resp = Http::get("https://api.binance.com/api/v3/ticker/price", ['symbol'=>$pair]);
                if ($resp->ok()) {
                    $j = $resp->json();
                    if (isset($j['price'])) $finalPrice = (float)$j['price'];
                }
            } catch (\Exception $e) {}
        }

        if ($finalPrice === null) {
            $map = ['btc'=>'bitcoin','eth'=>'ethereum','bnb'=>'binancecoin','trx'=>'tron','xrp'=>'ripple','doge'=>'dogecoin'];
            try {
                $s = strtolower($order->symbol);
                if (isset($map[$s])) {
                    $resp = Http::get('https://api.coingecko.com/api/v3/simple/price', ['ids'=>$map[$s],'vs_currencies'=>'usd']);
                    if ($resp->ok()) {
                        $j = $resp->json();
                        if (isset($j[$map[$s]]['usd'])) $finalPrice = (float)$j[$map[$s]]['usd'];
                    }
                }
            } catch (\Exception $e) {}
        }

        if ($finalPrice === null) {
            if (!empty($order->initial_price)) {
                $finalPrice = (float)$order->initial_price;
            } else {
                $order->result = 'error';
                $order->meta = array_merge((array)$order->meta, ['finalize_error' => 'fetch_failed']);
                $order->save();
                return response()->json(['status'=>'error','message'=>'final price fetch failed'], 500);
            }
        }

        // Calculate guaranteed win using the service
        $result = $this->tradeService->calculateAmountBasedGuaranteedWin([
            'user_id' => $order->user_id,
            'symbol' => $order->symbol,
            'direction' => $order->direction,
            'purchase_price' => $order->purchase_price,
            'purchase_quantity' => $order->purchase_quantity,
            'price_range_percent' => $order->price_range_percent
        ], $finalPrice);

        // Update the order with calculated values
        // Only persist allowed keys to avoid accidental mass-assignment of unexpected fields
        $allowed = ['result', 'final_price', 'profit_amount', 'payout'];
        foreach ($allowed as $k) {
            if (array_key_exists($k, $result)) {
                $order->{$k} = $result[$k];
            }
        }
        // Ensure result is set to either 'win' or 'lose'
        if (!in_array($order->result, ['win','lose','error','pending'])) {
            $order->result = 'error';
        }
        $order->save();

        // --- Update user's USDT wallet based on profit_amount ---
        try {
            DB::beginTransaction();
            $userId = $order->user_id;
            $profit = floatval($order->profit_amount ?? 0);

            // Only do wallet updates when there's a non-zero profit (positive or negative)
            if ($profit != 0) {
                // Find user's USDT wallet; coin column may be stored lowercase
                $wallet = UserWallet::where('user_id', $userId)
                    ->whereRaw('LOWER(coin) = ?', ['usdt'])
                    ->first();

                if (!$wallet) {
                    // Create wallet if missing
                    $wallet = UserWallet::create([
                        'user_id' => $userId,
                        'coin' => 'USDT',
                        'balance' => 0,
                    ]);
                }

                $prev = floatval($wallet->balance ?? 0);
                $new = $prev + $profit; // profit may be negative for a loss
                $wallet->balance = $new;
                $wallet->save();

                // Annotate order meta for auditing
                $meta = (array) $order->meta;
                $meta['wallet_update'] = [
                    'coin' => 'USDT',
                    'previous_balance' => $prev,
                    'applied_amount' => $profit,
                    'new_balance' => $new,
                    'timestamp' => now()->toDateTimeString()
                ];
                $order->meta = $meta;
                $order->save();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            // record the error in order meta but do not fail the finalize call
            $order->meta = array_merge((array)$order->meta, ['wallet_update_error' => $e->getMessage()]);
            $order->save();
        }

        return response()->json([
            'status' => 'ok',
            'result' => $order->result,
            'final_price' => $order->final_price,
            'profit_amount' => $order->profit_amount,
            'payout' => $order->payout
        ]);
    }
}
