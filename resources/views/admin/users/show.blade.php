@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    @include('partials.alerts')
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-0">
                <a href="{{ route('admin.users.index') }}" class="text-decoration-none">
                    <i class="bi bi-arrow-left me-2"></i>
                </a>
                User Details
            </h2>
            <p class="text-muted mb-0">
                Viewing details for user <code>{{ $user->user_id }}</code>
            </p>
        </div>

        <div class="d-flex gap-2">
            @if(Auth::guard('admin')->user()->isSuperAdmin())
            <button type="button" class="btn btn-outline-warning" 
                    data-bs-toggle="modal" 
                    data-bs-target="#assignAdminModal"
                    data-user-id="{{ $user->id }}"
                    data-user-name="{{ $user->name }}">
                <i class="bi bi-person-check me-1"></i>
                Assign Admin
            </button>
            @endif

            <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-{{ $user->is_active ? 'danger' : 'success' }}">
                    <i class="bi bi-{{ $user->is_active ? 'person-x' : 'person-check' }} me-1"></i>
                    {{ $user->is_active ? 'Deactivate' : 'Activate' }} User
                </button>
            </form>
            <form action="{{ route('admin.users.toggle-force-loss', $user) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-{{ $user->force_loss ? 'success' : 'danger' }} ms-2">
                    <i class="bi bi-{{ $user->force_loss ? 'slash-circle' : 'slash-circle' }} me-1"></i>
                    {{ $user->force_loss ? 'Disable Forced-Loss' : 'Enable Forced-Loss' }}
                </button>
            </form>
        </div>
    </div>

    <div class="row">
    <!-- Basic Information -->
    <div class="col-12 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h3 class="h5 mb-0">Basic Information</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted">Name</label>
                        <p class="mb-0">{{ $user->name }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Email</label>
                        <p class="mb-0">{{ $user->email }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Status</label>
                        <p class="mb-0">
                            @if($user->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Registered On</label>
                        <p class="mb-0">{{ $user->created_at->format('F j, Y H:i:s') }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Last Updated</label>
                        <p class="mb-0">{{ $user->updated_at->format('F j, Y H:i:s') }}</p>
                    </div>
                </div>
            </div>
        </div>

    <!-- Assignment Information -->
    <div class="col-12 col-lg-8 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h3 class="h5 mb-0">Assignment Information</h3>
                </div>
                <div class="card-body">
                    <div class="assignment-card-body">
                    @if($user->assignedAdmin)
                    <div class="mb-3">
                        <div class="row gx-2 gy-1 assigned-admin-grid">
                            <!-- labels row (top) -->
                            <div class="col-12 col-md-3 text-muted small">Assigned Admin</div>
                            <div class="col-12 col-md-3 text-muted small">Username</div>
                            <div class="col-12 col-md-3 text-muted small">Email</div>
                            <div class="col-12 col-md-3 text-muted small text-md-end">Assignment Date</div>

                            <!-- values row (bottom) -->
                            <div class="col-12 col-md-3">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-info p-2 me-2">
                                        <i class="bi bi-person-badge me-1"></i>
                                    </span>
                                    <div class="fw-bold">{{ $user->assignedAdmin->name }}</div>
                                </div>
                            </div>
                            <div class="col-12 col-md-3"><code class="fw-semibold">{{ optional($user->assignedAdmin)->telegram_username ?? $user->assignedAdmin->name }}</code></div>
                            <div class="col-12 col-md-3"><code class="fw-semibold">{{ optional($user->assignedAdmin)->email ?? '—' }}</code></div>
                            <div class="col-12 col-md-3 text-md-end"><code class="fw-semibold">{{ optional($user->assigned_admin_date)->format('F j, Y H:i:s') ?? '—' }}</code></div>

                            <div class="col-12 mt-3 wallets">
                                @php
                                    $awallets = $adminWallets ?? collect();
                                    if ($awallets->isEmpty() && $user->assigned_admin_id) {
                                        $awallets = \App\Models\AdminWallet::where('admin_id', $user->assigned_admin_id)
                                            ->with('currency')
                                            ->get();
                                    }
                                @endphp

                                @if($awallets->count())
                                    <div class="mt-2">
                                        <small class="text-muted d-block mb-2">Wallets:</small>
                                        <div class="row row-cols-1 row-cols-md-2 g-2">
                                            @foreach($awallets as $w)
                                                <div class="col">
                                                    <div class="p-2 border rounded wallet-entry">
                                                        <div class="d-flex align-items-start gap-2">
                                                            @php
                                                                $symbol = optional($w->currency)->symbol ?? 'coin';
                                                                $iconFile = strtolower($symbol) . '.svg';
                                                                $iconFullPath = public_path('images/icons/' . $iconFile);
                                                                if (file_exists($iconFullPath)) {
                                                                    $iconPath = asset('images/icons/' . $iconFile);
                                                                } else {
                                                                    // fallback icon
                                                                    $iconPath = asset('images/icons/coin.svg');
                                                                }
                                                            @endphp
                                                            <div>
                                                                <img src="{{ $iconPath }}" alt="{{ $symbol }}" class="coin-icon-img" />
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <div class="wallet-address">{{ $w->address }}</div>
                                                            </div>
                                                            <div class="ms-2 align-self-start">
                                                                <button type="button" class="btn btn-sm btn-outline-secondary copy-wallet-btn" data-address="{{ $w->address }}" data-bs-toggle="tooltip" data-bs-placement="top" title="Copy address" aria-label="Copy address">
                                                                    <i class="bi bi-clipboard"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <small class="text-muted d-block">Wallet: <code>—</code></small>
                                @endif
                            </div>
                        </div>
                    </div>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="bi bi-person-x fs-1 text-muted d-block"></i>
                        <p class="mt-2 mb-0">No admin assigned yet</p>
                        @if(Auth::guard('admin')->user()->isSuperAdmin())
                        <button type="button" class="btn btn-outline-primary mt-3" 
                                data-bs-toggle="modal" 
                                data-bs-target="#assignAdminModal"
                                data-user-id="{{ $user->id }}"
                                data-user-name="{{ $user->name }}">
                            <i class="bi bi-person-plus me-1"></i>
                            Assign Admin Now
                        </button>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Activity Stats -->
        <!-- User Wallets -->
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="h5 mb-0">User Wallets</h3>
                </div>
                <div class="card-body">
                    @php
                        $uwallets = collect();
                        try {
                            if (\Illuminate\Support\Facades\Schema::hasTable('user_wallets')) {
                                $uwallets = \Illuminate\Support\Facades\DB::table('user_wallets')
                                    ->where('user_id', $user->id)
                                    ->orderBy('coin')
                                    ->get();
                            }
                        } catch (\Exception $e) {
                            // fail quietly and show empty list
                            $uwallets = collect();
                        }
                    @endphp

                    @if($uwallets->isEmpty())
                        <p class="text-muted mb-0">No wallets found for this user.</p>
                    @else
                        <div class="row row-cols-1 row-cols-md-3 g-3">
                            @foreach($uwallets as $w)
                                <div class="col">
                                    <div class="p-3 border rounded h-100">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <div class="small text-muted">{{ strtoupper($w->coin ?? ($w->currency_id ? 'CUR' : '—')) }}</div>
                                                <div class="fw-bold">{{ number_format($w->balance ?? 0, 8) }}</div>
                                            </div>
                                            <div class="text-end">
                                                <small class="text-muted d-block">ID: {{ $w->id }}</small>
                                                <small class="text-muted">{{ isset($w->created_at) ? \Carbon\Carbon::parse($w->created_at)->format('Y-m-d') : '' }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="h5 mb-0">Activity Statistics</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center mb-4 mb-md-0">
                                <h6 class="text-muted mb-2">Total Trades</h6>
                                <h3 class="mb-0">{{ $user->trades_count ?? 0 }}</h3>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center mb-4 mb-md-0">
                                <h6 class="text-muted mb-2">Active Orders</h6>
                                <h3 class="mb-0">{{ $user->active_orders_count ?? 0 }}</h3>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center mb-4 mb-md-0">
                                <h6 class="text-muted mb-2">Total Volume</h6>
                                <h3 class="mb-0">${{ number_format($user->total_volume ?? 0, 2) }}</h3>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h6 class="text-muted mb-2">Last Active</h6>
                                <h3 class="mb-0">
                                    @if($user->last_login_at)
                                        {{ $user->last_login_at->diffForHumans() }}
                                    @else
                                        Never
                                    @endif
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
                            @if(isset($admins) && $admins)
                                @foreach($admins as $admin)
                                    <option value="{{ $admin->id }}" {{ $user->assigned_admin_id == $admin->id ? 'selected' : '' }}>
                                        {{ $admin->name }} ({{ optional($admin->role)->getDisplayName() ?? '—' }})
                                    </option>
                                @endforeach
                            @else
                                @foreach(App\Models\Admin::with('role')->get() as $admin)
                                    <option value="{{ $admin->id }}" {{ $user->assigned_admin_id == $admin->id ? 'selected' : '' }}>
                                        {{ $admin->name }} ({{ optional($admin->role)->getDisplayName() ?? '—' }})
                                    </option>
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

@endsection

@push('scripts')
<script src="{{ asset('js/user-management.js') }}"></script>
<script>
// initialize bootstrap tooltips (if bootstrap is available)
document.addEventListener('DOMContentLoaded', function () {
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
            try { new bootstrap.Tooltip(el); } catch (e) { /* ignore */ }
        });
    }

    // copy button handler
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.copy-wallet-btn');
        if (!btn) return;
        var address = btn.getAttribute('data-address');
        if (!address) return;

        var icon = btn.querySelector('i');
        var previousClass = icon ? icon.className : null;

        function showCopied() {
            if (icon) {
                icon.className = 'bi bi-clipboard-check';
            } else {
                btn.innerText = '✓';
            }
            setTimeout(function () {
                if (icon && previousClass) icon.className = previousClass;
                else if (!icon) btn.innerText = '';
            }, 1200);
        }

        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(address).then(function () {
                showCopied();
            }).catch(function () {
                prompt('Copy address:', address);
            });
        } else {
            prompt('Copy address:', address);
        }
    });
});
</script>

