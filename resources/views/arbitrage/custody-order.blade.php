@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Blue Navbar with Back/History buttons -->
    <div class="card bg-primary text-white border-0 mb-4" style="border-radius: 20px;">
        <div class="card-body p-4">
            <div class="d-flex justify-content-center align-items-center position-relative">
                <a href="{{ url('/arbitrage') }}" class="position-absolute start-0 top-50 translate-middle-y text-white">
                    <i class="bi bi-arrow-left fs-4"></i>
                </a>
                <h3 class="mb-0 text-white text-center">Custody Order</h3>
            </div>
        </div>
    </div>

    <!-- Toggle Buttons -->
    <div class="bg-white rounded-4 p-2 mb-4 shadow-sm">
        <div class="btn-group w-100">
            <button type="button" class="btn btn-lg flex-grow-1 active" id="escrowBtn" onclick="showEscrow()">
                Escrow
            </button>
            <button type="button" class="btn btn-lg flex-grow-1" id="terminationBtn" onclick="showTermination()">
                Termination
            </button>
        </div>
    </div>

    <!-- Escrow Content -->
    <div id="escrowContent">
        @forelse($activePlans as $plan)
            <div class="card shadow-sm border-0 mb-3 rounded-4">
                <div class="card-body p-4">
                    {{-- Plan name badge/header --}}
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="text-muted">Plan</div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="fw-medium">{{ strtoupper($plan->plan_name ?? '') }}</div>
                            @php
                                $st = isset($plan->status) ? strtolower($plan->status) : 'unknown';
                                $badge = $st === 'active' ? 'bg-success' : ($st === 'completed' ? 'bg-secondary' : 'bg-warning');
                            @endphp
                            <span class="status-badge {{ $badge }} text-white">{{ ucfirst($st) }}</span>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="text-muted">Purchase Amount</div>
                        @php
                            $qty = isset($plan->quantity) ? number_format($plan->quantity, 6, '.', '') : '0';
                            $qty = rtrim(rtrim($qty, '0'), '.');
                        @endphp
                        <div class="fw-medium">{{ $qty }} USDT</div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="text-muted">Position Period</div>
                        <div class="fw-medium">{{ $plan->duration_days }}days</div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="text-muted">Yield</div>
                        @php
                            $minPct = isset($plan->pct_min) ? $plan->pct_min : ($plan->daily_revenue_percentage - 0.1);
                            $maxPct = isset($plan->pct_max) ? $plan->pct_max : ($plan->daily_revenue_percentage + 0.1);
                        @endphp
                        <div class="fw-medium">{{ number_format($minPct, 2) }}%-{{ number_format($maxPct, 2) }}%</div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="text-muted">Expected Daily Profit</div>
                        <div class="fw-medium">{{ number_format($plan->expected_daily_profit, 2) }} USDT</div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="text-muted">Buy Time</div>
                        @php
                            $buy = \Carbon\Carbon::parse($plan->created_at)->timezone('America/New_York');
                            $buyFmt = $buy->format($buy->format('u') === '000000' ? 'Y-m-d H:i:s' : 'Y-m-d H:i:s.u');
                        @endphp
                        <div class="fw-medium">{{ $buyFmt }}</div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">End Time</div>
                        @php
                            $end = \Carbon\Carbon::parse($plan->created_at)->addDays($plan->duration_days)->timezone('America/New_York');
                            $endFmt = $end->format($end->format('u') === '000000' ? 'Y-m-d H:i:s' : 'Y-m-d H:i:s.u');
                        @endphp
                        <div class="fw-medium">{{ $endFmt }}</div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-4">
                <p class="mb-0 text-muted">No active plans found</p>
            </div>
        @endforelse
    </div>

    <!-- Termination Content -->
    <div id="terminationContent" style="display: none;">
        @forelse($completedPlans as $plan)
            <div class="card shadow-sm border-0 mb-3 rounded-4">
                <div class="card-body p-4">
                    {{-- Plan name badge/header --}}
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="text-muted">Plan</div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="fw-medium">{{ strtoupper($plan->plan_name ?? '') }}</div>
                            @php
                                $st = isset($plan->status) ? strtolower($plan->status) : 'unknown';
                                $badge = $st === 'active' ? 'bg-success' : ($st === 'completed' ? 'bg-secondary' : 'bg-warning');
                            @endphp
                            <span class="status-badge {{ $badge }} text-white">{{ ucfirst($st) }}</span>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="text-muted">Purchase Amount</div>
                        @php
                            $qty = isset($plan->quantity) ? number_format($plan->quantity, 6, '.', '') : '0';
                            $qty = rtrim(rtrim($qty, '0'), '.');
                        @endphp
                        <div class="fw-medium">{{ $qty }} USDT</div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="text-muted">Position Period</div>
                        <div class="fw-medium">{{ $plan->duration_days }}days</div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="text-muted">Yield</div>
                        @php
                            $minPct = isset($plan->pct_min) ? $plan->pct_min : ($plan->daily_revenue_percentage - 0.1);
                            $maxPct = isset($plan->pct_max) ? $plan->pct_max : ($plan->daily_revenue_percentage + 0.1);
                        @endphp
                        <div class="fw-medium">{{ number_format($minPct, 2) }}%-{{ number_format($maxPct, 2) }}%</div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="text-muted">Acquired earnings</div>
                        {{-- Show only the plan profit (do NOT show principal + profit) --}}
                        <div class="fw-medium">{{ number_format($plan->profit ?? 0, 2) }} USDT</div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="text-muted">Buy Time</div>
                        @php
                            $buy = \Carbon\Carbon::parse($plan->created_at)->timezone('America/New_York');
                            $buyFmt = $buy->format($buy->format('u') === '000000' ? 'Y-m-d H:i:s' : 'Y-m-d H:i:s.u');
                        @endphp
                        <div class="fw-medium">{{ $buyFmt }}</div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">End Time</div>
                        @php
                            $end = \Carbon\Carbon::parse($plan->created_at)->addDays($plan->duration_days)->timezone('America/New_York');
                            $endFmt = $end->format($end->format('u') === '000000' ? 'Y-m-d H:i:s' : 'Y-m-d H:i:s.u');
                        @endphp
                        <div class="fw-medium">{{ $endFmt }}</div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-4">
                <p class="mb-0 text-muted">No completed plans found</p>
            </div>
        @endforelse
    </div>
</div>

@push('styles')
<style>
.btn-group .btn {
    border: none;
    padding: 12px 24px;
    border-radius: 12px !important;
    color: #6c757d;
    transition: all 0.3s ease;
}

.btn-group .btn.active {
    background-color: #0d6efd;
    color: white;
    box-shadow: 0 2px 4px rgba(13, 110, 253, 0.2);
}

.card {
    border: none;
}

.status-badge {
    padding: 4px 8px;
    border-radius: 999px;
    font-size: 0.78rem;
    font-weight: 700;
}
</style>
@endpush

@push('scripts')
<script>
function showEscrow() {
    document.getElementById('escrowBtn').classList.add('active');
    document.getElementById('terminationBtn').classList.remove('active');
    document.getElementById('escrowContent').style.display = 'block';
    document.getElementById('terminationContent').style.display = 'none';
}

function showTermination() {
    document.getElementById('escrowBtn').classList.remove('active');
    document.getElementById('terminationBtn').classList.add('active');
    document.getElementById('escrowContent').style.display = 'none';
    document.getElementById('terminationContent').style.display = 'block';
}

// Show escrow content by default when page loads
document.addEventListener('DOMContentLoaded', function() {
    showEscrow();
});
</script>
@endpush
@endsection
