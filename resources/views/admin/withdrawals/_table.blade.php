@php $currentAdmin = Auth::guard('admin')->user(); @endphp
<style>
    .withdrawals-header-container { position: sticky; top: 0; z-index: 10; background-color: #fff; padding-top: 0; margin-bottom: 0; }
    .withdrawals-table-wrapper { overflow-y: auto; max-height: calc(100vh - 200px); }
</style>
<div class="withdrawals-header-container">
    <h3 class="mb-4">Withdrawals</h3>
</div>
<div class="card">
    <div class="card-body table-responsive withdrawals-table-wrapper">
        @if(isset($withdrawals) && $withdrawals->count())
            <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>User</th>
                            <th>Coin</th>
                            <th>Amount</th>
                            @if(Schema::hasColumn('withdrawals','processed_amount'))
                                <th>Processed</th>
                            @endif
                            @if(Schema::hasColumn('withdrawals','fee'))
                                <th>Fee</th>
                            @endif
                            <th>Address</th>
                            <th>Status</th>
                            <th>Assigned Admin</th>
                            <th>Date</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($withdrawals as $w)
                        <tr>
                            <td>{{ $w->id }}</td>
                            <td>{{ optional($w->user)->name ?? '—' }}</td>
                            <td>{{ strtoupper($w->coin ?? '') }}</td>
                            <td>{{ $w->amount }}</td>
                            @if(Schema::hasColumn('withdrawals','processed_amount'))
                                <td>{{ isset($w->processed_amount) ? number_format($w->processed_amount,8) : '—' }}</td>
                            @endif
                            @if(Schema::hasColumn('withdrawals','fee'))
                                <td>{{ isset($w->fee) ? number_format($w->fee,8) : '—' }}</td>
                            @endif
                            <td style="max-width:220px;overflow:hidden;text-overflow:ellipsis;">{{ $w->destination_address ?? '—' }}</td>
                            <td>
                                @php
                                    $statusName = optional($w->actionStatus)->name ?? $w->status ?? 'unknown';
                                    $s = strtolower($statusName);
                                    if (in_array($s, ['completed','complete','paid','success'])) {
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
                            <td>{{ optional($w->admin)->name ?? (optional(optional($w->user)->assignedAdmin)->name ?? '—') }}</td>
                            <td>{{ optional($w->created_at)->format('Y-m-d H:i') }}</td>
                            <td>
                                @php
                                    $canAct = false;
                                    if (isset($currentAdmin)) {
                                        $canAct = $currentAdmin->isSuperAdmin() || ($w->admin_id === $currentAdmin->id) || (optional($w->user)->assigned_admin_id === $currentAdmin->id);
                                    }
                                @endphp

                                @if($canAct)
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editWithdrawalModal"
                                        data-id="{{ $w->id }}"
                                        data-action="{{ url('/admin/withdraws/'.$w->id.'/status') }}"
                                        data-amount="{{ $w->amount }}"
                                        data-status-id="{{ $w->action_status_id }}"
                                        data-coin="{{ $w->coin }}"
                                    >Edit</button>

                                    <button type="button" class="btn btn-sm btn-outline-danger delete-deposit-btn ms-2" data-action="{{ url('/admin/withdraws/'.$w->id) }}" data-id="{{ $w->id }}">Delete</button>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

            {{-- Pagination centered below table --}}
            @if($withdrawals->count())
                <div class="d-flex flex-column align-items-center mt-4">
                    <nav aria-label="Withdrawals pagination">
                        <ul class="pagination mb-2">
                            {{-- Previous --}}
                            <li class="page-item {{ $withdrawals->onFirstPage() ? 'disabled' : '' }}">
                                @if($withdrawals->onFirstPage())
                                    <span class="page-link">« Previous</span>
                                @else
                                    <a class="page-link" href="{{ $withdrawals->previousPageUrl() }}" rel="prev">« Previous</a>
                                @endif
                            </li>

                            {{-- Page numbers: show first, last, current +/-1, and ellipses when appropriate --}}
                            @foreach(range(1, $withdrawals->lastPage()) as $page)
                                @if($page == $withdrawals->currentPage())
                                    <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                                @elseif($page == 1 || $page == $withdrawals->lastPage() || abs($page - $withdrawals->currentPage()) <= 1)
                                    <li class="page-item"><a class="page-link" href="{{ $withdrawals->url($page) }}">{{ $page }}</a></li>
                                @elseif($page == 2 && $withdrawals->currentPage() > 3)
                                    <li class="page-item disabled"><span class="page-link">…</span></li>
                                @elseif($page == $withdrawals->lastPage() - 1 && $withdrawals->currentPage() < $withdrawals->lastPage() - 2)
                                    <li class="page-item disabled"><span class="page-link">…</span></li>
                                @endif
                            @endforeach

                            {{-- Next --}}
                            <li class="page-item {{ $withdrawals->hasMorePages() ? '' : 'disabled' }}">
                                @if($withdrawals->hasMorePages())
                                    <a class="page-link" href="{{ $withdrawals->nextPageUrl() }}" rel="next">Next »</a>
                                @else
                                    <span class="page-link">Next »</span>
                                @endif
                            </li>
                        </ul>
                    </nav>
                </div>
            @endif
            </div>
        @else
            <p class="text-muted">No withdrawals found.</p>
        @endif
        </div>
    </div>
</div>
