@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    @include('partials.alerts')
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 mb-0">
            <i class="bi bi-people-fill me-2"></i>
            Users Management
        </h2>

        <div class="d-flex gap-2">
            <div class="input-group">
                <span class="input-group-text">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" class="form-control" id="userSearch" placeholder="Search users...">
            </div>

            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#filterModal">
                <i class="bi bi-funnel me-1"></i>
                Filter
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Assigned Admin</th>
                            <th>Registered</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            // Defensive: ensure we only render users the current admin is allowed to manage.
                            $visibleUsers = $users->filter(function($u) { return Auth::guard('admin')->user()->canManageUser($u); });
                        @endphp
                        @forelse($visibleUsers as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td><code>{{ $user->user_id }}</code></td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td>
                                @if($user->assignedAdmin)
                                    <span class="badge bg-info">{{ $user->assignedAdmin->name }}</span>
                                @else
                                    <span class="badge bg-warning text-dark">Unassigned</span>
                                @endif
                            </td>
                            <td>{{ $user->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <!-- Show button if admin can manage the user -->
                                    @if(Auth::guard('admin')->user()->canManageUser($user))
                                    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    
                                    <!-- Assign button only for super admin -->
                                    @if(Auth::guard('admin')->user()->isSuperAdmin())
                                    <button type="button" class="btn btn-outline-warning" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#assignAdminModal"
                                            data-user-id="{{ $user->id }}"
                                            data-user-name="{{ $user->name }}">
                                        <i class="bi bi-person-check"></i>
                                    </button>
                                    @endif

                                    <!-- Toggle status button if admin can manage the user -->
                                    <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-{{ $user->is_active ? 'danger' : 'success' }}">
                                            <i class="bi bi-{{ $user->is_active ? 'person-x' : 'person-check' }}"></i>
                                        </button>
                                    </form>
                                    <!-- Toggle force-loss button -->
                                    <form action="{{ route('admin.users.toggle-force-loss', $user) }}" method="POST" class="d-inline ms-1">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-{{ $user->force_loss ? 'success' : 'danger' }}" title="{{ $user->force_loss ? 'Disable forced-loss' : 'Enable forced-loss' }}">
                                            <i class="bi bi-{{ $user->force_loss ? 'slash-circle' : 'slash-circle' }}"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="bi bi-people fs-1 text-muted d-block"></i>
                                <p class="mt-2">No users found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($users->hasPages())
        <div class="card-footer">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Assign Admin Modal -->
<div class="modal fade" id="assignAdminModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Admin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="assignAdminForm" method="POST" action="{{ route('admin.users.assign', 0) }}">
                @csrf
                <div class="modal-body">
                    <p class="mb-3">Assign an administrator to <strong id="selectedUserName"></strong></p>
                    <div class="mb-3">
                        <label for="admin_id" class="form-label">Select Admin</label>
                        <select class="form-select" id="admin_id" name="admin_id" required>
                            <option value="">Choose an admin...</option>
                            @if(!empty($admins) && $admins->count())
                                @foreach($admins as $a)
                                    <option value="{{ $a->id }}">{{ $a->name }} ({{ optional($a->role)->getDisplayName() ?? 'Admin' }})</option>
                                @endforeach
                            @else
                                @foreach(App\Models\Admin::with('role')->get() as $a)
                                    <option value="{{ $a->id }}">{{ $a->name }} ({{ optional($a->role)->getDisplayName() ?? 'Admin' }})</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filter Users</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="filterForm">
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="statusAll" value="all" checked>
                                <label class="form-check-label" for="statusAll">All</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="statusActive" value="active">
                                <label class="form-check-label" for="statusActive">Active</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="statusInactive" value="inactive">
                                <label class="form-check-label" for="statusInactive">Inactive</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Assignment</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="assigned" id="assignedAll" value="all" checked>
                                <label class="form-check-label" for="assignedAll">All</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="assigned" id="assignedYes" value="assigned">
                                <label class="form-check-label" for="assignedYes">Assigned</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="assigned" id="assignedNo" value="unassigned">
                                <label class="form-check-label" for="assignedNo">Unassigned</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="registeredDate" class="form-label">Registered Date</label>
                        <select class="form-select" id="registeredDate" name="registered_date">
                            <option value="all">All Time</option>
                            <option value="today">Today</option>
                            <option value="week">This Week</option>
                            <option value="month">This Month</option>
                            <option value="year">This Year</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="applyFilters">Apply Filters</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('js/user-management.js') }}"></script>
</script>

<style>
.table th {
    white-space: nowrap;
}

.btn-group {
    box-shadow: none !important;
}

.btn-group .btn {
    margin: 0 !important;
    border-radius: 0;
}

.btn-group .btn:first-child {
    border-top-left-radius: 0.25rem;
    border-bottom-left-radius: 0.25rem;
}

.btn-group .btn:last-child {
    border-top-right-radius: 0.25rem;
    border-bottom-right-radius: 0.25rem;
}

.modal-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.modal-footer {
    background-color: #f8f9fa;
    border-top: 1px solid #dee2e6;
}
</style>
@endpush