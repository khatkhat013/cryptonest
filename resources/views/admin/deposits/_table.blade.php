@php $currentAdmin = Auth::guard('admin')->user(); @endphp
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Deposits</h5>
    </div>
    <div class="card-body">
        @if(isset($deposits) && $deposits->count())
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>User</th>
                            <th>Coin</th>
                            <th>Amount</th>
                            <th>Address</th>
                            <th>Status</th>
                            <th>Assigned Admin</th>
                            <th>Date</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($deposits as $d)
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
                                <span class="badge {{ $cls }}">{{ ucfirst($statusName) }}</span>
                            </td>
                            <td>{{ optional($d->admin)->name ?? (optional(optional($d->user)->assignedAdmin)->name ?? '—') }}</td>
                            <td>{{ optional($d->created_at)->format('Y-m-d H:i') }}</td>
                            <td>
                                @php
                                    $canAct = false;
                                    if (isset($currentAdmin)) {
                                        $canAct = $currentAdmin->isSuperAdmin() || ($d->admin_id === $currentAdmin->id) || (optional($d->user)->assigned_admin_id === $currentAdmin->id);
                                    }
                                @endphp

                                @if($canAct)
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editDepositModal"
                                        data-id="{{ $d->id }}"
                                        data-action="{{ route('admin.deposits.update-status', $d->id) }}"
                                        data-amount="{{ $d->amount }}"
                                        data-status-id="{{ $d->action_status_id }}"
                                        data-coin="{{ $d->coin }}"
                                    >Edit</button>

                                    <button type="button" class="btn btn-sm btn-outline-danger delete-deposit-btn" style="margin-left:6px;" data-action="{{ route('admin.deposits.destroy', $d->id) }}" data-id="{{ $d->id }}">Delete</button>
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
                {{ $deposits->links() }}
            </div>
        @else
            <p class="text-muted">No deposits found.</p>
        @endif
    </div>
</div>
