@extends('layouts.app')

@section('content')
@include('components.wire-transfer-modal')
<div class="container">
    <div class="card bg-primary text-white border-0 mb-4" style="border-radius: 20px;">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <a href="{{ url('/') }}" class="text-white">
                    <i class="bi bi-arrow-left fs-4"></i>
                </a>
                <a href="{{ route('transaction.index') }}" class="text-white">
                    <i class="bi bi-clock-history fs-4"></i>
                </a>
            </div>
            <div class="text-center mb-2">
                <h3 class="mb-3">Send cryptocurrency now</h3>
                <p class="mb-0">Choose a wallet to send cryptocurrency</p>
            </div>
            <div class="text-center">
                <img src="{{ asset('images/wallet-hero.svg') }}" alt="Wallet illustration" class="img-fluid" style="max-height: 200px;">
            </div>
        </div>
    </div>

    <div class="card shadow-sm" style="border-radius: 15px;">
        <div class="card-body p-4">
            <h5 class="mb-3">Select a wallet</h5>
            <div class="wallet-list">
                @php
                    $symbols = ['BTC','ETH','USDT','USDC','PYUSD','DOGE','XRP'];
                    $currencies = \App\Models\Currency::whereIn('symbol', $symbols)->get()->keyBy(function($c){ return strtoupper($c->symbol); });
                    $userId = auth()->id();

                    function fmtAmount($n) {
                        $s = number_format($n ?? 0, 8, '.', '');
                        $s = rtrim(rtrim($s, '0'), '.');
                        return $s === '' ? '0' : $s;
                    }
                @endphp

                @foreach($symbols as $sym)
                    @php
                        $currency = $currencies->get($sym);
                        $type = strtolower($sym);
                        // prefer local SVG icons in public/images/icons when available
                        $localSvg = public_path('images/icons/' . $type . '.svg');
                        if (file_exists($localSvg)) {
                            $img = asset('images/icons/' . $type . '.svg');
                        } else {
                            $img = asset('images/icons/' . $type . '.svg');
                        }
                        $wallet = \App\Models\UserWallet::where('user_id', $userId)
                                    ->whereRaw('UPPER(coin) = ?', [$sym])->first();
                        $balance = $wallet ? $wallet->balance : 0;
                        $balanceDisplay = fmtAmount($balance);
                    @endphp

                    <a href="{{ route('wallet.detail', ['type' => $type]) }}" class="wallet-item">
                        <div class="d-flex justify-content-between align-items-center p-3 mb-3 rounded hover-bg">
                            <div class="d-flex align-items-center">
                                <div class="coin-icon me-3">
                                    <img src="{{ $img }}" alt="{{ $sym }}" width="40">
                                </div>
                                <div>
                                    <h6 class="mb-1">{{ $sym }} Wallet</h6>
                                    <small class="text-muted">{{ $sym }} Coin</small>
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="wallet-usd" data-symbol="{{ $sym }}" data-balance="{{ $balanceDisplay }}">US$ 0.00</div>
                                <small class="text-muted">{{ $balanceDisplay }} {{ $sym }}</small>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-4">
                <button type="button" class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#wireTransferModal">
                    Wire Transfers
                </button>
            </div>
        </div>
    </div>

@push('styles')
<style>
    .wallet-list {
        margin: -12px;
    }
    .wallet-item {
        text-decoration: none;
        color: inherit;
        display: block;
    }
    .hover-bg {
        transition: background-color 0.2s;
    }
    .hover-bg:hover {
        background-color: #f8f9fa;
    }
    .coin-icon img {
        border-radius: 50%;
    }
</style>
@endpush
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    function formatUSD(n) {
        if (isNaN(n) || n === null) return '0.00';
        return Number(n).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});
    }

    async function fetchPriceFromCoingecko(symbol) {
        const map = { BTC: 'bitcoin', ETH: 'ethereum', USDT: 'tether', USDC: 'usd-coin', PYUSD: 'paypal-usd', DOGE: 'dogecoin', XRP: 'ripple' };
        const id = map[symbol.toUpperCase()];
        if (!id) return null;
        try {
            const resp = await fetch(`https://api.coingecko.com/api/v3/simple/price?ids=${id}&vs_currencies=usd`);
            if (!resp.ok) return null;
            const j = await resp.json();
            if (j && j[id] && j[id].usd) return parseFloat(j[id].usd);
        } catch (e) { console.warn('CoinGecko fetch failed', e); }
        return null;
    }

    async function getPrice(symbol) {
        try {
            const stored = JSON.parse(localStorage.getItem('latestPrices') || '{}');
            const entry = stored[symbol.toLowerCase()];
            if (entry && entry.price && (Date.now() - (entry.ts || 0) < 60 * 1000)) {
                return parseFloat(entry.price);
            }
        } catch (e) {}
        // fallback to CoinGecko
        const p = await fetchPriceFromCoingecko(symbol);
        return p;
    }

    const usdEls = document.querySelectorAll('.wallet-usd');
    usdEls.forEach(async function(el) {
        const sym = el.getAttribute('data-symbol');
        const balRaw = el.getAttribute('data-balance') || '0';
        const bal = parseFloat(balRaw) || 0;
        if (bal <= 0) {
            el.textContent = 'US$ 0.00';
            return;
        }
        const price = await getPrice(sym);
        if (!price) {
            el.textContent = 'US$ 0.00';
            return;
        }
        const usd = price * bal;
        el.textContent = 'US$ ' + formatUSD(usd);
    });
});
</script>
@endpush
</div>
@endsection
