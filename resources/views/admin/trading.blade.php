@extends('layouts.admin')
@section('content')
<div class="container-fluid">
    <style>
        /* Modern admin page header */
        .admin-page-header {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            padding: 2rem 1.5rem;
            border-radius: 16px;
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px rgba(79, 172, 254, 0.2);
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
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
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
                color: #4facfe;
                margin-right: 1rem;
            }
        }

        .trading-header-container { position: sticky; top: 0; z-index: 10; background-color: transparent; padding-top: 0; margin-bottom: 0; }
        .trading-table-wrapper { overflow-y: auto; max-height: calc(100vh - 200px); }
    </style>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3" style="background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); border-left: 4px solid #28a745;">
            <i class="bi bi-check-circle me-2" style="color: #155724;"></i>
            <span style="color: #155724; font-weight: 600;">{{ session('success') }}</span>
        </div>
    @endif

    <div class="admin-card">
        <div class="card-header">
            <h5><i class="bi bi-table me-2"></i>Trading List</h5>
        </div>
        <div class="card-body table-responsive trading-table-wrapper p-0">
            <table class="table admin-table table-hover">
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
                        <td data-label="ID"><code style="background-color: #f0f0f0; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.85rem;">{{ $trade->id }}</code></td>
                        <td data-label="User"><strong>{{ $trade->user?->name ?? $trade->user?->email ?? '—' }}</strong></td>
                        <td data-label="Symbol"><span class="badge" style="background: #4facfe; color: white;">{{ strtoupper($trade->symbol) }}</span></td>
                        <td data-label="Direction">
                            @php
                                $directionClass = strtolower($trade->direction) === 'up' ? 'success' : 'danger';
                                $directionText = strtolower($trade->direction) === 'up' ? '↑ Up' : '↓ Down';
                            @endphp
                            <span class="admin-badge {{ $directionClass }}">{{ $directionText }}</span>
                        </td>
                        <td data-label="Qty">{{ rtrim(rtrim(number_format($trade->purchase_quantity, 8, '.', ''), '0'), '.') }}</td>
                        <td data-label="Price"><strong style="color: #4facfe;">{{ rtrim(rtrim(number_format($trade->purchase_price, 8, '.', ''), '0'), '.') }}</strong></td>
                        <td data-label="Result">
                            @php
                                $resultLower = strtolower($trade->result ?? '');
                                $resultClass = 'secondary';
                                $resultIcon = '';
                                if (str_contains($resultLower, 'win') || $resultLower === 'win') {
                                    $resultClass = 'success';
                                    $resultIcon = '✓ ';
                                } elseif (str_contains($resultLower, 'lose') || str_contains($resultLower, 'loss') || $resultLower === 'lose') {
                                    $resultClass = 'danger';
                                    $resultIcon = '✕ ';
                                }
                            @endphp
                            @if($trade->result)
                                <span class="admin-badge {{ $resultClass }}">{{ $resultIcon }}{{ ucfirst($trade->result) }}</span>
                            @else
                                <span class="admin-badge secondary">—</span>
                            @endif
                        </td>
                        <td data-label="Date"><small class="text-muted">{{ $trade->created_at->format('Y-m-d H:i') }}</small></td>
                        <td data-label="Action" class="d-flex gap-2">
                            <a href="{{ route('admin.trading.edit', $trade->id) }}" class="btn btn-sm admin-btn primary"><i class="bi bi-pencil me-1"></i>Edit</a>
                            <form method="POST" action="{{ route('admin.trading.destroy', $trade->id) }}" onsubmit="return confirm('Are you sure you want to delete this trade? This action cannot be undone.');" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm admin-btn danger"><i class="bi bi-trash me-1"></i>Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="text-center text-muted py-5">
                        <i class="bi bi-inbox" style="font-size: 2rem; opacity: 0.5;"></i>
                        <div class="mt-2 text-center">No trades found.</div>
                    </td></tr>
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
    </div>
</div>

<style>
    /* Pagination styling */
    .pagination { margin: 0; display:flex; gap:6px; }
    .pagination .page-link {
        color: #667eea;
        border: 1.5px solid #e0e0e0;
        background: white;
        padding: 0.35rem 0.6rem;
        border-radius: 6px;
        min-width: 38px;
        text-align: center;
        transition: all 0.2s ease;
    }
    .pagination .page-link:hover { background: #f8f9fa; color: #764ba2; }
    .pagination .page-item.active .page-link { background-color: #667eea; border-color: #667eea; color: #fff; }
    .pagination .page-item.disabled .page-link { color: #ccc; background: #f0f0f0; border-color: #e0e0e0; pointer-events: none; }
    
    .pagination .page-link { line-height: 1.2; }

    .text-muted.small { color: #999; }
</style>
@endsection
