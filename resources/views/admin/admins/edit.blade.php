@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Edit Admin</h5>
                        <a href="{{ route('admin.admins.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.admins.update', $admin) }}" method="POST">
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
                                    <option value="1" {{ old('role_id', $admin->role_id) == 1 ? 'selected' : '' }}>Admin</option>
                                    <option value="2" {{ old('role_id', $admin->role_id) == 2 ? 'selected' : '' }}>Super Admin</option>
                                </select>
                                @error('role_id')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>

                        {{-- Password fields removed per UI request --}}

                        @if(isset($adminWallets) && $adminWallets->count())
                        <div class="mb-3">
                            <label class="form-label">Wallet Addresses</label>
                            <div class="list-group">
                                @foreach($adminWallets as $w)
                                    <div class="list-group-item d-flex align-items-center gap-3">
                                        <div style="width:48px;">
                                            @if(optional($w->currency)->symbol)
                                                <img src="{{ asset('images/icons/' . strtolower(optional($w->currency)->symbol) . '.svg') }}" alt="{{ optional($w->currency)->symbol }}" style="width:36px;height:36px;" />
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <label class="form-label visually-hidden">Address</label>
                                            <input type="text" name="wallets[{{ $w->id }}][address]" class="form-control" value="{{ old('wallets.' . $w->id . '.address', $w->address) }}" />
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Update Admin</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

