@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">My Profile</h5>
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

                                    <!-- Update Profile button moved above wallets header as requested -->
                                </div>
                            </form>
                            
                            <div class="row mb-3 align-items-center">
                                    <div class="{{ $colClass }} d-flex justify-content-start mb-2">
                                    <button type="button" class="btn btn-primary" onclick="document.getElementById('profile-form').submit();">Update Profile</button>
                                </div>
                                <div class="col-12 col-md-6 d-flex justify-content-end mb-2">
                                    {{-- Wallets button only shown when wallets exist --}}
                                    @if(isset($adminWallets) && $adminWallets->count())
                                        <button type="button" class="btn btn-secondary" onclick="document.getElementById('wallets-form').submit();">Update Wallets</button>
                                    @endif
                                </div>
                            </div>

                            @if(isset($adminWallets) && $adminWallets->count())
                            <hr />
                            <h6 class="mt-3">Your Wallet Addresses</h6>
                            <form id="wallets-form" method="POST" action="{{ route('admin.profile.update') }}">
                                @csrf
                                <div class="row">
                                    @foreach($adminWallets as $w)
                                            <div class="{{ $colClass }} mb-3">
                                            <div class="card h-100">
                                                <div class="card-body d-flex align-items-start gx-2">
                                                    <div class="me-3">
                                                        <div style="width:48px;">
                                                            @if(optional($w->currency)->symbol)
                                                                <img src="{{ asset('images/icons/' . strtolower(optional($w->currency)->symbol) . '.svg') }}" alt="{{ optional($w->currency)->symbol }}" style="width:36px;height:36px;" />
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <div class="mb-1"><strong>{{ optional($w->currency)->symbol ?? 'COIN' }}</strong></div>
                                                        <input type="text" name="wallets[{{ $w->id }}][address]" class="form-control" value="{{ old('wallets.' . $w->id . '.address', $w->address) }}" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach

                                    {{-- Update Wallets button moved above wallets header as requested --}}
                                </div>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
    </div>
</div>
@endsection