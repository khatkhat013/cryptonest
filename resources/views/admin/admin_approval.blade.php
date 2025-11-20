@extends('layouts.admin')

@section('title', 'Admin Approval Management')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-check-circle"></i> Admin Approval Management
                    </h4>
                    <small class="text-white-50">Site Owner: Approve or Reject new admins before they can edit records</small>
                </div>

                <div class="card-body">
                    <!-- Status Overview -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="alert alert-info mb-0">
                                <h6 class="mb-1">Total Admins</h6>
                                <h4 class="mb-0">{{ $admins->total() }}</h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="alert alert-success mb-0">
                                <h6 class="mb-1">Approved</h6>
                                <h4 class="mb-0">{{ $admins->getCollection()->where('is_approved', true)->count() }}</h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="alert alert-warning mb-0">
                                <h6 class="mb-1">Pending</h6>
                                <h4 class="mb-0">{{ $admins->getCollection()->where('is_approved', false)->whereNull('rejection_reason')->count() }}</h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="alert alert-danger mb-0">
                                <h6 class="mb-1">Rejected</h6>
                                <h4 class="mb-0">{{ $admins->getCollection()->where('is_approved', false)->whereNotNull('rejection_reason')->count() }}</h4>
                            </div>
                        </div>
                    </div>

                    @if($admins->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                        <th>Registered</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($admins as $admin)
                                        <tr>
                                            <td><strong>#{{ $admin->id }}</strong></td>
                                            <td>{{ $admin->name }}</td>
                                            <td>{{ $admin->email }}</td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    {{ $admin->role->name ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($admin->isApproved())
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-check-circle"></i> Approved
                                                    </span>
                                                @elseif($admin->isPending())
                                                    <span class="badge bg-warning">
                                                        <i class="bi bi-clock"></i> Pending
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger">
                                                        <i class="bi bi-x-circle"></i> Rejected
                                                    </span>
                                                @endif
                                            </td>
                                            <td>{{ $admin->created_at?->format('M d, Y') ?? 'N/A' }}</td>
                                            <td>
                                                <a href="{{ route('admin.admin_approval.show', $admin) }}" class="btn btn-sm btn-info">
                                                    <i class="bi bi-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $admins->links() }}
                        </div>
                    @else
                        <div class="alert alert-info text-center">
                            <i class="bi bi-info-circle"></i> No admins found.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
