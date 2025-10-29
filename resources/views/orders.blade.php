@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Header Card -->
    <div class="card bg-primary text-white border-0 mb-4" style="border-radius: 20px;">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <a href="{{ url('/trade') }}" class="text-white">
                    <i class="bi bi-arrow-left fs-4"></i>
                </a>
                <h3 class="mb-0 text-white">Orders</h3>
                <div style="width: 24px;"></div> <!-- Spacer for centering -->
            </div>
            
            <!-- Pills -->
            <div class="d-flex justify-content-center">
                <div class="bg-white bg-opacity-10 rounded-pill p-1">
                    <button class="btn btn-sm px-4 py-2 rounded-pill active" id="holdingBtn" onclick="showTab('holding')">
                        Holding Details
                    </button>
                    <button class="btn btn-sm px-4 py-2 rounded-pill" id="historicalBtn" onclick="showTab('historical')">
                        Historical Details
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Holding Details Card -->
    <div class="card shadow-sm" id="holdingCard" style="border-radius: 15px;">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h6 class="mb-1">BTC/USDT</h6>
                    <p class="text-muted mb-0">Long Position</p>
                </div>
                <div class="text-end">
                    <h6 class="mb-1">0.05634 BTC</h6>
                    <p class="text-success mb-0">+2.45%</p>
                </div>
            </div>
            <div class="d-flex justify-content-between text-muted">
                <span>Entry Price</span>
                <span>41,235.00 USDT</span>
            </div>
        </div>
    </div>

    <!-- Historical Details Card -->
    <div class="card shadow-sm d-none" id="historicalCard" style="border-radius: 15px;">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h6 class="mb-1">ETH/USDT</h6>
                    <p class="text-muted mb-0">Closed Position</p>
                </div>
                <div class="text-end">
                    <h6 class="mb-1">1.2 ETH</h6>
                    <p class="text-danger mb-0">-1.23%</p>
                </div>
            </div>
            <div class="d-flex justify-content-between text-muted">
                <span>Exit Price</span>
                <span>2,156.00 USDT</span>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function showTab(tab) {
    // Update buttons
    document.getElementById('holdingBtn').classList.remove('active');
    document.getElementById('historicalBtn').classList.remove('active');
    document.getElementById(tab + 'Btn').classList.add('active');
    
    // Update cards
    document.getElementById('holdingCard').classList.add('d-none');
    document.getElementById('historicalCard').classList.add('d-none');
    document.getElementById(tab + 'Card').classList.remove('d-none');
}

// Style for active pill button
document.addEventListener('DOMContentLoaded', function() {
    const style = document.createElement('style');
    style.textContent = `
        .btn.active {
            background-color: white !important;
            color: var(--bs-primary) !important;
        }
        .btn:not(.active) {
            color: white !important;
        }
    `;
    document.head.appendChild(style);
});
</script>
@endpush
@endsection