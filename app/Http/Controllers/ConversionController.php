<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserWallet;
use App\Models\Conversion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ConversionController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user();
        if (!$user) return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);

        $request->validate([
            'from_currency_id' => 'required|exists:currencies,id',
            'to_currency_id' => 'required|exists:currencies,id',
            'from_amount' => 'required|numeric|min:0.00000001',
            // require a positive to_amount — protect against client sending zero/invalid values
            'to_amount' => 'required|numeric|min:0.00000001'
        ]);

        $fromCurrencyId = $request->input('from_currency_id');
        $toCurrencyId = $request->input('to_currency_id');
        $fromAmount = $request->input('from_amount');
            $toAmount = $request->input('to_amount');

            // Defensive server-side validation: ensure toAmount is positive and numeric
            if (!is_numeric($toAmount) || floatval($toAmount) <= 0) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Invalid conversion target amount'], 422);
            }

        // ensure user_wallets table exists
        if (!\Illuminate\Support\Facades\Schema::hasTable('user_wallets')) {
            return response()->json(['success' => false, 'message' => 'user_wallets table missing. Run migrations.'], 500);
        }

        DB::beginTransaction();
        try {
            // lock rows for update
            $fromWalletQuery = UserWallet::where('user_id', $user->id);
            $toWalletQuery = UserWallet::where('user_id', $user->id);

            if (\Illuminate\Support\Facades\Schema::hasColumn('user_wallets', 'currency_id')) {
                $fromWalletQuery->where('currency_id', $fromCurrencyId);
                $toWalletQuery->where('currency_id', $toCurrencyId);
            } else {
                // older schema: use coin symbol instead
                $fromSym = DB::table('currencies')->where('id', $fromCurrencyId)->value('symbol');
                $toSym = DB::table('currencies')->where('id', $toCurrencyId)->value('symbol');
                $fromWalletQuery->whereRaw('LOWER(coin) = ?', [strtolower($fromSym ?? '')]);
                $toWalletQuery->whereRaw('LOWER(coin) = ?', [strtolower($toSym ?? '')]);
            }

            $fromWallet = $fromWalletQuery->lockForUpdate()->first();
            $toWallet = $toWalletQuery->lockForUpdate()->first();

            // If schema has currency_id but no wallet found, try to find an existing coin-only wallet and attach currency_id
            if (\Illuminate\Support\Facades\Schema::hasColumn('user_wallets', 'currency_id')) {
                if (!$fromWallet) {
                    $fromSym = DB::table('currencies')->where('id', $fromCurrencyId)->value('symbol');
                    if ($fromSym) {
                        $fallback = UserWallet::where('user_id', $user->id)->whereRaw('LOWER(coin) = ?', [strtolower($fromSym)])->lockForUpdate()->first();
                        if ($fallback) {
                            $fallback->currency_id = $fromCurrencyId;
                            $fallback->save();
                            $fromWallet = $fallback;
                        }
                    }
                }

                if (!$toWallet) {
                    $toSym = DB::table('currencies')->where('id', $toCurrencyId)->value('symbol');
                    if ($toSym) {
                        $fallback = UserWallet::where('user_id', $user->id)->whereRaw('LOWER(coin) = ?', [strtolower($toSym)])->lockForUpdate()->first();
                        if ($fallback) {
                            $fallback->currency_id = $toCurrencyId;
                            $fallback->save();
                            $toWallet = $fallback;
                        }
                    }
                }
            }

            if (!$fromWallet || ($fromWallet->balance < $fromAmount)) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Insufficient balance'], 422);
            }

            // create toWallet row if missing
            if (!$toWallet) {
                $createData = [
                    'user_id' => $user->id,
                    'balance' => 0
                ];

                // get symbol for coin column (ensure coin is populated because DB has non-null coin)
                $sym = DB::table('currencies')->where('id', $toCurrencyId)->value('symbol');
                if ($sym) {
                    $createData['coin'] = strtolower($sym);
                } else {
                    // fallback to generic lowercase of currency id to avoid NULL insert failure
                    $createData['coin'] = 'c' . intval($toCurrencyId);
                }

                if (\Illuminate\Support\Facades\Schema::hasColumn('user_wallets', 'currency_id')) {
                    $createData['currency_id'] = $toCurrencyId;
                }

                $toWallet = UserWallet::create($createData);
            }

            // update balances
            $fromWallet->balance = bcsub((string)$fromWallet->balance, (string)$fromAmount, 16);
            $toWallet->balance = bcadd((string)$toWallet->balance, (string)$toAmount, 16);
            $fromWallet->save();
            $toWallet->save();

            $rate = $toAmount > 0 ? ($fromAmount / $toAmount) : null;

            $conv = Conversion::create([
                'user_id' => $user->id,
                'from_currency_id' => $fromCurrencyId,
                'to_currency_id' => $toCurrencyId,
                'from_amount' => $fromAmount,
                'to_amount' => $toAmount,
                'rate' => $rate,
                'status' => 'completed'
            ]);

            DB::commit();
            return response()->json(['success' => true, 'conversion' => $conv], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Conversion error: '.$e->getMessage());
            return response()->json(['success' => false, 'message' => 'Conversion failed'], 500);
        }
    }
}
