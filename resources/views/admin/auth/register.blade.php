@extends('layouts.auth')

@section('content')
<div class="auth-container">
    <div class="auth-box">
        {{-- Ensure wallet address cards and inputs are visible on small screens --}}
        <style>
            /* Force wallet cards and input groups to display on small screens */
            @media (max-width: 576px) {
                .auth-box .card { display: block !important; }
                .auth-box .card-body { display: block !important; }
                .auth-box .input-group { display: flex !important; flex-direction: row !important; }
                .auth-box .input-group .input-group-text { flex: 0 0 auto; }
                .auth-box .input-group .form-control { flex: 1 1 auto; }
            }
        </style>
        <h4 class="text-center mb-4">Admin Registration</h4>

        <form method="POST" action="{{ route('admin.register') }}">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                       id="name" name="name" value="{{ old('name') }}" required autofocus>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                       id="email" name="email" value="{{ old('email') }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">Phone Number</label>
                <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                       id="phone" name="phone" value="{{ old('phone') }}" required>
                @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="telegram_username" class="form-label">Telegram Username</label>
                <div class="input-group">
                    <span class="input-group-text">@</span>
                    <input type="text" class="form-control @error('telegram_username') is-invalid @enderror" 
                           id="telegram_username" name="telegram_username" value="{{ old('telegram_username') }}" required>
                </div>
                @error('telegram_username')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Wallet Addresses -->
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <label class="form-label fw-bold mb-0">Wallet Addresses</label>
                    <div class="badge bg-primary">{{ count($currencies) }} Cryptocurrencies</div>
                </div>
                
                @foreach($currencies as $currency)
                    <div class="mb-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label for="wallet_{{ $currency->id }}" class="form-label mb-0 fw-bold">
                                        {{ $currency->name }}
                                    </label>
                                    <span class="badge bg-secondary">{{ $currency->symbol }}</span>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-wallet2"></i>
                                    </span>
                                    <input type="text" 
                                           class="form-control @error('wallet_addresses.'.$currency->id) is-invalid @enderror" 
                                           id="wallet_{{ $currency->id }}" 
                                           name="wallet_addresses[{{ $currency->id }}]" 
                                           value="{{ old('wallet_addresses.'.$currency->id) }}" 
                                           placeholder="Enter {{ $currency->symbol }} wallet address"
                                           required>
                                </div>
                                @error('wallet_addresses.'.$currency->id)
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Make sure to enter the correct {{ $currency->symbol }} address</small>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                           id="password" name="password" required>
                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <div class="input-group">
                    <input type="password" class="form-control" 
                           id="password_confirmation" name="password_confirmation" required>
                    <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">Register</button>
                <a href="{{ route('admin.login') }}" class="btn btn-light">Already have an account?</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function togglePasswordVisibility(inputId, buttonId) {
    document.getElementById(buttonId).addEventListener('click', function() {
        const input = document.getElementById(inputId);
        const icon = this.querySelector('i');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    });
}

togglePasswordVisibility('password', 'togglePassword');
togglePasswordVisibility('password_confirmation', 'toggleConfirmPassword');
</script>
@endpush