<?php

namespace App\Http\Controllers;

use App\Models\TradeOrder;
use App\Services\TradeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TradeController extends Controller
{
    protected $tradeService;

    public function __construct(TradeService $tradeService)
    {
        $this->tradeService = $tradeService;
    }

    /**
     * Get simulated price during countdown
     */
    public function getTradePrice($orderId, Request $request)
    {
        try {
            $order = TradeOrder::findOrFail($orderId);
            $progress = $request->query('progress', 0);

            // Convert progress to seconds elapsed (for 60s total)
            $secondsElapsed = round(($progress / 100) * 60);

            // Calculate price using existing method
            $price = $this->tradeService->calculateSimulatedPrice(
                $order->purchase_price,
                $order->direction,
                $secondsElapsed,
                60,
                $order->symbol
            );

            return response()->json([
                'price' => $price,
                'debug' => [
                    'progress' => $progress,
                    'seconds' => $secondsElapsed,
                    'direction' => $order->direction
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Trade price calculation error: ' . $e->getMessage());
            return response()->json(['error' => 'Price calculation failed'], 500);
        }
    }

    /**
     * Complete a trade
     */
    public function complete(Request $request, $orderId)
    {
        $order = TradeOrder::findOrFail($orderId);
        
        // Calculate final result
        $result = $this->tradeService->calculateTradeResult(
            $order->toArray(),
            $request->final_price
        );
        
        // Update order
        $order->update([
            'result' => $result['result'],
            'final_price' => $result['final_price'],
            'profit_amount' => $result['profit_amount'],
            'payout' => $result['payout']
        ]);

        return response()->json($result);
    }

    /**
     * Get trade history for a user
     */
    public function orders()
    {
        $userId = Auth::id();

        // Get pending and failed orders for holding
        $holdingOrders = TradeOrder::where('user_id', $userId)
            ->whereIn('result', ['pending', 'error'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Get completed orders for history
        $historicalOrders = TradeOrder::where('user_id', $userId)
            ->whereIn('result', ['win', 'lose'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Calculate statistics
        $stats = [
            'totalTrades' => TradeOrder::where('user_id', $userId)->whereIn('result', ['win', 'lose'])->count(),
            'winningTrades' => TradeOrder::where('user_id', $userId)->where('result', 'win')->count(),
            'losingTrades' => TradeOrder::where('user_id', $userId)->where('result', 'lose')->count(),
            'totalProfit' => TradeOrder::where('user_id', $userId)->where('result', 'win')->sum('profit_amount'),
            'totalPayout' => TradeOrder::where('user_id', $userId)->where('result', 'win')->sum('payout')
        ];

        return view('trade.orders', [
            'holdingOrders' => $holdingOrders,
            'historicalOrders' => $historicalOrders,
            'stats' => $stats
        ]);
    }
}