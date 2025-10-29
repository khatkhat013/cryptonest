@extends('layouts.app')

@push('styles')
<style>
/* Mining page styles (moved out of trade view) */
.bg-primary {
    background: linear-gradient(135deg, #0051ff 0%, #0066ff 100%);
}
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
    <div class="bg-primary text-white position-relative" style="border-radius: 0 0 30px 30px;">
        <div class="container py-3">
            <div class="d-flex justify-content-between align-items-center">
                <a href="{{ url('/wallets') }}" class="text-white text-decoration-none">
                    <i class="bi bi-arrow-left fs-4"></i>
                </a>
                <h5 class="mb-0 text-center flex-grow-1">Mining</h5>
                <a href="{{ url('/financial/record') }}" class="text-white text-decoration-none">
                    <i class="bi bi-clock-history fs-4"></i>
                </a>
            </div>
        </div>
    </div>
    
    <!-- mining sub-cards will appear below Pool Data -->

    <div class="container" style="margin-top: 12px;">
    <div class="mining-card mx-auto" style="max-width:720px;">
            <div class="mining-hero p-3 rounded-top">
                <h4 class="text-white mb-1">Popular Mining</h4>
                <p class="text-white-50 small mb-0">Start earning millions</p>
            </div>
            <div class="mining-panel p-3 rounded-bottom">
                <div class="fund-large text-center text-white mb-3">
                    <div class="small text-muted">Total Liquid Income Fund</div>
                    <div class="display-6 fw-bold mt-2">4,869,671,232.61<span class="ms-1 small text-muted">ETH</span></div>
                </div>

                <div class="row text-center text-white-50 mb-3 stats-row">
                        <div class="col-6 mb-3 text-center">
                            <div class="stat-icon mb-1"><i class="fa-solid fa-cog fa-lg"></i></div>
                            <div class="small">Total Output</div>
                            <div class="h6 mb-0">0.00 ETH</div>
                        </div>
                        <div class="col-6 mb-3 text-center">
                            <div class="stat-icon mb-1"><i class="fa-solid fa-chart-line fa-lg"></i></div>
                            <div class="small">Daily Revenue</div>
                            <div class="h6 mb-0">0.00 ETH</div>
                        </div>
                        <div class="col-6 mb-3 text-center">
                            <div class="stat-icon mb-1"><i class="fa-solid fa-wallet fa-lg"></i></div>
                            <div class="small">Wallet Balance</div>
                            <div class="h6 mb-0">0.00 USDT</div>
                        </div>
                        <div class="col-6 mb-3 text-center">
                            <div class="stat-icon mb-1"><i class="fa-solid fa-user fa-lg"></i></div>
                            <div class="small">Account Balance</div>
                            <div class="h6 mb-0">0.00 USDT</div>
                        </div>
                </div>

                        <div class="d-grid">
                            <button class="btn btn-start btn-lg"><i class="fa-solid fa-play me-2"></i>Start</button>
                        </div>
            </div>
        </div>

        <!-- Acquired earnings -->
        <h5 class="mt-4 mb-2 ps-2">Acquired earnings</h5>
        <!-- Acquired earnings card (image attachment) -->
        <div class="acquired-card mx-auto mt-1" style="max-width:720px;">
            <div class="card-body">

                <div class="top-keys text-white-50">
                    <div class="left">Time</div>
                    <div class="right">Quantity</div>
                </div>

                <div class="center-illustration">
                    <!-- Fallback inline SVG icon similar to attachment to avoid missing asset issues -->
                    <svg class="placeholder-illustration" viewBox="0 0 96 96" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="No data">
                        <defs>
                            <linearGradient id="g1" x1="0" x2="1" y1="0" y2="1">
                                <stop offset="0" stop-color="#ffffff" stop-opacity="0.9"/>
                                <stop offset="1" stop-color="#ffffff" stop-opacity="0.6"/>
                            </linearGradient>
                        </defs>
                        <rect rx="14" width="96" height="96" fill="none"/>
                        <g transform="translate(12,12)" fill="none" stroke="rgba(255,255,255,0.85)" stroke-width="2">
                            <rect x="0" y="0" width="72" height="56" rx="6" stroke-opacity="0.08" fill="rgba(255,255,255,0.03)"/>
                            <path d="M8 14h24" stroke-opacity="0.6" stroke-linecap="round"/>
                            <circle cx="44" cy="28" r="8" stroke-opacity="0.12"/>
                            <path d="M52 36c0 4-3 7-7 7" stroke-opacity="0.18" stroke-linecap="round"/>
                        </g>
                    </svg>
                    <div class="no-data text-white-50">No Data</div>
                </div>
            </div>
        </div>
        <!-- Pool Data -->
        <h5 class="mt-4 mb-2 ps-2">Pool Data</h5>
        <div class="mx-auto" style="max-width:720px;">
            <div class="pool-data-card">
                <div class="pool-inner">
                    <div class="row">
                        <div class="col-8 label">Total Exports</div>
                        <div class="col-4 value">4,869,671,232.6100 ETH</div>
                    </div>
                    <div class="divider"></div>
                    <div class="row">
                        <div class="col-8 label">Valid Nodes</div>
                        <div class="col-4 value">84,987,157</div>
                    </div>
                    <div class="divider"></div>
                    <div class="row">
                        <div class="col-8 label">Participant</div>
                        <div class="col-4 value">1,989,024</div>
                    </div>
                    <div class="divider"></div>
                    <div class="row">
                        <div class="col-8 label">User Revenue</div>
                        <div class="col-4 value">59,871,295.4120 ETH</div>
                    </div>
                </div>
            </div>
        </div>
    
    <!-- Liquid Mining Output -->
    <h5 class="mt-4 mb-2 ps-2">Liquid Mining Output</h5>
    <div class="liquid-card mx-auto" style="max-width:720px;">
        <div class="liquid-inner">
            <div class="liquid-head">Recent outputs</div>
            <div class="liquid-mask mt-2">
                @php
                    // Build a list of sample outputs if real data isn't provided.
                    // We generate 100 rows for smooth scrolling. If you have real data,
                    // pass $liquidOutputs to the view as an array of ['addr'=>..., 'amt'=>...].
                    if (!isset($liquidOutputs) || !is_array($liquidOutputs)) {
                        $liquidOutputs = [];
                        for ($i = 0; $i < 100; $i++) {
                            // deterministic-ish address fragments
                            $hash = substr(md5($i . '_lm'), 0, 12);
                            $addr = '0x' . substr($hash, 0, 6) . '...' . substr($hash, -4);
                            // small amounts between 0.02 and 0.5
                            $amt = number_format((($i % 47) + 20) / 1000, 3);
                            $liquidOutputs[] = ['addr' => $addr, 'amt' => $amt];
                        }
                    }

                    $count = count($liquidOutputs);
                    // duration: ~0.8s per item (slower for many items), min 30s
                    $duration = max(30, intval($count * 0.8));
                @endphp

                <div class="liquid-list" id="liquidList" style="animation: scrollUp {{ $duration }}s linear infinite;">
                    {{-- Render two copies for seamless loop --}}
                    @foreach(array_merge($liquidOutputs, $liquidOutputs) as $o)
                        <div class="liquid-row"><div class="addr">{{ $o['addr'] }}</div><div class="amt">+{{ $o['amt'] }} ETH</div></div>
                    @endforeach
                </div>

                <div class="liquid-fade-top"></div>
                <div class="liquid-fade-bottom"></div>
            </div>
        </div>
    </div>

    <!-- Our Advantages -->
    <h5 class="mt-4 mb-2 ps-2">Our Advantages</h5>
    <div class="advantages-row mx-auto" style="max-width:720px;">
        <div class="adv-card">
            <div class="icon"><i class="fa-solid fa-chart-simple"></i></div>
            <h6>High Yield</h6>
            <p>Competitive daily returns with flexible lock periods.</p>
        </div>
        <div class="adv-card">
            <div class="icon"><i class="fa-solid fa-shield-check"></i></div>
            <h6>Secure</h6>
            <p>Audited smart contracts and secure custody for user funds.</p>
        </div>
        <div class="adv-card">
            <div class="icon"><i class="fa-solid fa-hand-holding-dollar"></i></div>
            <h6>Easy to Use</h6>
            <p>Simple UI to join and withdraw from pools at any time.</p>
        </div>
    </div>

    <!-- Dex Gallery -->
    <h5 class="mt-4 mb-2 ps-2">DEX Audit Organization</h5>
    <div class="dex-row mx-auto" style="max-width:720px;">
        <div class="partner-card dex-item"><img src="/images/dex/logo5.bed2ab3b.png" alt="DEX 1"></div>
        <div class="partner-card dex-item"><img src="/images/dex/logo6.6b082f79.png" alt="DEX 2"></div>
        <div class="partner-card dex-item"><img src="/images/dex/logo7.7031efd9.png" alt="DEX 3"></div>
    </div>

    <!-- Partners -->
    <h5 class="mt-4 mb-2 ps-2">Partners</h5>
    <div class="partners-grid mx-auto" style="max-width:720px;">
        @php
            $partnerDir = public_path('images/partners');
            $partnerFiles = [];
            if (is_dir($partnerDir)) {
                $files = scandir($partnerDir);
                foreach ($files as $f) {
                    if (in_array(strtolower(pathinfo($f, PATHINFO_EXTENSION)), ['png','jpg','jpeg','webp','gif'])) {
                        $partnerFiles[] = $f;
                    }
                }
            }
        @endphp

        @forelse($partnerFiles as $file)
            <div class="partner-card"><img src="{{ asset('images/partners/' . $file) }}" alt="Partner"></div>
        @empty
            <div class="partner-card"><div class="card-body text-muted">No partners found.</div></div>
        @endforelse
    </div>
    </div>
@endsection
