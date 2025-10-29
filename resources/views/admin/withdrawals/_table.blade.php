@php $currentAdmin = Auth::guard('admin')->user(); @endphp
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Withdrawals</h5>
    </div>
    <div class="card-body">
        @if(isset($withdrawals) && $withdrawals->count())
            <div class="table-responsive">
                <table class="table table-hover">
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
                                <span class="badge {{ $cls }}">{{ ucfirst($statusName) }}</span>
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

                                    <button type="button" class="btn btn-sm btn-outline-danger delete-deposit-btn" style="margin-left:6px;" data-action="{{ url('/admin/withdraws/'.$w->id) }}" data-id="{{ $w->id }}">Delete</button>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-2">
                {{ $withdrawals->links() }}
            </div>
        @else
            <p class="text-muted">No withdrawals found.</p>
        @endif
    </div>
</div>
