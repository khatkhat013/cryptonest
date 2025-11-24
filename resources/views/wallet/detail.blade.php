@extends('layouts.app')

@push('scripts')
<!-- QR Code Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<!-- QR Code Functionality -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize QR Code
    var qrcode = new QRCode("qrcode", {
        width: 220,
        height: 220,
        colorDark : "#000000",
        colorLight : "#ffffff",
        correctLevel : QRCode.CorrectLevel.H
    });

    // Get elements
    var toggleQRBtn = document.getElementById('toggleQR');
    var qrSection = document.getElementById('qrCodeSection');
    var walletAddress = document.getElementById('walletAddress');
    var isQRVisible = false;

    // Generate initial QR code
    if (walletAddress.value) {
        qrcode.makeCode(walletAddress.value);
    }

    // Update QR code when wallet address changes
    walletAddress.addEventListener('input', function() {
        qrcode.clear();
        qrcode.makeCode(this.value);
    });

    // Toggle QR code visibility
    toggleQRBtn.addEventListener('click', function() {
        isQRVisible = !isQRVisible;
        qrSection.classList.toggle('d-none');
        this.innerHTML = isQRVisible ? 
            '<i class="bi bi-qr-code me-2"></i>Hide QR Code' : 
            '<i class="bi bi-qr-code me-2"></i>Show QR Code';
    });

    // Copy address function
    window.copyAddress = function() {
        walletAddress.select();
        document.execCommand('copy');
        
        // Visual feedback
        const btn = document.querySelector('[onclick="copyAddress()"]');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-check2 me-2"></i>Copied!';
        setTimeout(() => {
            btn.innerHTML = originalText;
        }, 2000);
    };
});
</script>

@push('styles')
<style>
/* Main Header Background */
.bg-primary {
    background: linear-gradient(135deg, #0051ff 0%, #0066ff 100%);
}

body {
    background-color: #F3F4F6;
}

/* Container Styles */
.content-container {
    max-width: 600px;
    margin: 0 auto;
}

/* Card Styles */
.wallet-card {
    background: white;
    border-radius: 1rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

/* Form Controls */
.form-control {
    height: 3.5rem;
    border-radius: 0.75rem;
    border: 1px solid #e5e7eb;
    transition: all 0.3s ease;
    background-color: transparent !important;
}

.form-control:focus {
    background-color: transparent !important;
    border-color: #0066ff;
    box-shadow: none;
}

/* Action Buttons */
.btn-action {
    color: #fff;
    background: transparent;
    border: 1px solid #ddd;
    transition: all 0.3s ease;
}

.btn-action:hover {
    color: #fff !important;
    border-color: #0066ff;
    background: rgba(0, 102, 255, 0.8);
}

.btn-action.active {
    color: #fff !important;
    border-color: #0066ff;
    background: #0066ff;
}

/* Responsive Utilities */
@media (max-width: 768px) {
    .w-md-auto {
        width: auto !important;
    }
    .flex-md-row {
        flex-direction: row !important;
    }
}

/* Input Group Styling */
.input-group {
    border-radius: 0.75rem;
    overflow: hidden;
}

.form-control::placeholder {
    color: #9CA3AF;
}

.form-control:focus {
    border-color: #0066ff;
    box-shadow: none;
    background-color: #ffffff;
}

/* Buttons */
.btn-primary {
    height: 3.5rem;
    border-radius: 0.75rem;
    font-weight: 500;
    transition: all 0.3s ease;
    background-color: #0066ff;
    border-color: #0066ff;
    color: white !important;
}

.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 6px -1px rgba(0, 102, 255, 0.2);
    background-color: #0051ff;
    border-color: #0051ff;
    color: white !important;
}

.btn-outline-primary {
    color: #0066ff;
    border-color: #0066ff;
    background: transparent;
}

.btn-outline-primary:hover {
    color: white;
    background-color: #0066ff;
    border-color: #0066ff;
}

/* Fix for button active states */
.btn:active, .btn:focus {
    color: inherit;
    background-color: inherit;
    border-color: inherit;
    box-shadow: none !important;
}

/* Network badge styling (pill) */
.network-badge {
    padding: .35rem .6rem;
    font-weight: 700;
    border-radius: 999px;
    font-size: .8rem;
    letter-spacing: .02em;
}

/* Center wallet address text inside the input */
#walletAddress {
    text-align: center;
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, 'Roboto Mono', 'Segoe UI Mono', 'Courier New', monospace;
}
</style>
@endpush

