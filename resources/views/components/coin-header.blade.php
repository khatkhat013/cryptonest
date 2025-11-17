@props(['symbol', 'showPrice' => true, 'type' => 'crypto'])

<div class="coin-nav">
    <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between py-2">
            <!-- Back Button -->
            <a href="{{ url('/') }}" class="btn-action" aria-label="Go back">
                <i class="bi bi-arrow-left"></i>
            </a>

            <!-- Coin Price -->
            <div class="coin-price-display">
                <div class="d-flex align-items-center gap-2">
                    @php
                        $symLower = strtolower($symbol ?? '');
                        $typeLower = strtolower($type ?? 'crypto');
                        $localIconSvg = public_path('images/icons/' . $symLower . '.svg');
                        $localIconPng = public_path('images/icons/' . $symLower . '.png');
                        $legacyCoinPng = public_path('images/coins/' . $symLower . '.png');

                        // Known coinmarketcap ids for a few crypto symbols (fallback)
                        $cmcMap = [
                            'btc' => 1,
                            'eth' => 1027,
                            'trx' => 1958,
                            'xrp' => 52,
                            'doge' => 74,
                        ];
                    @endphp

                    {{-- Prefer project-provided icons first (icons/...), then legacy coins/, then sensible fallbacks per type. --}}
                    @if($symLower && file_exists($localIconSvg))
                        <img src="{{ asset('images/icons/' . $symLower . '.svg') }}" alt="{{ strtoupper($symbol) }}" class="coin-icon">
                    @elseif($symLower && file_exists($localIconPng))
                        <img src="{{ asset('images/icons/' . $symLower . '.png') }}" alt="{{ strtoupper($symbol) }}" class="coin-icon">
                    @elseif($symLower && file_exists($legacyCoinPng) && $typeLower === 'crypto')
                        <img src="{{ asset('images/coins/' . $symLower . '.png') }}" alt="{{ strtoupper($symbol) }}" class="coin-icon">
                    @elseif($typeLower === 'forex')
                        {{-- For forex pages show a currency badge with the currency code if no image exists --}}
                        <div class="coin-icon text-center" style="display:flex;align-items:center;justify-content:center;border-radius:50%;background:var(--primary);color:#fff;font-weight:700;">
                            {{ strtoupper($symbol) }}
                        </div>
                    @elseif($typeLower === 'metal')
                        @php
                            // Map metals to simple bootstrap icons
                            $metalClass = ($symLower === 'xau') ? 'bi-coin' : (($symLower === 'xag') ? 'bi-disc' : (($symLower === 'xpt') ? 'bi-hexagon' : 'bi-octagon'));
                        @endphp
                        <div class="coin-icon text-center" style="display:flex;align-items:center;justify-content:center;color:var(--primary);font-size:1.2rem;">
                            <i class="bi {{ $metalClass }}" aria-hidden="true"></i>
                        </div>
                    @else
                        @php $cmcId = $cmcMap[$symLower] ?? 1; @endphp
                        <img src="https://s2.coinmarketcap.com/static/img/coins/64x64/{{ $cmcId }}.png" alt="{{ strtoupper($symbol) }}" class="coin-icon">
                    @endif

                    <div class="text-center">
                        @php
                            // Show USD for forex and metal types, otherwise default to USDT for crypto
                            $quote = in_array($typeLower, ['forex', 'metal']) ? 'USD' : 'USDT';
                        @endphp
                        <div class="coin-symbol">{{ strtoupper($symbol) }}/{{ $quote }}</div>
                        @if($showPrice)
                            <div class="placeholder-price">00.00</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- History Button -->
            <button class="btn-action" onclick="showHistory()" aria-label="Show history">
                <i class="bi bi-clock-history"></i>
            </button>
        </div>
    </div>
</div>

<style>
.coin-nav {
    position: fixed;
    top: 64px;
    left: 0;
    right: 0;
    background: var(--bg-surface);
    border-bottom: 1px solid var(--border);
    z-index: 1000;
}

.coin-price-display {
    background: var(--bg-main);
    padding: 8px 16px;
    border-radius: 50px;
    min-width: 180px;
}

.coin-icon {
    width: 32px;
    height: 32px;
}

.coin-symbol {
    font-size: 0.9rem;
    color: var(--text-muted);
}

.placeholder-price {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--success);
}

@media (max-width: 576px) {
    .coin-price-display {
        min-width: 140px;
        padding: 6px 12px;
    }
    
    .coin-icon {
        width: 24px;
        height: 24px;
    }
    
    .coin-symbol {
        font-size: 0.8rem;
    }
    
    .placeholder-price {
        font-size: 1rem;
    }
}
</style>

<script>
function showHistory() {
    // This will be implemented later
    alert('Trade history will be shown here');
}
</script>