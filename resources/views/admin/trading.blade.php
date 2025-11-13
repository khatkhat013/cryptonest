@extends('layouts.admin')
@section('content')
<div class="container-fluid">
    <h3 class="mb-4">Trading Orders</h3>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Symbol</th>
                        <th>Direction</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Result</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($trades as $trade)
                    <tr>
                        <td>{{ $trade->id }}</td>
                        <td>{{ $trade->user?->name ?? $trade->user?->email ?? '—' }}</td>
                        <td>{{ strtoupper($trade->symbol) }}</td>
                        <td>
                            @php
                                $directionClass = strtolower($trade->direction) === 'up' ? 'bg-success' : 'bg-danger';
                                $directionText = strtolower($trade->direction) === 'up' ? '↑ Up' : '↓ Down';
                            @endphp
                            <span class="badge rounded-pill {{ $directionClass }}">{{ $directionText }}</span>
                        </td>
                        <td>{{ rtrim(rtrim(number_format($trade->purchase_quantity, 8, '.', ''), '0'), '.') }}</td>
                        <td>{{ rtrim(rtrim(number_format($trade->purchase_price, 8, '.', ''), '0'), '.') }}</td>
                        <td>
                            @php
                                $resultLower = strtolower($trade->result ?? '');
                                $resultClass = 'bg-secondary';
                                $resultIcon = '';
                                if (str_contains($resultLower, 'win') || $resultLower === 'win') {
                                    $resultClass = 'bg-success';
                                    $resultIcon = '✓ ';
                                } elseif (str_contains($resultLower, 'lose') || str_contains($resultLower, 'loss') || $resultLower === 'lose') {
                                    $resultClass = 'bg-danger';
                                    $resultIcon = '✕ ';
                                }
                            @endphp
                            @if($trade->result)
                                <span class="badge rounded-pill {{ $resultClass }}">{{ $resultIcon }}{{ ucfirst($trade->result) }}</span>
                            @else
                                <span class="badge rounded-pill bg-secondary">—</span>
                            @endif
                        </td>
                        <td>{{ $trade->created_at->format('Y-m-d H:i') }}</td>
                        <td class="d-flex gap-2">
                            <a href="{{ route('admin.trading.edit', $trade->id) }}" class="btn btn-sm btn-primary">Edit</a>
                            <form method="POST" action="{{ route('admin.trading.destroy', $trade->id) }}" onsubmit="return confirm('Are you sure you want to delete this trade? This action cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="text-center text-muted">No trades found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Custom Pagination (single control) -->
    <div class="d-flex flex-column align-items-center mt-4">
        <nav aria-label="Trades pagination">
            <ul class="pagination mb-2">
                {{-- Previous --}}
                <li class="page-item {{ $trades->onFirstPage() ? 'disabled' : '' }}">
                    @if($trades->onFirstPage())
                        <span class="page-link">« Previous</span>
                    @else
                        <a class="page-link" href="{{ $trades->previousPageUrl() }}" rel="prev">« Previous</a>
                    @endif
                </li>

                {{-- Page numbers: show first, last, current +/-1, and ellipses when appropriate --}}
                @foreach(range(1, $trades->lastPage()) as $page)
                    @if($page == $trades->currentPage())
                        <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                    @elseif($page == 1 || $page == $trades->lastPage() || abs($page - $trades->currentPage()) <= 1)
                        <li class="page-item"><a class="page-link" href="{{ $trades->url($page) }}">{{ $page }}</a></li>
                    @elseif($page == 2 && $trades->currentPage() > 3)
                        <li class="page-item disabled"><span class="page-link">…</span></li>
                    @elseif($page == $trades->lastPage() - 1 && $trades->currentPage() < $trades->lastPage() - 2)
                        <li class="page-item disabled"><span class="page-link">…</span></li>
                    @endif
                @endforeach

                {{-- Next --}}
                <li class="page-item {{ $trades->hasMorePages() ? '' : 'disabled' }}">
                    @if($trades->hasMorePages())
                        <a class="page-link" href="{{ $trades->nextPageUrl() }}" rel="next">Next »</a>
                    @else
                        <span class="page-link">Next »</span>
                    @endif
                </li>
            </ul>
        </nav>

        {{-- Result count removed per UI request --}}
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