@section('content')
<div class="min-vh-100">
    <!-- Header Section -->
    <div class="bg-primary text-white position-relative" style="border-radius: 0 0 30px 30px;">
        <div class="container py-3">
            <div class="d-flex justify-content-between align-items-center">
                <a href="{{ url('/wallets') }}" class="text-white text-decoration-none">
                    <i class="bi bi-arrow-left fs-4"></i>
                </a>
                <h5 class="mb-0 text-center flex-grow-1">
                    {{ strtoupper($type) }} Wallet
                </h5>
                <a href="{{ url('/financial/record') }}" class="text-white text-decoration-none">
                    <i class="bi bi-clock-history fs-4"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mt-4">
        <div class="content-container">
            <div class="wallet-card p-4">
                <div class="text-center mb-4">
                    <h6 class="text-muted mb-3">Available Balance</h6>
                    <div class="d-flex align-items-center justify-content-center gap-2">
                            @php
                                $symIcon = strtolower($type);
                                $localIcon = public_path('images/icons/' . $symIcon . '.svg');
                            @endphp
                            @if(file_exists($localIcon))
                                <img src="{{ asset('images/icons/' . $symIcon . '.svg') }}" alt="{{ $type }}" width="32" height="32">
                            @else
                                <img src="{{ asset('images/icons/' . $symIcon . '.svg') }}" alt="{{ $type }}" width="32" height="32">
                            @endif
                        @php
                            $displayInitial = isset($initialBalance) ? number_format($initialBalance, 8, '.', '') : '0';
                            $displayInitial = rtrim(rtrim($displayInitial, '0'), '.');
                            if ($displayInitial === '') $displayInitial = '0';
                        @endphp
                        <h2 class="mb-0"><span id="headerAvailableBalance">{{ $displayInitial }}</span> {{ strtoupper($type) }}</h2>
                    </div>
                </div>

                {{-- debug block removed: no sensitive info should be rendered in production --}}
            </div>

            <!-- Action Tabs -->
            <div class="position-relative mt-5">
                <!-- Tabs overlapping the card -->
                <div class="d-flex gap-2 position-absolute w-100" style="top: -24px; left: 0;">
                    <button class="btn flex-grow-1 btn-action active" data-tab="receive">
                        <i class="bi bi-download me-2"></i>Receive
                    </button>
                    <button class="btn flex-grow-1 btn-action" data-tab="send">
                        <i class="bi bi-send me-2"></i>Send
                    </button>
                    <button class="btn flex-grow-1 btn-action" data-tab="convert">
                        <i class="bi bi-arrow-left-right me-2"></i>Convert
                    </button>
                </div>
                
                <!-- Card with top padding for tabs -->
                <div class="wallet-card p-4" style="padding-top: 2.5rem !important;">

                <!-- Tab Contents -->
                <div class="tab-content">
                    <!-- Receive Tab -->
                    <div id="receive-tab" class="tab-pane active">
                        <div class="text-center">
                            <h5 class="mb-4 fw-semibold">Deposit Funds</h5>
                            
                            <!-- QR Code Section with Toggle -->
                            <div class="mb-4">
                                <button id="toggleQR" class="btn btn-outline-primary px-4 mb-3">
                                    <i class="bi bi-qr-code me-2"></i>Show QR Code
                                </button>
                                
                                <div id="qrCodeSection" class="d-none">
                                    <div class="bg-white rounded-4 p-4 mb-4 mx-auto" style="max-width: 280px;">
                                        <div class="bg-white rounded-3" style="width: 240px; height: 240px;">
                                            <div id="qrcode" class="d-flex justify-content-center align-items-center h-100"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Wallet Address -->
                            <div class="mb-3">
                                <div class="w-100 mb-2 d-flex justify-content-between align-items-center">
                                    <small class="text-muted">Wallet Address</small>
                                    <div class="d-flex align-items-center">
                                        <small class="text-muted me-2">Network</small>
                                        <span class="badge bg-primary text-white network-badge">{{ !empty($network) ? strtoupper($network) : 'Network' }}</span>
                                    </div>
                                </div>
                                <div class="bg-light rounded-4 p-3 d-flex align-items-center justify-content-between">
                                    <div class="flex-grow-1 me-3">
                                        <input type="text" id="walletAddress" class="form-control border-0 bg-transparent text-center" value="{{ $address ?? '0x1234...5678' }}" readonly>
                                    </div>
                                </div>
                            </div>

                            <!-- Copy Button -->
                            <button class="btn btn-outline-primary px-4" onclick="copyAddress()">
                                <i class="bi bi-clipboard me-2"></i>Copy Address
                            </button>
                        </div>

                        <!-- Top Up Card -->
                        <div class="wallet-card p-4 mt-4">
                            <form method="POST" action="{{ route('wallet.deposit') }}" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="coin" value="{{ strtolower($type) }}">
                                {{-- Persist the displayed wallet address so it can be recorded as sent_address --}}
                                <input type="hidden" name="sent_address" value="{{ $address ?? '' }}">

                                <!-- Image Upload Section -->
                                <div class="upload-section mb-4">
                                    <input type="file" name="image" class="upload-input" id="paymentImage" accept="image/*">
                                    <label for="paymentImage" class="upload-label">
                                        <div class="text-center p-4">
                                            <div class="upload-icon mb-2">
                                                <i class="bi bi-camera text-primary fs-1"></i>
                                            </div>
                                            <div class="text-muted">Upload Payment Screenshot</div>
                                        </div>
                                    </label>
                                </div>

                                <!-- Amount Input -->
                                <div class="mb-4">
                                    <input type="number" step="0.00000001" name="amount" class="form-control" id="topupAmount" placeholder="Please enter your top-up amount" required>
                                </div>

                                <!-- Top Up Button -->
                                <button type="submit" class="btn btn-primary w-100">
                                    Top Up
                                </button>
                            </form>
                        </div>

                        <!-- Warning Message Card -->
                        <div class="wallet-card p-4 mt-4">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <i class="bi bi-exclamation-circle-fill text-warning fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="mb-2 fw-semibold">You know what?</h6>
                                    <p class="text-muted mb-0" style="line-height: 1.6;">
                                        Please do not send other types of assets to the above address. 
                                        This may result in the loss of your assets. After the successful delivery, 
                                        the network node needs to confirm the receipt of the corresponding assets. 
                                        Therefore, when you complete the transfer, please contact the online 
                                        customer service to verify the arrival.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Send Tab -->
                    <div id="send-tab" class="tab-pane">
                        <!-- Main Send Card -->
                        <div class="wallet-card p-4">
                            <form id="withdrawForm" method="POST" action="{{ route('wallet.withdraw') }}">
                                @csrf
                                <input type="hidden" name="coin" value="{{ strtolower($type) }}">

                                <!-- Delivery Address Input -->
                                <div class="mb-4">
                                    <label class="form-label text-muted mb-2">Delivery Address</label>
                                    <div class="address-input-container">
                                        <div class="input-group mb-2 mb-md-0">
                                            <span class="input-group-text border-end-0 bg-light">
                                                <i class="bi bi-wallet2"></i>
                                            </span>
                                            <input id="destinationAddress" name="destination_address" type="text" 
                                                class="form-control border-start-0 ps-0" 
                                                placeholder="Enter wallet address" required>
                                        </div>
                                        <button type="button" 
                                            class="btn btn-outline-secondary paste-button" 
                                            id="pasteAddressBtn" 
                                            title="Paste from clipboard">
                                            <i class="bi bi-clipboard"></i> Paste
                                        </button>
                                    </div>
                                </div>

                                <!-- Quantity Input -->
                                <div class="mb-4">
                                    <label class="form-label text-muted mb-2">Quantity</label>
                                    <div class="input-group">
                                        <span class="input-group-text border-end-0 bg-light">
                                            @php $symIcon = strtolower($type); $localIcon = public_path('images/icons/' . $symIcon . '.svg'); @endphp
                                            @if(file_exists($localIcon))
                                                <img src="{{ asset('images/icons/' . $symIcon . '.svg') }}" alt="{{ $type }}" width="20" height="20">
                                            @else
                                                <img src="{{ asset('images/icons/' . $symIcon . '.svg') }}" alt="{{ $type }}" width="20" height="20">
                                            @endif
                                        </span>
                                        <input id="withdrawAmount" name="amount" type="number" step="0.00000001" class="form-control border-start-0 ps-0" placeholder="0.00" required>
                                    </div>
                                </div>

                                <!-- Fee Information -->
                                <div class="bg-light rounded-4 p-3 mb-4">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted">Withdrawal Fee:</span>
                                        <span class="text-dark fw-medium">1%</span>
                                    </div>
                                </div>

                                <!-- Send Button -->
                                <button type="submit" class="btn btn-primary w-100 mb-3" id="sendNowButton">
                                    Send Now
                                </button>

                                <!-- Inline error placeholder for withdrawal validation (hidden by default) -->
                                <div id="withdrawError" class="alert alert-danger mt-3 {{ session('error') || $errors->any() ? '' : 'd-none' }}" role="alert" style="{{ session('error') || $errors->any() ? 'display:block;' : 'display:none;' }}">
                                    @if(session('error'))
                                        {!! session('error') !!}
                                    @elseif($errors->any())
                                        <ul class="mb-0">
                                            @foreach($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        &nbsp;
                                    @endif
                                </div>

                                <!-- Warning Message -->
                                <div class="alert alert-danger border-0 d-flex align-items-center" 
                                     style="background-color: rgba(239, 68, 68, 0.1);">
                                    <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>
                                    <strong class="text-danger">Do not transfer funds to strangers</strong>
                                </div>

                                <!-- Address Check Reminder -->
                                <div class="alert alert-light border mt-3 mb-0">
                                    <small class="text-muted d-block">
                                        Please check that your shipping address is correct before sending to avoid loss of assets.
                                    </small>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Convert Tab -->
                    <div id="convert-tab" class="tab-pane">
                        <!-- Main Convert Card -->
                <div class="wallet-card p-4">
                    <h6 class="mb-4 fw-semibold">Convert</h6>

                    <!-- From Section -->
                            <div class="mb-4">
                                <label class="form-label text-muted mb-2">From</label>
                                <div class="input-group mb-2">
                                    <span class="input-group-text border-end-0 bg-light">
                                        @php $symIcon = strtolower($type); $localIcon = public_path('images/icons/' . $symIcon . '.svg'); @endphp
                                        @if(file_exists($localIcon))
                                            <img src="{{ asset('images/icons/' . $symIcon . '.svg') }}"
                                        @else
                                            <img src="{{ asset('images/icons/' . $symIcon . '.svg') }}"
                                        @endif
                                             alt="{{ $type }}" width="24" height="24">
                                    </span>
                                    <input type="number" class="form-control border-start-0 ps-0" 
                                           placeholder="Please enter the amount" id="fromAmount">
                                </div>
                                <div class="d-flex justify-content-between align-items-center px-1">
                                    <small class="text-muted">Available: <span id="availableBalance">{{ $displayInitial }}</span> {{ $type }}</small>
                                    <button class="btn btn-link btn-sm text-primary p-0" onclick="setMaxAmount()">Maximum</button>
                                </div>
                            </div>

                            <!-- Exchange Icon -->
                            <div class="text-center mb-4">
                                <i class="bi bi-arrow-down-circle-fill text-primary fs-4"></i>
                            </div>

                            <!-- To Section -->
                            <div class="mb-4">
                                <label class="form-label text-muted mb-2">To</label>

                                <!-- Dropdown: selectable target coins (exclude current wallet coin) (moved above input) -->
                                <div class="mb-3">
                                    <div class="form-floating">
                                        <select id="convertTargetSelect" class="form-select">
                                            @foreach(App\Models\Currency::where('symbol', '!=', strtoupper($type))->get() as $currency)
                                                <option value="{{ $currency->id }}" data-symbol="{{ $currency->symbol }}" {{ $loop->first ? 'selected' : '' }}>
                                                    {{ $currency->name }} ({{ $currency->symbol }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <label for="convertTargetSelect">Convert To</label>
                                    </div>
                                </div>

                                <div class="input-group mb-2">
                                    <span class="input-group-text border-end-0 bg-light" id="toCoinIcon">
                                        @php $u = 'usdt'; $localIcon = public_path('images/icons/' . $u . '.svg'); @endphp
                                        @if(file_exists($localIcon))
                                            <img src="{{ asset('images/icons/' . $u . '.svg') }}"
                                        @else
                                            <img src="{{ asset('images/icons/usdt.svg') }}"
                                        @endif
                                             alt="USDT" width="24" height="24">
                                    </span>
                                    <input type="number" class="form-control border-start-0 ps-0" 
                                           placeholder="0.00" id="toAmount" readonly>
                                </div>
                                <div class="d-flex justify-content-between align-items-center px-1">
                                    <small class="text-muted" id="exchangeRateText">1 {{ strtoupper($type) }} ≈ --</small>
                                </div>
                            </div>

                            <!-- Convert Button -->
                            <button id="convertBtn" class="btn btn-primary w-100 mb-4">
                                Continue
                            </button>

                            <!-- Animation overlay (hidden by default) -->
                            <div id="convertOverlay" style="display:none; position:fixed; left:0; top:0; right:0; bottom:0; background: rgba(0,0,0,0.35); z-index:1050; align-items:center; justify-content:center;">
                                    <div id="convertAnim" style="width:300px; height:300px; background: transparent; display:flex; align-items:center; justify-content:center; border-radius:12px; position:relative;">
                                        <!-- dotlottie removed; show fallback spinner only -->
                                        <!-- Fallback spinner (hidden by default) -->
                                        <div id="convertFallback" style="display:none; position:absolute; inset:0; display:flex; align-items:center; justify-content:center;">
                                            <div style="width:140px; height:140px; border-radius:16px; background: rgba(255,255,255,0.95); display:flex; align-items:center; justify-content:center; box-shadow: 0 6px 20px rgba(0,0,0,0.15);">
                                                <div class="fallback-spinner" style="width:80px; height:80px;"></div>
                                            </div>
                                        </div>
                                    </div>
                            </div>

                            <!-- Info Message -->
                            <div class="alert alert-light border mb-0">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 me-3">
                                        <i class="bi bi-info-circle text-primary"></i>
                                    </div>
                                    <small class="text-muted">
                                        You cannot trade directly between two cryptocurrencies. 
                                        You should first convert one cryptocurrency to USDT and 
                                        then convert USDT to any other cryptocurrency.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('.btn-action');
    const tabPanes = document.querySelectorAll('.tab-pane');

    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            tabButtons.forEach(btn => btn.classList.remove('active'));
            // Add active class to clicked button
            this.classList.add('active');

            // Hide all tab panes
            tabPanes.forEach(pane => pane.classList.remove('active'));
            // Show the selected tab pane
            const tabId = this.getAttribute('data-tab');
            document.getElementById(tabId + '-tab').classList.add('active');
            // If user switched to the send tab, validate and show insufficient balance message immediately
            if (tabId === 'send') {
                try { validateBalanceAndShow(); } catch (e) {}
            }
        });
    });

    // Also support the alternate/legacy tab controls (buttons with class "tab-button")
    // Some templates render a second set of controls using ids like "receive", "send", "convert".
    const navTabButtons = document.querySelectorAll('.tab-button');
    if (navTabButtons && navTabButtons.length) {
        const navPanels = document.querySelectorAll('.tab-panel, .tab-pane');
        navTabButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                navTabButtons.forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                // hide all nav panels
                navPanels.forEach(p => p.classList.remove('active'));

                const target = this.getAttribute('data-tab');
                // Try both panel id patterns: "send" and "send-tab"
                const panel = document.getElementById(target) || document.getElementById(target + '-tab');
                if (panel) panel.classList.add('active');

                if (target === 'send') {
                    try { validateBalanceAndShow(); } catch (e) {}
                }
            });
        });
    }

    // Convert amount calculation (use the same live sources as home page: Coinbase -> Binance -> CoinGecko)
    const fromAmount = document.getElementById('fromAmount');
    const toAmount = document.getElementById('toAmount');
    const rateText = document.getElementById('exchangeRateText');

    async function fetchLivePrice(symbol) {
        // Try local cache first (home page stores latestPrices in localStorage)
        try {
            const stored = JSON.parse(localStorage.getItem('latestPrices') || '{}');
            const entry = stored[symbol.toLowerCase()];
            // treat cached zero prices as invalid (likely seeded or stale)
            if (entry && entry.price !== undefined && entry.price !== null && entry.price !== 0 && (Date.now() - (entry.ts || 0) < 60 * 1000)) {
                const p = parseFloat(entry.price);
                if (!isNaN(p)) return p;
            }
        } catch (e) {}

        // Special-case common stablecoins — treat as USD=1
        try {
            const up = String(symbol).toUpperCase();
            const stableSet = new Set(['USDT','USDC','PYUSD','TETHER','BUSD']);
            if (stableSet.has(up)) return 1.0;
        } catch (e) {}

        // Query server single-source prices (BitCryptoForest)
        try {
            const resp = await fetch('/prices?symbols=' + encodeURIComponent(symbol) + '&prefer=bitcryptoforest');
            if (resp.ok) {
                const j = await resp.json();
                if (j && j.data && j.data[symbol] && j.data[symbol].price !== undefined && j.data[symbol].price !== null) {
                    const amt = parseFloat(j.data[symbol].price);
                    if (!isNaN(amt)) {
                        try {
                            const s = JSON.parse(localStorage.getItem('latestPrices') || '{}');
                            s[symbol.toLowerCase()] = { price: amt, change: j.data[symbol].change ?? 0, ts: Date.now() };
                            localStorage.setItem('latestPrices', JSON.stringify(s));
                        } catch (e) {}
                        return amt;
                    }
                }
            }
        } catch (e) {
            // ignore network errors
        }

        // If direct lookup failed, try common alias forms (USD{SYM}, {SYM}USD)
        try {
            const up = String(symbol).toUpperCase();
            const altCandidates = [ 'USD' + up, up + 'USD' ];
            for (let k = 0; k < altCandidates.length; k++) {
                const alt = altCandidates[k];
                try {
                    const resp2 = await fetch('/prices?symbols=' + encodeURIComponent(alt) + '&prefer=bitcryptoforest');
                    if (!resp2.ok) continue;
                    const j2 = await resp2.json();
                    if (j2 && j2.data && (j2.data[alt] && j2.data[alt].price !== undefined && j2.data[alt].price !== null)) {
                        const amt2 = parseFloat(j2.data[alt].price);
                        if (!isNaN(amt2)) {
                            try {
                                const s = JSON.parse(localStorage.getItem('latestPrices') || '{}');
                                s[symbol.toLowerCase()] = { price: amt2, change: j2.data[alt].change ?? 0, ts: Date.now() };
                                localStorage.setItem('latestPrices', JSON.stringify(s));
                            } catch (e) {}
                            return amt2;
                        }
                    }
                } catch (e) {
                    // ignore and try next
                }
            }
        } catch (e) {}

        return null;
    }

    // Convert logic between two coins: use USD quote for each and compute toAmount = fromAmount * (fromPriceUSD / toPriceUSD)
    const convertTargetSelect = document.getElementById('convertTargetSelect');
    const toCoinIcon = document.getElementById('toCoinIcon');
    let targetCurrencyId = convertTargetSelect ? convertTargetSelect.value : null;
    let targetSymbol = convertTargetSelect ? convertTargetSelect.selectedOptions[0].dataset.symbol : 'USDT';
    // keep a lowercase symbol for image paths
    let targetCoin = targetSymbol.toLowerCase();

    async function updateConversion() {
        const value = parseFloat(fromAmount.value) || 0;
        const fromSymbol = '{{ strtoupper($type) }}';
        const toSymbol = targetSymbol;

        // fetch prices in USD for both symbols

        // Show loading state while fetching
        if (rateText) rateText.textContent = 'Fetching price...';
        toAmount.value = '';
        try {
            const btn = document.getElementById('convertBtn');
            if (btn) btn.disabled = true;
        } catch (e) {}

        let fromPrice = null, toPrice = null, attempts = 0, maxAttempts = 3;
        while ((!fromPrice || !toPrice) && attempts < maxAttempts) {
            [fromPrice, toPrice] = await Promise.all([
                fetchLivePrice(fromSymbol),
                fetchLivePrice(toSymbol)
            ]);
            attempts++;
            if ((!fromPrice || !toPrice) && attempts < maxAttempts) {
                if (rateText) rateText.textContent = 'Retrying price fetch...';
                await new Promise(res => setTimeout(res, 700));
            }
        }

        // If either price is missing, avoid using a huge arbitrary fallback.
        // Special-case stablecoin <-> stablecoin conversions as 1:1, otherwise show unavailable.
        if (!fromPrice || !toPrice) {
            const fromLc = fromSymbol.toLowerCase();
            const toLc = toSymbol.toLowerCase();
            const stableSet = new Set(['usdt', 'usdc', 'pyusd', 'tether', 'busd']);

            if (stableSet.has(fromLc) && stableSet.has(toLc)) {
                // Stablecoin to stablecoin should be roughly 1:1 — show input value.
                toAmount.value = value ? value.toFixed(6) : '';
                if (rateText) rateText.textContent = `1 ${fromSymbol} ≈ 1 ${toSymbol}`;
            } else {
                // Unknown prices — clear target amount and indicate unavailable rate.
                toAmount.value = '';
                if (rateText) rateText.textContent = `Price unavailable. Click Convert again to retry.`;
            }

            // Ensure Convert button is enabled so user can retry
            try {
                const btn = document.getElementById('convertBtn');
                btn && (btn.disabled = false);
            } catch (e) {}

            return;
        }

        // Compute conversion: fromAmount (in FROM coin) -> USD -> TO coin
        const usdValue = value * fromPrice;
        const toAmountVal = usdValue / toPrice;
        toAmount.value = toAmountVal.toFixed(8);

        if (rateText) rateText.textContent = `1 ${fromSymbol} ≈ ${ (fromPrice / toPrice) < 1 ? (fromPrice / toPrice).toFixed(8) : (fromPrice / toPrice).toFixed(6) } ${toSymbol}`;

        // update toCoinIcon image
        if (toCoinIcon) {
            const img = toCoinIcon.querySelector('img');
            if (img) img.src = `{{ asset('images/icons') }}/${targetSymbol.toLowerCase()}.svg`;
        }
    }

    // handle dropdown changes
    if (convertTargetSelect) {
        convertTargetSelect.addEventListener('change', function(e) {
            targetCurrencyId = this.value;
            targetSymbol = this.selectedOptions[0].dataset.symbol;
            const img = toCoinIcon.querySelector('img');
            if (img) img.src = `{{ asset('images/icons') }}/${targetSymbol.toLowerCase()}.svg`;
            updateConversion();
        });
    }

    // Initialize DOM elements and variables
    let walletBalance = 0.00;
    const headerAvailable = document.getElementById('headerAvailableBalance');
    const availableSpan = document.getElementById('availableBalance');
    const withdrawErrorEl = document.getElementById('withdrawError');
    const withdrawForm = document.getElementById('withdrawForm');
    const sendNowBtn = document.getElementById('sendNowButton');
    const destinationAddressInput = document.getElementById('destinationAddress');
    const pasteAddressBtn = document.getElementById('pasteAddressBtn');
    const coinName = '{{ strtoupper($type) }}';

    // Setup clipboard paste functionality
    if (pasteAddressBtn && destinationAddressInput) {
        pasteAddressBtn.addEventListener('click', async () => {
            try {
                const text = await navigator.clipboard.readText();
                if (text) {
                    destinationAddressInput.value = text.trim();
                }
            } catch (err) {
                console.error('Failed to read clipboard:', err);
            }
        });
    }

    // Tab functionality
    function initializeTabs() {
        const tabButtons = document.querySelectorAll('.btn-action[data-tab]');
        const tabPanes = document.querySelectorAll('.tab-pane');

        tabButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const tabId = this.getAttribute('data-tab');
                
                // Update button states
                tabButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                
                // Update tab pane visibility
                tabPanes.forEach(pane => pane.classList.remove('active'));
                const targetPane = document.getElementById(tabId + '-tab');
                if (targetPane) targetPane.classList.add('active');
            });
        });
    }

    // Error handling functions
    function showWithdrawError(msg) {
        if (!withdrawErrorEl) return;
        withdrawErrorEl.classList.remove('d-none');
        withdrawErrorEl.classList.remove('alert-success');
        withdrawErrorEl.classList.add('alert-danger');
        withdrawErrorEl.innerHTML = msg;
        withdrawErrorEl.style.display = 'block';
        try { withdrawErrorEl.scrollIntoView({behavior: 'smooth', block: 'center'}); } catch(e) {}
    }

    function clearWithdrawError() {
        if (!withdrawErrorEl) return;
        withdrawErrorEl.classList.add('d-none');
        withdrawErrorEl.style.display = 'none';
    }

    // Balance validation
    function validateBalanceAndShow() {
        try {
            const amtEl = document.getElementById('withdrawAmount');
            if (!amtEl) return;

            const requested = parseFloat(amtEl.value || '0');
            if (isNaN(requested) || requested <= 0) {
                if (sendNowBtn) sendNowBtn.disabled = true;
                return;
            }

            const feeRate = 0.01;
            const requiredTotal = requested + (requested * feeRate);
            
            if (requiredTotal > (walletBalance || 0)) {
                showWithdrawError('Insufficient balance for ' + coinName + '.');
                if (sendNowBtn) sendNowBtn.disabled = true;
            } else {
                clearWithdrawError();
                if (sendNowBtn) sendNowBtn.disabled = false;
            }
        } catch (e) {
            console.error('Error in validateBalanceAndShow:', e);
            if (sendNowBtn) sendNowBtn.disabled = true;
        }
    }

    // Add form submit prevention when balance is insufficient
    if (withdrawForm) {
        withdrawForm.addEventListener('submit', function(e) {
            const amtEl = document.getElementById('withdrawAmount');
            if (!amtEl) return;

            const requested = parseFloat(amtEl.value || '0');
            const feeRate = 0.01;
            const requiredTotal = requested + (requested * feeRate);

            if (requiredTotal > (walletBalance || 0)) {
                e.preventDefault();
                showWithdrawError('Insufficient balance for ' + coinName + '.');
                if (sendNowBtn) sendNowBtn.disabled = true;
                return false;
            }
        });
    }

    // Initialize page functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tabs
        initializeTabs();

        // Set up amount validation
        const withdrawAmountInput = document.getElementById('withdrawAmount');
        if (withdrawAmountInput) {
            withdrawAmountInput.addEventListener('input', validateBalanceAndShow);
            validateBalanceAndShow();
        }
    });

    // Set up amount input validation
    const withdrawAmountInput = document.getElementById('withdrawAmount');
    if (withdrawAmountInput) {
        withdrawAmountInput.addEventListener('input', validateBalanceAndShow);
        // Run initial validation
        validateBalanceAndShow();
    }

    async function fetchWalletBalance() {
        try {
            // include credentials so session cookie is sent (Laravel session auth)
            const resp = await fetch(`/api/wallet/balance/{{ strtolower($type) }}`, {
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json' }
            });
            if (resp.ok) {
                const j = await resp.json();
                const b = parseFloat(j.balance) || 0.00;
                walletBalance = b;
                if (headerAvailable) headerAvailable.textContent = b.toFixed(8).replace(/(?:\.0+|(?<=\.[0-9]*[1-9])0+)$/, '');
                if (availableSpan) availableSpan.textContent = b.toFixed(8).replace(/(?:\.0+|(?<=\.[0-9]*[1-9])0+)$/, '');
            }
        } catch (e) {
            console.warn('Could not fetch wallet balance', e);
        }
    }

    // (No duplicate selectMax link — Maximum button uses setMaxAmount())

    fromAmount.addEventListener('input', updateConversion);
    // initial update
    // set initial icon
    if (toCoinIcon) {
        const img = toCoinIcon.querySelector('img');
    if (img) img.src = `{{ asset('images/icons') }}/${targetSymbol.toLowerCase()}.svg`;
    }
    // Fetch the wallet balance and then run conversion update so available is shown
    fetchWalletBalance().then(() => { updateConversion(); validateBalanceAndShow(); });

            // Convert button: POST conversion and show animation overlay for 3 seconds
    const convertBtn = document.getElementById('convertBtn');
    const convertOverlay = document.getElementById('convertOverlay');
    const convertFallback = document.getElementById('convertFallback');
    if (convertBtn) {
        convertBtn.addEventListener('click', async function(e) {
            e.preventDefault();

            const fromAmt = parseFloat(fromAmount.value) || 0;
            const toAmt = parseFloat(toAmount.value);
            const fromCurrency = {{ App\Models\Currency::where('symbol', strtoupper($type))->first()->id }};
            const toCurrency = parseInt(targetCurrencyId);

            if (fromAmt <= 0) {
                alert('Please enter an amount to convert');
                return;
            }

            // Basic client-side validation: ensure we calculated a sensible to-amount
            if (!isFinite(toAmt) || toAmt === null || toAmt <= 0) {
                alert('Conversion price unavailable or invalid. Please try again later.');
                return;
            }

            // show overlay and fallback spinner
            if (convertOverlay) {
                convertOverlay.style.display = 'flex';
                if (convertFallback) convertFallback.style.display = 'flex';
            }

            // send POST to server
            try {
                const tokenMeta = document.querySelector('meta[name="csrf-token"]');
                const csrf = tokenMeta ? tokenMeta.getAttribute('content') : '';

                const resp = await fetch('/wallet/convert', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ from_currency_id: fromCurrency, to_currency_id: toCurrency, from_amount: fromAmt, to_amount: toAmt })
                });
                // show animation for 3 seconds regardless of response (UI requirement)
                setTimeout(() => {
                    if (convertOverlay) convertOverlay.style.display = 'none';
                    if (convertFallback) convertFallback.style.display = 'none';
                }, 3000);

                if (!resp.ok) {
                    const j = await resp.json().catch(() => null);
                    const msg = j && j.message ? j.message : 'Conversion failed';
                    alert(msg);
                    return;
                }

                const json = await resp.json();
                if (json && json.success) {
                    // refresh wallet balance after conversion
                    await fetchWalletBalance();
                    updateConversion();

                    // Clear input boxes so values don't persist after successful conversion
                    try {
                        fromAmount.value = '';
                        toAmount.value = '';
                    } catch (e) {}
                } else {
                    const msg = json && json.message ? json.message : 'Conversion failed';
                    alert(msg);
                }
            } catch (err) {
                console.error('Conversion POST failed', err);
                alert('Conversion request failed');
                if (convertOverlay) convertOverlay.style.display = 'none';
                if (convertFallback) convertFallback.style.display = 'none';
            }
        });
    }

    function setMaxAmount() {
        const maxAmount = walletBalance || 0.00;
        fromAmount.value = maxAmount;
        fromAmount.dispatchEvent(new Event('input'));
    }
    // Expose to global scope to support inline onclick attribute in template
    window.setMaxAmount = setMaxAmount;

    // Image upload preview
    const paymentImage = document.getElementById('paymentImage');
    const uploadLabel = document.querySelector('.upload-label');
    const originalContent = uploadLabel.innerHTML;

    paymentImage.addEventListener('change', function(e) {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                uploadLabel.innerHTML = `
                    <div class="text-center p-2">
                        <img src="${e.target.result}" class="img-fluid rounded-3" style="max-height: 150px;">
                        <div class="text-muted mt-2">Click to change image</div>
                    </div>
                `;
            }
            
            reader.readAsDataURL(this.files[0]);
        } else {
            uploadLabel.innerHTML = originalContent;
        }
    });

    // Paste address button handler and amount input validation
    document.addEventListener('DOMContentLoaded', function() {
        const pasteBtn = document.getElementById('pasteAddressBtn');
        const destInput = document.getElementById('destinationAddress');
        const amountInput = document.getElementById('withdrawAmount');

                if (pasteBtn && destInput) {
            pasteBtn.addEventListener('click', async function() {
                try {
                    const text = await navigator.clipboard.readText();
                    if (text) {
                        destInput.value = text.trim();
                    } else {
                        // show small inline message if needed (fallback to alert)
                        const withdrawErrorEl = document.getElementById('withdrawError');
                        if (withdrawErrorEl) {
                            withdrawErrorEl.classList.remove('d-none');
                            withdrawErrorEl.innerHTML = 'Clipboard is empty or does not contain text';
                            withdrawErrorEl.style.display = 'block';
                        } else {
                            alert('Clipboard is empty or does not contain text');
                        }
                    }
                } catch (e) {
                    const withdrawErrorEl = document.getElementById('withdrawError');
                    if (withdrawErrorEl) {
                        withdrawErrorEl.classList.remove('d-none');
                        withdrawErrorEl.innerHTML = 'Unable to read from clipboard. Please allow clipboard permissions or paste manually.';
                        withdrawErrorEl.style.display = 'block';
                    } else {
                        alert('Unable to read from clipboard. Please allow clipboard permissions or paste manually.');
                    }
                }
            });
        }

        if (amountInput) {
            // prevent non-numeric input via keypress (allows decimal point)
            amountInput.addEventListener('keypress', function(e) {
                const char = String.fromCharCode(e.which || e.keyCode);
                if (!/[0-9.]|\b/.test(char)) {
                    e.preventDefault();
                }
            });
        }

        // Log form submission data
        if (withdrawForm) {
            withdrawForm.addEventListener('submit', function(e) {
                const formData = new FormData(withdrawForm);
                console.log('=== FORM BEING SUBMITTED ===');
                for (let [key, value] of formData.entries()) {
                    console.log(`${key}: ${value}`);
                }
                console.log('walletBalance:', walletBalance);
                console.log('============================');
            });
        }
    });
});

