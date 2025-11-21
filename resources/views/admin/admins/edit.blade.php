@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center w-100">
                        <div class="me-2">
                            <a href="{{ route('admin.admins.index') }}" class="text-decoration-none d-inline-flex align-items-center" title="Back to list">
                                <i class="bi bi-arrow-left me-2" style="color:#5b8cff;font-size:1.25rem;"></i>
                            </a>
                        </div>

                        <div class="flex-grow-1 text-center">
                            <h5 class="mb-0">Edit Admin</h5>
                        </div>

                        <div>
                            <button type="button" class="btn btn-primary" onclick="document.getElementById('admin-form').submit();">Update Admin</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form id="admin-form" action="{{ route('admin.admins.update', $admin) }}" method="POST">
                        @csrf
                        @method('PUT')
                        @php $colClass = 'col-12 col-md-6'; @endphp
                        <div class="row">
                            <div class="{{ $colClass }} mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name', $admin->name) }}" 
                                       required>
                                @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>

                            <div class="{{ $colClass }} mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email', $admin->email) }}" 
                                       required>
                                @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>

                            <div class="{{ $colClass }} mb-3">
                                <label for="telegram_username" class="form-label">Username (Telegram)</label>
                                <input type="text"
                                       class="form-control @error('telegram_username') is-invalid @enderror"
                                       id="telegram_username"
                                       name="telegram_username"
                                       value="{{ old('telegram_username', $admin->telegram_username) }}">
                                @error('telegram_username')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="{{ $colClass }} mb-3">
                                <label for="role" class="form-label">Role</label>
                                <select class="form-select @error('role_id') is-invalid @enderror" 
                                        id="role" 
                                        name="role_id" 
                                        required>
                                    <option value="">Select Role</option>
                                    <option value="{{ config('roles.normal_id', 1) }}" {{ old('role_id', $admin->role_id) == config('roles.normal_id', 1) ? 'selected' : '' }}>Normal</option>
                                    <option value="{{ config('roles.admin_id', 2) }}" {{ old('role_id', $admin->role_id) == config('roles.admin_id', 2) ? 'selected' : '' }}>Admin</option>
                                    <option value="{{ config('roles.super_id', 3) }}" {{ old('role_id', $admin->role_id) == config('roles.super_id', 3) ? 'selected' : '' }}>Super Admin</option>
                                </select>
                                @error('role_id')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>

                        {{-- Password fields removed per UI request --}}

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <label class="form-label mb-0">Wallet Addresses</label>
                            </div>
                            <div class="list-group">
                                @php
                                    $adminWallets = isset($adminWallets) ? $adminWallets : collect();
                                @endphp
                                @foreach($currencies as $currency)
                                    @php
                                        $existing = $adminWallets->firstWhere('currency_id', $currency->id);
                                        $key = $existing ? $existing->id : 'new_' . $currency->id;
                                        $address = old('wallets.' . $key . '.address', $existing ? $existing->address : '');
                                    @endphp
                                    <div class="list-group-item d-flex align-items-center gap-3">
                                        <div style="width:48px;">
                                            @if(optional($currency)->symbol)
                                                <img src="{{ asset('images/icons/' . strtolower(optional($currency)->symbol) . '.svg') }}" alt="{{ optional($currency)->symbol }}" style="width:36px;height:36px;" />
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <label class="form-label visually-hidden">{{ optional($currency)->symbol }} Address</label>
                                            <div class="row gx-2">
                                                <div class="col-8">
                                                    <input type="text" name="wallets[{{ $key }}][address]" class="form-control" value="{{ $address }}" />
                                                </div>
                                                <div class="col-4">
                                                    @php
                                                        $oldSel = old('wallets.' . $key . '.network_id', null);
                                                        if ($oldSel !== null && $oldSel !== '') {
                                                            $currentNetworkId = $oldSel;
                                                        } else {
                                                            if (isset($existing)) {
                                                                if (is_object($existing->network)) {
                                                                    $currentNetworkId = optional($existing->network)->id ?? ($existing->network_id ?? null);
                                                                } else {
                                                                    $currentNetworkId = $existing->network_id ?? (is_numeric($existing->network) ? (int)$existing->network : null);
                                                                }
                                                            } else {
                                                                $currentNetworkId = null;
                                                            }
                                                        }
                                                    @endphp
                                                    <select name="wallets[{{ $key }}][network_id]" class="form-select">
                                                        <option value="">Networks</option>
                                                        @foreach(optional($networks ?? collect())->sortBy('name') as $n)
                                                            <option value="{{ $n->id }}" {{ $currentNetworkId == $n->id ? 'selected' : '' }}>{{ $n->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <input type="hidden" name="wallets[{{ $key }}][currency_id]" value="{{ $currency->id }}" />
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

                @push('styles')
                <style>
                    /* Fix flex overflow for wallet rows in admin edit */
                    .list-group-item { min-width: 0; }
                    .list-group-item .row { min-width: 0; }
                    .list-group-item .form-control { min-width: 0; overflow-wrap: anywhere; }
                    .list-group-item .form-select { min-width: 0; }
                </style>
                @endpush

