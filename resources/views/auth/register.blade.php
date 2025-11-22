@extends('layouts.app')

@section('content')
<style>
    /* Modern register page styling */
    .register-container {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        padding: 1rem;
        background: linear-gradient(135deg, #1f4068 0%, #162447 100%);
    }

    /* Modern card styling */
    .register-card {
        width: 100%;
        max-width: 500px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        padding: 2.5rem 2rem;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    /* Header styling */
    .register-header {
        text-align: center;
        margin-bottom: 2.5rem;
    }

    .register-title {
        font-size: 2rem;
        font-weight: 800;
        color: #1f4068;
        margin-bottom: 0.5rem;
        letter-spacing: -0.5px;
    }

    .register-subtitle {
        font-size: 0.95rem;
        color: #6c757d;
        margin-bottom: 0;
    }

    /* Form styling */
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

    /* Input group styling */
    .input-group-text {
        border-radius: 10px 0 0 10px;
        border: 1.5px solid #e0e0e0;
        background-color: #fafbfc;
        color: #495057;
        font-weight: 600;
        border-right: none;
    }

    .input-group .form-control {
        border-radius: 0 10px 10px 0;
        border-left: none;
    }

    .input-group .form-control:focus {
        border-color: #667eea;
    }

    .input-group .form-control:focus ~ .input-group-text,
    .input-group .input-group-text:has(+ .form-control:focus) {
        border-color: #667eea;
    }

    /* Form divider */
    .form-divider {
        margin: 1.75rem 0;
        position: relative;
        border: none;
        border-top: 2px solid transparent;
        border-image: linear-gradient(90deg, #667eea 0%, #764ba2 100%) 1;
    }

    .form-divider-text {
        text-align: center;
        color: #6c757d;
        font-size: 0.85rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin: -1.75rem 0 1.75rem;
        padding: 0 0.75rem;
        background: white;
        display: inline-block;
        width: 100%;
    }

    /* Form hints */
    .form-hint {
        font-size: 0.8rem;
        color: #6c757d;
        margin-top: 0.5rem;
    }

    /* Error messages */
    .invalid-feedback {
        color: #dc3545;
        font-size: 0.85rem;
        margin-top: 0.5rem;
    }

    .alert-danger {
        background-color: #fff5f5;
        border: 1.5px solid #dc3545;
        border-radius: 10px;
        padding: 1rem;
        color: #721c24;
        margin-bottom: 1.5rem;
    }

    .alert-danger ul {
        margin-bottom: 0;
        padding-left: 1.5rem;
    }

    .alert-danger li {
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
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
    }

    .btn-register:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        color: white;
    }

    .btn-register:active {
        transform: translateY(0);
    }

    /* Login link */
    .login-section {
        text-align: center;
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid #e9ecef;
    }

    .login-section p {
        margin-bottom: 0;
        color: #6c757d;
        font-size: 0.95rem;
    }

    .login-section a {
        color: #667eea;
        text-decoration: none;
        font-weight: 700;
        transition: all 0.2s ease;
    }

    .login-section a:hover {
        color: #764ba2;
        text-decoration: underline;
    }

    /* Responsive design */
    @media (max-width: 576px) {
        .register-card {
            max-width: 100%;
            padding: 2rem 1.5rem;
            border-radius: 16px;
        }

        .register-title {
            font-size: 1.5rem;
        }

        .register-subtitle {
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
    }

    @media (max-width: 380px) {
        .register-card {
            padding: 1.75rem 1.25rem;
        }

        .register-title {
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

<div class="register-container">
    <div class="register-card">
        <div class="register-header">
            <h1 class="register-title">Create Account</h1>
            <p class="register-subtitle">Join us and start trading</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register.post') }}">
            @csrf

            <!-- Personal Information Section -->
            <div class="mb-4">
                <label for="name" class="form-label">
                    <i class="bi bi-person" style="color: #667eea; margin-right: 0.5rem;"></i>Full Name
                </label>
                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" 
                       name="name" value="{{ old('name') }}" required autofocus
                       placeholder="John Doe">
                @error('name')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="email" class="form-label">
                    <i class="bi bi-envelope" style="color: #667eea; margin-right: 0.5rem;"></i>Email Address
                </label>
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                       name="email" value="{{ old('email') }}" required
                       placeholder="your@email.com">
                @error('email')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="phone" class="form-label">
                    <i class="bi bi-telephone" style="color: #667eea; margin-right: 0.5rem;"></i>Phone Number
                </label>
                <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" 
                       name="phone" value="{{ old('phone') }}" 
                       placeholder="+1 (555) 123-4567">
                <div class="form-hint">Optional: For account recovery and notifications</div>
                @error('phone')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <!-- Divider -->
            <div class="form-divider"></div>

            <!-- Password Section -->
            <div class="mb-4">
                <label for="password" class="form-label">
                    <i class="bi bi-lock" style="color: #667eea; margin-right: 0.5rem;"></i>Password
                </label>
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                       name="password" required
                       placeholder="Create a strong password">
                <div class="form-hint">At least 8 characters with uppercase, lowercase, and numbers</div>
                @error('password')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password_confirmation" class="form-label">
                    <i class="bi bi-shield-lock" style="color: #667eea; margin-right: 0.5rem;"></i>Confirm Password
                </label>
                <input id="password_confirmation" type="password" class="form-control @error('password_confirmation') is-invalid @enderror" 
                       name="password_confirmation" required
                       placeholder="Confirm your password">
                @error('password_confirmation')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <!-- Divider -->
            <div class="form-divider"></div>

            <!-- Additional Information Section -->

            <div class="d-grid gap-2 mb-4">
                <button type="submit" class="btn btn-register">
                    <i class="bi bi-check-circle me-2"></i>Create Account
                </button>
            </div>

            <div class="login-section">
                <p>Already have an account? <a href="{{ route('login') }}">Sign in here</a></p>
            </div>
        </form>
    </div>
</div>
@endsection
