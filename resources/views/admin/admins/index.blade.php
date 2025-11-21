@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Admin Management</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Telegram</th>
                            <th>Registered</th>
                            <th>Total Users</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($admins as $a)
                        <tr>
                            <td>{{ $a->id }}</td>
                            <td>{{ $a->name }}</td>
                            <td>{{ $a->email }}</td>
                            <td>
                                @php $role = optional($a->role); @endphp
                                {{ $role?->display_name ?? $role?->name ?? '—' }}
                            </td>
                            <td>{{ $a->telegram_username ?? '—' }}</td>
                            <td>{{ optional($a->created_at)->format('Y-m-d') ?? '—' }}</td>
                            <td>
                                @php $current = Auth::guard('admin')->user(); @endphp
                                @if($current->isSuperAdmin() || ($current->role_id ?? null) === config('roles.super_id', 3))
                                    <a href="{{ route('admin.users.index', ['admin_id' => $a->id]) }}" class="badge bg-primary text-decoration-none">{{ $a->assigned_users_count ?? 0 }}</a>
                                @else
                                    <span class="badge bg-secondary">{{ $a->assigned_users_count ?? 0 }}</span>
                                @endif
                            </td>
                            <td>
                                @php $current = Auth::guard('admin')->user(); @endphp
                                @if($current->isSuperAdmin() || ($current->role_id ?? null) === config('roles.super_id', 3))
                                    <a href="{{ route('admin.admins.edit', $a) }}" class="btn btn-sm btn-primary">Edit</a>

                                    <form action="{{ route('admin.admins.destroy', $a) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this admin?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                @else
                                    <span class="text-muted">-</span>
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
@endsection