<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Conversion;
use App\Services\PriceService;

class BackfillConversions extends Command
{
    protected $signature = 'conversions:backfill {--dry : Run in dry mode (no writes)}';
    protected $description = 'Backfill conversions.from_currency_id and to_currency_id from existing from_coin/to_coin symbols when null.';

    public function handle(): int
    {
        $this->info('Starting conversions backfill...');

        // Determine which columns exist to avoid selecting dropped columns
        $schema = DB::getSchemaBuilder();
        $selectCols = ['id', 'user_id', 'from_amount', 'to_amount'];
        if ($schema->hasColumn('conversions', 'from_coin')) $selectCols[] = 'from_coin';
        if ($schema->hasColumn('conversions', 'to_coin')) $selectCols[] = 'to_coin';

        $missing = DB::table('conversions')
            ->whereNull('from_currency_id')
            ->orWhereNull('to_currency_id')
            ->select($selectCols)
            ->get();

        $total = $missing->count();
        if ($total === 0) {
            $this->info('No conversion rows with missing currency ids found.');
            return 0;
        }

        $this->info("Found {$total} conversions with missing currency ids. Processing...");

        $dry = $this->option('dry');
        $processed = 0;

        // Preload available currencies and their symbols
        $currencies = DB::table('currencies')->select('id', 'symbol')->get()->mapWithKeys(function ($r) {
            return [strtolower($r->symbol) => $r->id];
        })->toArray();

        foreach ($missing as $row) {
            $fromId = null;
            $toId = null;

            // Conservative approach #1: if conversions table still had coin columns, use them
            $hasFromCoin = DB::getSchemaBuilder()->hasColumn('conversions', 'from_coin');
            $hasToCoin = DB::getSchemaBuilder()->hasColumn('conversions', 'to_coin');

            if ($hasFromCoin || $hasToCoin) {
                $fromCoin = $hasFromCoin ? strtolower(trim($row->from_coin ?? '')) : '';
                $toCoin = $hasToCoin ? strtolower(trim($row->to_coin ?? '')) : '';

                if ($fromCoin !== '' && isset($currencies[$fromCoin])) $fromId = $currencies[$fromCoin];
                if ($toCoin !== '' && isset($currencies[$toCoin])) $toId = $currencies[$toCoin];
            }

            // Conservative approach #2: Prefer deterministic wallet mapping; then try wallet-limited price inference
            $candidateWallets = DB::table('user_wallets')->where('user_id', $row->user_id)->whereNotNull('currency_id')->get();

            // If user has exactly one wallet with currency_id, map both sides to it as fallback
            if ($candidateWallets->count() === 1) {
                $singleId = $candidateWallets->first()->currency_id;
                if ($fromId === null) $fromId = $singleId;
                if ($toId === null) $toId = $singleId;
            } else {
                // Try to match by wallet coin string first
                foreach ($candidateWallets as $w) {
                    if (!empty($w->coin)) {
                        $sym = strtolower($w->coin);
                        if (isset($currencies[$sym])) {
                            if ($fromId === null) $fromId = $currencies[$sym];
                            if ($toId === null) $toId = $currencies[$sym];
                        }
                    }
                }
            }

            // If still undecided, try price-based inference limited to user's wallet symbols (safe and limited network calls)
            if ($fromId === null || $toId === null) {
                $walletSymbols = [];
                foreach ($candidateWallets as $w) {
                    if (!empty($w->coin)) $walletSymbols[strtolower($w->coin)] = $w->currency_id;
                }

                if (!empty($walletSymbols)) {
                    $convRow = DB::table('conversions')->where('id', $row->id)->first();
                    $fromAmt = (float)($convRow->from_amount ?? 0);
                    $toAmt = (float)($convRow->to_amount ?? 0);

                    if ($fromAmt > 0 && $toAmt > 0) {
                        $candidates = [];
                        foreach ($walletSymbols as $sym => $cid) {
                            // Skip stablecoins as 'from' candidate in most cases
                            // We'll still allow them, but prefer non-stable
                            $price = null;
                            try {
                                $price = PriceService::getCryptoPrice($sym);
                            } catch (\Throwable $e) {
                                $price = null;
                            }
                            if ($price === null) continue;
                            $expected = $price * $fromAmt;
                            $diff = abs($expected - $toAmt);
                            $rel = $toAmt > 0 ? ($diff / $toAmt) : 1;
                            if ($rel <= 0.02) {
                                $candidates[$cid] = ['sym' => $sym, 'rel' => $rel];
                            }
                        }

                        // If exactly one candidate found, assign it as fromId and try to set toId to a stablecoin if available
                        if (count($candidates) === 1) {
                            $matchedCid = array_keys($candidates)[0];
                            if ($fromId === null) $fromId = $matchedCid;
                            // set toId to a known stable wallet if present
                            foreach (['usdt','usdc','usd','tusd'] as $s) {
                                if (isset($walletSymbols[$s])) { $toId = $walletSymbols[$s]; break; }
                            }
                        }
                    }
                }
            }

            // If still no mapping, skip — we don't want to guess
            if ($fromId === null && $toId === null) continue;

            $processed++;

            if ($dry) {
                $this->line("[DRY] Would update conversion id={$row->id} from_currency_id={$fromId} to_currency_id={$toId}");
                continue;
            }

            DB::beginTransaction();
            try {
                DB::table('conversions')->where('id', $row->id)->update([
                    'from_currency_id' => $fromId,
                    'to_currency_id' => $toId,
                ]);
                DB::commit();
                $this->line("Updated conversion id={$row->id} -> from_currency_id={$fromId} to_currency_id={$toId}");
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Failed to backfill conversion id='.$row->id.': '.$e->getMessage());
                $this->error('Failed to update conversion id='.$row->id);
            }
        }

        $this->info("Processed: {$processed} rows (out of {$total})");
        $this->info('Done.');

        return 0;
    }
}
