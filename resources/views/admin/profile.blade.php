@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <div class="d-flex align-items-center w-100">
                <div class="me-2">
                    <a href="{{ url()->previous() }}" class="text-decoration-none d-inline-flex align-items-center" title="Back">
                        <i class="bi bi-arrow-left me-2" style="color:#5b8cff;font-size:1.25rem;"></i>
                    </a>
                </div>

                <div class="flex-grow-1 text-center">
                    <h5 class="card-title mb-0">My Profile</h5>
                </div>

                <div>
                    <button type="button" class="btn btn-primary" onclick="document.getElementById('profile-form').submit();">Update Profile</button>
                </div>
            </div>
        </div>
                <div class="card-body">
                    <div class="row justify-content-center">
                        <div class="col-12 col-md-10 col-lg-8">
                                @php
                                    $currentAdmin = Auth::guard('admin')->user();
                                    $colClass = ($currentAdmin && method_exists($currentAdmin, 'isSuperAdmin') && $currentAdmin->isSuperAdmin()) ? 'col-12 col-md-6' : 'col-12 col-md-6 col-lg-4';
                                @endphp
                            <form id="profile-form" method="POST" action="{{ route('admin.profile.update') }}">
                                @csrf
                                <div class="row">
                                        <div class="{{ $colClass }} mb-3">
                                        <label for="name" class="form-label">Name</label>
                                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $admin->name ?? '') }}" required>
                                    </div>

                                        <div class="{{ $colClass }} mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $admin->email ?? '') }}" required>
                                    </div>

                                        <div class="{{ $colClass }} mb-3">
                                        <label for="telegram_username" class="form-label">Telegram Username</label>
                                        <input type="text" class="form-control" id="telegram_username" name="telegram_username" value="{{ old('telegram_username', $admin->telegram_username ?? '') }}">
                                    </div>

                                        <div class="{{ $colClass }} mb-3">
                                        <label for="password" class="form-label">New Password</label>
                                        <input type="password" class="form-control" id="password" name="password">
                                    </div>

                                        <div class="{{ $colClass }} mb-3">
                                        <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                                    </div>

                                    <!-- Update Profile button moved to header -->
                                </div>

                            @if(isset($adminWallets) && $adminWallets->count())
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <h6 class="mb-3">Your Wallet Addresses</h6>
                                </div>
                                <div class="list-group">
                                    @foreach($adminWallets as $w)
                                        @php
                                            $currentAdminNetworkName = is_object($w->network) ? optional($w->network)->name : ($w->network ?? null);
                                            $oldSel = old('wallets.' . $w->id . '.network_id', null);
                                            if ($oldSel !== null && $oldSel !== '') {
                                                $currentNetworkId = $oldSel;
                                            } else {
                                                if (is_object($w->network)) {
                                                    $currentNetworkId = optional($w->network)->id ?? ($w->network_id ?? null);
                                                } else {
                                                    $currentNetworkId = $w->network_id ?? (is_numeric($w->network) ? (int)$w->network : null);
                                                }
                                            }
                                        @endphp
                                        <div class="list-group-item d-flex align-items-center gap-3">
                                            <div style="width:48px;">
                                                @if(optional($w->currency)->symbol)
                                                    <img src="{{ asset('images/icons/' . strtolower(optional($w->currency)->symbol) . '.svg') }}" alt="{{ optional($w->currency)->symbol }}" style="width:36px;height:36px;" />
                                                @endif
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <strong>{{ optional($w->currency)->symbol ?? 'COIN' }}</strong>
                                                    <span class="badge bg-primary text-white network-badge">{{ $currentAdminNetworkName ? strtoupper($currentAdminNetworkName) : 'Network' }}</span>
                                                </div>
                                                <div class="row gx-2 align-items-center">
                                                    <div class="col-8">
                                                        <input type="text" name="wallets[{{ $w->id }}][address]" class="form-control" value="{{ old('wallets.' . $w->id . '.address', $w->address) }}" />
                                                    </div>
                                                    <div class="col-4">
                                                        <select name="wallets[{{ $w->id }}][network_id]" class="form-select network-select">
                                                            <option value="">Network</option>
                                                            @foreach(optional($networks ?? collect())->sortBy('name') as $n)
                                                                <option value="{{ $n->id }}" {{ $currentNetworkId == $n->id ? 'selected' : '' }}>{{ $n->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <input type="hidden" name="wallets[{{ $w->id }}][currency_id]" value="{{ optional($w->currency)->id }}" />
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Make network badge more visible and pill-shaped */
    .network-badge {
        padding: .35rem .6rem;
        font-weight: 700;
        border-radius: 999px;
        font-size: .8rem;
        letter-spacing: .02em;
    }

    /* Ensure wallet rows align centers for inputs and selects */
    .card-body .row.gx-2 {
        align-items: center;
    }

    /* Wallet row: make input and select same height and aligned */
    .wallet-row .form-control,
    .wallet-row .form-select {
        height: 44px;
        padding: .5rem .75rem;
        border-radius: .5rem;
    }

    /* Prevent flex children from overflowing their container (important for long addresses) */
    .wallet-row, .card-body, .row {
        min-width: 0;
    }
    .wallet-row .flex-grow-1 {
        min-width: 0;
    }
    .wallet-row .form-control {
        width: 100%;
        min-width: 0;
        overflow-wrap: anywhere;
    }

    /* Reset previous badge-inside-input rules; show badge next to coin name */
    .wallet-address-with-badge { position: static; }
    .wallet-address-with-badge .form-control { padding-right: .75rem; }
    .network-badge { margin-left: .5rem; }
    .network-select { min-width: 130px; }

    @media (max-width: 767px) {
        .network-badge { font-size: .75rem; }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update network badge when admin changes select before submitting
    document.querySelectorAll('.network-select').forEach(function(sel) {
        sel.addEventListener('change', function(e) {
            var container = this.closest('.card-body');
            var badge = container ? container.querySelector('.network-badge') : null;
            var text = this.options[this.selectedIndex].text;
            if (badge) {
                if (this.value === '') badge.textContent = 'Network';
                else badge.textContent = text;
            }
        });
    });
});
</script>
@endpush