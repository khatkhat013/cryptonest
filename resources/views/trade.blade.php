@extends('layouts.app')

@push('styles')
<style>
/* Header gradient used across wallet and mining pages */
.bg-primary {
    background: linear-gradient(135deg, #0051ff 0%, #0066ff 100%);
}
/* Mining card styles */
.mining-card {
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 6px 20px rgba(2,6,23,0.6);
}
.mining-hero {
    background: linear-gradient(90deg, #111827 0%, #6b21a8 50%, #111827 100%);
    border-bottom: 1px solid rgba(255,255,255,0.04);
    border-radius: 12px 12px 0 0;
}
.mining-hero h4 { font-weight:700; }
.mining-panel {
    background: linear-gradient(180deg,#0b1220 0%, #151426 100%);
    color: #e6eef8;
}
.fund-large .display-6 { font-size: 1.6rem; letter-spacing: -0.5px; }
.stats-row .stat-icon { font-size: 18px; }
.btn-start {
    background: linear-gradient(90deg,#5eead4,#60a5fa);
    border: none;
    color: #0b1220;
    font-weight: 700;
    border-radius: 999px;
    padding: 12px 18px;
}
.btn-start:hover { opacity: 0.95; }

/* Acquired earnings card (matches provided attachment) */
.acquired-card {
    border-radius: 12px;
    overflow: hidden;
    color: #fff;
    box-shadow: 0 6px 18px rgba(2,6,23,0.5);
    background: linear-gradient(135deg, #06b6d4 0%, #0ea5e9 28%, rgba(2,6,23,0.12) 58%, #7c3aed 100%);
}
.acquired-card .card-body {
    padding: 18px 20px;
}
.acquired-card .header-row {
    display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;
}
.acquired-card .header-row .title { font-weight:700; }
.acquired-card .top-keys { display:flex;justify-content:space-between;align-items:flex-start;padding:6px 2px 12px 2px; }
.acquired-card .top-keys .left, .acquired-card .top-keys .right { font-weight:600; }
.acquired-card .center-illustration { text-align:center; padding:18px 0; opacity:0.95 }
.acquired-card .no-data { opacity:0.85; margin-top:8px; }
.acquired-card .placeholder-illustration { width:96px; height:96px; filter: drop-shadow(0 6px 18px rgba(0,0,0,0.45)); }
/* Pool Data card styles */
.pool-data-card {
    border-radius: 14px;
    overflow: hidden;
    padding: 10px;
    background: linear-gradient(135deg,#0b1220 0%, #0f1724 40%, rgba(124,58,237,0.06) 100%);
    border: 1px solid rgba(255,255,255,0.04);
}
.pool-data-card .pool-inner {
    border-radius: 10px;
    padding: 14px;
    background: linear-gradient(90deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01));
    box-shadow: 0 8px 24px rgba(2,6,23,0.45) inset;
}
.pool-data-card .row { display:flex; align-items:center; justify-content:space-between; }
.pool-data-card .row > div { padding: 10px 12px; }
.pool-data-card .label { color: rgba(255,255,255,0.78); font-weight:600; flex:1; padding-right:12px; min-width:0; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.pool-data-card .value { background: linear-gradient(90deg,#4ee6ff,#60f0d9); -webkit-background-clip:text; -webkit-text-fill-color:transparent; font-weight:800; text-align:right; flex:0 0 46%; max-width:46%; word-break:break-all; overflow-wrap:anywhere; white-space:normal; }
.pool-data-card .divider { height:1px; background: linear-gradient(90deg, rgba(255,255,255,0.03), rgba(255,255,255,0.02)); margin:6px 0; border-radius:1px; }

@media (max-width: 576px) {
    .pool-data-card .label { white-space:normal; font-size:0.95rem; }
    .pool-data-card .value { flex:0 0 50%; max-width:50%; font-size:0.95rem; }
    .pool-data-card { padding: 8px; }
}

/* Liquid Mining Output scrolling list */
.liquid-card { border-radius:14px; overflow:hidden; background: linear-gradient(90deg,#0b1220 0%, #111827 45%, #6b21a8 100%); border:1px solid rgba(255,255,255,0.04); }
.liquid-inner { padding:12px; }
.liquid-head { font-weight:600; color: rgba(255,255,255,0.75); padding:8px 4px; }
.liquid-mask { height: 260px; overflow:hidden; position:relative; }
.liquid-list { display:block; }
.liquid-row { display:flex; justify-content:space-between; align-items:center; padding:8px 6px; min-height:34px; border-bottom:1px solid rgba(255,255,255,0.02); color: rgba(255,255,255,0.92); font-family: var(--bs-font-sans-serif); }
.liquid-row .addr { color: rgba(255,255,255,0.72); font-family: monospace; font-size:0.95rem; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.liquid-row .amt { color: #baf7ff; font-weight:700; text-align:right; min-width:120px; }
.liquid-list.paused { animation-play-state:paused; }

@keyframes scrollUp {
    from { transform: translateY(0); }
    to { transform: translateY(-50%); }
}

/* we'll set the animation on the .liquid-list element inline so duration scales with items */
.liquid-list { will-change: transform; }
.liquid-list:hover { animation-play-state: paused !important; }

@media (max-width:576px) {
    .liquid-mask { height: 220px; }
    .liquid-row .addr { font-size:0.85rem; }
    .liquid-row .amt { min-width:90px; font-size:0.9rem; }
}

/* fade overlays to soften edges */
.liquid-fade-top, .liquid-fade-bottom { position:absolute; left:0; right:0; height:36px; pointer-events:none; z-index:5; }
.liquid-fade-top { top:0; background: linear-gradient(180deg, rgba(11,17,32,0.95), rgba(11,17,32,0)); }
.liquid-fade-bottom { bottom:0; background: linear-gradient(0deg, rgba(11,17,32,0.95), rgba(11,17,32,0)); }

/* Our Advantages cards */
.advantages-row { display:flex; gap:18px; margin-top:18px; }
.adv-card { flex:1; border-radius:14px; padding:18px; background: linear-gradient(135deg, rgba(6,11,26,0.92), rgba(30,18,60,0.92)); box-shadow: 0 18px 46px rgba(2,6,23,0.6); border:1px solid rgb(255 255 255 / 6%); position:relative; overflow:hidden; transition:transform .28s ease, box-shadow .28s ease; }
.adv-card:hover { transform: translateY(-8px); box-shadow: 0 22px 56px rgba(2,6,23,0.7); }
.adv-card::after { content: ''; position:absolute; inset:0; pointer-events:none; border-radius:14px; padding:2px; background: linear-gradient(90deg, rgba(96,165,250,0.04), rgba(167,139,250,0.03)); mix-blend-mode: overlay; }
.adv-card .icon { width:46px; height:46px; border-radius:50%; background:linear-gradient(90deg,#60a5fa,#a78bfa); display:flex; align-items:center; justify-content:center; color:#07203b; font-weight:700; margin-bottom:12px; box-shadow: 0 6px 16px rgba(96,165,250,0.06) inset; }
.adv-card h6 { color:#fff; margin-bottom:8px; font-size:1.05rem; }
.adv-card p { color: rgba(255,255,255,0.92); margin:0; padding-right:6px; transition:all .22s ease; }
.adv-card.expanded p { -webkit-line-clamp:unset; max-height:none; }

@media (max-width:768px) {
    .advantages-row { flex-direction:column; }
}

/* Dex gallery */
.dex-row { display:flex; gap:14px; margin-top:16px; flex-wrap:nowrap; overflow-x:auto; -webkit-overflow-scrolling:touch; }
.dex-item {
    flex:0 0 33.3333%;
    border-radius:14px;
    overflow:hidden;
    border: 1px solid rgb(255 255 255 / 8%);
    /* stronger dark background so light logos are readable */
    background: linear-gradient(135deg, rgba(6,11,26,0.96), rgba(30,20,60,0.95));
    display:flex; align-items:center; justify-content:center; padding:18px;
    transition:transform .22s ease, box-shadow .22s ease, border-color .22s ease;
    box-shadow: 0 10px 28px rgba(2,6,23,0.22);
}
.dex-item:hover {
    transform: translateY(-6px) scale(1.02);
    box-shadow: 0 20px 50px rgba(124,58,237,0.18);
    border-color: rgb(167 139 250 / 30%);
}
.dex-item img { width:100%; max-width:220px; height:110px; object-fit:contain; display:block; filter: drop-shadow(0 8px 24px rgba(0,0,0,0.6)) saturate(1.15) contrast(1.05); opacity:1; }
@media (max-width:480px) { .dex-item { flex:0 0 33.3333%; padding:12px; } .dex-item img { max-width:160px; height:90px; } }

/* Partners grid - 2 columns */
.partners-grid { display:grid; grid-template-columns: repeat(2, 1fr); gap:14px; margin-top:20px; }
.partner-card {
    border-radius:12px; overflow:hidden; padding:12px; display:flex; align-items:center; justify-content:center;
    /* dark background to reveal white/light logos */
    background: linear-gradient(90deg, rgba(8,12,18,0.96), rgba(22,22,30,0.96));
    border: 1px solid rgb(255 255 255 / 6%);
    transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
    box-shadow: 0 12px 36px rgba(2,6,23,0.12);
}
.partner-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 16px 40px rgba(2,6,23,0.12);
    border-color: rgb(96 165 250 / 18%);
}
.partner-card img { width:100%; max-width:260px; height:100px; object-fit:contain; filter: drop-shadow(0 8px 20px rgba(0,0,0,0.6)) saturate(1.05); opacity:1; }
@media (max-width:576px) { .partners-grid { grid-template-columns: repeat(2, 1fr); } }
</style>
@endpush

@section('content')
    @if(isset($symbol))
    {{-- Pass the page type so the header can choose appropriate fallbacks for forex/metal --}}
    <x-coin-header :symbol="$symbol" :showPrice="false" :type="$type ?? 'crypto'" />
        <div class="main-content" style="padding-top: 65px">
            <div class="container">
                <div class="card">
                    <div class="card-body p-0 overflow-hidden">
                        <div class="chart-container">
                            <div class="tradingview-widget-container" style="height:100%;width:100%">
                                <div class="tradingview-widget-container__widget" style="height:100%;width:100%"></div>
                                <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-advanced-chart.js" async>
                                @php
                                    // Compute TradingView symbol server-side to avoid embedding PHP inside a quoted JS string
                                    $tvSymbol = strtoupper($symbol ?? '');
                                    if (in_array($tvSymbol, ['XPT', 'XPD'])) {
                                        $tvSymbol = 'OANDA:' . $tvSymbol . 'USD';
                                    } elseif (in_array($tvSymbol, ['XAU', 'XAG'])) {
                                        $tvSymbol = 'FOREXCOM:' . $tvSymbol . 'USD';
                                    } elseif (in_array($tvSymbol, ['CAD', 'JPY'])) {
                                        $tvSymbol = 'SAXO:' . $tvSymbol . 'USD';
                                    } elseif ($tvSymbol === 'CHF') {
                                        $tvSymbol = 'IBKR:CHFUSD';
                                    } else {
                                        $tvSymbol = 'FOREXCOM:' . $tvSymbol . 'USD';
                                    }
                                @endphp
                                {
                                    "allow_symbol_change": false,
                                    "calendar": false,
                                    "details": false,
                                    "hide_side_toolbar": true,
                                    "hide_top_toolbar": false,
                                    "hide_legend": false,
                                    "hide_volume": false,
                                    "hotlist": false,
                                    "interval": "1",
                                    "locale": "en",
                                    "save_image": true,
                                    "style": "1",
                                    "symbol": {!! json_encode($tvSymbol) !!},
                                    "theme": "dark",
                                    "timezone": "Etc/UTC",
                                    "backgroundColor": "#0F0F0F",
                                    "gridColor": "rgba(242, 242, 242, 0.06)",
                                    "watchlist": [],
                                    "withdateranges": false,
                                    "compareSymbols": [],
                                    "studies": [],
                                    "autosize": true
                                }
                                </script>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Function Section -->
                <h5 class="mt-4 mb-3 ps-2 tracking-in-expand-fwd">Function</h5>
                
                <!-- Stats Section -->
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-clock-history fs-4 text-primary me-2"></i>
                                    <div>
                                        <div class="text-muted small">24 Hour Volume</div>
                                        <div class="h5 mb-0 volume-value">Loading...</div>
                                    </div>
                                </div>
                                <div class="badge bg-success-subtle text-success volume-change">+0.00%</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-currency-dollar fs-4 text-primary me-2"></i>
                                    <div>
                                        <div class="text-muted small">24-hour Credit</div>
                                        <div class="h5 mb-0 credit-value">Loading...</div>
                                    </div>
                                </div>
                                <div class="badge bg-success-subtle text-success credit-change">+0.00%</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Appointment Section -->
                <div class="card mt-3">
                    <div class="card-body text-center py-4">
                        <button class="btn btn-outline-success btn-lg px-5 rounded-pill" onclick="openAppointmentModal()">
                            <i class="bi bi-calendar-plus me-2"></i>Appointment
                        </button>
                    </div>
                </div>

                <!-- Trading Timer Section -->
                <div id="tradingTimer" style="display: none;">
                    <div class="card mt-3">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Current Price</h5>
                                    <div class="h3" id="current-price">{{ number_format($currentPrice ?? 0, 2) }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div id="countdown-section">
                                        <h5>Time Remaining</h5>
                                        <div class="h3" id="countdown">60</div>
                                    </div>
                                    <div id="results-section" style="display: none;">
                                        <h5>Result</h5>
                                        <div class="h3" id="result-status"></div>
                                        <div class="h4">Profit: <span id="profit-amount">0.00</span> USDT</div>
                                        <button id="done-button" class="btn btn-primary">Done</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Appointment Modal -->
                <div class="modal fade" id="appointmentModal" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header border-0">
                                <h5 class="modal-title">Place Order</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <!-- Coin Selection -->
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div class="d-flex align-items-center">
                                        <img src="https://s2.coinmarketcap.com/static/img/coins/64x64/1.png" alt="BTC" class="me-2" style="width: 32px">
                                        <div class="h5 mb-0">@php
                                            $symbol = strtoupper($symbol);
                                            if (in_array($symbol, ['XAU', 'XPD', 'XPT', 'XAG', 'GBP', 'EUR', 'CHF', 'CAD', 'AUD', 'JPY'])) {
                                                echo $symbol . '/USD';
                                            } else {
                                                echo $symbol . '/USDT';
                                            }
                                        @endphp</div>
                                    </div>
                                    <div class="btn-group" role="group">
                                        <input type="radio" class="btn-check" name="direction" id="upBtn" checked>
                                        <label class="btn btn-outline-success" for="upBtn" onclick="setDirection('up')">Up</label>
                                        
                                        <input type="radio" class="btn-check" name="direction" id="downBtn">
                                        <label class="btn btn-outline-danger" for="downBtn" onclick="setDirection('down')">Down</label>
                                    </div>
                                </div>

                                <!-- Delivery Time Selection -->
                                <div class="mb-4">
                                    <label class="form-label">Delivery Time</label>
                                    <select class="form-select" id="deliveryTime" onchange="updatePriceRange()">
                                        <option value="60">60s</option>
                                        <option value="120">120s</option>
                                        <option value="180">180s</option>
                                        <option value="300">300s</option>
                                        <option value="600">600s</option>
                                        <option value="1200">1200s</option>
                                    </select>
                                </div>

                                <!-- Current Price -->
                                <div class="mb-4">
                                    <label class="form-label">Current Price</label>
                                    <div class="d-flex align-items-center">
                                        <input type="text" class="form-control" id="currentPrice" readonly>
                                        <span class="ms-2">USDT</span>
                                    </div>
                                </div>

                                <!-- Price Range -->
                                <div class="mb-4">
                                    <label class="form-label">Price Range</label>
                                    <div class="d-flex align-items-center">
                                        <input type="text" class="form-control" id="priceRange" readonly>
                                    </div>
                                </div>

                                <!-- Purchase Quantity -->
                                <div class="mb-4">
                                    <label class="form-label">Purchase Quantity</label>
                                    <div class="d-flex align-items-center">
                                        <input type="number" class="form-control" id="purchaseQuantity" min="100">
                                        <span class="ms-2">USDT</span>
                                    </div>
                                    <div class="form-text">Minimum: 100 USDT</div>
                                </div>

                                <!-- Available Balance -->
                                <div class="d-flex justify-content-between text-muted small mb-4">
                                    <span>Available Balance:</span>
                                    <span id="availableBalance">0.00 USDT</span>
                                </div>
                                <div id="balanceMessage" class="text-danger small mb-2 d-none">Insufficient balance (minimum 100 USDT)</div>

                                <!-- Submit Button -->
                                <button id="placeOrderButton" type="button" class="btn btn-success w-100" onclick="submitOrder()">
                                    Place Order
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <style>
        /* Chart Responsive Height */
        .chart-container {
            height: 70vh;
            max-height: 600px;
            min-height: 300px;
            position: relative;
        }

        @media (max-width: 768px) {
            .chart-container {
                height: 50vh;
                min-height: 250px;
            }
        }

        /* Hide TradingView logo */
        .tradingview-widget-copyright {
            display: none !important;
        }

        .tracking-in-expand-fwd {
            animation: tracking-in-expand-fwd 0.8s cubic-bezier(0.215, 0.610, 0.355, 1.000) both;
        }

        @keyframes tracking-in-expand-fwd {
            0% {
                letter-spacing: -0.5em;
                transform: translateZ(-700px);
                opacity: 0;
            }
            40% {
                opacity: 0.6;
            }
            100% {
                transform: translateZ(0);
                opacity: 1;
            }
        }
        
        .card-body.p-0 {
            border-radius: 0.5rem;
            overflow: hidden;
        }

        .modal-content {
            background: var(--bg-surface);
            border: 1px solid var(--border);
        }

        .btn-group .btn {
            min-width: 80px;
        }

        .form-control, .form-select {
            background-color: var(--bg-main);
            border-color: var(--border);
            color: var(--text);
        }

        .form-control:disabled, .form-control[readonly] {
            background-color: var(--bg-main);
            opacity: 0.8;
        }
        </style>

        <script>
        // Simulated data update
        function updateStats() {
            const volume = Math.floor(Math.random() * 50000) + 10000;
            const volumeChange = ((Math.random() * 10) - 5).toFixed(2);
            const credit = Math.floor(Math.random() * 5000000) + 1000000;
            const creditChange = ((Math.random() * 10) - 5).toFixed(2);

            document.querySelector('.volume-value').textContent = volume.toLocaleString();
            document.querySelector('.volume-change').textContent = `${volumeChange}%`;
            document.querySelector('.credit-value').textContent = `US$ ${credit.toLocaleString()}`;
            document.querySelector('.credit-change').textContent = `${creditChange}%`;

            // Update badge colors
            updateBadgeColor('.volume-change', volumeChange);
            updateBadgeColor('.credit-change', creditChange);
        }

        function updateBadgeColor(selector, value) {
            const badge = document.querySelector(selector);
            if (parseFloat(value) >= 0) {
                badge.classList.remove('bg-danger-subtle', 'text-danger');
                badge.classList.add('bg-success-subtle', 'text-success');
            } else {
                badge.classList.remove('bg-success-subtle', 'text-success');
                badge.classList.add('bg-danger-subtle', 'text-danger');
            }
        }

        // Start updating stats
        updateStats();
        setInterval(updateStats, 5000);

        // Modal functionality
        let tradeDirection = 'up';
        const priceRanges = {
            '60': '41%',
            '120': '52%',
            '180': '73%',
            '300': '100%',
            '600': '150%',
            '1200': '200%'
        };

        function openAppointmentModal() {
                    const modal = new bootstrap.Modal(document.getElementById('appointmentModal'));
                    // Populate current price from home page cached values (localStorage) to avoid extra API calls
                    try {
                        const sym = '{{ $symbol }}'.toLowerCase();
                        // prefer sessionStorage (set when clicking a link on home page), then localStorage
                        let stored = JSON.parse(sessionStorage.getItem('latestPrices') || '{}');
                        if (!(stored && stored[sym] && stored[sym].price)) {
                            stored = JSON.parse(localStorage.getItem('latestPrices') || '{}');
                        }
                        if (stored && stored[sym] && stored[sym].price) {
                            const cp = document.getElementById('currentPrice');
                            cp.value = parseFloat(stored[sym].price).toFixed(stored[sym].price < 1 ? 4 : 2);
                            // lock the field to avoid being overwritten by the periodic updater
                            cp.dataset.locked = '1';
                        } else {
                            // fallback to existing updater (will set the field asynchronously)
                            updateCurrentPrice();
                        }
                    } catch (e) {
                        updateCurrentPrice();
                    }

                    // Ensure price range field is correct when opening
                    updatePriceRange();

                    // Refresh user's available USDT balance when opening the modal so they see the current value
                    try { fetchAvailableBalance(); } catch(e) {}

                    modal.show();
        }

        function setDirection(direction) {
            tradeDirection = direction;
            if (direction === 'up') {
                document.getElementById('upBtn').checked = true;
                document.getElementById('downBtn').checked = false;
            } else {
                document.getElementById('upBtn').checked = false;
                document.getElementById('downBtn').checked = true;
            }
        }

        function updatePriceRange() {
            const seconds = document.getElementById('deliveryTime').value;
            document.getElementById('priceRange').value = priceRanges[seconds];
        }

        // Update current price every 5 seconds
        function updateCurrentPrice() {
            const el = document.getElementById('currentPrice');
            if (!el) return;
            // If modal populated a cached price, it sets data-locked to prevent overwrites.
            if (el.dataset && el.dataset.locked === '1') return;
            const randomPrice = (Math.random() * 1000 + 25000).toFixed(2);
            el.value = randomPrice;
        }

        // When the appointment modal hides, clear any lock so future updates can proceed
        document.getElementById('appointmentModal').addEventListener('hidden.bs.modal', () => {
            const el = document.getElementById('currentPrice');
            if (el && el.dataset) delete el.dataset.locked;
        });

        // Start current price updates
        updateCurrentPrice();
        setInterval(updateCurrentPrice, 5000);

        // Fetch and display user's USDT available balance and manage place-order enable/disable
        async function fetchAvailableBalance() {
            try {
                const resp = await fetch('/api/wallet/balance/usdt', {
                    method: 'GET',
                    headers: { 'Accept': 'application/json' },
                    credentials: 'same-origin'
                });
                if (!resp.ok) {
                    console.warn('Failed to fetch balance', resp.status);
                    return;
                }
                const json = await resp.json();
                const raw = json && json.balance ? json.balance : '0.00';
                const bal = parseFloat(String(raw).replace(/,/g, '')) || 0;
                const span = document.getElementById('availableBalance');
                if (span) span.textContent = bal.toFixed(2) + ' USDT';

                const placeBtn = document.getElementById('placeOrderButton');
                const msg = document.getElementById('balanceMessage');
                if (placeBtn) {
                    if (bal < 100) {
                        placeBtn.disabled = true;
                        if (msg) msg.classList.remove('d-none');
                    } else {
                        placeBtn.disabled = false;
                        if (msg) msg.classList.add('d-none');
                    }
                }
                // expose balance for other client checks
                window.__available_usdt_balance = bal;
            } catch (e) {
                console.error('Error fetching available balance', e);
            }
        }

        // Ensure balance is fetched on page load
        fetchAvailableBalance();

        // Re-check button state when user edits the purchase quantity
        (function attachQuantityWatcher(){
            const qtyEl = document.getElementById('purchaseQuantity');
            if (!qtyEl) return;
            qtyEl.addEventListener('input', () => {
                const val = parseFloat(qtyEl.value) || 0;
                const bal = window.__available_usdt_balance || 0;
                const placeBtn = document.getElementById('placeOrderButton');
                const msg = document.getElementById('balanceMessage');
                if (placeBtn) {
                    if (val < 100 || val > bal) {
                        placeBtn.disabled = true;
                        if (msg) {
                            msg.textContent = val < 100 ? 'Minimum purchase is 100 USDT' : 'Insufficient balance for this amount';
                            msg.classList.remove('d-none');
                        }
                    } else {
                        // valid quantity and within balance
                        if (bal < 100) {
                            placeBtn.disabled = true;
                            if (msg) { msg.textContent = 'Insufficient balance (minimum 100 USDT)'; msg.classList.remove('d-none'); }
                        } else {
                            placeBtn.disabled = false;
                            if (msg) msg.classList.add('d-none');
                        }
                    }
                }
            });
        })();

        // Live single-symbol fetch using the same source as the home page (CoinGecko).
        // This updates the modal's Current Price in real-time and will override the temporary lock so the user sees live data.
        async function fetchLivePrice() {
            try {
                const sym = '{{ $symbol }}'.toLowerCase();
                const symbolToPair = {
                    'btc': 'BTC',
                    'eth': 'ETH',
                    'bnb': 'BNB',
                    'trx': 'TRX',
                    'xrp': 'XRP',
                    'doge': 'DOGE'
                };
                const pairSym = symbolToPair[sym];
                if (!pairSym) return;

                // Try Coinbase first
                try {
                    const resp = await fetch(`https://api.coinbase.com/v2/prices/${pairSym}-USD/spot`);
                    if (resp.ok) {
                        const j = await resp.json();
                        const amt = j && j.data && parseFloat(j.data.amount);
                        if (!isNaN(amt)) {
                            const cp = document.getElementById('currentPrice');
                            if (cp) {
                                cp.value = amt.toFixed(amt < 1 ? 4 : 2);
                                if (cp.dataset && cp.dataset.locked === '1') delete cp.dataset.locked;
                            }
                            return;
                        }
                    }
                } catch (e) {}

                // Optional Binance (only if enabled)
                try {
                    if (window.APP_CONFIG?.allowBinanceClient) {
                        const resp = await fetch(`https://api.binance.com/api/v3/ticker/price?symbol=${pairSym}USDT`);
                        if (resp.ok) {
                            const j = await resp.json();
                            const amt = j && parseFloat(j.price);
                            if (!isNaN(amt)) {
                                const cp = document.getElementById('currentPrice');
                                if (cp) {
                                    cp.value = amt.toFixed(amt < 1 ? 4 : 2);
                                    if (cp.dataset && cp.dataset.locked === '1') delete cp.dataset.locked;
                                }
                                return;
                            }
                        }
                    }
                } catch (e) {}
                if (price !== null && !isNaN(price)) {
                    const cp = document.getElementById('currentPrice');
                    if (cp) {
                        cp.value = price.toFixed(price < 1 ? 4 : 2);
                        // If we had locked the field because it came from session/local storage, allow live updates now
                        if (cp.dataset && cp.dataset.locked === '1') {
                            delete cp.dataset.locked;
                        }
                    }
                }
            } catch (e) {
                // ignore network errors
            }
        }

        // Start live fetches at the same cadence as the home page
        fetchLivePrice();
        setInterval(fetchLivePrice, 5000);

        async function submitOrder() {
            const quantity = parseFloat(document.getElementById('purchaseQuantity').value);
            if (!quantity || quantity < 100) {
                alert('Please enter a valid purchase quantity (minimum 100 USDT)');
                return;
            }

            const delivery = parseInt(document.getElementById('deliveryTime').value, 10);
            const priceRangeText = document.getElementById('priceRange').value || '41%';
            const priceRangePercent = parseInt(priceRangeText.replace(/[^0-9]/g, ''), 10) || 41;
            const direction = tradeDirection;
            const purchasePrice = parseFloat((document.getElementById('currentPrice').value || '').replace(/[^0-9.-]+/g, '')) || 0;

            // Build order payload matching server expectations
            const payload = {
                symbol: '{{ $symbol }}',
                direction,
                delivery_seconds: delivery,
                price_range_percent: priceRangePercent,
                purchase_quantity: quantity,
                purchase_price: purchasePrice
            };

            // Hide modal
            const modalEl = document.getElementById('appointmentModal');
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();

            // Send to server to create a pending order (server will finalize and delete losing orders)
            try {
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const resp = await fetch('/orders', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify(payload)
                });

                if (!resp.ok) {
                    let bodyText = await resp.text();
                    let json = null;
                    try { json = JSON.parse(bodyText); } catch(e){}
                    console.error('Order creation failed', resp.status, bodyText, json);
                    if (resp.status === 419) {
                        alert('Session expired — please refresh and log in again.');
                        return;
                    }
                    if (resp.status === 401) {
                        alert('You must be logged in to place an order.');
                        return;
                    }
                    if (resp.status === 422 && json && json.errors) {
                        alert(Object.values(json.errors).flat().join('\n'));
                        return;
                    }
                    alert(json?.message || 'Failed to create order. Please try again.');
                    return;
                }

                const data = await resp.json();

                // Map server payload (snake_case) to the camelCase fields startCountdownOverlay expects
                // Build order object including both snake_case and camelCase keys and local variables
                const order = {
                    id: data.id,
                    symbol: payload.symbol,
                    direction: payload.direction,
                    // canonical keys (camelCase) expected by overlay
                    delivery: payload.delivery_seconds,
                    purchaseQuantity: payload.purchase_quantity,
                    purchasePrice: payload.purchase_price,
                    priceRangeText: priceRangeText,
                    priceRangePercent: payload.price_range_percent || priceRangePercent,
                    // also include snake_case so older code paths can read them
                    delivery_seconds: payload.delivery_seconds,
                    purchase_quantity: payload.purchase_quantity,
                    purchase_price: payload.purchase_price
                };

                // Debug: log order payload so we can confirm fields in the browser console
                console.log('Created order payload for overlay:', order);
                // Start overlay which simulates per-second price movement and will call finalize when done
                startCountdownOverlay(order);
            } catch (e) {
                console.error('Order creation error', e);
                alert('Network error while creating order.');
            }
        }

        function startCountdownOverlay(order) {
            // Resolve delivery seconds robustly: prefer order.delivery, then snake_case, then DOM, then default 60
            const resolvedDelivery = parseInt(order.delivery || order.delivery_seconds || (document.getElementById('deliveryTime')?.value), 10) || 60;
            const total = resolvedDelivery;
            let remaining = total;

            // Create overlay
            const overlay = document.createElement('div');
            overlay.style.position = 'fixed';
            overlay.style.left = 0;
            overlay.style.right = 0;
            overlay.style.top = 0;
            overlay.style.bottom = 0;
            overlay.style.display = 'flex';
            overlay.style.alignItems = 'center';
            overlay.style.justifyContent = 'center';
            overlay.style.zIndex = 3000;
            overlay.style.background = 'rgba(0,0,0,0.6)';

            // Inner card
            const card = document.createElement('div');
            card.style.width = '360px';
            card.style.maxWidth = '92%';
            card.style.background = 'var(--bg-surface, #fff)';
            card.style.borderRadius = '16px';
            card.style.padding = '22px';
            card.style.boxSizing = 'border-box';
            card.style.color = 'var(--text, #111)';
            card.style.textAlign = 'center';

            // Circular progress (SVG)
            const size = 140;
            const svgNS = 'http://www.w3.org/2000/svg';
            const svg = document.createElementNS(svgNS, 'svg');
            svg.setAttribute('width', size);
            svg.setAttribute('height', size);
            svg.style.display = 'block';
            svg.style.margin = '0 auto 12px';

            const bgCircle = document.createElementNS(svgNS, 'circle');
            const cx = size/2, cy = size/2, r = (size/2) - 8;
            bgCircle.setAttribute('cx', cx);
            bgCircle.setAttribute('cy', cy);
            bgCircle.setAttribute('r', r);
            bgCircle.setAttribute('fill', 'none');
            bgCircle.setAttribute('stroke', 'rgba(0,0,0,0.06)');
            bgCircle.setAttribute('stroke-width', '12');

            const fgCircle = document.createElementNS(svgNS, 'circle');
            fgCircle.setAttribute('cx', cx);
            fgCircle.setAttribute('cy', cy);
            fgCircle.setAttribute('r', r);
            fgCircle.setAttribute('fill', 'none');
            fgCircle.setAttribute('stroke', 'var(--primary, #3b82f6)');
            fgCircle.setAttribute('stroke-width', '12');
            fgCircle.setAttribute('stroke-linecap', 'round');
            fgCircle.setAttribute('transform', `rotate(-90 ${cx} ${cy})`);

            const circumference = 2 * Math.PI * r;
            fgCircle.setAttribute('stroke-dasharray', `${circumference} ${circumference}`);
            fgCircle.setAttribute('stroke-dashoffset', `${circumference}`);

            svg.appendChild(bgCircle);
            svg.appendChild(fgCircle);

            const secondsEl = document.createElement('div');
            secondsEl.style.fontSize = '32px';
            secondsEl.style.fontWeight = '700';
            secondsEl.style.marginBottom = '8px';
            secondsEl.textContent = remaining;

            // Details list (two-column)
            const details = document.createElement('div');
            details.style.textAlign = 'left';
            details.style.marginTop = '8px';

            function addRow(label, value) {
                const row = document.createElement('div');
                row.style.display = 'flex';
                row.style.justifyContent = 'space-between';
                row.style.padding = '6px 0';
                const lab = document.createElement('div'); lab.style.opacity = '0.8'; lab.textContent = label;
                const val = document.createElement('div'); val.style.fontWeight = '600'; val.textContent = value;
                row.appendChild(lab); row.appendChild(val);
                details.appendChild(row);
            }

            addRow('Direction of Purchase', order.direction === 'up' ? 'Up' : 'Down');
            // Resolve purchase quantity/price falling back to DOM values if needed
            const resolvedQuantity = Number(order.purchaseQuantity ?? order.purchase_quantity ?? (document.getElementById('purchaseQuantity')?.value)) || 0;
            const resolvedPurchasePrice = Number(order.purchasePrice ?? order.purchase_price ?? (document.getElementById('currentPrice')?.value)) || 0;

            addRow('Quantity', resolvedQuantity ? resolvedQuantity.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2}) : '0.00');
            addRow('Purchase Price', resolvedPurchasePrice ? resolvedPurchasePrice.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2}) : '0.00');

            // Also show live price placeholder (we will update it from fetchLivePrice if running)
            const livePriceRowLabel = document.createElement('div');
            livePriceRowLabel.style.display = 'flex';
            livePriceRowLabel.style.justifyContent = 'space-between';
            livePriceRowLabel.style.padding = '6px 0';
            const lpLab = document.createElement('div'); lpLab.style.opacity = '0.8'; lpLab.textContent = order.symbol.toUpperCase();
            const lpVal = document.createElement('div'); lpVal.style.fontWeight = '600'; lpVal.textContent = Number(order.purchasePrice).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});
            livePriceRowLabel.appendChild(lpLab); livePriceRowLabel.appendChild(lpVal);
            details.appendChild(livePriceRowLabel);

            addRow('Price range', order.priceRangeText || '');
            addRow('Delivery Time', (order.delivery || order.delivery_seconds || resolvedDelivery) + 'S');

            // Assemble
            card.appendChild(svg);
            card.appendChild(secondsEl);
            card.appendChild(details);
            overlay.appendChild(card);
            document.body.appendChild(overlay);

            // Progress updater with per-second simulated price movement
            const start = Date.now();
            let lastSecond = 0;
            // Track the last price update
            let lastPrice = Number(order.purchase_price || order.purchasePrice || 0);
            
            let priceUpdateInterval;
            let timer;
            
            async function updatePriceFromBackend(progress) {
                try {
                    const token = document.querySelector('meta[name="csrf-token"]').content;
                    const response = await fetch(`/api/trade/${order.id}/price?progress=${progress}`, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': token
                        }
                    });
                    
                    if (response.ok) {
                        const data = await response.json();
                        console.log('Price update:', data); // Debug log
                        
                        if (data.price) {
                            const formattedPrice = Number(data.price).toLocaleString(undefined, {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: order.symbol.toLowerCase() === 'doge' ? 4 : 2
                            });
                            lpVal.textContent = formattedPrice;
                            return true;
                        }
                    }
                } catch (e) {
                    console.error('Price update error:', e);
                }
                return false;
            }

            function tick() {
                const elapsed = Math.floor((Date.now() - start) / 1000);
                remaining = Math.max(total - elapsed, 0);
                
                // Update countdown display
                secondsEl.textContent = remaining;
                const offset = circumference - (elapsed / total) * circumference;
                fgCircle.setAttribute('stroke-dashoffset', Math.max(0, offset));

                // Update price if needed
                if (elapsed !== lastSecond && elapsed <= total) {
                    const progress = (elapsed / total) * 100;
                    updatePriceFromBackend(progress);
                    lastSecond = elapsed;
                }

                // Check if countdown is finished
                if (remaining <= 0) {
                    clearInterval(timer);
                    if (window.priceUpdateInterval) {
                        clearInterval(window.priceUpdateInterval);
                    }
                    showFinalizingState(card);
                    finalizeOrderAndShowResult(order, card, overlay, lpVal, secondsEl);
                }
            }

            // Start the main countdown timer
            timer = setInterval(tick, 250);

            // Start price update interval
            window.priceUpdateInterval = setInterval(function() {
                if (remaining > 0) {
                    const progress = ((total - remaining) / total) * 100;
                    updatePriceFromBackend(progress);
                }
            }, 1000);

            // Run initial tick
            tick();


        }

        function showFinalizingState(card) {
            // remove existing children and show a working state
            while (card.firstChild) card.removeChild(card.firstChild);
            const p = document.createElement('div');
            p.style.fontWeight = '700';
            p.style.marginBottom = '8px';
            p.textContent = 'Finalizing...';
            card.appendChild(p);
        }

        async function finalizeOrderAndShowResult(order, card, overlay, lpVal, secondsEl) {
            try {
                // Clear any existing intervals
                if (window.priceUpdateInterval) {
                    clearInterval(window.priceUpdateInterval);
                }

                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const resp = await fetch(`/orders/${order.id}/finalize`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    }
                });

                const body = await resp.json().catch(() => null);
                if (!resp.ok) {
                    console.error('Finalize failed', resp.status, body);
                    if (resp.status === 419) {
                        showResultCard(card, 'error', 0, 'Session expired', null, null);
                        setTimeout(() => document.body.removeChild(overlay), 2000);
                        return;
                    }
                    showResultCard(card, 'error', 0, body?.message || 'Finalization failed', null, null);
                    setTimeout(() => document.body.removeChild(overlay), 2000);
                    return;
                }

                const result = body?.result || 'lose';
                const profit = Number(body?.profit_amount || 0);
                const payout = Number(body?.payout || 0);
                const finalPrice = body?.final_price ? Number(body.final_price).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2}) : lpVal.textContent;

                // Update small trading timer/result area if present
                try {
                    const profitIsPositive = (typeof profit === 'number' && profit > 0) || result === 'win';
                    const profitColor = profitIsPositive ? '#16A34A' : '#EF4444';
                    const profitAbs = Math.abs(Number(profit) || 0);
                    const profitSign = profitIsPositive && profitAbs > 0 ? '+' : (result === 'lose' && profitAbs > 0 ? '-' : '');

                    const resultStatusEl = document.getElementById('result-status');
                    const profitAmountEl = document.getElementById('profit-amount');
                    const countdownSection = document.getElementById('countdown-section');
                    const resultsSection = document.getElementById('results-section');

                    if (resultStatusEl) {
                        resultStatusEl.textContent = result === 'win' ? 'Win' : (result === 'lose' ? 'Lose' : (result === 'error' ? 'Error' : 'Result'));
                        resultStatusEl.style.color = profitColor;
                    }
                    if (profitAmountEl) {
                        profitAmountEl.textContent = `${profitSign}${profitAbs.toFixed(2)}`;
                        profitAmountEl.style.color = profitColor;
                    }
                    if (countdownSection && resultsSection) {
                        countdownSection.style.display = 'none';
                        resultsSection.style.display = 'block';
                    }
                } catch (e) {
                    console.warn('Failed updating small result UI', e);
                }

                showResultCard(card, result, profit, payout, result === 'win' ? 'Success' : 'You lost', order, finalPrice);
            } catch (e) {
                console.error('Finalize error', e);
                showResultCard(card, 'error', 0, 'Network error');
            }
        }

        function showResultCard(card, result, profit, payout, titleText, tradeOrder, finalPrice) {
            while (card.firstChild) card.removeChild(card.firstChild);

            // Main profit display (color by result)
            const profitDisplay = document.createElement('div');
            const profitIsPositive = (typeof profit === 'number' && profit > 0) || result === 'win';
            const profitColor = profitIsPositive ? '#16A34A' : '#EF4444'; // green for win, red for loss
            profitDisplay.style.color = profitColor;
            profitDisplay.style.fontSize = '28px';
            profitDisplay.style.fontWeight = '700';
            profitDisplay.style.marginBottom = '30px';
            profitDisplay.style.marginTop = '20px';
            // Prefix and label depending on result: show "Profit" for wins (green), "Loss" for losses (red)
            const profitAbs = Math.abs(Number(profit) || 0);
            const profitSign = profitIsPositive && profitAbs > 0 ? '+' : (result === 'lose' && profitAbs > 0 ? '-' : '');
            let label = 'Result';
            if (result === 'win') label = 'Profit';
            else if (result === 'lose') label = 'Loss';
            profitDisplay.textContent = `${label} ${profitSign}$ ${profitAbs.toLocaleString(undefined, {minimumFractionDigits:0, maximumFractionDigits:0})}`;

            // Create a table-like layout for trade details
            const detailsContainer = document.createElement('div');
            detailsContainer.style.width = '100%';
            detailsContainer.style.marginBottom = '20px';

            // Add trade details in rows
            const details = [
                ['Direction of Purchase', tradeOrder.direction === 'up' ? 'Up' : 'Down'],
                ['Quantity', Number(tradeOrder.purchaseQuantity).toLocaleString()],
                ['Purchase Price', Number(tradeOrder.purchasePrice).toLocaleString()],
                [tradeOrder.symbol.toUpperCase(), finalPrice],  // Using final price from backend
                ['Price range', tradeOrder.priceRangeText],
                ['Delivery Time', tradeOrder.delivery + 'S']
            ];

            details.forEach(([label, value]) => {
                const row = document.createElement('div');
                row.style.display = 'flex';
                row.style.justifyContent = 'space-between';
                row.style.padding = '8px 0';
                row.style.borderBottom = '1px solid rgba(255,255,255,0.1)';

                const labelEl = document.createElement('div');
                labelEl.style.color = 'rgba(255,255,255,0.7)';
                labelEl.textContent = label;

                const valueEl = document.createElement('div');
                valueEl.style.fontWeight = '500';
                valueEl.textContent = value;

                row.appendChild(labelEl);
                row.appendChild(valueEl);
                detailsContainer.appendChild(row);
            });

            // Create Close button
            const btn = document.createElement('button');
            btn.className = 'btn btn-primary w-100';
            btn.style.backgroundColor = '#4A7AFF';
            btn.style.borderRadius = '8px';
            btn.style.padding = '12px';
            btn.style.border = 'none';
            btn.textContent = 'Close';
            btn.onclick = () => {
                // Add loading state to button first
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Redirecting...';
                
                // Small delay before removing overlay and redirecting
                setTimeout(() => {
                    // Safely remove overlay
                    try {
                        const overlayEl = card.closest('[style*="position: fixed"]');
                        if (overlayEl && overlayEl.parentElement === document.body) {
                            document.body.removeChild(overlayEl);
                        }
                    } catch (e) {
                        console.error('Error removing overlay:', e);
                    }
                    
                    // Redirect to home page
                    window.location.href = '/';
                }, 500);
            };

            // Add all elements to card
            card.appendChild(profitDisplay);
            card.appendChild(detailsContainer);
            card.appendChild(btn);

            // Style the card container: use light background for readability on mobile
            card.style.backgroundColor = '#ffffff';
            card.style.color = '#111827';
            card.style.borderRadius = '16px';
            card.style.padding = '24px';
            card.style.width = '360px';
            card.style.maxWidth = '90%';
            card.style.margin = '0 auto';

            // Adjust detail rows (they were created with dark-theme styles); convert to light-theme separators
            Array.from(detailsContainer.children).forEach(row => {
                row.style.borderBottom = '1px solid rgba(0,0,0,0.06)';
                if (row.firstChild) row.firstChild.style.color = 'rgba(0,0,0,0.6)';
            });
            // Ensure profit text remains highlighted (warm orange)
            profitDisplay.style.color = '#C26A00';
        }

        // Initialize price range
        document.addEventListener('DOMContentLoaded', updatePriceRange);
        </script>
    @endif
    <!-- Non-symbol (mining) section intentionally removed to avoid showing mining cards on coin pages -->
        
@endsection
