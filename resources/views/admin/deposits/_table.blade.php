@php $currentAdmin = Auth::guard('admin')->user(); @endphp
<style>
    .deposits-header-container { position: sticky; top: 0; z-index: 10; background-color: #fff; padding-top: 0; margin-bottom: 0; }
    .deposits-table-wrapper { overflow-y: auto; max-height: calc(100vh - 200px); }
</style>
<div class="deposits-header-container">
    <h3 class="mb-4">Deposits</h3>
</div>
<div class="card">
    <div class="card-body table-responsive deposits-table-wrapper">
        <table class="table table-hover mb-0">
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
                @forelse($deposits as $d)
                    <tr>
                        <td>{{ $d->id }}</td>
                        <td>{{ optional($d->user)->name ?? '—' }}</td>
                        <td>{{ strtoupper($d->coin ?? '') }}</td>
                        <td>{{ $d->amount }}</td>
                        <td style="max-width:220px;overflow:hidden;text-overflow:ellipsis;">{{ $d->sent_address ?? '—' }}</td>
                        <td>
                            @php
                                $statusName = optional($d->actionStatus)->name ?? $d->status ?? 'unknown';
                                $s = strtolower($statusName);
                                if (in_array($s, ['completed','complete','paid','credited','success'])) {
                                    $cls = 'bg-success';
                                } elseif (in_array($s, ['pending','pending_review','waiting'])) {
                                    $cls = 'bg-warning';
                                } elseif (in_array($s, ['failed','rejected','cancelled'])) {
                                    $cls = 'bg-danger';
                                } else {
                                    $cls = 'bg-secondary';
                                }
                            @endphp
                            <span class="badge rounded-pill {{ $cls }}">{{ ucfirst($statusName) }}</span>
                        </td>
                        <td>{{ optional($d->admin)->name ?? (optional(optional($d->user)->assignedAdmin)->name ?? '—') }}</td>
                        <td>{{ optional($d->created_at)->format('Y-m-d H:i') }}</td>
                        <td class="d-flex gap-2">
                            @php
                                $canAct = false;
                                if (isset($currentAdmin)) {
                                    $canAct = $currentAdmin->isSuperAdmin() || ($d->admin_id === $currentAdmin->id) || (optional($d->user)->assigned_admin_id === $currentAdmin->id);
                                }
                            @endphp

                            @if($canAct)
                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editDepositModal"
                                    data-id="{{ $d->id }}"
                                    data-action="{{ route('admin.deposits.update-status', $d->id) }}"
                                    data-amount="{{ $d->amount }}"
                                    data-status-id="{{ $d->action_status_id }}"
                                    data-coin="{{ $d->coin }}"
                                >Edit</button>

                                <button type="button" class="btn btn-sm btn-danger delete-deposit-btn" data-action="{{ route('admin.deposits.destroy', $d->id) }}" data-id="{{ $d->id }}">Delete</button>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted">No deposits found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination centered below table --}}
        @if($deposits->count())
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
            </div>
        @endif
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
