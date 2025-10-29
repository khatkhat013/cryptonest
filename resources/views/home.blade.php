@extends('layouts.app')

@section('header-animation')
    <!-- Lottie Animation: Only on Home page -->
    <div style="margin-top:64px;">
        <dotlottie-wc src="https://lottie.host/b8c3e07f-6857-4919-95d6-0dee9e6127ce/MJoyjsN8w3.lottie" style="width: 100%;height: auto" autoplay loop></dotlottie-wc>
    </div>
    
    <!-- Markets Title -->
    <div class="container mt-4">
        <h2 class="section-title tracking-in-expand-fwd" onclick="document.getElementById('marketTabs').scrollIntoView({behavior: 'smooth'})">
            <i class="bi bi-graph-up"></i>
            Markets
        </h2>
        
        <!-- Market Tabs -->
        <ul class="nav nav-tabs market-tabs mt-4" id="marketTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="crypto-tab" data-bs-toggle="tab" data-bs-target="#crypto" type="button" role="tab" aria-selected="true">Crypto</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="forex-tab" data-bs-toggle="tab" data-bs-target="#forex" type="button" role="tab" aria-selected="false">Forex</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="gold-tab" data-bs-toggle="tab" data-bs-target="#gold" type="button" role="tab" aria-selected="false">Gold</button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content mt-3" id="marketTabContent">
            <!-- Crypto Tab -->
            <div class="tab-pane fade show active" id="crypto" role="tabpanel">
                <div class="row g-3">
                    @foreach(['BTC' => 'Bitcoin', 'ETH' => 'Ethereum', 'BNB' => 'Binance Coin', 
                             'TRX' => 'TRON', 'XRP' => 'Ripple', 'DOGE' => 'Dogecoin'] as $symbol => $name)
                        <div class="col-md-4">
                            <a href="{{ url('/coin/' . strtolower($symbol)) }}" class="text-decoration-none market-link" data-symbol="{{ strtolower($symbol) }}">
                                <div class="card market-card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            @php
                                                $symLower = strtolower($symbol);
                                                $localSvg = public_path('images/icons/' . $symLower . '.svg');
                                                $localIconSvg = public_path('images/icons/' . $symLower . '.svg');
                                            @endphp
                                            @if(file_exists($localSvg) || file_exists($localIconSvg))
                                                <img src="{{ asset('images/icons/' . $symLower . '.svg') }}" alt="{{ $name }}" class="me-2" style="width: 32px; height: 32px;">
                                            @else
                                                <img src="https://s2.coinmarketcap.com/static/img/coins/64x64/{{ array_search($symbol, ['BTC', 'ETH', 'BNB', 'TRX', 'XRP', 'DOGE']) + 1 }}.png" 
                                                     alt="{{ $name }}" class="me-2" style="width: 32px; height: 32px;">
                                            @endif
                                            <div>
                                                <h5 class="mb-0">{{ $symbol }}</h5>
                                                <small class="text-muted">{{ $name }}</small>
                                            </div>
                                            <div class="ms-auto">
                                                <div class="h5 mb-0 text-success">$<span class="price">0.00</span></div>
                                                <small class="text-success"><span class="change">0.00</span>%</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Forex Tab -->
            <div class="tab-pane fade" id="forex" role="tabpanel">
                <div class="row g-3">
                    @foreach([
                        'GBP' => 'British Pound',
                        'EUR' => 'Euro',
                        'CHF' => 'Swiss Franc',
                        'CAD' => 'Canadian Dollar',
                        'AUD' => 'Australian Dollar',
                        'JPY' => 'Japanese Yen'
                    ] as $symbol => $name)
                        <div class="col-md-4">
                            <a href="{{ url('/forex/' . strtolower($symbol)) }}" class="text-decoration-none">
                                <div class="card market-card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            @php
                                                $symLower = strtolower($symbol);
                                                $localPng = public_path('images/icons/' . $symLower . '.png');
                                            @endphp
                                            @if(file_exists($localPng))
                                                <img src="{{ asset('images/icons/' . $symLower . '.png') }}" alt="{{ $symbol }}" class="me-2" style="width:32px;height:32px;object-fit:contain;">
                                            @else
                                                <div class="currency-icon me-2">{{ $symbol }}</div>
                                            @endif
                                            <div>
                                                <h5 class="mb-0">USD/{{ $symbol }}</h5>
                                                <small class="text-muted">{{ $name }}</small>
                                            </div>
                                            <div class="ms-auto">
                                                <div class="h5 mb-0 text-success">$<span class="forex-price">0.00</span></div>
                                                <small class="text-success"><span class="forex-change">0.00</span>%</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Gold Tab -->
            <div class="tab-pane fade" id="gold" role="tabpanel">
                <div class="row g-3">
                    @foreach([
                        'XAU' => 'Gold',
                        'XAG' => 'Silver',
                        'XPT' => 'Platinum',
                        'XPD' => 'Palladium'
                    ] as $symbol => $name)
                        <div class="col-md-4">
                            <a href="{{ url('/metal/' . strtolower($symbol)) }}" class="text-decoration-none">
                                <div class="card market-card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="metal-icon me-2">
                                                <i class="bi {{ $symbol === 'XAU' ? 'bi-coin' : 
                                                              ($symbol === 'XAG' ? 'bi-disc' : 
                                                              ($symbol === 'XPT' ? 'bi-hexagon' : 'bi-octagon')) }}"></i>
                                            </div>
                                            <div>
                                                <h5 class="mb-0">{{ $symbol }}/USD</h5>
                                                <small class="text-muted">{{ $name }}</small>
                                            </div>
                                            <div class="ms-auto">
                                                <div class="h5 mb-0 text-success">$<span class="metal-price">0.00</span></div>
                                                <small class="text-success"><span class="metal-change">0.00</span>%</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="container mt-5">
        <!-- AI Arbitrage -->
        <div class="mb-5">
            <h2 class="section-title tracking-in-expand-fwd mb-4" onclick="window.location.href='{{ url('/arbitrage') }}'">
                <i class="bi bi-cpu"></i>
                AI Arbitrage
            </h2>
            <div class="feature-grid">
                <!-- AI Arbitrage Card -->
                <a href="{{ url('/arbitrage') }}" class="text-decoration-none">
                    <div class="card market-card feature-card position-relative overflow-hidden">
                        <div class="position-absolute w-100 h-100 animation-wrapper">
                            <dotlottie-wc src="https://lottie.host/4c89fb6d-25b1-4505-8149-a669c9c57e3d/EaLJaJluzA.lottie" 
                                        style="width: 100%; height: 100%; opacity: 0.5;" 
                                        autoplay loop>
                            </dotlottie-wc>
                        </div>
                        <div class="card-body position-relative z-1 p-4">
                        </div>
                    </div>
                </a>
                <!-- Mining Card -->
                <a href="{{ url('/mining') }}" class="text-decoration-none">
                    <div class="card market-card feature-card position-relative overflow-hidden">
                        <div class="card-body position-relative z-1 p-4 d-flex flex-column justify-content-center align-items-center">
                            <i class="bi bi-minecart-loaded text-success mb-3" style="font-size: 2.5rem;"></i>
                            <h3 class="text-success">Mining Pool</h3>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Social Features -->
        <div>
            <h2 class="section-title tracking-in-expand-fwd mb-4" onclick="shareInvite()">
                <i class="bi bi-people"></i>
                Community
            </h2>
            <div class="feature-grid">
                <!-- Invite Friends Card -->
                <div class="card market-card feature-card position-relative overflow-hidden" onclick="shareInvite()">
                    <div class="position-absolute w-100 h-100 animation-wrapper">
                        <dotlottie-wc src="https://lottie.host/bb1ba882-5cec-4676-add1-d008f39ae2ee/I9uAcddYg9.lottie" 
                                    style="width: 100%; height: 100%; opacity: 0.5;" 
                                    autoplay loop>
                        </dotlottie-wc>
                    </div>
                    <div class="card-body position-relative z-1 p-4">
                    </div>
                </div>
                <!-- News Card -->
                <a href="{{ url('/news') }}" class="text-decoration-none">
                    <div class="card market-card feature-card position-relative overflow-hidden">
                        <div class="card-body position-relative z-1 p-4 d-flex flex-column justify-content-center align-items-center">
                            <i class="bi bi-newspaper text-success mb-3" style="font-size: 2.5rem;"></i>
                            <h3 class="text-success">Crypto News</h3>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://unpkg.com/@lottiefiles/dotlottie-wc@0.8.1/dist/dotlottie-wc.js" type="module"></script>
    @endpush

    @push('styles')
    <style>
        /* Label Animation */
        @-webkit-keyframes tracking-in-expand-fwd {
            0% {
                letter-spacing: -0.5em;
                -webkit-transform: translateZ(-700px);
                transform: translateZ(-700px);
                opacity: 0;
            }
            40% {
                opacity: 0.6;
            }
            100% {
                -webkit-transform: translateZ(0);
                transform: translateZ(0);
                opacity: 1;
            }
        }
        
        @keyframes tracking-in-expand-fwd {
            0% {
                letter-spacing: -0.5em;
                -webkit-transform: translateZ(-700px);
                transform: translateZ(-700px);
                opacity: 0;
            }
            40% {
                opacity: 0.6;
            }
            100% {
                -webkit-transform: translateZ(0);
                transform: translateZ(0);
                opacity: 1;
            }
        }

        .section-title {
            position: relative;
            padding: 8px 16px;
            border-radius: 30px;
            background: rgba(34, 197, 94, 0.1);
            color: var(--success);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .section-title:hover {
            background: rgba(34, 197, 94, 0.2);
        }

        .section-title i {
            font-size: 1.2em;
        }

        .tracking-in-expand-fwd {
            -webkit-animation: tracking-in-expand-fwd 0.8s cubic-bezier(0.215, 0.610, 0.355, 1.000) both;
            animation: tracking-in-expand-fwd 0.8s cubic-bezier(0.215, 0.610, 0.355, 1.000) both;
        }

        /* Pill-style tabs */
        .market-tabs {
            background: rgba(34, 197, 94, 0.1);
            border-radius: 50px;
            padding: 5px;
        }

        .market-tabs .nav-link {
            border-radius: 50px;
            padding: 8px 24px;
            color: var(--success);
            border: none;
            transition: all 0.3s ease;
        }

        .market-tabs .nav-link.active {
            background: var(--success);
            color: white;
        }

        @keyframes tabActivate {
            0% {
                background-color: transparent;
            }
            50% {
                background-color: var(--primary-bg);
            }
            100% {
                background-color: var(--primary-bg);
            }
        }
        
        .market-card {
            transition: transform 0.2s ease-in-out;
            border: 1px solid var(--border);
            background-color: var(--bg-surface);
        }
        
        .market-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        .market-tabs .nav-link {
            color: var(--text-muted);
            border: none;
            border-bottom: 2px solid transparent;
            padding: 0.5rem 1.5rem;
        }

        .market-tabs .nav-link.active {
            color: var(--primary);
            border-bottom-color: var(--primary);
            background-color: var(--primary-bg);
            font-weight: 500;
        }

        .market-tabs .nav-link:hover:not(.active) {
            border-bottom-color: var(--text-muted);
            background-color: rgba(0,0,0,0.03);
        }

        body.dark .market-tabs .nav-link:hover:not(.active) {
            background-color: rgba(255,255,255,0.03);
        }

        .market-tabs .nav-link.active-animated {
            animation: tabActivate 0.3s ease-in-out;
        }

        .currency-icon {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--primary);
            color: white;
            border-radius: 50%;
            font-size: 0.8rem;
            font-weight: bold;
        }

        .metal-icon {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 1.5rem;
        }

        .feature-card {
            height: 300px;
            transition: all 0.3s ease;
            border-radius: 20px;
            cursor: pointer;
            border: none;
            background: var(--bg-surface);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .feature-card:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }

        .animation-wrapper {
            top: 0;
            left: 0;
            pointer-events: none;
            border-radius: 20px;
            overflow: hidden;
        }

        .z-1 {
            z-index: 1;
        }

        body.dark .feature-card {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
        }

        body.dark .feature-card:hover {
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        .btn-primary {
            background: var(--primary);
            border-color: var(--primary);
            width: 40px;
            height: 40px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0,82,204,0.2);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 4px 8px rgba(0,82,204,0.3);
        }

        .btn-primary i {
            font-size: 1.2rem;
        }

        body.dark .animation-wrapper {
            background: linear-gradient(45deg, rgba(0,102,255,0.05), rgba(0,102,255,0.15));
        }

        .text-primary-dark {
            color: var(--primary-dark);
        }

        body.dark .text-primary-dark {
            color: var(--primary-light);
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        // Auto tab switching
        (function() {
            const tabs = ['crypto-tab', 'forex-tab', 'gold-tab'];
            let currentTabIndex = 0;
            
            function switchTab() {
                currentTabIndex = (currentTabIndex + 1) % tabs.length;
                const tab = document.getElementById(tabs[currentTabIndex]);
                tab.click();
                
                // Add animation class
                tab.classList.add('active-animated');
                setTimeout(() => {
                    tab.classList.remove('active-animated');
                }, 300);
            }
            
            // Switch tabs every 20 seconds
            setInterval(switchTab, 20000);
            
            // Simulate price updates
            function updatePrices() {
                const markets = {
                    crypto: {
                        'BTC': {base: 40000, variance: 2000},
                        'ETH': {base: 2200, variance: 100},
                        'BNB': {base: 220, variance: 10},
                        'TRX': {base: 0.08, variance: 0.004},
                        'XRP': {base: 0.5, variance: 0.02},
                        'DOGE': {base: 0.07, variance: 0.003}
                    },
                    forex: {
                        'GBP': {base: 1.27, variance: 0.02},
                        'EUR': {base: 1.08, variance: 0.015},
                        'CHF': {base: 0.92, variance: 0.01},
                        'CAD': {base: 0.75, variance: 0.008},
                        'AUD': {base: 0.67, variance: 0.007},
                        'JPY': {base: 0.0068, variance: 0.0001}
                    },
                    metals: {
                        'XAU': {base: 1950, variance: 25},
                        'XAG': {base: 23.5, variance: 0.5},
                        'XPT': {base: 920, variance: 15},
                        'XPD': {base: 1250, variance: 20}
                    }
                };
                
                    // Update crypto prices using Coinbase primary, Binance optional, fallback to seeded/random
                (async () => {
                    const useBinance = window.APP_CONFIG?.allowBinanceClient === true;
                    const symbolToId = {
                        'BTC': 'BTC',
                        'ETH': 'ETH',
                        'BNB': 'BNB',
                        'TRX': 'TRX',
                        'XRP': 'XRP',
                        'DOGE': 'DOGE'
                    };

                    // Try Coinbase public spot prices per symbol (concurrent). Prefer these when available.
                    const coinbasePrices = {};
                    await Promise.all(Object.keys(symbolToId).map(async (sym) => {
                        try {
                            const resp = await fetch(`https://api.coinbase.com/v2/prices/${sym}-USD/spot`);
                            if (!resp.ok) return;
                            const j = await resp.json();
                            const amt = j && j.data && parseFloat(j.data.amount);
                            if (!isNaN(amt)) coinbasePrices[sym] = amt;
                        } catch (err) {
                            // ignore individual coin errors
                        }
                    }));

                    // Optionally try Binance public API if allowed
                    const binancePrices = {};
                    if (useBinance) {
                        await Promise.all(Object.keys(symbolToId).map(async (sym) => {
                            try {
                                // Binance uses lowercase symbols and USDT pairs
                                const pair = sym.toUpperCase() + 'USDT';
                                const resp = await fetch(`https://api.binance.com/api/v3/ticker/price?symbol=${pair}`);
                                if (!resp.ok) return;
                                const j = await resp.json();
                                const amt = j && parseFloat(j.price);
                                if (!isNaN(amt)) binancePrices[sym] = amt;
                            } catch (err) {}
                        }));
                    }

                    Object.entries(markets.crypto).forEach(([symbol, {base, variance}]) => {
                        // select all .card elements and filter by their h5 text
                        const cards = Array.from(document.querySelectorAll('.card')).filter(card => {
                            const h5 = card.querySelector('h5');
                            return h5 && h5.textContent.trim().toLowerCase() === String(symbol).toLowerCase();
                        });

                        const id = symbolToId[symbol];
                        let fetchedPrice = null;
                        let fetchedChange = null;
                        // Prefer Coinbase, then Binance if enabled, then fallback to seeded/random
                        if (coinbasePrices[symbol] !== undefined) {
                            fetchedPrice = coinbasePrices[symbol];
                        } else if (binancePrices[symbol] !== undefined) {
                            fetchedPrice = binancePrices[symbol];
                        }

                        cards.forEach(card => {
                            const priceEl = card.querySelector('.price');
                            const changeEl = card.querySelector('.change');

                            // Prefer Coinbase price if we have it
                            if (coinbasePrices[symbol] !== undefined) {
                                const price = coinbasePrices[symbol];
                                const change = fetchedChange !== null ? fetchedChange : '0.00';
                                if (priceEl) priceEl.textContent = price.toFixed(price < 1 ? 4 : 2);
                                if (changeEl) {
                                    const num = parseFloat(change);
                                    const signed = (isNaN(num) ? change : (num >= 0 ? `+${num.toFixed(change.includes('.') ? change.split('.')[1].length : 2)}` : `${num.toFixed(change.includes('.') ? change.split('.')[1].length : 2)}`));
                                    changeEl.textContent = signed;
                                    changeEl.parentElement.className = num >= 0 ? 'text-success' : 'text-danger';
                                    if (priceEl && priceEl.parentElement) {
                                        priceEl.parentElement.className = num >= 0 ? 'h5 mb-0 text-success' : 'h5 mb-0 text-danger';
                                    }
                                }
                                // Persist latest price to localStorage for use by trade pages
                                try {
                                    const stored = JSON.parse(localStorage.getItem('latestPrices') || '{}');
                                    stored[symbol.toLowerCase()] = { price: price, change: parseFloat(change) || 0, ts: Date.now() };
                                    localStorage.setItem('latestPrices', JSON.stringify(stored));
                                } catch (e) {
                                    // ignore storage errors
                                }
                                return;
                            }

                            // Prefer CoinGecko fetched price next
                            if (fetchedPrice !== null) {
                                const price = fetchedPrice;
                                const change = fetchedChange !== null ? fetchedChange : '0.00';
                                if (priceEl) priceEl.textContent = price.toFixed(price < 1 ? 4 : 2);
                                if (changeEl) {
                                    const num = parseFloat(change);
                                    const signed = (isNaN(num) ? change : (num >= 0 ? `+${num.toFixed(change.includes('.') ? change.split('.')[1].length : 2)}` : `${num.toFixed(change.includes('.') ? change.split('.')[1].length : 2)}`));
                                    changeEl.textContent = signed;
                                    changeEl.parentElement.className = num >= 0 ? 'text-success' : 'text-danger';
                                    if (priceEl && priceEl.parentElement) {
                                        priceEl.parentElement.className = num >= 0 ? 'h5 mb-0 text-success' : 'h5 mb-0 text-danger';
                                    }
                                }
                                // Persist latest price to localStorage for use by trade pages
                                try {
                                    const stored = JSON.parse(localStorage.getItem('latestPrices') || '{}');
                                    stored[symbol.toLowerCase()] = { price: price, change: parseFloat(change) || 0, ts: Date.now() };
                                    localStorage.setItem('latestPrices', JSON.stringify(stored));
                                } catch (e) {
                                    // ignore storage errors
                                }
                                return;
                            }

                            // No fetched price from any source: only apply fallback if displayed price is empty or zero
                            const existing = priceEl ? parseFloat(priceEl.textContent.replace(/[^0-9.-]+/g, '')) : 0;
                            if (!priceEl || !existing || existing === 0) {
                                // fallback to seeded/random behaviour
                                const price = base + (Math.random() - 0.5) * variance;
                                const change = (Math.random() * 5 - 1).toFixed(2);
                                if (priceEl) priceEl.textContent = price.toFixed(price < 1 ? 4 : 2);
                                // Persist latest price to localStorage for use by trade pages
                                try {
                                    const stored = JSON.parse(localStorage.getItem('latestPrices') || '{}');
                                    stored[symbol.toLowerCase()] = { price: price, change: parseFloat(change) || 0, ts: Date.now() };
                                    localStorage.setItem('latestPrices', JSON.stringify(stored));
                                } catch (e) {
                                    // ignore storage errors
                                }
                                if (changeEl) {
                                    const num = parseFloat(change);
                                    const signed = (isNaN(num) ? change : (num >= 0 ? `+${num.toFixed(change.includes('.') ? change.split('.')[1].length : 2)}` : `${num.toFixed(change.includes('.') ? change.split('.')[1].length : 2)}`));
                                    changeEl.textContent = signed;
                                    changeEl.parentElement.className = num >= 0 ? 'text-success' : 'text-danger';
                                    if (priceEl && priceEl.parentElement) {
                                        priceEl.parentElement.className = num >= 0 ? 'h5 mb-0 text-success' : 'h5 mb-0 text-danger';
                                    }
                                }
                            }
                        });
                    });
                })();

                // Update forex prices
                Object.entries(markets.forex).forEach(([symbol, {base, variance}]) => {
                    // Match cards whose h5 equals 'USD/<symbol>' (case-insensitive)
                    const lookup = `USD/${symbol}`;
                    const cards = Array.from(document.querySelectorAll('.card')).filter(card => {
                        const h5 = card.querySelector('h5');
                        return h5 && h5.textContent.trim().toLowerCase() === lookup.toLowerCase();
                    });
                    cards.forEach(card => {
                        const price = base + (Math.random() - 0.5) * variance;
                        const change = (Math.random() * 2 - 0.5).toFixed(3);
                        const priceEl = card.querySelector('.forex-price');
                        const changeEl = card.querySelector('.forex-change');
                        if (priceEl) priceEl.textContent = price.toFixed(4);
                        if (changeEl) {
                            const num = parseFloat(change);
                            const signed = (isNaN(num) ? change : (num >= 0 ? `+${num.toFixed(change.includes('.') ? change.split('.')[1].length : 3)}` : `${num.toFixed(change.includes('.') ? change.split('.')[1].length : 3)}`));
                            changeEl.textContent = signed;
                            changeEl.parentElement.className = num >= 0 ? 'text-success' : 'text-danger';
                            if (priceEl && priceEl.parentElement) {
                                priceEl.parentElement.className = num >= 0 ? 'h5 mb-0 text-success' : 'h5 mb-0 text-danger';
                            }
                        }
                    });
                });

                // Attach click handlers to market links so the clicked card price is stored in sessionStorage
                // This ensures the trade page can read the exact price from the home list immediately after navigation.
                try {
                    document.querySelectorAll('a.market-link').forEach(a => {
                        a.addEventListener('click', (e) => {
                            try {
                                const sym = a.getAttribute('data-symbol');
                                const card = a.querySelector('.card');
                                const priceEl = card ? (card.querySelector('.price') || card.querySelector('.forex-price') || card.querySelector('.metal-price')) : null;
                                if (sym && priceEl) {
                                    const price = parseFloat(priceEl.textContent.replace(/[^0-9.-]+/g, ''));
                                    if (!isNaN(price)) {
                                        const stored = JSON.parse(sessionStorage.getItem('latestPrices') || '{}');
                                        stored[sym] = { price: price, ts: Date.now() };
                                        sessionStorage.setItem('latestPrices', JSON.stringify(stored));
                                    }
                                }
                            } catch (err) {
                                // ignore
                            }
                        });
                    });
                } catch (e) {
                    // ignore
                }

                // Update metals prices
                Object.entries(markets.metals).forEach(([symbol, {base, variance}]) => {
                    // Match cards whose h5 text equals like 'XAU/USD' (we render h5 as e.g. 'XAU/USD')
                    const lookup = `${symbol}/USD`;
                    const cards = Array.from(document.querySelectorAll('.card')).filter(card => {
                        const h5 = card.querySelector('h5');
                        return h5 && h5.textContent.trim().toLowerCase() === lookup.toLowerCase();
                    });
                    cards.forEach(card => {
                        const price = base + (Math.random() - 0.5) * variance;
                        const change = (Math.random() * 3 - 1).toFixed(2);
                        const priceEl = card.querySelector('.metal-price');
                        const changeEl = card.querySelector('.metal-change');
                        if (priceEl) priceEl.textContent = price.toFixed(2);
                        if (changeEl) {
                            const num = parseFloat(change);
                            const signed = (isNaN(num) ? change : (num >= 0 ? `+${num.toFixed(change.includes('.') ? change.split('.')[1].length : 2)}` : `${num.toFixed(change.includes('.') ? change.split('.')[1].length : 2)}`));
                            changeEl.textContent = signed;
                            changeEl.parentElement.className = num >= 0 ? 'text-success' : 'text-danger';
                            if (priceEl && priceEl.parentElement) {
                                priceEl.parentElement.className = num >= 0 ? 'h5 mb-0 text-success' : 'h5 mb-0 text-danger';
                            }
                        }
                    });
                });
            }
            
            // Update prices every 5 seconds
            updatePrices();
            setInterval(updatePrices, 5000);
        })();

        // Share Invite Function
        function shareInvite() {
            const shareData = {
                title: 'Join Crypto Nest',
                text: 'Join me on Crypto Nest - The most advanced crypto trading platform. Get started with AI-powered trading!',
                url: window.location.origin
            };

            if (navigator.share) {
                navigator.share(shareData)
                    .catch((err) => {
                        console.log('Error sharing:', err);
                        showFallbackShare();
                    });
            } else {
                showFallbackShare();
            }
        }

        function showFallbackShare() {
            // Create a temporary input for copying
            const input = document.createElement('input');
            input.value = window.location.origin;
            document.body.appendChild(input);
            input.select();
            document.execCommand('copy');
            document.body.removeChild(input);

            // Show toast or alert
            alert('Invite link copied to clipboard!');
        }
    </script>
    @endpush
@endsection

@push('scripts')
    <script src="https://unpkg.com/@lottiefiles/dotlottie-wc@0.8.1/dist/dotlottie-wc.js" type="module"></script>
@endpush

@section('content')
@endsection
