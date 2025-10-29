@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Header -->
    <div class="bg-primary text-white position-relative mb-4" style="border-radius: 0 0 25px 25px;">
        <div class="container py-3">
            <div class="d-flex justify-content-between align-items-center">
                <a href="{{ url('/arbitrage') }}" class="text-white">
                    <i class="bi bi-arrow-left fs-4"></i>
                </a>
                <h5 class="mb-0">Hosting Details</h5>
                <div class="invisible">
                    <i class="bi bi-arrow-left fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Toggle Buttons -->
    <div class="btn-group w-100 mb-4">
        <button type="button" class="btn btn-lg flex-grow-1 active" id="escrowBtn" onclick="showEscrow()">
            Escrow
        </button>
        <button type="button" class="btn btn-lg flex-grow-1" id="terminationBtn" onclick="showTermination()">
            Termination
        </button>
    </div>

    <!-- Escrow Content -->
    <div id="escrowContent">
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="text-muted">Position Period</div>
                    <div>1days</div>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="text-muted">Purchase Amount</div>
                    <div>2,000.00</div>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="text-muted">Yield</div>
                    <div>1.60%-1.70%</div>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="text-muted">Expected Profit</div>
                    <div>0.00 USDT</div>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="text-muted">Buy Time</div>
                    <div>2025-10-03 21:51:34</div>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">End Time</div>
                    <div>2025-10-04 21:51:34</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Termination Content (Hidden by default) -->
    <div id="terminationContent" style="display: none;">
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="text-muted">Purchase Amount</div>
                    <div>10000.00</div>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="text-muted">Position Period</div>
                    <div>2days</div>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="text-muted">Yield</div>
                    <div>1.90%-2.10%</div>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="text-muted">Acquired earnings</div>
                    <div>386.00 USDT</div>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="text-muted">Buy Time</div>
                    <div>2025-09-10 20:49:24</div>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">End Time</div>
                    <div>2025-09-12 20:49:24</div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.btn-group .btn {
    background-color: #f8f9fa;
    border: none;
    padding: 15px;
    color: #6c757d;
}

.btn-group .btn.active {
    background-color: #0d6efd;
    color: white;
}

.card {
    border-radius: 15px;
}

.text-muted {
    color: #6c757d !important;
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
</script>
@endpush
@endsection