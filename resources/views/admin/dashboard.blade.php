@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-md-3">
            <a href="/admin/deposits" class="text-white" style="text-decoration:none;">
            <div class="card stats-card bg-primary text-white">
                <div class="card-body text-center">
                    <h5 class="card-title mb-0">Deposits</h5>
                    <h2 class="mt-2 mb-0">{{ number_format($depositsCount ?? 0) }}</h2>
                    @if(isset($depositsNew) && $depositsNew > 0)
                        <div class="mt-1"><span class="badge rounded-pill bg-light text-dark">New {{ $depositsNew }}</span></div>
                    @endif
                    <div class="mt-2">
                        <i class="fas fa-download fa-2x"></i>
                    </div>
                </div>
            </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="/admin/withdraws" class="text-white" style="text-decoration:none;">
            <div class="card stats-card bg-success text-white">
                <div class="card-body text-center">
                    <h5 class="card-title mb-0">Withdraw</h5>
                    <h2 class="mt-2 mb-0">{{ number_format($withdrawalsCount ?? 0) }}</h2>
                    @if(isset($withdrawalsNew) && $withdrawalsNew > 0)
                        <div class="mt-1"><span class="badge rounded-pill bg-light text-dark">New {{ $withdrawalsNew }}</span></div>
                    @endif
                    <div class="mt-2">
                        <i class="fas fa-arrow-circle-down fa-2x"></i>
                    </div>
                </div>
            </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="/admin/trading" class="text-white" style="text-decoration:none;">
            <div class="card stats-card bg-warning text-white">
                <div class="card-body text-center">
                    <h5 class="card-title mb-0">Trading</h5>
                    <h2 class="mt-2 mb-0">{{ number_format($tradesCount ?? 0) }}</h2>
                    @if(isset($tradesNew) && $tradesNew > 0)
                        <div class="mt-1"><span class="badge rounded-pill bg-light text-dark">New {{ $tradesNew }}</span></div>
                    @endif
                    <div class="mt-2">
                        <i class="fas fa-chart-line fa-2x"></i>
                    </div>
                </div>
            </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="/admin/ai-arbitrage" class="text-white" style="text-decoration:none;">
            <div class="card stats-card bg-info text-white">
                <div class="card-body text-center">
                    <h5 class="card-title mb-0">AI Arbitrage</h5>
                    <h2 class="mt-2 mb-0">{{ number_format($aiArbCount ?? 0) }}</h2>
                    @if(isset($aiArbNew) && $aiArbNew > 0)
                        <div class="mt-1"><span class="badge rounded-pill bg-light text-dark">New {{ $aiArbNew }}</span></div>
                    @endif
                    <div class="mt-2">
                        <i class="fas fa-robot fa-2x"></i>
                    </div>
                </div>
            </div>
            </a>
        </div>
    </div>
    <!-- Charts removed per request -->

    <!-- Recent Activities -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recent Activities</h5>
                </div>
                <div class="card-body">
                    <style>
                        /* Limit the height of the recent activities table and make only the body scrollable */
                        .recent-activities-body { max-height: 420px; overflow-y: auto; }
                        .recent-activities-body table thead th { position: sticky; top: 0; z-index: 3; background-color: #fff; }
                        /* Ensure small screens still behave reasonably */
                        @media (max-width: 576px) { .recent-activities-body { max-height: 300px; } }
                    </style>

                    <div class="table-responsive recent-activities-body">
                        <table class="table table-hover mb-0">
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
{{-- Charts removed per request: no dashboard chart scripts rendered --}}
@endpush

{{-- Edit/Delete deposit scripts removed per request --}}