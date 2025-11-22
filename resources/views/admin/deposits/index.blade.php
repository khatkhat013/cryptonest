@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <style>
        /* Modern admin page header */
        .admin-page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 1.5rem;
            border-radius: 16px;
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.2);
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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

        .admin-badge.warning {
            background-color: #fff3cd;
            color: #856404;
            box-shadow: 0 2px 8px rgba(245, 158, 11, 0.2);
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
                color: #667eea;
                margin-right: 1rem;
            }
        }

        .deposits-header-container { position: sticky; top: 0; z-index: 10; background-color: transparent; padding-top: 0; margin-bottom: 0; }
        .deposits-table-wrapper { overflow-y: auto; max-height: calc(100vh - 200px); }
    </style>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3" style="background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); border-left: 4px solid #28a745;">
            <i class="bi bi-check-circle me-2" style="color: #155724;"></i>
            <span style="color: #155724; font-weight: 600;">{{ session('success') }}</span>
        </div>
    @endif

    <div class="admin-card">
        <div class="card-header">
            <h5><i class="bi bi-table me-2"></i>Deposits List</h5>
        </div>
        <div class="card-body table-responsive deposits-table-wrapper p-0">
            <table class="table admin-table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Coin</th>
                        <th>Amount</th>
                        <th>Address</th>
                        <th>Status</th>
                        <th>Assigned Admin</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                @php $currentAdmin = Auth::guard('admin')->user(); @endphp
                @forelse($deposits as $d)
                    <tr>
                        <td data-label="ID"><code style="background-color: #f0f0f0; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.85rem;">{{ $d->id }}</code></td>
                        <td data-label="User"><strong>{{ optional($d->user)->name ?? '—' }}</strong></td>
                        <td data-label="Coin"><span class="badge" style="background: #4facfe; color: white;">{{ strtoupper($d->coin ?? '') }}</span></td>
                        <td data-label="Amount"><strong style="color: #667eea;">{{ $d->amount }}</strong></td>
                        <td data-label="Address" style="max-width:220px;overflow:hidden;text-overflow:ellipsis;"><code style="font-size: 0.8rem;">{{ $d->sent_address ?? '—' }}</code></td>
                        <td data-label="Status">
                            @php
                                $statusName = optional($d->actionStatus)->name ?? $d->status ?? 'unknown';
                                $s = strtolower($statusName);
                                if (in_array($s, ['completed','complete','paid','credited','success'])) {
                                    $cls = 'success';
                                } elseif (in_array($s, ['pending','pending_review','waiting'])) {
                                    $cls = 'warning';
                                } elseif (in_array($s, ['failed','rejected','cancelled'])) {
                                    $cls = 'danger';
                                } else {
                                    $cls = 'secondary';
                                }
                            @endphp
                            <span class="admin-badge {{ $cls }}">{{ ucfirst($statusName) }}</span>
                        </td>
                        <td data-label="Assigned Admin">{{ optional($d->admin)->name ?? (optional(optional($d->user)->assignedAdmin)->name ?? '—') }}</td>
                        <td data-label="Date"><small class="text-muted">{{ optional($d->created_at)->format('Y-m-d H:i') }}</small></td>
                        <td data-label="Action" class="d-flex gap-2">
                            @php
                                $canAct = false;
                                if (isset($currentAdmin)) {
                                    $canAct = $currentAdmin->isSuperAdmin() || ($d->admin_id === $currentAdmin->id) || (optional($d->user)->assigned_admin_id === $currentAdmin->id);
                                }
                            @endphp

                            @if($canAct)
                                <button class="btn btn-sm admin-btn primary" data-bs-toggle="modal" data-bs-target="#editDepositModal"
                                    data-id="{{ $d->id }}"
                                    data-action="{{ route('admin.deposits.update-status', $d->id) }}"
                                    data-amount="{{ $d->amount }}"
                                    data-status-id="{{ $d->action_status_id }}"
                                    data-coin="{{ $d->coin }}"
                                ><i class="bi bi-pencil me-1"></i>Edit</button>

                                <button type="button" class="btn btn-sm admin-btn danger delete-deposit-btn" data-action="{{ route('admin.deposits.destroy', $d->id) }}" data-id="{{ $d->id }}"><i class="bi bi-trash me-1"></i>Delete</button>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="text-center text-muted py-5">
                        <i class="bi bi-inbox" style="font-size: 2rem; opacity: 0.5;"></i>
                        <div class="mt-2">No deposits found.</div>
                    </td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Custom Pagination (single control) -->
    <div class="d-flex flex-column align-items-center mt-4">
        <nav aria-label="Deposits pagination">
            <ul class="pagination mb-2">
                {{-- Previous --}}
                <li class="page-item {{ $deposits->onFirstPage() ? 'disabled' : '' }}">
                    @if($deposits->onFirstPage())
                        <span class="page-link">« Previous</span>
                    @else
                        <a class="page-link" href="{{ $deposits->previousPageUrl() }}" rel="prev">« Previous</a>
                    @endif
                </li>

                {{-- Page numbers: show first, last, current +/-1, and ellipses when appropriate --}}
                @foreach(range(1, $deposits->lastPage()) as $page)
                    @if($page == $deposits->currentPage())
                        <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                    @elseif($page == 1 || $page == $deposits->lastPage() || abs($page - $deposits->currentPage()) <= 1)
                        <li class="page-item"><a class="page-link" href="{{ $deposits->url($page) }}">{{ $page }}</a></li>
                    @elseif($page == 2 && $deposits->currentPage() > 3)
                        <li class="page-item disabled"><span class="page-link">…</span></li>
                    @elseif($page == $deposits->lastPage() - 1 && $deposits->currentPage() < $deposits->lastPage() - 2)
                        <li class="page-item disabled"><span class="page-link">…</span></li>
                    @endif
                @endforeach

                {{-- Next --}}
                <li class="page-item {{ $deposits->hasMorePages() ? '' : 'disabled' }}">
                    @if($deposits->hasMorePages())
                        <a class="page-link" href="{{ $deposits->nextPageUrl() }}" rel="next">Next »</a>
                    @else
                        <span class="page-link">Next »</span>
                    @endif
                </li>
            </ul>
        </nav>

        {{-- Result count removed per UI request --}}
    </div>

    @include('admin.deposits._modal')
</div>

<style>
    /* Pagination styling tuned for dark admin theme while keeping Bootstrap conventions */
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
    .pagination .page-item.disabled .page-link span { opacity: 0.8; }
    
    /* Make sure the pager doesn't create oversized clickable areas */
    .pagination .page-link { line-height: 1.2; }

    .text-muted.small { color: #999; }
</style>
</div>
@endsection
