@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- Orders Card with Toggle -->
    <div class="card bg-primary text-white border-0 mb-4" style="border-radius: 20px;">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <a href="{{ url('/') }}" class="text-white">
                    <i class="bi bi-arrow-left fs-4"></i>
                </a>
                <h3 class="mb-0 text-white">Orders</h3>
                <div style="width: 24px;"></div>
            </div>
            
            <!-- Toggle Pills -->
            <div class="d-flex justify-content-center">
                <div class="bg-white bg-opacity-10 rounded-pill p-1 d-flex align-items-center" style="gap:6px;">
                    <button class="btn btn-sm flex-fill text-center py-2 rounded-pill" id="holdingBtn" style="border-top-right-radius:0;border-bottom-right-radius:0;">
                        Holding Details
                    </button>
                    <button class="btn btn-sm flex-fill text-center py-2 rounded-pill" id="historicalBtn" style="border-top-left-radius:0;border-bottom-left-radius:0;">
                        Historical Details
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Sections -->
    <div class="order-sections">
        <!-- Holding Details Section -->
        <div id="holdingSection" class="section-content">
            @forelse($holdingOrders as $order)
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            @php $sym = strtolower($order->symbol); $local = public_path('images/icons/' . $sym . '.svg'); @endphp
                            @if(file_exists($local))
                                <img src="{{ asset('images/icons/' . $sym . '.svg') }}" alt="{{ $order->symbol }}" class="me-2" width="24" height="24">
                            @else
                                <img src="{{ asset('images/icons/' . $sym . '.svg') }}" alt="{{ $order->symbol }}" class="me-2" width="24" height="24">
                            @endif
                            <h5 class="mb-0 text-uppercase">{{ $order->symbol }}/USDT</h5>
                        </div>
                        <span class="badge bg-{{ $order->result === 'pending' ? 'warning' : 'danger' }}">
                            {{ ucfirst($order->result) }}
                        </span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <div>
                            <small class="text-muted d-block">Position</small>
                            <span class="badge bg-{{ $order->direction === 'up' ? 'success' : 'danger' }}">
                                {{ ucfirst($order->direction) }}
                            </span>
                        </div>
                        <div class="text-end">
                            <small class="text-muted d-block">Amount</small>
                            <span>{{ number_format($order->purchase_quantity, 2) }} USDT</span>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <div>
                            <small class="text-muted d-block">Entry Price</small>
                            <span>{{ number_format($order->purchase_price, $order->symbol === 'DOGE' ? 4 : 2) }}</span>
                        </div>
                        <div class="text-end">
                            <small class="text-muted d-block">Time</small>
                            <span>{{ $order->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-5">
                <i class="bi bi-hourglass display-4 text-muted"></i>
                <p class="mt-3">No pending orders</p>
            </div>
            @endforelse
        </div>

        <!-- Historical Details Section -->
        <div id="historicalSection" class="section-content" style="display: none;">
            <!-- Stats Cards -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title text-muted">Total Trades</h6>
                            <h3 class="mb-0">{{ $stats['totalTrades'] }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title text-muted">Win Rate</h6>
                            <h3 class="mb-0">{{ $stats['totalTrades'] > 0 ? round(($stats['winningTrades'] / $stats['totalTrades']) * 100, 1) : 0 }}%</h3>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title text-muted">Total Profit</h6>
                            <h3 class="mb-0">{{ number_format($stats['totalProfit'], 2) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title text-muted">Total Payout</h6>
                            <h3 class="mb-0">{{ number_format($stats['totalPayout'], 2) }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Historical Orders -->
            @forelse($historicalOrders as $order)
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            @php $sym = strtolower($order->symbol); $local = public_path('images/icons/' . $sym . '.svg'); @endphp
                            @if(file_exists($local))
                                <img src="{{ asset('images/icons/' . $sym . '.svg') }}" alt="{{ $order->symbol }}" class="me-2" width="24" height="24">
                            @else
                                <img src="{{ asset('images/icons/' . $sym . '.svg') }}" alt="{{ $order->symbol }}" class="me-2" width="24" height="24">
                            @endif
                            <h5 class="mb-0 text-uppercase">{{ $order->symbol }}/USDT</h5>
                        </div>
                        <small class="text-muted">{{ $order->created_at->format('M d, H:i') }}</small>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="badge bg-{{ $order->direction === 'up' ? 'success' : 'danger' }} px-3 py-2">
                            <i class="bi bi-arrow-{{ $order->direction === 'up' ? 'up' : 'down' }}"></i>
                            {{ ucfirst($order->direction) }}
                        </span>
                        <span class="badge bg-{{ $order->result === 'win' ? 'success' : 'danger' }} px-3 py-2">
                            {{ ucfirst($order->result) }}
                        </span>
                    </div>

                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <div class="p-2 rounded bg-light">
                                <small class="d-block text-muted">Entry</small>
                                <span class="fw-bold">{{ number_format($order->purchase_price, $order->symbol === 'DOGE' ? 4 : 2) }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 rounded bg-light">
                                <small class="d-block text-muted">Close</small>
                                <span class="fw-bold">{{ number_format($order->final_price, $order->symbol === 'DOGE' ? 4 : 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted d-block">Amount</small>
                            <span class="fw-bold">{{ number_format($order->purchase_quantity, 2) }} USDT</span>
                        </div>
                        <div class="text-end">
                            <small class="text-muted d-block">{{ $order->result === 'win' ? 'Profit' : 'Loss' }}</small>
                            <span class="fw-bold {{ $order->result === 'win' ? 'text-success' : 'text-danger' }}">
                                {{ $order->result === 'win' ? '+' : '-' }}{{ number_format(abs($order->profit_amount), 2) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-5">
                <i class="bi bi-clock-history display-4 text-muted"></i>
                <p class="mt-3">No trade history found</p>
            </div>
            @endforelse

            @if($historicalOrders->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $historicalOrders->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<style>
.card {
    border-radius: 12px;
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}
.rounded-pill .btn {
    transition: all 0.3s;
    color: rgba(255,255,255,0.8);
}
.rounded-pill .btn.active {
    background-color: #fff;
    color: #0d6efd;
}
.badge {
    font-weight: 500;
    padding: 0.5em 0.8em;
}
.section-content {
    transition: opacity 0.3s ease;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const holdingBtn = document.getElementById('holdingBtn');
    const historicalBtn = document.getElementById('historicalBtn');
    const holdingSection = document.getElementById('holdingSection');
    const historicalSection = document.getElementById('historicalSection');

    // Initialize with holding section active
    holdingBtn.classList.add('active');
    holdingSection.style.display = 'block';
    historicalSection.style.display = 'none';

    function toggleSections(showHolding) {
        if (showHolding) {
            holdingBtn.classList.add('active');
            historicalBtn.classList.remove('active');
            holdingSection.style.opacity = '0';
            historicalSection.style.opacity = '0';
            
            setTimeout(() => {
                holdingSection.style.display = 'block';
                historicalSection.style.display = 'none';
                setTimeout(() => {
                    holdingSection.style.opacity = '1';
                }, 50);
            }, 300);
        } else {
            historicalBtn.classList.add('active');
            holdingBtn.classList.remove('active');
            holdingSection.style.opacity = '0';
            historicalSection.style.opacity = '0';
            
            setTimeout(() => {
                holdingSection.style.display = 'none';
                historicalSection.style.display = 'block';
                setTimeout(() => {
                    historicalSection.style.opacity = '1';
                }, 50);
            }, 300);
        }
    }

    holdingBtn.addEventListener('click', () => toggleSections(true));
    historicalBtn.addEventListener('click', () => toggleSections(false));
});
</script>
@endsection