function copyAddress() {
    const codeEl = document.querySelector('code');
    if (!codeEl) return;
    const address = codeEl.textContent.trim();

    navigator.clipboard.writeText(address).then(() => {
        const btn = event.currentTarget;
        const originalHTML = btn.innerHTML;

        btn.innerHTML = '<i class="bi bi-check-lg me-2"></i>Copied!';
        btn.classList.remove('btn-outline-primary');
        btn.classList.add('btn-success');

        setTimeout(() => {
            btn.innerHTML = originalHTML;
            btn.classList.remove('btn-success');
            btn.classList.add('btn-outline-primary');
        }, 2000);
    }).catch(() => {
        alert('Could not copy address to clipboard.');
    });
}
</script>
@endpush

@push('styles')
<style>
/* Action Buttons */
.btn-action {
    padding: 0.75rem;
    border: 1px solid #e5e7eb;
    border-radius: 0.75rem;
    background: white;
    color: #6B7280;
    font-weight: 600;
    transition: all 0.3s ease;
    height: 48px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    font-size: 0.95rem;
}

.btn-action:hover {
    background-color: white;
    border-color: var(--primary);
    color: var(--primary);
    transform: translateY(-1px);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.btn-action.active {
    background-color: var(--primary);
    border-color: var(--primary);
    color: white;
    transform: translateY(-1px);
    box-shadow: 0 4px 6px rgba(59, 130, 246, 0.25);
}

/* Tab Content */
.tab-pane {
    display: none;
}

.tab-pane.active {
    display: block;
}

/* Upload Section Styles */
.upload-section {
    border: 2px dashed #e5e7eb;
    border-radius: 1rem;
    position: relative;
    transition: all 0.3s ease;
    background: #f9fafb;
    cursor: pointer;
}

.upload-section:hover {
    border-color: var(--primary);
    background: #f3f4f6;
}

.upload-input {
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    opacity: 0;
    cursor: pointer;
}

.upload-label {
    cursor: pointer;
    margin: 0;
    width: 100%;
}

.upload-icon {
    width: 64px;
    height: 64px;
    margin: 0 auto;
    background: white;
    border-radius: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

/* Form Control Styles */
.form-control {
    height: 52px;
    border: 1px solid #e5e7eb;
    padding: 0.75rem 1rem;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
}

.form-control::placeholder {
    color: #9CA3AF;
    font-size: 0.95rem;
}

/* Input Group Styles */
.input-group-text {
    background-color: #F9FAFB;
    border-color: #e5e7eb;
    color: #6B7280;
    padding: 0.75rem;
}

.input-group .form-control {
    border-color: #e5e7eb;
}

.input-group .form-control:focus {
    border-color: var(--primary);
    border-left-color: var(--primary) !important;
}

.input-group .form-control:focus + .input-group-text {
    border-color: var(--primary);
}

/* Alert Styles */
.alert {
    border-radius: 0.75rem;
}

.alert-danger {
    color: #DC2626;
    background-color: rgba(239, 68, 68, 0.1);
}

.alert-light {
    background-color: #F9FAFB;
    border-color: #e5e7eb !important;
}

/* Warning Card Styles */
.text-warning {
    color: #F59E0B !important;
}

.text-muted {
    color: #6B7280 !important;
}
</style>
@endpush
@endsection

@push('styles')
<style>
/* Main Header Background */
.bg-primary {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
}



/* Form Controls */
.form-control {
    border: 1px solid var(--border);
    transition: all 0.3s ease;
    height: 56px;
}

.form-control:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 0.25rem rgba(var(--primary-rgb), 0.1);
}

.form-floating > .form-control:focus ~ label,
.form-floating > .form-control:not(:placeholder-shown) ~ label {
    color: var(--bs-gray-500);
    transform: scale(0.85) translateY(-0.75rem) translateX(0.15rem);
}

/* QR Code Section */
.qr-code {
    border-radius: 8px;
}

.qr-container img {
    border: 8px solid white;
}

/* Upload Section */
.upload-input {
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    opacity: 0;
    cursor: pointer;
}

.upload-label {
    cursor: pointer;
    transition: all 0.3s ease;
}

.upload-label:hover {
    background-color: rgba(var(--primary-rgb), 0.05);
}

/* Alert Customization */
.alert {
    border-radius: 1rem;
    border: none;
}

.alert-success {
    background-color: rgba(0, 200, 81, 0.1);
    color: #00c851;
}

.alert-danger {
    background-color: rgba(255, 0, 0, 0.1);
    color: #ff0000;
}
</style>
@endpush

@section('content')
<div class="min-vh-100">
    <!-- Header Section -->
    <div class="bg-primary text-white position-relative" style="border-radius: 0 0 30px 30px; overflow: hidden; margin-bottom: -25px;">
        <!-- Background waves -->
        <div class="position-absolute w-100 h-100" style="opacity: 0.1;">
            <svg viewBox="0 0 500 150" preserveAspectRatio="none" style="height: 100%; width: 100%;">
                <path d="M0.00,49.98 C262.07,71.55 325.35,12.35 500.00,49.98 L500.00,150.00 L0.00,150.00 Z" style="stroke: none; fill: white;"></path>
            </svg>
        </div>

        <div class="container position-relative">
            <!-- Header Navigation -->
            <div class="d-flex justify-content-between align-items-center" style="padding-top: 0.5rem;">
                <a href="{{ url('/wallets') }}" class="text-white text-decoration-none">
                    <i class="bi bi-arrow-left fs-4"></i>
                </a>
                <h5 class="mb-0 text-center flex-grow-1">
                    {{ strtoupper($type) }} wallet
                </h5>
                <div class="invisible">
                    <i class="bi bi-arrow-left fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="container">

        <div class="container position-relative">
            <!-- Header Navigation -->
            <div class="d-flex justify-content-between align-items-center" style="padding-top: 0.5rem;">
                <a href="{{ url('/wallets') }}" class="text-white text-decoration-none">
                    <i class="bi bi-arrow-left fs-4"></i>
                </a>
                <h5 class="mb-0 text-center flex-grow-1">
                    {{ strtoupper($type) }} wallet
                </h5>
                <div class="invisible">
                    <i class="bi bi-arrow-left fs-4"></i>
                </div>
            </div>

            <!-- Balance Information -->
            <div class="text-center py-2">
                <h2 class="display-6 mb-2">US$ 0.00</h2>
                <div class="d-flex align-items-center justify-content-center mb-2">
                    @php $symIcon = strtolower($type); $localIcon = public_path('images/icons/' . $symIcon . '.svg'); @endphp
                    @if(file_exists($localIcon))
                        <img src="{{ asset('images/icons/' . $symIcon . '.svg') }}" alt="{{ strtoupper($type) }}" width="32" height="32" class="me-2">
                    @else
                        <img src="{{ asset('images/icons/' . $symIcon . '.svg') }}" alt="{{ strtoupper($type) }}" width="32" height="32" class="me-2">
                    @endif
                    <div class="fs-5">Available <span>0.00{{ strtoupper($type) }}</span></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="container mt-2">
        <div class="nav-tabs-container mb-3 bg-white rounded-4 p-2 shadow" style="position: relative; z-index: 1;">
            <div class="d-flex justify-content-between gap-2">
                <button class="tab-button flex-grow-1 active" data-tab="receive">Receive</button>
                <button class="tab-button flex-grow-1" data-tab="send">Send</button>
                <button class="tab-button flex-grow-1" data-tab="convert">Convert</button>
            </div>
        </div>

        <!-- Tab Panels -->
        <div id="receive" class="tab-panel active">
            <!-- Unified Card for QR, Amount, and Top Up -->
            <div class="card border-0 rounded-4 shadow-sm mb-3">
            <div class="card-body p-4">
                <!-- QR Code Section -->
                <div class="text-center mb-4">
                    <div class="bg-light rounded-4 p-4 mb-4">
                        <div class="d-inline-block bg-white p-3 rounded-3 shadow-sm">
                            <img src="{{ asset('images/qr-placeholder.png') }}" alt="QR Code" class="qr-code" width="200" height="200">
                        </div>
                        <div class="bg-white rounded-3 p-3 mt-3">
                            <span class="text-secondary font-monospace">
                                17t7dK5Xr6iqCNbLt8gV2fyE56Rhhprf7J
                            </span>
                        </div>
                        <button class="btn btn-outline-primary px-4 mt-3" onclick="copyAddress()">
                            Copy Address
                        </button>
                    </div>
                </div>

                <!-- Top Up Form Section -->
                <div class="bg-light rounded-4 p-4">
                    <!-- Upload Section -->
                    <div class="bg-white rounded-4 mb-4 position-relative overflow-hidden shadow-sm">
                        <input type="file" class="upload-input" accept="image/*" id="chargeImage">
                        <label for="chargeImage" class="upload-label d-flex flex-column align-items-center py-4 mb-0">
                            <div class="upload-icon mb-2">
                                <i class="bi bi-camera text-primary fs-3"></i>
                            </div>
                            <span class="text-muted">Upload Payment Screenshot</span>
                        </label>
                    </div>

                    <!-- Amount Input -->
                    <div class="mb-4">
                        <div class="form-floating">
                            <input type="number" class="form-control bg-white border-1 text-dark rounded-4" 
                                   id="topupAmount" placeholder="Enter amount">
                            <label for="topupAmount" class="text-muted">Please enter your top-up amount</label>
                        </div>
                    </div>

                    <!-- Top Up Button -->
                    <button class="btn btn-primary btn-lg w-100 rounded-4">
                        Top Up
                    </button>
                </div>
            </div>
        </div>

        <!-- Send Tab Panel -->
        <div id="send" class="tab-panel">
            <div class="card border-0 rounded-4 shadow-sm mb-3">
                <div class="card-body p-3">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="recipientAddress" placeholder="Enter recipient's address">
                        <label for="recipientAddress">Recipient's Address</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="number" class="form-control" id="sendAmount" placeholder="Enter amount">
                        <label for="sendAmount">Amount to Send</label>
                    </div>
                    <button class="btn btn-primary w-100 rounded-4">Send</button>
                </div>
            </div>
        </div>

        <!-- Convert Tab Panel -->
        <div id="convert" class="tab-panel">
            <div class="card border-0 rounded-4 shadow-sm mb-3">
                <div class="card-body p-3">
                    <div class="form-floating mb-3">
                        <select class="form-select" id="convertTo">
                            <option value="btc">Bitcoin (BTC)</option>
                            <option value="eth">Ethereum (ETH)</option>
                            <option value="usdt">Tether (USDT)</option>
                        </select>
                        <label for="convertTo">Convert To</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="number" class="form-control" id="convertAmount" placeholder="Enter amount">
                        <label for="convertAmount">Amount to Convert</label>
                    </div>
                    <button class="btn btn-primary w-100 rounded-4">Convert</button>
                </div>
            </div>
        </div>

        <!-- Warning Message -->
                <div class="alert alert-light border rounded-4 mb-0">
                    <div class="d-flex">
                        <div class="flex-shrink-0 me-3">
                            <i class="bi bi-exclamation-circle-fill text-warning fs-5"></i>
                        </div>
                        <div>
                            <h6 class="alert-heading text-dark mb-2">You know what?</h6>
                            <p class="text-muted small mb-0">
                                Please do not send other types of assets to the above address. 
                                This may result in the loss of your assets. After the successful delivery, 
                                the network node needs to confirm the receipt of the corresponding assets. 
                                Therefore, when you complete the transfer, please contact the online 
                                customer service to verify the arrival.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>


        function copyAddress() {
            const address = '17t7dK5Xr6iqCNbLt8gV2fyE56Rhhprf7J';
            navigator.clipboard.writeText(address).then(() => {
                const btn = event.currentTarget;
                const originalText = btn.textContent;
                const originalBg = btn.style.backgroundColor;
                
                // Add copied animation
                btn.style.backgroundColor = '#10b981';
                btn.style.borderColor = '#10b981';
                btn.style.color = '#fff';
                btn.textContent = 'Copied!';
                
                // Add success icon
                const checkIcon = document.createElement('i');
                checkIcon.className = 'bi bi-check-lg ms-1';
                btn.appendChild(checkIcon);
                
                setTimeout(() => {
                    // Reset button
                    btn.style.backgroundColor = originalBg;
                    btn.style.borderColor = '#3b82f6';
                    btn.style.color = '#3b82f6';
                    btn.textContent = originalText;
                }, 2000);
            });
        }
    </script>
    @endpush
</div>
@endsection

@push('styles')
<style>
/* Main Header Background */
.bg-primary {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
}



/* QR Section */
.qr-code {
    border-radius: 8px;
}

.tab-button.active {
    background: var(--primary);
    color: white;
}

.tab-button.active::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 3px;
    background: var(--primary);
}

