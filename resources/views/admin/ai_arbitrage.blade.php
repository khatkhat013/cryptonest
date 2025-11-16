@extends('layouts.admin')
@section('content')
<div class="container-fluid">
    <style>
        .ai-arb-header-container { position: sticky; top: 0; z-index: 10; background-color: #fff; padding-top: 0; margin-bottom: 0; }
        .ai-arb-table-wrapper { overflow-y: auto; max-height: calc(100vh - 200px); }
    </style>
    <div class="ai-arb-header-container">
        <h3 class="mb-4">AI Arbitrage Plans</h3>
    </div>
    <div class="card">
        <div class="card-body table-responsive ai-arb-table-wrapper">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Plan</th>
                        <th>Amount</th>
                        <th>Profit Rate</th>
                        <th>Duration (hrs)</th>
                        <th>Status</th>
                        <th>Started</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($plans as $p)
                    <tr>
                        <td>{{ $p->id }}</td>
                        <td>{{ $p->user_name ?? '—' }}</td>
                        <td>{{ strtoupper(data_get($p, 'plan_name') ?? data_get($p, 'plan') ?? data_get($p, 'name') ?? '—') }}</td>
                        @php
                            // amount can be stored in different columns depending on migrations: amount, quantity
                            $rawAmount = data_get($p, 'amount');
                            if (is_null($rawAmount)) $rawAmount = data_get($p, 'quantity');

                            // profit rate may be named profit_rate, daily_revenue_percentage, daily_pct, or profit
                            $rawProfitRate = data_get($p, 'profit_rate');
                            if (is_null($rawProfitRate)) $rawProfitRate = data_get($p, 'daily_revenue_percentage');
                            if (is_null($rawProfitRate)) $rawProfitRate = data_get($p, 'daily_pct');
                            if (is_null($rawProfitRate)) $rawProfitRate = data_get($p, 'profit');

                            // duration: prefer hours, fall back to days * 24
                            $durationHours = data_get($p, 'duration_hours');
                            if (is_null($durationHours)) {
                                $durationDays = data_get($p, 'duration_days');
                                if (!is_null($durationDays)) $durationHours = intval($durationDays) * 24;
                            }
                            $durationHours = $durationHours ?? 0;

                            // normalize numeric values
                            $amountDisplay = '0';
                            if (!is_null($rawAmount) && is_numeric($rawAmount)) {
                                // show up to 8 decimals but trim trailing zeros
                                $amountDisplay = rtrim(rtrim(number_format($rawAmount, 8, '.', ''), '0'), '.');
                            }

                            $profitDisplay = '0';
                            if (!is_null($rawProfitRate) && is_numeric($rawProfitRate)) {
                                $profitDisplay = rtrim(rtrim(number_format($rawProfitRate, 4, '.', ''), '0'), '.');
                            }
                        @endphp

                        <td>${{ $amountDisplay }}</td>
                        <td>{{ $profitDisplay }}%</td>
                        <td>{{ $durationHours }}</td>
                        <td>
                            @php
                                $status = strtolower($p->status ?? 'pending');
                                $badge = 'bg-secondary';
                                if (in_array($status, ['active','running','started'])) $badge = 'bg-info';
                                if (in_array($status, ['completed','finished'])) $badge = 'bg-success';
                                if (in_array($status, ['failed','cancelled','rejected'])) $badge = 'bg-danger';
                            @endphp
                            <span class="badge rounded-pill {{ $badge }}">{{ ucfirst($status) }}</span>
                        </td>
                        @php
                            // Try several fields for started timestamp: started_at, started, created_at, updated_at
                            $started = data_get($p, 'started_at');
                            if (is_null($started)) $started = data_get($p, 'started');
                            if (is_null($started)) $started = data_get($p, 'created_at');
                            if (is_null($started)) $started = data_get($p, 'updated_at');
                            if ($started) {
                                try {
                                    $startedFormatted = \Carbon\Carbon::parse($started)->format('Y-m-d H:i');
                                } catch (\Exception $e) {
                                    $startedFormatted = '—';
                                }
                            } else {
                                $startedFormatted = '—';
                            }
                        @endphp
                        <td>{{ $startedFormatted }}</td>
                        <td class="d-flex gap-2">
                            <a href="{{ url('/admin/ai-arbitrage/' . $p->id . '/edit') }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form method="POST" action="{{ url('/admin/ai-arbitrage/' . $p->id) }}" onsubmit="return confirm('Delete this plan?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="text-center text-muted">No arbitrage plans found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Custom Pagination (single control) -->
    <div class="d-flex flex-column align-items-center mt-4">
        <nav aria-label="Arbitrage plans pagination">
            <ul class="pagination mb-2">
                {{-- Previous --}}
                <li class="page-item {{ $plans->onFirstPage() ? 'disabled' : '' }}">
                    @if($plans->onFirstPage())
                        <span class="page-link">« Previous</span>
                    @else
                        <a class="page-link" href="{{ $plans->previousPageUrl() }}" rel="prev">« Previous</a>
                    @endif
                </li>

                @foreach(range(1, $plans->lastPage()) as $page)
                    @if($page == $plans->currentPage())
                        <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                    @elseif($page == 1 || $page == $plans->lastPage() || abs($page - $plans->currentPage()) <= 1)
                        <li class="page-item"><a class="page-link" href="{{ $plans->url($page) }}">{{ $page }}</a></li>
                    @elseif($page == 2 && $plans->currentPage() > 3)
                        <li class="page-item disabled"><span class="page-link">…</span></li>
                    @elseif($page == $plans->lastPage() - 1 && $plans->currentPage() < $plans->lastPage() - 2)
                        <li class="page-item disabled"><span class="page-link">…</span></li>
                    @endif
                @endforeach

                {{-- Next --}}
                <li class="page-item {{ $plans->hasMorePages() ? '' : 'disabled' }}">
                    @if($plans->hasMorePages())
                        <a class="page-link" href="{{ $plans->nextPageUrl() }}" rel="next">Next »</a>
                    @else
                        <span class="page-link">Next »</span>
                    @endif
                </li>
            </ul>
        </nav>

        {{-- Result count intentionally removed --}}
    </div>
</div>
{{-- View modal and scripts removed per request --}}
@endsection
