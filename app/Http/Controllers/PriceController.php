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
     *  - prefer=binance|coinbase|auto (optional)
     */
    public function prices(Request $request)
    {
        $symbols = $request->query('symbols', '');
        if (!$symbols) {
            return response()->json(['error' => 'symbols param required'], 400);
        }

        $prefer = $request->query('prefer', 'auto');
        $list = array_filter(array_map('trim', explode(',', $symbols)));
        $list = array_slice($list, 0, 30); // guard: limit to 30 symbols per request

        $out = [];
        foreach ($list as $sym) {
            $s = strtoupper($sym);
            // call PriceService with prefer parameter to get price + change
            $data = PriceService::getCryptoData($s, $prefer);
            $out[$s] = [
                'price' => $data['price'] ?? null,
                'change' => array_key_exists('change', $data) ? $data['change'] : null,
                'ts' => $data['ts'] ?? null,
                'source' => $data['source'] ?? null,
            ];
        }

        return response()->json(['data' => $out]);
    }
}
