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
                    @foreach(['BTC' => 'Bitcoin', 'ETH' => 'Ethereum',
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
                                                <img src="https://s2.coinmarketcap.com/static/img/coins/64x64/{{ array_search($symbol, ['BTC', 'ETH', 'TRX', 'XRP', 'DOGE']) + 1 }}.png" 
                                                     alt="{{ $name }}" class="me-2" style="width: 32px; height: 32px;">
                                            @endif
                                            <div>
                                                <h5 class="mb-0">{{ $symbol }}</h5>
                                                <small class="text-muted">{{ $name }}</small>
                                            </div>
                                            <div class="ms-auto">
                                                <div class="h5 mb-0 text-success">$<span class="price">0.00</span></div>
                                                <small class="text-success"><span class="change">0.00 (0.00%)</span></small>
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
                                                <h5 class="mb-0">{{ $symbol }}/USD</h5>
                                                <small class="text-muted">{{ $name }}</small>
                                            </div>
                                            <div class="ms-auto">
                                                <div class="h5 mb-0 text-success">$<span class="forex-price">0.00</span></div>
                                                <small class="text-success"><span class="forex-change">0.00 (0.000%)</span></small>
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
                                                <small class="text-success"><span class="metal-change">0.00 (0.00%)</span></small>
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
                <!-- AI Arbitrage Card (prominent animation) -->
                <a href="{{ url('/arbitrage') }}" class="text-decoration-none">
                        <div class="card market-card feature-card position-relative overflow-hidden" style="box-shadow: 0 10px 30px rgba(16,185,129,0.06); border: 1px solid rgba(34,197,94,0.08);">
                            <span class="feature-badge">AI Arbitrage</span>
                        <div class="position-absolute w-100 h-100 d-flex justify-content-center align-items-center" style="pointer-events:none; z-index:1;">
                            <dotlottie-wc src="https://lottie.host/4c89fb6d-25b1-4505-8149-a669c9c57e3d/EaLJaJluzA.lottie" style="width:80%; height:80%; max-width:420px; max-height:420px; opacity:0.98;" autoplay loop></dotlottie-wc>
                        </div>
                        <div class="card-body position-relative z-2 p-0" style="min-height:180px;"></div>
                    </div>
                </a>
                <!-- Mining Card (prominent animation only) -->
                <a href="{{ url('/mining') }}" class="text-decoration-none">
                    <div class="card market-card feature-card position-relative overflow-hidden" style="box-shadow: 0 10px 30px rgba(16,185,129,0.08); border: 1px solid rgba(34,197,94,0.10);">
                            <span class="feature-badge mining">Mining</span>
                        <!-- Large centered animation; pointer-events disabled so the link remains clickable -->
                        <div class="position-absolute w-100 h-100 d-flex justify-content-center align-items-center" style="pointer-events:none; z-index:1;">
                            <dotlottie-wc src="https://lottie.host/3b3a89d1-eaa6-4fb6-9118-1d5804faaf5a/RNsMUkyjxt.lottie" style="width: 80%; height: 80%; max-width: 420px; max-height: 420px; opacity:0.98;" autoplay loop></dotlottie-wc>
                        </div>
                        <!-- Keep an empty card-body so layout/spacing remains consistent -->
                        <div class="card-body position-relative z-2 p-0" style="min-height:180px;"></div>
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
                <!-- Invite Friends Card (prominent animation) -->
                <div class="card market-card feature-card position-relative overflow-hidden" onclick="shareInvite()" style="box-shadow: 0 10px 30px rgba(16,185,129,0.06); border: 1px solid rgba(34,197,94,0.08);">
                    <span class="feature-badge">Invite Friend</span>
                    <div class="position-absolute w-100 h-100 d-flex justify-content-center align-items-center" style="pointer-events:none; z-index:1;">
                        <dotlottie-wc src="https://lottie.host/bb1ba882-5cec-4676-add1-d008f39ae2ee/I9uAcddYg9.lottie" style="width:80%; height:80%; max-width:420px; max-height:420px; opacity:0.98;" autoplay loop></dotlottie-wc>
                    </div>
                    <div class="card-body position-relative z-2 p-0" style="min-height:180px;"></div>
                </div>
                <!-- News Card replaced with dotlottie animation -->
                <a href="{{ url('/news') }}" class="text-decoration-none">
                    <div class="card market-card feature-card position-relative overflow-hidden" style="border:1px solid rgba(34,197,94,0.06);">
                        <span class="feature-badge">News</span>
                        <div class="position-absolute w-100 h-100 d-flex justify-content-center align-items-center" style="pointer-events:none; z-index:1;">
                            <dotlottie-wc src="https://lottie.host/7169eae3-0464-443d-89bf-fa2ee684a3b8/f05cXmuwHZ.lottie" style="width:300px;height:300px;opacity:0.98;" autoplay loop></dotlottie-wc>
                        </div>
                        <div class="card-body position-relative z-2 p-0" style="min-height:180px;"></div>
                    </div>
                </a>
            </div>
        </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://unpkg.com/@lottiefiles/dotlottie-wc@0.8.5/dist/dotlottie-wc.js" type="module"></script>
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
            color: var(--text);
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

        /* removed dark-mode override for market tabs (light-mode only) */

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

        /* removed dark-mode feature-card shadow overrides (light-mode only) */

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        /* Feature badge (pill) */
        .feature-badge {
            position: absolute;
            top: 14px;
            right: 14px;
            left: auto;
            z-index: 3;
            display: inline-block;
            padding: 6px 12px;
            border-radius: 999px;
            background: linear-gradient(90deg, #f59e0b, #f97316);
            color: #fff;
            font-weight: 600;
            font-size: 0.85rem;
            box-shadow: 0 6px 18px rgba(0,0,0,0.25);
        }

        /* keep mining variant for future overrides (matches same look) */
        .feature-badge.mining {
            background: linear-gradient(90deg, #f59e0b, #f97316);
            color: #fff;
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

        /* removed dark-mode animation-wrapper background (light-mode only) */

        .text-primary-dark {
            color: var(--primary-dark);
        }

        /* removed dark-mode text-primary-dark override (light-mode only) */
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
            
            // Price update state
            let priceUpdateInProgress = false;
            
            // Simulate price updates
            async function updatePrices() {
                // Prevent overlapping requests
                if (priceUpdateInProgress) return;
                priceUpdateInProgress = true;
                
                try {
                    const markets = {
                        crypto: {
                            'BTC': {base: 40000, variance: 2000},
                            'ETH': {base: 2200, variance: 100},
                            
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
                    
                    // Build symbol lists for crypto, forex and metals
                    const symbolToId = { 'BTC':'BTC','ETH':'ETH','TRX':'TRX','XRP':'XRP','DOGE':'DOGE' };
                    const cryptoSymbols = Object.keys(symbolToId);
                    const forexSymbols = Object.keys(markets.forex);
                    const metalSymbols = Object.keys(markets.metals);

                    const allSymbols = [...cryptoSymbols, ...forexSymbols, ...metalSymbols];

                    const resp = await fetch('/prices?symbols=' + encodeURIComponent(allSymbols.join(',')) + '&prefer=bitcryptoforest');
                    if (resp.ok) {
                        const j = await resp.json();
                        const data = j && j.data ? j.data : {};

                        // Update crypto cards
                        cryptoSymbols.forEach(symbol => {
                            const cards = Array.from(document.querySelectorAll('.card')).filter(card => {
                                const h5 = card.querySelector('h5');
                                return h5 && h5.textContent.trim().toLowerCase() === String(symbol).toLowerCase();
                            });
                            const info = data[symbol] || null;
                            cards.forEach(card => {
                                const priceEl = card.querySelector('.price');
                                const changeEl = card.querySelector('.change');
                                if (info && info.price !== undefined && info.price !== null) {
                                        const price = parseFloat(info.price);
                                        const change = info.change !== undefined && info.change !== null ? parseFloat(info.change) : 0;
                                        const rate = info.rate !== undefined && info.rate !== null && !isNaN(info.rate) ? parseFloat(info.rate) : null;
                                        if (priceEl) priceEl.textContent = price.toFixed(price < 1 ? 4 : 2);
                                        if (changeEl) {
                                            // preserve API-provided change string exactly (do not round small amounts)
                                            const rawChange = (info.change !== undefined && info.change !== null) ? String(info.change) : '0';
                                            const parsedChange = parseFloat(rawChange) || 0;
                                            const signed = (/^[+-]/.test(rawChange)) ? rawChange : (parsedChange >= 0 ? `+${rawChange}` : rawChange);
                                            // prefer provided rate, else compute fallback percent
                                            let pctText = '';
                                            if (rate !== null) {
                                                pctText = `(${rate.toFixed(2)}%)`;
                                            } else {
                                                const prev = price - parsedChange;
                                                const pct = (!isNaN(prev) && prev !== 0) ? (parsedChange / prev) * 100 : null;
                                                pctText = pct !== null ? `(${pct.toFixed(2)}%)` : '';
                                            }
                                            changeEl.textContent = `${signed} ${pctText}`;
                                            // use parsedChange (numeric) when deciding positive/negative styling
                                            const num = parsedChange;
                                            changeEl.parentElement.className = num >= 0 ? 'text-success' : 'text-danger';
                                            if (priceEl && priceEl.parentElement) priceEl.parentElement.className = num >= 0 ? 'h5 mb-0 text-success' : 'h5 mb-0 text-danger';
                                        }
                                    // persist
                                    try {
                                        const stored = JSON.parse(localStorage.getItem('latestPrices') || '{}');
                                        stored[symbol.toLowerCase()] = { price: price, change: parseFloat(change) || 0, ts: Date.now() };
                                        localStorage.setItem('latestPrices', JSON.stringify(stored));
                                    } catch (e) {}
                                }
                            });
                        });

                        // Update forex cards (showing <symbol>/USD in the UI)
                        forexSymbols.forEach(symbol => {
                            const lookup = `${symbol}/USD`;
                            const cards = Array.from(document.querySelectorAll('.card')).filter(card => {
                                const h5 = card.querySelector('h5');
                                return h5 && h5.textContent.trim().toLowerCase() === lookup.toLowerCase();
                            });
                            const info = data[symbol] || null;
                            cards.forEach(card => {
                                const priceEl = card.querySelector('.forex-price');
                                const changeEl = card.querySelector('.forex-change');
                                if (info && info.price !== undefined && info.price !== null) {
                                    const price = parseFloat(info.price);
                                    const change = info.change !== undefined && info.change !== null ? parseFloat(info.change) : 0;
                                    const rate = info.rate !== undefined && info.rate !== null && !isNaN(info.rate) ? parseFloat(info.rate) : null;
                                    if (priceEl) priceEl.textContent = price.toFixed(4);
                                    if (changeEl) {
                                        // preserve API-provided change string exactly
                                        const rawChange = (info.change !== undefined && info.change !== null) ? String(info.change) : '0';
                                        const parsedChange = parseFloat(rawChange) || 0;
                                        const signed = (/^[+-]/.test(rawChange)) ? rawChange : (parsedChange >= 0 ? `+${rawChange}` : rawChange);
                                        let pctText = '';
                                        if (rate !== null) {
                                            pctText = `(${rate.toFixed(3)}%)`;
                                        } else {
                                            const prev = price - parsedChange;
                                            const pct = (!isNaN(prev) && prev !== 0) ? (parsedChange / prev) * 100 : null;
                                            pctText = pct !== null ? `(${pct.toFixed(3)}%)` : '';
                                        }
                                        changeEl.textContent = `${signed} ${pctText}`;
                                        const num = parsedChange;
                                        changeEl.parentElement.className = num >= 0 ? 'text-success' : 'text-danger';
                                        if (priceEl && priceEl.parentElement) priceEl.parentElement.className = num >= 0 ? 'h5 mb-0 text-success' : 'h5 mb-0 text-danger';
                                    }
                                }
                            });
                        });

                        // Update metals cards (XAU/USD etc)
                        metalSymbols.forEach(symbol => {
                            const lookup = `${symbol}/USD`;
                            const cards = Array.from(document.querySelectorAll('.card')).filter(card => {
                                const h5 = card.querySelector('h5');
                                return h5 && h5.textContent.trim().toLowerCase() === lookup.toLowerCase();
                            });
                            const info = data[symbol] || null;
                            cards.forEach(card => {
                                const priceEl = card.querySelector('.metal-price');
                                const changeEl = card.querySelector('.metal-change');
                                if (info && info.price !== undefined && info.price !== null) {
                                    const price = parseFloat(info.price);
                                    const change = info.change !== undefined && info.change !== null ? parseFloat(info.change) : 0;
                                    const rate = info.rate !== undefined && info.rate !== null && !isNaN(info.rate) ? parseFloat(info.rate) : null;
                                    if (priceEl) priceEl.textContent = price.toFixed(2);
                                    if (changeEl) {
                                        // preserve API-provided change string exactly
                                        const rawChange = (info.change !== undefined && info.change !== null) ? String(info.change) : '0';
                                        const parsedChange = parseFloat(rawChange) || 0;
                                        const signed = (/^[+-]/.test(rawChange)) ? rawChange : (parsedChange >= 0 ? `+${rawChange}` : rawChange);
                                        let pctText = '';
                                        if (rate !== null) {
                                            pctText = `(${rate.toFixed(2)}%)`;
                                        } else {
                                            const prev = price - parsedChange;
                                            const pct = (!isNaN(prev) && prev !== 0) ? (parsedChange / prev) * 100 : null;
                                            pctText = pct !== null ? `(${pct.toFixed(2)}%)` : '';
                                        }
                                        changeEl.textContent = `${signed} ${pctText}`;
                                        const num = parsedChange;
                                        changeEl.parentElement.className = num >= 0 ? 'text-success' : 'text-danger';
                                        if (priceEl && priceEl.parentElement) priceEl.parentElement.className = num >= 0 ? 'h5 mb-0 text-success' : 'h5 mb-0 text-danger';
                                    }
                                }
                            });
                        });
                    }
                } catch (e) {
                    console.warn('Price fetch failed', e);
                } finally {
                    priceUpdateInProgress = false;
                }

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

<!-- removed duplicate/old dotlottie script import (kept the 0.8.5 import above) -->

@section('content')
@endsection
