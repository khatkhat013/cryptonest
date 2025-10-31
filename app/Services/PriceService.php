<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

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
    public static function getCryptoPrice(string $symbol, string $prefer = 'auto'): ?float
    {
        $s = strtoupper(trim($symbol));

        // short server-side cache to avoid rapid repeated external calls (helps with rate limits)
        $cacheKey = "price:{$prefer}:{$s}";
        return Cache::remember($cacheKey, 5, function() use ($s, $prefer) {
            // prefer: 'auto' (current behaviour: coinbase then binance if enabled),
            // 'binance' (try binance first), 'coinbase' (force coinbase first)

            $tryOrder = [];
            if ($prefer === 'binance') {
                $tryOrder = ['binance', 'coinbase', 'coingecko'];
            } elseif ($prefer === 'coinbase') {
                $tryOrder = ['coinbase', 'binance', 'coingecko'];
            } else {
                $tryOrder = ['coinbase', 'binance', 'coingecko'];
            }

            foreach ($tryOrder as $source) {
                if ($source === 'coinbase') {
                    try {
                        $resp = Http::timeout(3)->get("https://api.coinbase.com/v2/prices/{$s}-USD/spot");
                        if ($resp->ok()) {
                            $j = $resp->json();
                            if (isset($j['data']['amount'])) {
                                $amt = (float)$j['data']['amount'];
                                if (is_finite($amt) && $amt > 0) return $amt;
                            }
                        }
                    } catch (\Exception $e) { /* ignore */ }
                }

                if ($source === 'binance') {
                    if (env('USE_BINANCE', false) || $prefer === 'binance') {
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
                        } catch (\Exception $e) { /* ignore */ }
                    }
                }

                if ($source === 'coingecko') {
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
                    } catch (\Exception $e) { /* ignore */ }
                }
            }

            return null;
        });
    }

    /**
     * Get price + change percent (24h) when available.
     * Returns ['price' => float|null, 'change' => float|null]
     */
    public static function getCryptoData(string $symbol, string $prefer = 'auto'): array
    {
        $s = strtoupper(trim($symbol));
        $cacheKey = "pricedata:{$prefer}:{$s}";

        return Cache::remember($cacheKey, 5, function() use ($s, $prefer) {
            $result = ['price' => null, 'change' => null];

            $tryOrder = [];
            if ($prefer === 'binance') {
                $tryOrder = ['binance', 'coinbase', 'coingecko'];
            } elseif ($prefer === 'coinbase') {
                $tryOrder = ['coinbase', 'binance', 'coingecko'];
            } else {
                $tryOrder = ['coinbase', 'binance', 'coingecko'];
            }

            foreach ($tryOrder as $source) {
                if ($source === 'coinbase') {
                    try {
                        $resp = Http::timeout(3)->get("https://api.coinbase.com/v2/prices/{$s}-USD/spot");
                        if ($resp->ok()) {
                            $j = $resp->json();
                            if (isset($j['data']['amount'])) {
                                $amt = (float)$j['data']['amount'];
                                if (is_finite($amt) && $amt > 0) {
                                    $result['price'] = $amt;
                                    // Coinbase doesn't provide change percent in this endpoint
                                    $result['change'] = null;
                                    return $result;
                                }
                            }
                        }
                    } catch (\Exception $e) { /* ignore */ }
                }

                if ($source === 'binance') {
                    if (env('USE_BINANCE', false) || $prefer === 'binance') {
                        try {
                            $pair = strtoupper($s) . 'USDT';
                            // use 24hr ticker which includes priceChangePercent and lastPrice
                            $resp = Http::timeout(3)->get('https://api.binance.com/api/v3/ticker/24hr', ['symbol' => $pair]);
                            if ($resp->ok()) {
                                $j = $resp->json();
                                if (isset($j['lastPrice'])) {
                                    $amt = (float)$j['lastPrice'];
                                    $result['price'] = is_finite($amt) && $amt > 0 ? $amt : null;
                                    // Binance returns priceChangePercent as string
                                    if (isset($j['priceChangePercent'])) {
                                        $ch = (float)$j['priceChangePercent'];
                                        $result['change'] = is_finite($ch) ? $ch : null;
                                    }
                                    return $result;
                                }
                            }
                        } catch (\Exception $e) { /* ignore */ }
                    }
                }

                if ($source === 'coingecko') {
                    try {
                        $map = [
                            'BTC' => 'bitcoin', 'ETH' => 'ethereum', 'DOGE' => 'dogecoin', 'XRP' => 'ripple',
                            'USDT' => 'tether', 'USDC' => 'usd-coin', 'BNB' => 'binancecoin', 'TRX' => 'tron'
                        ];
                        $id = $map[$s] ?? strtolower($s);
                        // Use coins/markets endpoint to get 24h change when possible
                        $resp = Http::timeout(3)->get('https://api.coingecko.com/api/v3/coins/markets', ['vs_currency' => 'usd', 'ids' => $id, 'price_change_percentage' => '24h']);
                        if ($resp->ok()) {
                            $j = $resp->json();
                            if (is_array($j) && isset($j[0]['current_price'])) {
                                $amt = (float)$j[0]['current_price'];
                                $result['price'] = is_finite($amt) && $amt > 0 ? $amt : null;
                                if (isset($j[0]['price_change_percentage_24h'])) {
                                    $ch = (float)$j[0]['price_change_percentage_24h'];
                                    $result['change'] = is_finite($ch) ? $ch : null;
                                }
                                return $result;
                            }
                        }
                    } catch (\Exception $e) { /* ignore */ }
                }
            }

            return $result;
        });
    }
}
