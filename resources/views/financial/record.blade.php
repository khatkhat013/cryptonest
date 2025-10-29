@extends('layouts.app')

@push('styles')
<style>
.transaction-item {
    cursor: pointer;
    transition: background-color 0.3s ease;
    padding: 16px;
    margin-bottom: 12px;
    border: 1px solid rgba(0, 0, 0, 0.05);
    background: white;
}

.transaction-item:hover {
    background: rgba(0, 0, 0, 0.02);
}



@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.status-badge {
    padding: 3px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    margin-top: 4px;
}

.status-completed {
    background: #10B981;
    color: white;
}

.status-pending {
    background: #F59E0B;
    color: white;
}

.status-failed {
    background: #EF4444;
    color: white;
}

/* Status icons */
.status-badge::before {
    content: '';
    display: inline-block;
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: currentColor;
    margin-right: 4px;
    box-shadow: 0 0 0 2px rgba(255,255,255,0.3);
    position: relative;
    top: -1px;
}

/* Status animation */
.status-pending::before {
    animation: pulse 1.5s infinite;
}

@keyframes pulse {
    0% {
        transform: scale(0.8);
        opacity: 0.5;
    }
    50% {
        transform: scale(1.2);
        opacity: 1;
    }
    100% {
        transform: scale(0.8);
        opacity: 0.5;
    }
}

.address-text {
    font-family: 'Roboto Mono', monospace;
    font-size: 12px;
    word-break: break-all;
    color: #666;
    background: rgba(0, 0, 0, 0.03);
    padding: 6px 8px;
    border-radius: 6px;
    margin: 0;
}
</style>
@endpush

@section('content')
<div class="container">
    <!-- Header Card -->
    <div class="card bg-primary text-white border-0 mb-4" style="border-radius: 20px;">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <a href="{{ url('/') }}" class="text-white">
                    <i class="bi bi-arrow-left fs-4"></i>
                </a>
                <h3 class="mb-0 text-white">Financial Record</h3>
                <div style="width: 24px;"></div> <!-- Spacer for centering -->
            </div>

                    <!-- Balance Info: show only total USD across all wallets -->
                    <div class="text-center mt-2">
                        <div class="small text-white-50">Total Balance</div>
                        <h2 class="display-5 mb-1">@if(isset($totalAllUsd))${{ number_format($totalAllUsd, 2) }}@else N/A @endif</h2>
                    </div>
        </div>
    </div>

    <!-- Transaction Type Pills -->
    <div class="d-flex justify-content-center mb-4">
        <div class="bg-white shadow-sm rounded-pill p-1">
            <button class="btn btn-sm px-4 py-2 rounded-pill active" id="depositBtn" onclick="showTab('deposit')">
                Deposit
            </button>
            <button class="btn btn-sm px-4 py-2 rounded-pill" id="withdrawalBtn" onclick="showTab('withdrawal')">
                Withdrawal
            </button>
        </div>
    </div>

    <!-- Deposit Transactions Card -->
    <div class="card shadow-sm" id="depositCard" style="border-radius: 15px;">
        <div class="card-body p-3">
            @forelse($deposits as $dep)
            <div class="transaction-item rounded-3">
                <!-- Main Info -->
                <div class="d-flex justify-content-between main-info">
                    <!-- Left Side -->
                    <div>
                        <h6 class="mb-1">{{ strtoupper($dep->coin) }} Deposit</h6>
                        <p class="text-muted mb-0">{{ $dep->created_at->format('Y-m-d H:i') }}</p>
                    </div>
                    <!-- Right Side -->
                    <div class="text-end">
                        <h6 class="mb-2 text-success">+{{ rtrim(rtrim(number_format($dep->amount, 8, '.', ''), '0'), '.') }} {{ strtoupper($dep->coin) }}</h6>
                        @php
                            $actionStatusId = $dep->action_status_id ?? 1; // Default to pending (1)
                            $statusClass = match($actionStatusId) {
                                1 => 'status-pending',    // pending
                                2 => 'status-failed',     // cancel
                                3 => 'status-failed',     // reject
                                4 => 'status-pending',    // frozen
                                5 => 'status-completed',  // complete
                                default => 'status-pending'
                            };
                            $statusText = match($actionStatusId) {
                                1 => 'Pending',
                                2 => 'Cancelled',
                                3 => 'Rejected',
                                4 => 'Frozen',
                                5 => 'Completed',
                                default => 'Pending'
                            };
                        @endphp
                        <span class="status-badge {{ $statusClass }}">
                            {{ $statusText }}
                        </span>
                    </div>
                </div>

            </div>
            @empty
            <p class="text-muted">No deposits found.</p>
            @endforelse
        </div>
    </div>

    <!-- Withdrawal Transactions Card -->
    <div class="card shadow-sm d-none" id="withdrawalCard" style="border-radius: 15px;">
        <div class="card-body p-3">
            @forelse($withdrawals as $w)
            <div class="transaction-item rounded-3">
                <!-- Main Info -->
                <div class="d-flex justify-content-between main-info">
                    <!-- Left Side -->
                    <div>
                        <h6 class="mb-1">{{ strtoupper($w->coin) }} Withdrawal</h6>
                        <p class="text-muted mb-0">{{ $w->created_at->format('Y-m-d H:i') }}</p>
                    </div>
                    <!-- Right Side -->
                    <div class="text-end">
                        <h6 class="mb-2 text-danger">-{{ rtrim(rtrim(number_format($w->amount, 8, '.', ''), '0'), '.') }} {{ strtoupper($w->coin) }}</h6>
                        @php
                            $actionStatusId = $w->action_status_id ?? 1; // Default to pending (1)
                            $statusClass = match($actionStatusId) {
                                1 => 'status-pending',    // pending
                                2 => 'status-failed',     // cancel
                                3 => 'status-failed',     // reject
                                4 => 'status-pending',    // frozen
                                5 => 'status-completed',  // complete
                                default => 'status-pending'
                            };
                            $statusText = match($actionStatusId) {
                                1 => 'Pending',
                                2 => 'Cancelled',
                                3 => 'Rejected',
                                4 => 'Frozen',
                                5 => 'Completed',
                                default => 'Pending'
                            };
                        @endphp
                        <span class="status-badge {{ $statusClass }}">
                            {{ $statusText }}
                        </span>
                    </div>
                </div>

            </div>
            @empty
            <p class="text-muted">No withdrawals found.</p>
            @endforelse
        </div>
    </div>
</div>

@push('scripts')
<script>

function showTab(tab) {
    // Update buttons
    document.getElementById('depositBtn').classList.remove('active');
    document.getElementById('withdrawalBtn').classList.remove('active');
    document.getElementById(tab + 'Btn').classList.add('active');
    
    // Update cards
    document.getElementById('depositCard').classList.add('d-none');
    document.getElementById('withdrawalCard').classList.add('d-none');
    document.getElementById(tab + 'Card').classList.remove('d-none');
}

// Style for active pill button
document.addEventListener('DOMContentLoaded', function() {
    const style = document.createElement('style');
    style.textContent = `
        .btn.active {
            background-color: var(--bs-primary);
            color: white !important;
        }
        .btn:not(.active) {
            color: var(--bs-primary) !important;
        }
        .transaction-item {
            position: relative;
            z-index: 1;
        }
        .address-details {
            position: relative;
            z-index: 2;
        }
    `;
    document.head.appendChild(style);
});
</script>
@endpush
@endsection