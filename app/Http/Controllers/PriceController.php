<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PriceService;

class PriceController extends Controller
{
    /**
     * Return prices for a comma-separated list of symbols.
    * Query params:
    *  - symbols=BTC,ETH,... (required)
    *
     */
    public function prices(Request $request)
    {
        $symbols = $request->query('symbols', '');
        if (!$symbols) {
            return response()->json(['error' => 'symbols param required'], 400);
        }

    // prefer param is ignored; PriceService is now a single-source BCF-backed service
    $prefer = $request->query('prefer', 'auto');
        $list = array_filter(array_map('trim', explode(',', $symbols)));
        $list = array_slice($list, 0, 30); // guard: limit to 30 symbols per request

        $out = [];
        foreach ($list as $sym) {
            $s = strtoupper($sym);
            // call PriceService (single BCF-backed source) to get price + change
            $data = PriceService::getCryptoData($s);
            $out[$s] = [
                'price' => $data['price'] ?? null,
                'change' => array_key_exists('change', $data) ? $data['change'] : null,
                'rate' => array_key_exists('rate', $data) ? (is_numeric($data['rate']) ? (float)$data['rate'] : null) : null,
                'ts' => array_key_exists('ts', $data) ? $data['ts'] : ($data['price'] ? round(microtime(true) * 1000) : null)
            ];
        }

        return response()->json(['data' => $out]);
    }
}
