@extends('layouts.auth')

@section('content')
<style>
    /* Modern auth container */
    .auth-container {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        padding: 1rem;
        background: linear-gradient(135deg, #1f4068 0%, #162447 100%);
    }

    /* Modern auth box styling */
    .auth-box {
        width: 100%;
        max-width: 500px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        padding: 2.5rem 2rem;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    /* Auth header */
    .auth-header {
        text-align: center;
        margin-bottom: 2rem;
    }

    .auth-logo {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
    }

    .auth-logo img {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }

    .auth-logo-text {
        font-size: 1.5rem;
        font-weight: 800;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .auth-title {
        font-size: 1.75rem;
        font-weight: 800;
        color: #1f4068;
        margin-bottom: 0.5rem;
        letter-spacing: -0.5px;
    }

    .auth-subtitle {
        font-size: 0.95rem;
        color: #6c757d;
        margin-bottom: 0;
    }

    /* Form controls */
    .form-label {
        font-weight: 700;
        color: #495057;
        font-size: 0.95rem;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
    }

    .form-control {
        border-radius: 10px;
        border: 1.5px solid #e0e0e0;
        padding: 0.85rem 1.1rem;
        font-size: 0.95rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background-color: #fafbfc;
    }

    .form-control:focus {
        background-color: #fff;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        outline: none;
    }

    .form-control::placeholder {
        color: #adb5bd;
    }

    .form-control.is-invalid {
        border-color: #dc3545;
        background-color: #fff5f5;
    }

    /* Input group styling */
    .input-group {
        position: relative;
    }

    .input-group .btn {
        border-radius: 0 10px 10px 0;
        border: 1.5px solid #e0e0e0;
        border-left: none;
        background-color: #fafbfc;
        color: #667eea;
        transition: all 0.2s ease;
        padding: 0.85rem 1rem;
    }

    .input-group .btn:hover {
        background-color: #f0f0f0;
        color: #764ba2;
    }

    .input-group .form-control {
        border-radius: 10px 0 0 10px;
    }

    /* Section divider */
    .form-divider {
        margin: 1.75rem 0;
        padding: 0 0 1.75rem 0;
        border-bottom: 2px solid #f0f0f0;
        position: relative;
    }

    .form-divider::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 50%;
        transform: translateX(-50%);
        width: 40px;
        height: 4px;
        background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        border-radius: 2px;
    }

    /* Wallet card styling */
    .wallet-card {
        background-color: #f8f9fa;
        border: 1.5px solid #e9ecef;
        border-radius: 12px;
        padding: 1.25rem;
        margin-bottom: 1rem;
        transition: all 0.2s ease;
    }

    .wallet-card:hover {
        border-color: #667eea;
        background-color: #fafbfc;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.1);
    }

    .wallet-badge {
        display: inline-block;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 0.4rem 0.85rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 700;
    }

    /* Submit button */
    .btn-register {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 10px;
        padding: 1rem 1.5rem;
        font-weight: 700;
        font-size: 1rem;
        color: white;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        margin-top: 1rem;
    }

    .btn-register:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        color: white;
    }

    .btn-register:active {
        transform: translateY(0);
    }

    /* Invalid feedback */
    .invalid-feedback {
        font-size: 0.85rem;
        color: #dc3545;
        margin-top: 0.5rem;
        display: block;
    }

    /* Hint text */
    .form-hint {
        font-size: 0.85rem;
        color: #6c757d;
        margin-top: 0.5rem;
        display: block;
    }

    /* Auth footer */
    .auth-footer {
        text-align: center;
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid #e9ecef;
    }

    .auth-footer p {
        margin-bottom: 0;
        color: #6c757d;
        font-size: 0.95rem;
    }

    .auth-footer a {
        color: #667eea;
        text-decoration: none;
        font-weight: 700;
        transition: all 0.2s ease;
    }

    .auth-footer a:hover {
        color: #764ba2;
        text-decoration: underline;
    }

    /* Responsive design */
    @media (max-width: 576px) {
        .auth-box {
            max-width: 100%;
            padding: 2rem 1.5rem;
            border-radius: 16px;
        }

        .auth-title {
            font-size: 1.5rem;
        }

        .auth-subtitle {
            font-size: 0.9rem;
        }

        .form-control {
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
        }

        .btn-register {
            padding: 0.9rem 1.2rem;
            font-size: 0.95rem;
        }

        .auth-logo img {
            width: 45px;
            height: 45px;
        }

        .auth-logo-text {
            font-size: 1.3rem;
        }

        .wallet-card {
            padding: 1rem;
            margin-bottom: 0.75rem;
        }

        .form-divider {
            margin: 1.5rem 0;
            padding-bottom: 1.5rem;
        }
    }

    @media (max-width: 380px) {
        .auth-box {
            padding: 1.75rem 1.25rem;
        }

        .auth-title {
            font-size: 1.3rem;
        }

        .form-label {
            font-size: 0.85rem;
        }

        .form-control {
            padding: 0.7rem 0.9rem;
            font-size: 0.85rem;
        }
    }
</style>

<div class="auth-container">
    <div class="auth-box">
        <div class="auth-header">
            <div class="auth-logo">
                <img src="{{ asset('images/cryptonest.jpg') }}" alt="Crypto Nest">
                <div class="auth-logo-text">Crypto Nest</div>
            </div>
            <h4 class="auth-title">Admin Registration</h4>
            <p class="auth-subtitle">Create your admin account to manage wallets</p>
        </div>

        <form method="POST" action="{{ route('admin.register') }}">
            @csrf

            <!-- Personal Information Section -->
            <div class="mb-4">
                <label for="name" class="form-label">
                    <i class="bi bi-person" style="color: #667eea; margin-right: 0.5rem;"></i>Full Name
                </label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                       id="name" name="name" value="{{ old('name') }}" required autofocus
                       placeholder="Enter your full name">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="email" class="form-label">
                    <i class="bi bi-envelope" style="color: #667eea; margin-right: 0.5rem;"></i>Email Address
                </label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                       id="email" name="email" value="{{ old('email') }}" required
                       placeholder="admin@example.com">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="phone" class="form-label">
                    <i class="bi bi-telephone" style="color: #667eea; margin-right: 0.5rem;"></i>Phone Number
                </label>
                <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                       id="phone" name="phone" value="{{ old('phone') }}" required
                       placeholder="+95 9XX XXX XXXX">
                @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="telegram_username" class="form-label">
                    <i class="bi bi-telegram" style="color: #667eea; margin-right: 0.5rem;"></i>Telegram Username
                </label>
                <div class="input-group">
                    <span class="input-group-text" style="border-radius: 10px 0 0 10px; border: 1.5px solid #d0d0d0; background-color: #fff; border-right: none;">
                        @
                    </span>
                    <input type="text" class="form-control @error('telegram_username') is-invalid @enderror" 
                           id="telegram_username" name="telegram_username" value="{{ old('telegram_username') }}" required
                           placeholder="your_username"
                           style="border-radius: 0 10px 10px 0; border: 1.5px solid #d0d0d0;">
                </div>
                @error('telegram_username')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
                <span class="form-hint">Your Telegram username for notifications</span>
            </div>

            <div class="mb-4">
                <label for="password" class="form-label">
                    <i class="bi bi-lock" style="color: #667eea; margin-right: 0.5rem;"></i>Password
                </label>
                <div class="input-group">
                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                           id="password" name="password" required
                           placeholder="Create a strong password">
                    <button class="btn" type="button" id="togglePassword" title="Toggle password visibility">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
                @error('password')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
                <span class="form-hint">
                    <i class="bi bi-info-circle"></i> Must be at least 8 characters
                </span>
            </div>

            <div class="mb-4">
                <label for="password_confirmation" class="form-label">
                    <i class="bi bi-lock-check" style="color: #667eea; margin-right: 0.5rem;"></i>Confirm Password
                </label>
                <div class="input-group">
                    <input type="password" class="form-control" 
                           id="password_confirmation" name="password_confirmation" required
                           placeholder="Re-enter your password">
                    <button class="btn" type="button" id="togglePasswordConfirm" title="Toggle password visibility">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            <!-- Wallet Section -->
            <div class="form-divider"></div>

            <div class="mb-4">
                <label class="form-label">
                    <i class="bi bi-wallet2" style="color: #667eea; margin-right: 0.5rem;"></i>Wallet Addresses
                </label>
                <span class="form-hint" style="margin-bottom: 1rem; display: block;">
                    Add your cryptocurrency wallet addresses
                </span>
                
                @if(isset($currencies) && count($currencies) > 0)
                    @foreach($currencies as $currency)
                        <div class="wallet-card">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label for="wallet_{{ $currency->id }}" class="form-label mb-0" style="font-weight: 700; font-size: 0.95rem;">
                                    {{ $currency->name }}
                                </label>
                                <span class="wallet-badge">{{ $currency->symbol }}</span>
                            </div>
                            <div class="input-group">
                                <span class="input-group-text" style="border-radius: 10px 0 0 10px; border: 1.5px solid #d0d0d0; background-color: #fff; border-right: none;">
                                    <i class="bi bi-wallet2" style="color: #667eea;"></i>
                                </span>
                                <input type="text" 
                                       class="form-control @error('wallet_addresses.'.$currency->id) is-invalid @enderror" 
                                       id="wallet_{{ $currency->id }}" 
                                       name="wallet_addresses[{{ $currency->id }}]" 
                                       value="{{ old('wallet_addresses.'.$currency->id) }}" 
                                       placeholder="Enter {{ $currency->symbol }} wallet address"
                                       style="border-radius: 0 10px 10px 0; border: 1.5px solid #d0d0d0;"
                                       required>
                            </div>
                            @error('wallet_addresses.'.$currency->id)
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <span class="form-hint">Ensure this is a valid {{ $currency->symbol }} address</span>
                        </div>
                    @endforeach
                @else
                    <div class="alert alert-warning mb-4" style="border-radius: 10px; border: 1.5px solid #ffc107; background-color: #fff8e1;">
                        <i class="bi bi-exclamation-circle me-2" style="color: #ff9800;"></i>No currencies available. Contact administrator.
                    </div>
                @endif
            </div>

            <!-- Submit Buttons -->
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-register">
                    <i class="bi bi-check-circle me-2"></i>Create Admin Account
                </button>
            </div>
        </form>

        <div class="auth-footer">
            <p>Already have an account? <a href="{{ route('admin.login') }}">Login here</a></p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Toggle Password Visibility
document.getElementById('togglePassword').addEventListener('click', function() {
    const password = document.getElementById('password');
    const icon = this.querySelector('i');
    
    if (password.type === 'password') {
        password.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        password.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
});

// Toggle Password Confirmation Visibility
document.getElementById('togglePasswordConfirm').addEventListener('click', function() {
    const passwordConfirm = document.getElementById('password_confirmation');
    const icon = this.querySelector('i');
    
    if (passwordConfirm.type === 'password') {
        passwordConfirm.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        passwordConfirm.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
});
</script>
@endpush