<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class PriceService
{
    /**
     * Return an associative array for a crypto symbol with keys:
     * - price: float|null (USD)
     * - change: float|null (24h percent change)
     * - source: string|null (binance|coingecko|coinbase)
     * - ts: int timestamp ms
     *
     * prefer: 'auto'|'binance'|'coinbase' to influence attempt order.
     */
    public static function getCryptoData(string $symbol, string $prefer = 'auto'): array
    {
        $s = strtoupper(trim($symbol));
        $cacheKey = "prices:data:{$prefer}:{$s}";

        return Cache::remember($cacheKey, 5, function() use ($s, $prefer) {
            $result = ['price' => null, 'change' => null, 'source' => null, 'ts' => (int)round(microtime(true) * 1000)];

            // Determine try order
            if ($prefer === 'binance') {
                $tryOrder = ['binance', 'coingecko', 'coinbase'];
            } elseif ($prefer === 'coinbase') {
                $tryOrder = ['coinbase', 'coingecko', 'binance'];
            } else {
                $tryOrder = ['binance', 'coingecko', 'coinbase'];
            }

            $candidates = [];

            foreach ($tryOrder as $source) {
                if ($source === 'coinbase') {
                    try {
                        $resp = Http::timeout(3)->get("https://api.coinbase.com/v2/prices/{$s}-USD/spot");
                        if ($resp->ok()) {
                            $j = $resp->json();
                            if (isset($j['data']['amount'])) {
                                $amt = (float)$j['data']['amount'];
                                if (is_finite($amt) && $amt > 0) {
                                    $candidates['coinbase'] = ['price' => $amt, 'change' => null];
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        // ignore
                    }
                }

                if ($source === 'binance') {
                    if (env('USE_BINANCE', false) || $prefer === 'binance') {
                        try {
                            $pair = strtoupper($s) . 'USDT';
                            $resp = Http::timeout(3)->get('https://api.binance.com/api/v3/ticker/24hr', ['symbol' => $pair]);
                            if ($resp->ok()) {
                                $j = $resp->json();
                                if (isset($j['lastPrice'])) {
                                    $amt = (float)$j['lastPrice'];
                                    $ch = isset($j['priceChangePercent']) ? (float)$j['priceChangePercent'] : null;
                                    $candidates['binance'] = ['price' => (is_finite($amt) && $amt > 0) ? $amt : null, 'change' => is_finite($ch) ? $ch : null];
                                }
                            }
                        } catch (\Exception $e) {
                            // ignore
                        }
                    }
                }

                if ($source === 'coingecko') {
                    try {
                        $map = [
                            'BTC' => 'bitcoin', 'ETH' => 'ethereum', 'DOGE' => 'dogecoin', 'XRP' => 'ripple',
                            'USDT' => 'tether', 'USDC' => 'usd-coin', 'BNB' => 'binancecoin', 'TRX' => 'tron'
                        ];
                        $id = $map[$s] ?? strtolower($s);
                        $resp = Http::timeout(4)->get('https://api.coingecko.com/api/v3/coins/markets', ['vs_currency' => 'usd', 'ids' => $id, 'price_change_percentage' => '24h']);
                        if ($resp->ok()) {
                            $j = $resp->json();
                            if (is_array($j) && isset($j[0]['current_price'])) {
                                $amt = (float)$j[0]['current_price'];
                                $ch = isset($j[0]['price_change_percentage_24h']) ? (float)$j[0]['price_change_percentage_24h'] : null;
                                $candidates['coingecko'] = ['price' => (is_finite($amt) && $amt > 0) ? $amt : null, 'change' => is_finite($ch) ? $ch : null];
                            }
                        }
                    } catch (\Exception $e) {
                        // ignore
                    }
                }
            }

            // Selection logic: prefer Binance when available and close to CoinGecko; if
            // large discrepancy prefer CoinGecko. Threshold configurable via env (percent).
            $threshold = floatval(env('PRICE_DISCREPANCY_THRESHOLD', 10));

            $percentDiff = function($a, $b) {
                if ($b == 0 || $a === null || $b === null) return null;
                return abs(($a - $b) / $b) * 100.0;
            };

            if (isset($candidates['binance']) && isset($candidates['coingecko']) && $candidates['binance']['price'] && $candidates['coingecko']['price']) {
                $diff = $percentDiff($candidates['binance']['price'], $candidates['coingecko']['price']);
                if ($diff !== null && $diff > $threshold) {
                    $result['price'] = $candidates['coingecko']['price'];
                    $result['change'] = $candidates['coingecko']['change'];
                    $result['source'] = 'coingecko';
                } else {
                    $result['price'] = $candidates['binance']['price'];
                    $result['change'] = $candidates['binance']['change'];
                    $result['source'] = 'binance';
                }
            } elseif (isset($candidates['coingecko'])) {
                $result['price'] = $candidates['coingecko']['price'];
                $result['change'] = $candidates['coingecko']['change'];
                $result['source'] = 'coingecko';
            } elseif (isset($candidates['binance'])) {
                $result['price'] = $candidates['binance']['price'];
                $result['change'] = $candidates['binance']['change'];
                $result['source'] = 'binance';
            } elseif (isset($candidates['coinbase'])) {
                $result['price'] = $candidates['coinbase']['price'];
                $result['change'] = null;
                $result['source'] = 'coinbase';
            }

            $result['ts'] = (int)round(microtime(true) * 1000);
            return $result;
        });
    }

    /**
     * Backwards-compatible helper that returns price only.
     */
    public static function getCryptoPrice(string $symbol, string $prefer = 'auto'): ?float
    {
        $data = static::getCryptoData($symbol, $prefer);
        return isset($data['price']) ? $data['price'] : null;
    }
}
