@extends('layouts.admin')

@section('title', 'Admin Approval - ' . $admin->name)

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-8">
            <!-- Admin Details -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">{{ $admin->name }} - Approval Status</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong>Name:</strong> {{ $admin->name }}</p>
                            <p><strong>Email:</strong> {{ $admin->email }}</p>
                            <p><strong>Phone:</strong> {{ $admin->phone ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Role:</strong> <span class="badge bg-secondary">{{ $admin->role->name ?? 'N/A' }}</span></p>
                            <p><strong>Registered:</strong> {{ $admin->created_at?->format('M d, Y H:i') ?? 'N/A' }}</p>
                            <p><strong>Status:</strong>
                                @if($admin->isApproved())
                                    <span class="badge bg-success"><i class="bi bi-check-circle"></i> Approved</span>
                                @elseif($admin->isPending())
                                    <span class="badge bg-warning"><i class="bi bi-clock"></i> Pending</span>
                                @else
                                    <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Rejected</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    @if($admin->isApproved())
                        <div class="alert alert-success">
                            <strong>✓ Approved</strong>
                            <p class="mb-0 mt-2">This admin can edit user records, permissions, and wallet addresses.</p>
                            @if($admin->approved_by)
                                <small class="text-muted d-block mt-2">
                                    Approved by Site Owner on {{ $admin->approved_at?->format('M d, Y H:i') ?? 'N/A' }}
                                </small>
                            @endif
                        </div>
                    @elseif($admin->isPending())
                        <div class="alert alert-warning">
                            <strong>⏳ Pending Approval</strong>
                            <p class="mb-0 mt-2">This admin is awaiting Site Owner approval before they can edit records.</p>
                        </div>
                    @else
                        <div class="alert alert-danger">
                            <strong>✗ Rejected</strong>
                            <p class="mb-2 mt-2">This admin has been rejected and cannot edit records.</p>
                            @if($admin->rejection_reason)
                                <p class="mb-0"><strong>Reason:</strong> {{ $admin->rejection_reason }}</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Approval Actions -->
            @if($admin->isPending())
                <div class="card border-warning mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Approval Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Approve Form -->
                            <div class="col-md-6">
                                <form action="{{ route('admin.admin_approval.approve', $admin) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-block" onclick="return confirm('Approve this admin?')">
                                        <i class="bi bi-check-circle"></i> Approve Admin
                                    </button>
                                </form>
                                <small class="text-muted d-block mt-2">
                                    This admin will be able to edit user records, permissions, and wallet addresses.
                                </small>
                            </div>

                            <!-- Reject Form -->
                            <div class="col-md-6">
                                <button type="button" class="btn btn-danger btn-block" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                    <i class="bi bi-x-circle"></i> Reject Admin
                                </button>
                                <small class="text-muted d-block mt-2">
                                    Provide a reason for rejection.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif($admin->isApproved())
                <div class="card border-danger mb-4">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0"><i class="bi bi-shield-exclamation"></i> Revoke Approval</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Remove this admin's approval permissions. They will no longer be able to edit records.</p>
                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#revokeModal">
                            <i class="bi bi-x-circle"></i> Revoke Approval
                        </button>
                    </div>
                </div>
            @else
                <div class="card border-info mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="bi bi-arrow-clockwise"></i> Reconsider Rejection</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">You can approve this rejected admin if you wish to reconsider.</p>
                        <form action="{{ route('admin.admin_approval.approve', $admin) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-info" onclick="return confirm('Approve this admin anyway?')">
                                <i class="bi bi-check-circle"></i> Override & Approve
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar Information -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">Permissions Info</h5>
                </div>
                <div class="card-body">
                    <p class="small mb-2"><strong>When Approved, this admin can:</strong></p>
                    <ul class="small">
                        <li>View and manage user accounts</li>
                        <li>Update user permissions</li>
                        <li>Manage wallet addresses</li>
                        <li>Process deposits & withdrawals</li>
                        <li>Manage trading operations</li>
                        <li>Manage AI arbitrage plans</li>
                    </ul>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">Approval Timeline</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div>
                                <strong>Registered</strong>
                                <p class="small text-muted">{{ $admin->created_at?->format('M d, Y H:i') ?? 'N/A' }}</p>
                            </div>
                        </div>

                        @if($admin->approved_at)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-success"></div>
                                <div>
                                    <strong>Approved</strong>
                                    <p class="small text-muted">{{ $admin->approved_at->format('M d, Y H:i') ?? 'N/A' }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Reject Admin</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.admin_approval.reject', $admin) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('rejection_reason') is-invalid @enderror" 
                                  id="rejection_reason" name="rejection_reason" rows="3" 
                                  placeholder="Provide a reason for rejecting this admin..." required></textarea>
                        @error('rejection_reason')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Admin</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Revoke Modal -->
<div class="modal fade" id="revokeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Revoke Approval</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.admin_approval.revoke', $admin) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <strong>Warning:</strong> This admin will immediately lose access to edit records.
                    </div>
                    <div class="mb-3">
                        <label for="revocation_reason" class="form-label">Revocation Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('revocation_reason') is-invalid @enderror" 
                                  id="revocation_reason" name="revocation_reason" rows="3" 
                                  placeholder="Provide a reason for revoking this admin's approval..." required></textarea>
                        @error('revocation_reason')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Revoke Approval</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .timeline-item {
        display: flex;
        margin-bottom: 15px;
    }
    .timeline-marker {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        margin-right: 10px;
        margin-top: 5px;
        flex-shrink: 0;
    }
    .btn-block {
        width: 100%;
    }
</style>
@endsection
