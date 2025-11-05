<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class PriceService
{
    // Single source of truth for the API URL — change here to switch provider for whole project
    protected const API_URL = 'https://api.bitcryptoforest.com/api/kline/getAllProduct';

    /**
     * Get price data for any symbol (crypto, forex, metal etc).
     * Returns ['price' => float|null, 'change' => float|null, 'rate' => string|null]
     */
    public static function getCryptoData(string $symbol): array
    {
        $s = strtoupper(trim($symbol));
        $all = self::getAllProducts();
        return $all[$s] ?? ['price' => null, 'change' => null, 'rate' => null];
    }

    /**
     * Get just the price for a symbol. Returns float or null if not found.
     */
    public static function getCryptoPrice(string $symbol): ?float
    {
        $d = self::getCryptoData($symbol);
        return $d['price'] ?? null;
    }

    /**
     * Return a normalized map of all products from the BCF API.
     * Map: SYMBOL => [ 'price'=>float, 'change'=>float|null, 'rate'=>string|null ]
     */
    public static function getAllProducts(): array
    {
        return Cache::remember('bcf:all_products_v2', 5, function () {
            try {
                $resp = Http::timeout(6)->get(self::API_URL);
                if (!$resp->ok()) return [];
                $j = $resp->json();

                // Expecting structure: { code:1, list: [ { currency: 'BTC', price: '...', change: 12.3, rate: '0.12', ... }, ... ] }
                $list = [];
                if (is_array($j) && isset($j['list']) && is_array($j['list'])) {
                    $list = $j['list'];
                } elseif (is_array($j)) {
                    // fallback: if API returns array directly
                    $list = $j;
                }

                $map = [];
                foreach ($list as $item) {
                    if (!is_array($item)) continue;

                    // Prefer 'currency' or 'symbol' as the source token, then normalize to a canonical symbol.
                    // Examples we need to support: 'BTC', 'BTCUSDT', 'USDGBP', 'USD/GBP', 'GBPUSD', 'GBP/USD'
                    $symbol = null;
                    $raw = null;
                    if (!empty($item['currency'])) {
                        $raw = strtoupper((string)$item['currency']);
                    } elseif (!empty($item['symbol'])) {
                        $raw = strtoupper((string)$item['symbol']);
                    }
                    if (empty($raw)) continue;

                    // Normalize separators to a single form
                    $rawClean = str_replace([' ', '\\', '\\/', '_'], ['','','/','/'], $raw);

                    // If contains a slash, split and pick the side that is not USD/USDT when possible
                    $canonical = null;
                    if (strpos($rawClean, '/') !== false) {
                        $parts = preg_split('/\//', $rawClean);
                        if (count($parts) >= 2) {
                            $a = $parts[0]; $b = $parts[1];
                            if (in_array($a, ['USD','USDT']) && !in_array($b, ['USD','USDT'])) {
                                $canonical = $b;
                            } elseif (in_array($b, ['USD','USDT']) && !in_array($a, ['USD','USDT'])) {
                                $canonical = $a;
                            } else {
                                // fallback to last part
                                $canonical = end($parts);
                            }
                        }
                    } else {
                        // Try patterns like USDGBP, GBPUSD, BTCUSDT
                        if (preg_match('/^(USD|USDT)([A-Z]{2,})$/', $rawClean, $m)) {
                            $canonical = $m[2];
                        } elseif (preg_match('/^([A-Z]{2,})(USD|USDT)$/', $rawClean, $m)) {
                            $canonical = $m[1];
                        } else {
                            // If it doesn't look like a pair, treat raw as canonical (e.g., BTC, GBP)
                            $canonical = $rawClean;
                        }
                    }

                    $canonical = $canonical ? strtoupper(trim($canonical, '/_')) : null;
                    if (empty($canonical)) continue;

                    // We'll register multiple lookup keys so callers can request 'GBP', 'USD/GBP' or 'USDGBP'
                    $symbol = $canonical;
                    $lookupKeys = array_unique(array_filter([
                        $symbol,
                        $raw,
                        str_replace('/', '', $rawClean),
                        'USD' . $symbol,
                        'USD/' . $symbol,
                        $symbol . 'USD',
                    ]));

                    // Extract numeric price (try 'price', fallback to 'last', fallback to last element of price_list)
                    $price = null;
                    if (isset($item['price']) && is_numeric($item['price'])) {
                        $price = (float)$item['price'];
                    } elseif (isset($item['last']) && is_numeric($item['last'])) {
                        $price = (float)$item['last'];
                    } elseif (isset($item['price_list']) && is_array($item['price_list']) && count($item['price_list']) > 0) {
                        // take the last value from price_list if numeric
                        $last = end($item['price_list']);
                        if (is_numeric($last)) {
                            $price = (float)$last;
                        }
                    }

                    // If price is zero but price_list has a non-zero latest value, prefer that
                    if (($price === null || $price === 0.0) && isset($item['price_list']) && is_array($item['price_list']) && count($item['price_list'])>0) {
                        $last = end($item['price_list']);
                        if (is_numeric($last) && ((float)$last) !== 0.0) {
                            $price = (float)$last;
                        }
                    }

                    // Extract change
                    $change = null;
                    if (isset($item['change']) && (is_numeric($item['change']) || is_string($item['change']))) {
                        // preserve original string sometimes used for small decimals
                        $change = $item['change'];
                        if (is_numeric($change)) $change = (float)$change;
                    } elseif ($price !== null && isset($item['pre_close']) && is_numeric($item['pre_close'])) {
                        // compute change from pre_close if provided
                        $change = $price - (float)$item['pre_close'];
                    }

                    // Normalize rate to float when possible (percent). If missing, we'll compute later when needed.
                    $rate = null;
                    if (isset($item['rate']) && $item['rate'] !== '') {
                        if (is_numeric($item['rate'])) {
                            $rate = (float)$item['rate'];
                        } else {
                            $r = filter_var($item['rate'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                            if (is_numeric($r)) $rate = (float)$r;
                        }
                    }

                    // Determine timestamp (ms). Use provided ts/timestamp if present, else server now
                    $ts = null;
                    if (isset($item['ts']) && is_numeric($item['ts'])) {
                        $ts = (int)$item['ts'];
                    } elseif (isset($item['timestamp']) && is_numeric($item['timestamp'])) {
                        $ts = (int)$item['timestamp'];
                    } else {
                        $ts = (int)round(microtime(true) * 1000);
                    }

                    // If rate still null and change & price available and price-change != 0, compute percent
                    if ($rate === null && $price !== null && $change !== null && is_numeric($change)) {
                        $prev = $price - (float)$change;
                        if ($prev != 0) {
                            $rate = ((float)$change / $prev) * 100;
                        }
                    }

                    // If we have a price, store it using the canonical symbol and helpful aliases
                    if ($price !== null) {
                        $entry = [
                            'price' => $price,
                            'change' => $change,
                            'rate' => $rate,
                            'ts' => $ts,
                        ];
                        foreach ($lookupKeys as $k) {
                            $kk = strtoupper((string)$k);
                            // only set if not already present to prefer first-seen data
                            $map[$kk] = $entry;
                        }
                    }
                }

                return $map;
            } catch (\Throwable $e) {
                // don't throw — return empty map and log for debugging
                try { \Log::error('PriceService::getAllProducts error: ' . $e->getMessage()); } catch (\Throwable $_) {}
                return [];
            }
        });
    }
}