.tab-panel {
    display: none;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.tab-panel.active {
    display: block;
    opacity: 1;
}

/* Button Styles */
.btn-outline-primary {
    border-color: var(--primary);
    color: var(--primary);
    font-weight: 500;
    transition: all 0.2s ease;
}

.btn-outline-primary:hover {
    background-color: var(--primary);
    border-color: var(--primary);
    color: white;
}

/* Upload Section */
.upload-input {
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    opacity: 0;
    cursor: pointer;
}

.upload-label {
    cursor: pointer;
    transition: all 0.3s ease;
}

.upload-label:hover {
    background-color: rgba(var(--primary-rgb), 0.05);
}

/* Form Controls */
.form-control {
    border: 1px solid var(--border);
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 0.25rem rgba(var(--primary-rgb), 0.1);
}

.camera-button {
    width: 100%;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 0.75rem;
    padding: 2rem;
    cursor: pointer;
    transition: all 0.2s ease;
}

.camera-button:hover {
    border-color: #2563eb;
}

.camera-icon {
    font-size: 2rem;
    color: #6b7280;
    margin-bottom: 0.5rem;
}

/* Form Controls */
.form-control {
    border: 1px solid #e5e7eb;
    border-radius: 0.75rem;
    padding: 0.75rem 1rem;
}

.form-control:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 1px rgba(37, 99, 235, 0.1);
}