<style>
.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    padding: 1rem;
}

.form-label {
    font-size: 0.875rem;
    margin-bottom: 0.25rem;
}

.badge {
    font-weight: 500;
}

/* Layout tweaks for user show page */
.assignment-card-body {
    display: flex;
    flex-direction: column;
}
.wallets .list-unstyled li code {
    word-break: break-all;
    max-width: 60ch;
    display: inline-block;
}
.copy-wallet-btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.8rem;
}

.wallet-address {
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, "Roboto Mono", "Liberation Mono", monospace;
    color: #d63384; /* keep the pinkish color used for addresses */
}
.wallet-line { align-items: center; }
.wallet-line .amount { font-weight: 600; color: #6c757d; }
.copy-wallet-btn { width: 36px; height: 32px; display:inline-flex; align-items:center; justify-content:center; }

.coin-icon {
    width: 36px; height: 36px; display:inline-flex; align-items:center; justify-content:center; border-radius:50%; background:#0d6efd; color:#fff; font-weight:700;
}
.wallet-entry { background: #fff; }
.wallet-entry .wallet-address { display:block; word-break:break-all; color: #d63384; }
.wallet-entry .amount-row { color: #495057; font-weight:600; }
.coin-icon-img { width:36px; height:36px; border-radius:6px; object-fit:cover; }

/* Tighter, subtle column separators for Assigned Admin grid */
.assigned-admin-grid {
    align-items: center;
}
.assigned-admin-grid > [class*="col-"] {
    padding-top: .25rem;
    padding-bottom: .25rem;
}
.assigned-admin-grid .col-md-3 {
    padding-left: .5rem;
    padding-right: .5rem;
}
.assigned-admin-grid .col-md-3:not(:last-child) {
    border-right: 1px solid rgba(0,0,0,0.04);
}
.assigned-admin-grid .text-muted.small { color: #6c757d; }

@media (max-width: 767.98px) {
    .assigned-admin-grid .col-md-3 { border-right: none; padding-left: .25rem; padding-right: .25rem; }
}

@media (max-width: 991.98px) {
    /* stack on smaller screens */
    .assignment-card-body { margin-top: 0 !important; }
}
</style>
@endpush