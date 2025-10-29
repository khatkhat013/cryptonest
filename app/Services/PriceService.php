<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PriceService
{
    /**
     * Get a USD price for a crypto symbol using the same preference order as the home page JS:
     * 1) Coinbase public spot API
     * 2) Binance public API (if enabled)
     * 3) CoinGecko simple price
     *
     * Returns float price or null on failure.
     */
    public static function getCryptoPrice(string $symbol): ?float
    {
        $s = strtoupper(trim($symbol));

        // 1) Coinbase
        try {
            $resp = Http::timeout(3)->get("https://api.coinbase.com/v2/prices/{$s}-USD/spot");
            if ($resp->ok()) {
                $j = $resp->json();
                if (isset($j['data']['amount'])) {
                    $amt = (float)$j['data']['amount'];
                    if (is_finite($amt) && $amt > 0) return $amt;
                }
            }
        } catch (\Exception $e) {
            // ignore and fall through
        }

        // 2) Binance (optional)
        if (env('USE_BINANCE', false)) {
            try {
                $pair = strtoupper($s) . 'USDT';
                $resp = Http::timeout(3)->get('https://api.binance.com/api/v3/ticker/price', ['symbol' => $pair]);
                if ($resp->ok()) {
                    $j = $resp->json();
                    if (isset($j['price'])) {
                        $amt = (float)$j['price'];
                        if (is_finite($amt) && $amt > 0) return $amt;
                    }
                }
            } catch (\Exception $e) {
                // ignore
            }
        }

        // 3) CoinGecko fallback
        try {
            $map = [
                'BTC' => 'bitcoin', 'ETH' => 'ethereum', 'DOGE' => 'dogecoin', 'XRP' => 'ripple',
                'USDT' => 'tether', 'USDC' => 'usd-coin', 'BNB' => 'binancecoin', 'TRX' => 'tron'
            ];
            $id = $map[$s] ?? strtolower($s);
            $resp = Http::timeout(3)->get('https://api.coingecko.com/api/v3/simple/price', ['ids' => $id, 'vs_currencies' => 'usd']);
            if ($resp->ok()) {
                $j = $resp->json();
                if (isset($j[$id]['usd'])) {
                    $amt = (float)$j[$id]['usd'];
                    if (is_finite($amt) && $amt > 0) return $amt;
                }
            }
        } catch (\Exception $e) {
            // ignore
        }

        return null;
    }
}