/* Submit Button */
.btn-submit {
    background: #10b981;
    color: white;
    border: none;
    border-radius: 0.75rem;
    padding: 0.75rem 1.5rem;
    font-weight: 500;
    transition: all 0.2s ease;
}

.btn-submit:hover {
    background: #059669;
}

/* Form Controls */
.form-control {
    height: 56px;
}

.form-control:focus {
    background-color: rgba(0, 0, 0, 0.3);
    box-shadow: none;
    border-color: var(--primary);
}

.form-floating > .form-control:focus ~ label,
.form-floating > .form-control:not(:placeholder-shown) ~ label {
    color: var(--bs-gray-500);
    transform: scale(0.85) translateY(-0.75rem) translateX(0.15rem);
}

/* Upload Section */
.upload-section {
    border: 2px dashed rgba(255, 255, 255, 0.1);
    transition: all 0.3s ease;
}

.upload-section:hover {
    border-color: var(--primary);
}

.upload-input {
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    opacity: 0;
    cursor: pointer;
}

.upload-label {
    cursor: pointer;
    margin: 0;
}


.alert-dark {
    background-color: rgba(0, 0, 0, 0.2);
    border: none;
}

/* Button States */
.btn-outline-primary:hover {
    background-color: var(--primary);
    border-color: var(--primary);
}

/* QR Code Container */
.qr-container img {
    border: 8px solid white;
}

.address-container {
    word-break: break-all;
}
</style>
@endpush

@push('styles')
<style>
.fallback-spinner {
    border-radius: 50%;
    border: 8px solid #e9eef7;
    border-top-color: #1366ff;
    animation: fallback-spin 1s linear infinite;
}

@keyframes fallback-spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>
@endpush