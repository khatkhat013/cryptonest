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
            <p class="text-muted mb-0">Viewing details for user <code>{{ $user->user_id }}</code></p>
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
            @else
            <!-- Disabled assign button for non-super admins -->
            <button type="button" class="btn btn-outline-secondary" disabled title="Only super admin can assign users">
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
                    <i class="bi bi-slash-circle me-1"></i>
                    {{ $user->force_loss ? 'Disable Forced-Loss' : 'Enable Forced-Loss' }}
                </button>
            </form>
        </div>
    </div>

    <div class="row">
        <!-- Left column: Basic info (with embedded User Wallets) -->
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

                    <!-- Embedded User Wallets inside Basic Information -->
                    <hr />
                    <h6 class="text-muted mb-2">User Wallets</h6>
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
                            $uwallets = collect();
                        }
                    @endphp

                    @if($uwallets->isEmpty())
                        <p class="text-muted mb-0">No wallets found for this user.</p>
                    @else
                        <div class="row row-cols-1 row-cols-md-2 g-3">
                            @foreach($uwallets as $w)
                                @php
                                    $rawCoin = data_get($w, 'coin', null);
                                    $symbol = strtolower(is_string($rawCoin) && $rawCoin !== '' ? $rawCoin : 'cur');
                                    // prefer icons folder (svg, png) then fallback to a generic icon
                                    $candidates = [
                                        'images/icons/' . $symbol . '.svg',
                                        'images/icons/' . $symbol . '.png',
                                        'images/coins/' . $symbol . '.svg',
                                        'images/coins/' . $symbol . '.png',
                                    ];
                                    $iconPath = asset('images/icons/coin.svg');
                                    foreach ($candidates as $rel) {
                                        $full = public_path($rel);
                                        if (is_string($full) && file_exists($full)) {
                                            $iconPath = asset($rel);
                                            break;
                                        }
                                    }
                                @endphp
                                <div class="col">
                                    <div class="p-2 border rounded">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <div class="small text-muted d-flex align-items-center">
                                                    <img src="{{ $iconPath }}" alt="{{ $symbol }}" class="coin-icon-img me-2" style="width:20px;height:20px;object-fit:cover;border-radius:4px;" />
                                                    <span>{{ strtoupper($symbol) }}</span>
                                                </div>
                                                <form method="POST" action="{{ route('admin.user_wallets.update_balance', $w->id) }}">
                                                    @csrf
                                                    <div class="input-group input-group-sm mt-1">
                                                        <input name="balance" type="number" step="0.00000001" class="form-control form-control-sm" value="{{ number_format($w->balance ?? 0, 8, '.', '') }}" />
                                                        <button class="btn btn-sm btn-primary" type="submit">Save</button>
                                                    </div>
                                                </form>
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

        <!-- Right column: Assignment Information -->
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
                                    <div class="col-12 col-md-3 text-muted small">Assigned Admin</div>
                                        <div class="col-12 col-md-3 text-muted small">Username</div>
                                        <div class="col-12 col-md-3 text-muted small">Email</div>
                                        <div class="col-12 col-md-3 text-muted small text-md-end">Assignment Date</div>

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
                                </div>
                            </div>
                            @php
                                // render admin wallets in a separate block to preserve grid layout
                                $adminWallets = collect();
                                try {
                                    if (\Illuminate\Support\Facades\Schema::hasTable('admin_wallets') && $user->assigned_admin_id) {
                                        // join currencies to get canonical symbol (many admin_wallets only store currency_id)
                                        $adminWallets = \Illuminate\Support\Facades\DB::table('admin_wallets as aw')
                                            ->leftJoin('currencies as c', 'aw.currency_id', '=', 'c.id')
                                            ->where('aw.admin_id', $user->assigned_admin_id)
                                            ->select('aw.*', 'c.symbol as currency_symbol')
                                            ->orderBy('aw.currency_id')
                                            ->get();
                                        }
                                } catch (\Exception $e) {
                                    $adminWallets = collect();
                                }
                            @endphp
                                @if($adminWallets->isNotEmpty())
                                <div class="mt-3">
                                    <div class="small text-muted mb-2">Admin Wallets:</div>
                                    <div class="row g-2">
                                        @foreach($adminWallets as $aw)
                                            @php
                                                // prefer currency symbol from joined currencies, fallback to coin column
                                                $rawCoin = data_get($aw, 'currency_symbol', data_get($aw, 'coin', null));
                                                $symbol = strtolower(is_string($rawCoin) && $rawCoin !== '' ? $rawCoin : 'btc');
                                                // try several locations and extensions to match project icons
                                                $candidates = [
                                                    'images/coins/' . $symbol . '.svg',
                                                    'images/coins/' . $symbol . '.png',
                                                    'images/icons/' . $symbol . '.svg',
                                                    'images/icons/' . $symbol . '.png',
                                                ];
                                                // fallback to btc icon (exists in icons folder)
                                                $iconPath = asset('images/icons/btc.svg');
                                                $found = false;
                                                foreach ($candidates as $rel) {
                                                    $full = public_path($rel);
                                                    if (is_string($full) && file_exists($full)) {
                                                        $iconPath = asset($rel);
                                                        $found = true;
                                                        break;
                                                    }
                                                }
                                                // If not found, try a case-insensitive search inside images/icons/
                                                if (!$found) {
                                                    try {
                                                        $iconsDir = public_path('images/icons');
                                                        if (is_dir($iconsDir)) {
                                                            $files = scandir($iconsDir);
                                                            if (is_array($files)) {
                                                                foreach ($files as $f) {
                                                                    if (in_array($f, ['.', '..'])) continue;
                                                                    // compare filename without extension
                                                                    $name = pathinfo($f, PATHINFO_FILENAME);
                                                                    if (strtolower($name) === strtolower($symbol) || strpos(strtolower($name), strtolower($symbol)) !== false) {
                                                                        $iconPath = asset('images/icons/' . $f);
                                                                        $found = true;
                                                                        break;
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    } catch (\Exception $e) {
                                                        // ignore and fallback to default
                                                    }
                                                }
                                                $addressText = data_get($aw, 'address', data_get($aw, 'wallet_address', '—'));
                                            @endphp
                                            <div class="col-12 col-md-6">
                                                <div class="d-flex align-items-center justify-content-between p-2 border rounded wallet-entry">
                                                    <div class="d-flex align-items-center">
                                                        <img src="{{ $iconPath }}" alt="{{ $symbol }}" class="coin-icon-img me-2" />
                                                        <div class="wallet-address">{{ $addressText }}</div>
                                                    </div>
                                                    <button type="button" class="btn btn-outline-secondary btn-sm copy-wallet-btn" data-address="{{ $addressText }}" aria-label="Copy address">
                                                        <i class="bi bi-clipboard"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
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
                                @else
                                <button type="button" class="btn btn-outline-secondary mt-3" disabled title="Only super admin can assign users">
                                    <i class="bi bi-person-plus me-1"></i>
                                    Assign Admin Now
                                </button>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    
<!-- Assign Admin Modal -->
@if(Auth::guard('admin')->user()->isSuperAdmin())
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
@endif

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