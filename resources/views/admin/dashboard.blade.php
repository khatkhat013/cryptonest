@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-md-3">
            <a href="/admin/deposits" class="text-white" style="text-decoration:none;">
            <div class="card stats-card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title mb-0">Deposits</h5>
                            <h2 class="mt-2 mb-0">2,451</h2>
                        </div>
                        <div>
                            <i class="fas fa-download fa-2x"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="text-white-50">
                            <i class="fas fa-arrow-up"></i> 12% increase
                        </span>
                    </div>
                </div>
            </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="/admin/withdraws" class="text-white" style="text-decoration:none;">
            <div class="card stats-card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title mb-0">Withdraw</h5>
                            <h2 class="mt-2 mb-0">$34.5K</h2>
                        </div>
                        <div>
                            <i class="fas fa-arrow-circle-down fa-2x"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="text-white-50">
                            <i class="fas fa-arrow-up"></i> 8.5% increase
                        </span>
                    </div>
                </div>
            </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="/admin/trading" class="text-white" style="text-decoration:none;">
            <div class="card stats-card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title mb-0">Trading</h5>
                            <h2 class="mt-2 mb-0">1,287</h2>
                        </div>
                        <div>
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="text-white-50">
                            <i class="fas fa-arrow-up"></i> 5.2% increase
                        </span>
                    </div>
                </div>
            </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="/admin/ai-arbitrage" class="text-white" style="text-decoration:none;">
            <div class="card stats-card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title mb-0">AI Arbitrage</h5>
                            <h2 class="mt-2 mb-0">891</h2>
                        </div>
                        <div>
                            <i class="fas fa-robot fa-2x"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="text-white-50">
                            <i class="fas fa-arrow-up"></i> 3.1% increase
                        </span>
                    </div>
                </div>
            </div>
            </a>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Transaction Analytics</h5>
                </div>
                <div class="card-body">
                    <div id="transactionChart" style="height: 350px;"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Wallet Distribution</h5>
                </div>
                <div class="card-body">
                    <div id="walletDistribution" style="height: 350px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recent Activities</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Transaction ID</th>
                                    <th>User</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentActivities ?? [] as $act)
                                <tr>
                                    <td>{{ $act->tx_id ?? ($act->type[0] . str_pad($act->id,5,'0',STR_PAD_LEFT)) }}</td>
                                    <td>{{ $act->user?->name ?? $act->user?->email ?? '—' }}</td>
                                    <td>
                                        @if($act->type === 'deposit')
                                            Deposit
                                        @elseif($act->type === 'withdrawal')
                                            Withdrawal
                                        @else
                                            Trade
                                        @endif
                                    </td>
                                    <td>
                                        @if($act->amount !== null)
                                            {{ rtrim(rtrim(number_format($act->amount, 8, '.', ''), '0'), '.') }} {{ strtoupper($act->coin ?? '') }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $status = strtolower((string)($act->status ?? ''));
                                        @endphp
                                        @if(str_contains($status, 'comp') || $status === 'completed')
                                            <span class="badge bg-success">{{ $act->status }}</span>
                                        @elseif(str_contains($status, 'pend') || $status === 'pending' || $status === 'open')
                                            <span class="badge bg-warning">{{ $act->status }}</span>
                                        @elseif(str_contains($status, 'fail') || $status === 'failed')
                                            <span class="badge bg-danger">{{ $act->status }}</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $act->status }}</span>
                                        @endif
                                    </td>
                                    <td>{{ optional($act->created_at)->format('Y-m-d H:i') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No recent activities found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Deposits removed per request --}}

        {{-- Deposit modal and related controls removed per request --}}
</div>
@endsection

@push('scripts')
<script>
// Transaction Analytics Chart
var transactionOptions = {
    series: [{
        name: 'Transactions',
        data: [30, 40, 35, 50, 49, 60, 70]
    }],
    chart: {
        type: 'area',
        height: 350,
        toolbar: {
            show: false
        }
    },
    dataLabels: {
        enabled: false
    },
    stroke: {
        curve: 'smooth'
    },
    xaxis: {
        categories: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
    },
    tooltip: {
        x: {
            format: 'dd/MM/yy HH:mm'
        }
    }
};

var transactionChart = new ApexCharts(document.querySelector("#transactionChart"), transactionOptions);
transactionChart.render();

// Wallet Distribution Chart
var walletOptions = {
    series: [44, 55, 13, 43],
    chart: {
        type: 'donut',
        height: 350
    },
    labels: ['Bitcoin', 'Ethereum', 'USDT', 'Others'],
    responsive: [{
        breakpoint: 480,
        options: {
            chart: {
                width: 200
            },
            legend: {
                position: 'bottom'
            }
        }
    }]
};

var walletChart = new ApexCharts(document.querySelector("#walletDistribution"), walletOptions);
walletChart.render();
</script>
@endpush

{{-- Edit/Delete deposit scripts removed per request --}}