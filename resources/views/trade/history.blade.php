@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title text-muted">Total Trades</h6>
                    <h3 class="mb-0">{{ $totalTrades }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title text-muted">Win Rate</h6>
                    <h3 class="mb-0">{{ $totalTrades > 0 ? round(($winningTrades / $totalTrades) * 100, 1) : 0 }}%</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title text-muted">Total Profit</h6>
                    <h3 class="mb-0">{{ number_format($totalProfit, 2) }} USDT</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title text-muted">Total Payout</h6>
                    <h3 class="mb-0">{{ number_format($totalPayout, 2) }} USDT</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Trade History</h5>
            <div class="btn-group">
                <button type="button" class="btn btn-sm btn-outline-secondary">Export</button>
                <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown">
                    <span class="visually-hidden">Toggle Dropdown</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#">CSV</a></li>
                    <li><a class="dropdown-item" href="#">Excel</a></li>
                    <li><a class="dropdown-item" href="#">PDF</a></li>
                </ul>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="px-3">
                @forelse($trades as $trade)
                    <div class="card mb-3 trade-card">
                        <div class="card-body">
                            <!-- Header with Symbol and Date -->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="d-flex align-items-center">
                                    @php $sym = strtolower($trade->symbol); $local = public_path('images/icons/' . $sym . '.svg'); @endphp
                                    @if(file_exists($local))
                                        <img src="{{ asset('images/icons/' . $sym . '.svg') }}" alt="{{ $trade->symbol }}" class="coin-icon me-2" width="24" height="24">
                                    @else
                                        <img src="{{ asset('images/icons/' . $sym . '.svg') }}" alt="{{ $trade->symbol }}" class="coin-icon me-2" width="24" height="24">
                                    @endif
                                    <h5 class="mb-0 text-uppercase">{{ $trade->symbol }}/USDT</h5>
                                </div>
                                <small class="text-muted">
                                    {{ $trade->created_at->format('M d, H:i') }}
                                </small>
                            </div>

                            <!-- Direction and Result -->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="badge bg-{{ $trade->direction === 'up' ? 'success' : 'danger' }} px-3 py-2">
                                    <i class="bi bi-arrow-{{ $trade->direction === 'up' ? 'up' : 'down' }}"></i>
                                    {{ ucfirst($trade->direction) }}
                                </span>
                                <span class="badge bg-{{ $trade->result === 'win' ? 'success' : ($trade->result === 'lose' ? 'danger' : 'warning') }} px-3 py-2">
                                    {{ ucfirst($trade->result) }}
                                </span>
                            </div>

                            <!-- Price Info -->
                            <div class="row g-2 mb-2">
                                <div class="col-6">
                                    <div class="price-box p-2 rounded bg-light">
                                        <small class="d-block text-muted">Entry</small>
                                        <span class="fw-bold">{{ number_format($trade->purchase_price, $trade->symbol === 'DOGE' ? 4 : 2) }}</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="price-box p-2 rounded bg-light">
                                        <small class="d-block text-muted">Close</small>
                                        <span class="fw-bold">{{ number_format($trade->final_price, $trade->symbol === 'DOGE' ? 4 : 2) }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Amount and Profit -->
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted d-block">Amount</small>
                                    <span class="fw-bold">{{ number_format($trade->purchase_quantity, 2) }} USDT</span>
                                </div>
                                <div class="text-end">
                                    <small class="text-muted d-block">Profit</small>
                                    <span class="fw-bold {{ $trade->result === 'win' ? 'text-success' : 'text-danger' }}">
                                        {{ $trade->result === 'win' ? '+' : '-' }}{{ number_format($trade->profit_amount, 2) }}
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
            </div>

            <style>
            .coin-icon {
                width: 24px;
                height: 24px;
                object-fit: contain;
            }
            .trade-card {
                border-radius: 12px;
                transition: transform 0.2s;
            }
            .trade-card:hover {
                transform: translateY(-2px);
            }
            .price-box {
                background-color: var(--bs-light);
                border-radius: 8px;
            }
            .badge {
                font-weight: 500;
                font-size: 0.85rem;
            }
            @media (max-width: 768px) {
                .container {
                    padding-left: 10px;
                    padding-right: 10px;
                }
                .card-body {
                    padding: 1rem;
                }
            }
            </style>
        </div>
        @if($trades->hasPages())
        <div class="card-footer">
            {{ $trades->links() }}
        </div>
        @endif
    </div>
</div>

<style>
.table td, .table th {
    padding: 1rem;
    vertical-align: middle;
}
.badge {
    padding: 0.5em 0.8em;
    font-weight: 500;
}
</style>
@endsection