@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="admin-page-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; padding: 2rem; margin-bottom: 2rem; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);">
        <div>
            <h1 class="text-white mb-0" style="font-size: 2rem; font-weight: 700;">
                <i class="bi bi-people-fill me-2"></i>Admin Management
            </h1>
        </div>
    </div>

    <!-- Admin Card -->
    <div class="admin-card" style="background: #ffffff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); overflow: hidden;">
        <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; padding: 1.5rem;">
            <h5 class="card-title mb-0 text-white" style="font-size: 1.1rem; font-weight: 600;">
                <i class="bi bi-list-ul me-2"></i>All Admins
            </h5>
        </div>
        <div class="card-body" style="padding: 1.5rem;">
            <div class="table-responsive">
                <table class="admin-table" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid #f0f0f0;">
                            <th style="padding: 1rem; text-align: left; font-weight: 600; color: #333; font-size: 0.9rem;">ID</th>
                            <th style="padding: 1rem; text-align: left; font-weight: 600; color: #333; font-size: 0.9rem;">Name</th>
                            <th style="padding: 1rem; text-align: left; font-weight: 600; color: #333; font-size: 0.9rem;">Email</th>
                            <th style="padding: 1rem; text-align: left; font-weight: 600; color: #333; font-size: 0.9rem;">Role</th>
                            <th style="padding: 1rem; text-align: left; font-weight: 600; color: #333; font-size: 0.9rem;">Telegram</th>
                            <th style="padding: 1rem; text-align: left; font-weight: 600; color: #333; font-size: 0.9rem;">Registered</th>
                            <th style="padding: 1rem; text-align: left; font-weight: 600; color: #333; font-size: 0.9rem;">Users</th>
                            <th style="padding: 1rem; text-align: left; font-weight: 600; color: #333; font-size: 0.9rem;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($admins as $a)
                        <tr style="border-bottom: 1px solid #f0f0f0; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#f8f9fa'" onmouseout="this.style.backgroundColor='transparent'">
                            <td data-label="ID" style="padding: 1rem; color: #666;">{{ $a->id }}</td>
                            <td data-label="Name" style="padding: 1rem; color: #333; font-weight: 500;">{{ $a->name }}</td>
                            <td data-label="Email" style="padding: 1rem; color: #666; font-size: 0.9rem;">{{ $a->email }}</td>
                            <td data-label="Role" style="padding: 1rem;">
                                @php $role = optional($a->role); @endphp
                                <span class="admin-badge" style="display: inline-block; padding: 0.4rem 0.8rem; border-radius: 6px; font-size: 0.85rem; font-weight: 500; background: #e7f3ff; color: #0066ff;">
                                    {{ $role?->display_name ?? $role?->name ?? '—' }}
                                </span>
                            </td>
                            <td data-label="Telegram" style="padding: 1rem; color: #666;">{{ $a->telegram_username ?? '—' }}</td>
                            <td data-label="Registered" style="padding: 1rem; color: #666; font-size: 0.9rem;">{{ optional($a->created_at)->format('Y-m-d') ?? '—' }}</td>
                            <td data-label="Users" style="padding: 1rem;">
                                @php $current = Auth::guard('admin')->user(); @endphp
                                @if($current->isSuperAdmin() || ($current->role_id ?? null) === config('roles.super_id', 3))
                                    <a href="{{ route('admin.users.index', ['admin_id' => $a->id]) }}" class="admin-badge" style="display: inline-block; padding: 0.4rem 0.8rem; border-radius: 6px; font-size: 0.85rem; font-weight: 500; background: #e7f5ff; color: #0052cc; text-decoration: none; cursor: pointer;">
                                        {{ $a->assigned_users_count ?? 0 }}
                                    </a>
                                @else
                                    <span class="admin-badge" style="display: inline-block; padding: 0.4rem 0.8rem; border-radius: 6px; font-size: 0.85rem; font-weight: 500; background: #f0f0f0; color: #999;">
                                        {{ $a->assigned_users_count ?? 0 }}
                                    </span>
                                @endif
                            </td>
                            <td data-label="Actions" style="padding: 1rem;">
                                @php $current = Auth::guard('admin')->user(); @endphp
                                @if($current->isSuperAdmin() || ($current->role_id ?? null) === config('roles.super_id', 3))
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.admins.edit', $a) }}" class="admin-btn" style="display: inline-block; padding: 0.4rem 0.8rem; border-radius: 6px; font-size: 0.85rem; font-weight: 500; background: #0066ff; color: white; text-decoration: none; cursor: pointer; border: none; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#0052cc'" onmouseout="this.style.backgroundColor='#0066ff'">
                                            <i class="bi bi-pencil-square me-1"></i>Edit
                                        </a>
                                        <form action="{{ route('admin.admins.destroy', $a) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete this admin?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="admin-btn" style="display: inline-block; padding: 0.4rem 0.8rem; border-radius: 6px; font-size: 0.85rem; font-weight: 500; background: #ff4444; color: white; text-decoration: none; cursor: pointer; border: none; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#cc0000'" onmouseout="this.style.backgroundColor='#ff4444'">
                                                <i class="bi bi-trash me-1"></i>Delete
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span style="color: #999; font-size: 0.9rem;">-</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .admin-page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    /* Responsive mobile styling */
    @media (max-width: 768px) {
        .admin-table {
            font-size: 0.85rem !important;
        }

        .admin-table thead {
            display: none;
        }

        .admin-table tbody tr {
            display: block;
            margin-bottom: 1.2rem;
            border: 1px solid #2a2a3a;
            border-radius: 10px;
            background: #1a1a2e;
            overflow: hidden;
        }

        .admin-table tbody tr td {
            display: grid;
            grid-template-columns: 120px 1fr;
            align-items: center;
            gap: 1rem;
            padding: 0.8rem 1rem !important;
            border-bottom: 1px solid #2a2a3a;
            color: #c9cace;
        }

        .admin-table tbody tr td:last-child {
            border-bottom: none;
            grid-template-columns: 1fr;
            display: block;
            padding: 1rem !important;
        }

        .admin-table tbody tr td::before {
            content: attr(data-label);
            font-weight: 700;
            color: #667eea;
            font-size: 0.9rem;
        }

        .admin-table tbody tr td:last-child::before {
            display: none;
        }

        .admin-badge {
            width: 100% !important;
            text-align: center !important;
            display: block !important;
            padding: 0.6rem 1rem !important;
            margin-bottom: 0 !important;
        }

        .admin-table tbody tr td .d-flex {
            display: flex !important;
            flex-direction: column !important;
            gap: 0.5rem !important;
        }

        .admin-table tbody tr td .d-flex a,
        .admin-table tbody tr td .d-flex button {
            width: 100% !important;
            display: block !important;
            padding: 0.7rem 1rem !important;
            text-align: center !important;
            border-radius: 6px !important;
            font-weight: 500 !important;
            font-size: 0.9rem !important;
            margin: 0 !important;
        }

        .admin-page-header {
            flex-direction: column;
            align-items: flex-start;
            padding: 1.5rem !important;
        }

        .admin-page-header h1 {
            font-size: 1.3rem !important;
        }

        .admin-card {
            border-radius: 10px !important;
        }

        .admin-card .card-header {
            padding: 1rem !important;
        }

        .admin-card .card-body {
            padding: 1rem !important;
        }
    }
</style>
@endsection