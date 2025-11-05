@extends('layouts.app')

@section('content')
<div class="main-content" style="padding-top: 65px">
    <div class="container">
        <!-- Merged header card with centered pill toggle -->
        <div class="card bg-primary text-white border-0 mb-5 merged-top-card" style="border-radius: 20px; position: relative;">
            <div class="card-body p-3 d-flex justify-content-between align-items-center">
                <a href="{{ url('/') }}" class="text-white">
                    <i class="bi bi-arrow-left fs-4"></i>
                </a>
                <h3 class="mb-0 text-white">Transaction History</h3>
                <div style="width: 24px;"></div>
            </div>

            <!-- Centered pill toggle sits inside the same card -->
            <div class="nav-tabs-container centered p-2 shadow" style="position:absolute; left:50%; transform:translateX(-50%); bottom:-22px; z-index:2;">
                <div class="d-flex gap-2 align-items-center">
                    <button class="tab-button active" data-tab="conversion">Conversions</button>
                    <button class="tab-button" data-tab="mining">Mining</button>
                </div>
            </div>
        </div>

    <div id="conversion" class="tab-panel active">
            @if(!empty($conversionItems))
                @foreach($conversionItems as $conv)
                <div class="card mb-3" style="border-radius:12px;">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-semibold">{{ $conv['from_amount'] }} {{ $conv['from_symbol'] ?? '' }} → {{ $conv['to_amount'] }} {{ $conv['to_symbol'] ?? '' }}</div>
                            <div class="text-muted small">{{ $conv['created_at'] }}</div>
                            <div class="text-muted small">Status: {{ ucfirst($conv['status']) }}</div>
                        </div>
                        <div class="text-end">
                            @if($conv['from_usd'] !== null)
                                <div class="small text-muted">From ≈ ${{ number_format($conv['from_usd'], 2) }}</div>
                            @endif
                            @if($conv['to_usd'] !== null)
                                <div class="small text-muted">To ≈ ${{ number_format($conv['to_usd'], 2) }}</div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            @else
                <div class="card mb-3"><div class="card-body text-muted">No conversions found.</div></div>
            @endif
    </div>

    <div id="mining" class="tab-panel">
            @if(!empty($miningRecords))
                @foreach($miningRecords as $m)
                <div class="card mb-3" style="border-radius:12px;">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            @php
                                // Format quantity to remove trailing zeros (e.g. 2500.000000 => 2500)
                                $qty = isset($m['quantity']) ? number_format($m['quantity'], 6, '.', '') : null;
                                if ($qty !== null) {
                                    $qty = rtrim(rtrim($qty, '0'), '.');
                                } else {
                                    $qty = '0';
                                }
                                // created_at may be a Carbon instance or string
                                try {
                                    $created = \Carbon\Carbon::parse($m['created_at'])->format('Y-m-d H:i:s');
                                } catch (\Exception $e) {
                                    $created = $m['created_at'];
                                }
                            @endphp
                            <div class="fw-semibold">Plan: {{ $m['plan_name'] ?? 'N/A' }} ({{ $qty }})</div>
                            <div class="text-muted small">{{ $created }}</div>
                        </div>
                        <div class="text-end">
                            @php
                                $status = isset($m['status']) ? strtolower($m['status']) : 'unknown';
                                $badgeClass = $status === 'active' ? 'bg-success' : ($status === 'completed' ? 'bg-secondary' : 'bg-warning');
                            @endphp
                            <div class="small"><span class="badge {{ $badgeClass }} text-white">{{ ucfirst($status) }}</span></div>
                        </div>
                    </div>
                </div>
                @endforeach
            @else
                <div class="card mb-3"><div class="card-body text-muted">No mining records found.</div></div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabPanels = document.querySelectorAll('.tab-panel');

    function showTab(name) {
        tabButtons.forEach(b => b.classList.remove('active'));
        tabPanels.forEach(p => p.classList.remove('active'));

        const btn = document.querySelector('.tab-button[data-tab="' + name + '"]');
        const panel = document.getElementById(name);
        if (btn) btn.classList.add('active');
        if (panel) panel.classList.add('active');
    }

    tabButtons.forEach(btn => {
        btn.addEventListener('click', function (e) {
            const name = btn.getAttribute('data-tab');
            showTab(name);
        });
    });

    // If URL contains ?tab=mining or #mining, open that tab on load
    try {
        const params = new URLSearchParams(window.location.search);
        const tabParam = params.get('tab') || (window.location.hash ? window.location.hash.replace('#','') : null);
        if (tabParam) {
            // sanitize: only allow 'mining' or 'conversion'
            const allowed = ['mining','conversion'];
            if (allowed.includes(tabParam)) {
                showTab(tabParam);
            }
        }
    } catch (e) {
        // ignore
    }
});
</script>
@endpush

@push('styles')
<style>
/* Toggle pill container */
.nav-tabs-container {
    display: inline-block;
    background: linear-gradient(90deg,#0ea5e9,#2563eb); /* bright blue to deeper blue */
    padding: 6px;
    border-radius: 999px;
}

.nav-tabs-container .tab-button {
    border: none;
    background: transparent;
    color: rgba(255,255,255,0.95);
    padding: 8px 22px;
    border-radius: 999px;
    font-weight: 600;
    transition: all 220ms ease;
}

.nav-tabs-container .tab-button:not(.active) {
    color: rgba(255,255,255,0.85);
    opacity: 0.9;
}

/* Inner active pill */
.nav-tabs-container .tab-button.active {
    background: #061025; /* deep navy for contrast */
    color: #fff;
    box-shadow: 0 6px 18px rgba(2,6,23,0.35) inset, 0 6px 16px rgba(2,6,23,0.12);
    transform: translateY(-2px);
}

/* Slight spacing and layout */
.nav-tabs-container .tab-button + .tab-button {
    margin-left: 6px;
}

/* Tab panel visibility: only show active panel */
.tab-panel { display: none; }
.tab-panel.active { display: block; }

/* smaller screens adjustment */
@media (max-width: 576px) {
    .nav-tabs-container { padding: 4px; }
    .nav-tabs-container .tab-button { padding: 6px 12px; font-size: 0.9rem; }
}

/* Merged header tweaks */
.merged-top-card { padding-bottom: 40px; }
.nav-tabs-container.centered { box-shadow: 0 10px 28px rgba(2,6,23,0.25); border-radius: 999px; }

/* Ensure content below doesn't overlap the centered pill */
.container { position: relative; }
.content-wrapper { margin-top: 18px; }
</style>
@endpush

@endsection
