@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
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

        .admin-page-header .header-actions {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .admin-page-header .header-actions .input-group {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 0;
        }

        .admin-page-header .header-actions .input-group-text {
            background: transparent;
            border: none;
            color: white;
        }

        .admin-page-header .header-actions input {
            background: transparent;
            border: none;
            color: white;
        }

        .admin-page-header .header-actions input::placeholder {
            color: rgba(255, 255, 255, 0.7);
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
            white-space: nowrap;
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
            padding: 0.4rem 0.75rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.8rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .admin-badge.active {
            background-color: #d4edda;
            color: #155724;
            box-shadow: 0 2px 8px rgba(34, 197, 94, 0.2);
        }

        .admin-badge.inactive {
            background-color: #f8d7da;
            color: #721c24;
            box-shadow: 0 2px 8px rgba(239, 68, 68, 0.2);
        }

        .admin-badge.unassigned {
            background-color: #fff3cd;
            color: #856404;
            box-shadow: 0 2px 8px rgba(245, 158, 11, 0.2);
        }

        .admin-badge.assigned {
            background-color: #d1ecf1;
            color: #0c5460;
            box-shadow: 0 2px 8px rgba(23, 162, 184, 0.2);
        }

        /* Button styling */
        .admin-btn-group {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .admin-btn {
            border-radius: 8px;
            border: 1.5px solid #e0e0e0;
            padding: 0.4rem 0.8rem;
            font-weight: 600;
            font-size: 0.75rem;
            transition: all 0.2s ease;
            background: white;
            color: #667eea;
        }

        .admin-btn:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .admin-btn.danger {
            color: #f5576c;
            border-color: #f5576c;
        }

        .admin-btn.danger:hover {
            background: #f5576c;
            color: white;
            box-shadow: 0 4px 12px rgba(245, 87, 108, 0.3);
        }

        /* Search and filter button */
        .admin-filter-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 10px;
            padding: 0.5rem 1rem;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .admin-filter-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            color: white;
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

            .admin-page-header .header-actions {
                width: 100%;
                justify-content: center;
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
                align-items: center;
            }

            .admin-table tbody td::before {
                content: attr(data-label);
                font-weight: 700;
                color: #667eea;
                margin-right: 1rem;
            }
        }

        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
        }

        .modal-header .btn-close {
            filter: brightness(0) invert(1);
        }

        .modal-title {
            font-weight: 700;
        }
    </style>

    @include('partials.alerts')
    
    <div class="admin-page-header">
        <div>
            <h1><i class="bi bi-people-fill me-2"></i>Users Management</h1>
            <p class="text-white opacity-75 mb-0 mt-1">Monitor and manage all registered users</p>
        </div>
        <div class="header-actions">
            <div class="input-group" style="width: auto;">
                <span class="input-group-text">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" class="form-control" id="userSearch" placeholder="Search users...">
            </div>
            <button type="button" class="btn admin-filter-btn" data-bs-toggle="modal" data-bs-target="#filterModal">
                <i class="bi bi-funnel me-1"></i>
                Filter
            </button>
        </div>
    </div>

    <div class="admin-card">
        <div class="card-header">
            <h5><i class="bi bi-table me-2"></i>Users List</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table admin-table table-hover mb-0">
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
                            <td data-label="ID"><code style="background-color: #f0f0f0; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.85rem;">{{ $user->id }}</code></td>
                            <td data-label="User ID"><code style="background-color: #f0f0f0; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.85rem;">{{ $user->user_id }}</code></td>
                            <td data-label="Name"><strong>{{ $user->name }}</strong></td>
                            <td data-label="Email">{{ $user->email }}</td>
                            <td data-label="Status">
                                @if($user->is_active)
                                    <span class="admin-badge active">✓ Active</span>
                                @else
                                    <span class="admin-badge inactive">✗ Inactive</span>
                                @endif
                            </td>
                            <td data-label="Assigned Admin">
                                @if($user->assignedAdmin)
                                    <span class="admin-badge assigned">{{ $user->assignedAdmin->name }}</span>
                                @else
                                    <span class="admin-badge unassigned">Unassigned</span>
                                @endif
                            </td>
                            <td data-label="Registered"><small class="text-muted">{{ $user->created_at->format('Y-m-d H:i') }}</small></td>
                            <td data-label="Actions">
                                <div class="admin-btn-group">
                                    <!-- Show button if admin can manage the user -->
                                    @if(Auth::guard('admin')->user()->canManageUser($user))
                                    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm admin-btn" title="View details">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    
                                    <!-- Assign button only for super admin -->
                                    @if(Auth::guard('admin')->user()->isSuperAdmin())
                                    <button type="button" class="btn btn-sm admin-btn" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#assignAdminModal"
                                            data-user-id="{{ $user->id }}"
                                            data-user-name="{{ $user->name }}"
                                            title="Assign admin">
                                        <i class="bi bi-person-check"></i>
                                    </button>
                                    @else
                                    <!-- Disabled assign button for non-super admins -->
                                    <button type="button" class="btn btn-sm admin-btn" disabled title="Only super admin can assign users">
                                        <i class="bi bi-person-check"></i>
                                    </button>
                                    @endif

                                    <!-- Toggle status button if admin can manage the user -->
                                    <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm admin-btn" title="{{ $user->is_active ? 'Deactivate' : 'Activate' }}">
                                            <i class="bi bi-{{ $user->is_active ? 'person-x' : 'person-check' }}"></i>
                                        </button>
                                    </form>
                                    <!-- Toggle force-loss button -->
                                    <form action="{{ route('admin.users.toggle-force-loss', $user) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm admin-btn danger" title="{{ $user->force_loss ? 'Disable forced-loss' : 'Enable forced-loss' }}">
                                            <i class="bi bi-{{ $user->force_loss ? 'slash-circle' : 'slash-circle' }}"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="bi bi-people fs-1 text-muted d-block" style="opacity: 0.3;"></i>
                                <p class="mt-3 text-muted">No users found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($users->hasPages())
        <div class="card-footer" style="background-color: #f8f9fa; border-top: 1px solid #e9ecef; padding: 1.5rem;">
            <div class="d-flex justify-content-center">
                {{ $users->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Assign Admin Modal -->
@if(Auth::guard('admin')->user()->isSuperAdmin())
<div class="modal fade" id="assignAdminModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 16px; border: 0;">
            <div class="modal-header">
                <h5 class="modal-title">Assign Administrator</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="assignAdminForm" method="POST" action="{{ route('admin.users.assign', 0) }}">
                @csrf
                <div class="modal-body">
                    <p class="mb-3">Assign an administrator to <strong id="selectedUserName"></strong></p>
                    <div class="mb-3">
                        <label for="admin_id" class="form-label" style="font-weight: 600;">Select Admin</label>
                        <select class="form-select" id="admin_id" name="admin_id" required style="border-radius: 10px; border: 1.5px solid #e0e0e0;">
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
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: 0;">Assign</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 16px; border: 0;">
            <div class="modal-header">
                <h5 class="modal-title">Filter Users</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="filterForm">
                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600;">Status</label>
                        <div style="display: flex; gap: 2rem;">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status" id="statusAll" value="all" checked>
                                <label class="form-check-label" for="statusAll">All</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status" id="statusActive" value="active">
                                <label class="form-check-label" for="statusActive">Active</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status" id="statusInactive" value="inactive">
                                <label class="form-check-label" for="statusInactive">Inactive</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600;">Assignment</label>
                        <div style="display: flex; gap: 2rem;">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="assigned" id="assignedAll" value="all" checked>
                                <label class="form-check-label" for="assignedAll">All</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="assigned" id="assignedYes" value="assigned">
                                <label class="form-check-label" for="assignedYes">Assigned</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="assigned" id="assignedNo" value="unassigned">
                                <label class="form-check-label" for="assignedNo">Unassigned</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="registeredDate" class="form-label" style="font-weight: 600;">Registered Date</label>
                        <select class="form-select" id="registeredDate" name="registered_date" style="border-radius: 10px; border: 1.5px solid #e0e0e0;">
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
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn" id="applyFilters" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: 0;">Apply Filters</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('js/user-management.js') }}"></script>
@endpush

@push('scripts')
<script src="{{ asset('js/user-management.js') }}"></script>
@endpush