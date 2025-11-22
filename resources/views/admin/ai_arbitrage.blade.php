@extends('layouts.admin')
@section('content')
<div class="container-fluid">
    <style>
        /* Modern admin page header */
        .admin-page-header {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            color: white;
            padding: 2rem 1.5rem;
            border-radius: 16px;
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px rgba(67, 233, 123, 0.2);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 2rem;
            flex-wrap: wrap;
        }

        .admin-page-header h1 {
            font-size: 1.75rem;
            font-weight: 800;
            margin: 0;
            letter-spacing: -0.5px;
        }

        .admin-page-header .header-icon {
            font-size: 2.5rem;
            opacity: 0.3;
        }

        /* Modern card styling */
        .admin-card {
            border: 0;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .admin-card:hover {
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
        }

        .admin-card .card-header {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            border: none;
            padding: 1.5rem;
            color: white;
        }

        .admin-card .card-header h5 {
            margin: 0;
            font-weight: 700;
            font-size: 1.1rem;
        }

        /* Table styling */
        .admin-table {
            margin-bottom: 0;
        }

        .admin-table thead th {
            background-color: #f8f9fa;
            border: none;
            border-bottom: 2px solid #e9ecef;
            font-weight: 700;
            color: #495057;
            padding: 1rem 0.75rem;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .admin-table tbody tr {
            border: none;
            border-bottom: 1px solid #f0f0f0;
            transition: background-color 0.2s ease;
        }

        .admin-table tbody tr:hover {
            background-color: #fafbfc;
        }

        .admin-table tbody td {
            padding: 1rem 0.75rem;
            font-size: 0.9rem;
            color: #495057;
            vertical-align: middle;
        }

        /* Badge styling */
        .admin-badge {
            padding: 0.5rem 0.85rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.8rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .admin-badge.info {
            background-color: #d1ecf1;
            color: #0c5460;
            box-shadow: 0 2px 8px rgba(23, 162, 184, 0.2);
        }

        .admin-badge.success {
            background-color: #d4edda;
            color: #155724;
            box-shadow: 0 2px 8px rgba(34, 197, 94, 0.2);
        }

        .admin-badge.danger {
            background-color: #f8d7da;
            color: #721c24;
            box-shadow: 0 2px 8px rgba(239, 68, 68, 0.2);
        }

        .admin-badge.secondary {
            background-color: #e2e3e5;
            color: #383d41;
        }

        /* Button styling */
        .admin-btn {
            border-radius: 8px;
            border: none;
            padding: 0.5rem 1rem;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.2s ease;
        }

        .admin-btn.primary {
            background-color: #667eea;
            color: white;
        }

        .admin-btn.primary:hover {
            background-color: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .admin-btn.danger {
            background-color: #f5576c;
            color: white;
        }

        .admin-btn.danger:hover {
            background-color: #e63946;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(245, 87, 108, 0.3);
        }

        /* Responsive styling */
        @media (max-width: 768px) {
            .admin-page-header {
                padding: 1.5rem 1rem;
                flex-direction: column;
                text-align: center;
            }

            .admin-page-header h1 {
                font-size: 1.5rem;
            }

            .admin-table thead {
                display: none;
            }

            .admin-table tbody tr {
                display: block;
                margin-bottom: 1rem;
                border: 1px solid #e9ecef;
                border-radius: 10px;
                padding: 0.75rem;
                background-color: #fafbfc;
            }

            .admin-table tbody td {
                display: flex;
                justify-content: space-between;
                padding: 0.5rem 0;
                border: none;
            }

            .admin-table tbody td::before {
                content: attr(data-label);
                font-weight: 700;
                color: #43e97b;
                margin-right: 1rem;
            }
        }

        .ai-arb-header-container { position: sticky; top: 0; z-index: 10; background-color: transparent; padding-top: 0; margin-bottom: 0; }
        .ai-arb-table-wrapper { overflow-y: auto; max-height: calc(100vh - 200px); }
    </style>

    <div class="admin-card">
        <div class="card-header">
            <h5><i class="bi bi-table me-2"></i>Plans List</h5>
        </div>
        <div class="card-body table-responsive ai-arb-table-wrapper p-0">
            <table class="table admin-table table-hover">
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
                        <td data-label="ID"><code style="background-color: #f0f0f0; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.85rem;">{{ $p->id }}</code></td>
                        <td data-label="User"><strong>{{ $p->user_name ?? '—' }}</strong></td>
                        <td data-label="Plan"><span class="badge" style="background: #43e97b; color: white;">{{ strtoupper(data_get($p, 'plan_name') ?? data_get($p, 'plan') ?? data_get($p, 'name') ?? '—') }}</span></td>
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

                        <td data-label="Amount"><strong style="color: #43e97b;">${{ $amountDisplay }}</strong></td>
                        <td data-label="Profit Rate"><strong>{{ $profitDisplay }}%</strong></td>
                        <td data-label="Duration">{{ $durationHours }} hrs</td>
                        <td data-label="Status">
                            @php
                                $status = strtolower($p->status ?? 'pending');
                                $badge = 'secondary';
                                if (in_array($status, ['active','running','started'])) $badge = 'info';
                                if (in_array($status, ['completed','finished'])) $badge = 'success';
                                if (in_array($status, ['failed','cancelled','rejected'])) $badge = 'danger';
                            @endphp
                            <span class="admin-badge {{ $badge }}">{{ ucfirst($status) }}</span>
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
                        <td data-label="Started"><small class="text-muted">{{ $startedFormatted }}</small></td>
                        <td data-label="Action" class="d-flex gap-2">
                            <a href="{{ url('/admin/ai-arbitrage/' . $p->id . '/edit') }}" class="btn btn-sm admin-btn primary"><i class="bi bi-pencil me-1"></i>Edit</a>
                            <form method="POST" action="{{ url('/admin/ai-arbitrage/' . $p->id) }}" onsubmit="return confirm('Delete this plan?');" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm admin-btn danger" type="submit"><i class="bi bi-trash me-1"></i>Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="text-center text-muted py-5">
                        <i class="bi bi-inbox" style="font-size: 2rem; opacity: 0.5;"></i>
                        <div class="mt-2">No arbitrage plans found.</div>
                    </td></tr>
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

<style>
    /* Pagination styling tuned for dark admin theme while keeping Bootstrap conventions */
    .pagination { margin: 0; display:flex; gap:6px; }
    .pagination .page-link {
        color: #4ea1ff;
        border: 1px solid rgba(255,255,255,0.06);
        background: rgba(0,0,0,0.35);
        padding: 0.35rem 0.6rem;
        border-radius: 6px;
        min-width: 38px;
        text-align: center;
    }
    .pagination .page-link:hover { background: rgba(255,255,255,0.04); color: #a8dbff; }
    .pagination .page-item.active .page-link { background-color: #0d6efd; border-color: #0d6efd; color: #fff; }
    .pagination .page-item.disabled .page-link { color: #6c757d; background: rgba(0,0,0,0.25); border-color: rgba(255,255,255,0.03); pointer-events: none; }
    .pagination .page-item.disabled .page-link span { opacity: 0.8; }
    
    /* Make sure the pager doesn't create oversized clickable areas */
    .pagination .page-link { line-height: 1.2; }

    .text-muted.small { color: #b9c2c9; }
</style>
@endsection

