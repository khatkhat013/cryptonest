@extends('layouts.app')

@section('content')
<style>
    /* Modern login page styling */
    .login-container {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        padding: 1rem;
        background: linear-gradient(135deg, #1f4068 0%, #162447 100%);
    }

    /* Modern card styling */
    .login-card {
        width: 100%;
        max-width: 450px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        padding: 2.5rem 2rem;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    /* Header styling */
    .login-header {
        text-align: center;
        margin-bottom: 2.5rem;
    }

    .login-title {
        font-size: 2rem;
        font-weight: 800;
        color: #1f4068;
        margin-bottom: 0.5rem;
        letter-spacing: -0.5px;
    }

    .login-subtitle {
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

    /* Form check styling */
    .form-check {
        display: flex;
        align-items: center;
    }

    .form-check-input {
        width: 1.2rem;
        height: 1.2rem;
        margin-right: 0.75rem;
        border-radius: 6px;
        border: 1.5px solid #d0d0d0;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .form-check-input:checked {
        background-color: #667eea;
        border-color: #667eea;
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
    }

    .form-check-label {
        margin-bottom: 0;
        color: #495057;
        font-weight: 500;
        font-size: 0.9rem;
        cursor: pointer;
    }

    /* Form footer */
    .form-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        gap: 1rem;
    }

    .forgot-password-link {
        color: #667eea;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.2s ease;
    }

    .forgot-password-link:hover {
        color: #764ba2;
        text-decoration: underline;
    }

    /* Submit button */
    .btn-login {
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

    .btn-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        color: white;
    }

    .btn-login:active {
        transform: translateY(0);
    }

    /* Error messages */
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

    /* Divider */
    .form-divider {
        margin: 1.75rem 0;
        border: none;
        border-top: 1px solid #e9ecef;
    }

    /* Register link */
    .register-section {
        text-align: center;
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid #e9ecef;
    }

    .register-section p {
        margin-bottom: 0;
        color: #6c757d;
        font-size: 0.95rem;
    }

    .register-section a {
        color: #667eea;
        text-decoration: none;
        font-weight: 700;
        transition: all 0.2s ease;
    }

    .register-section a:hover {
        color: #764ba2;
        text-decoration: underline;
    }

    /* Responsive design */
    @media (max-width: 576px) {
        .login-card {
            max-width: 100%;
            padding: 2rem 1.5rem;
            border-radius: 16px;
        }

        .login-title {
            font-size: 1.5rem;
        }

        .login-subtitle {
            font-size: 0.9rem;
        }

        .form-control {
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
        }

        .btn-login {
            padding: 0.9rem 1.2rem;
            font-size: 0.95rem;
        }

        .form-footer {
            flex-direction: column;
            gap: 1rem;
        }
    }

    @media (max-width: 380px) {
        .login-card {
            padding: 1.75rem 1.25rem;
        }

        .login-title {
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

<div class="login-container">
    <div class="login-card">
        <div class="login-header">
            <h1 class="login-title">Login</h1>
            <p class="login-subtitle">Sign in to your account</p>
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

        <form method="POST" action="{{ route('login.post') }}">
            @csrf

            <div class="mb-4">
                <label for="email" class="form-label">
                    <i class="bi bi-envelope" style="color: #667eea; margin-right: 0.5rem;"></i>Email Address
                </label>
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                       name="email" value="{{ old('email') }}" required autofocus
                       placeholder="your@email.com">
                @error('email')
                    <div class="invalid-feedback d-block" style="color: #dc3545; font-size: 0.85rem; margin-top: 0.5rem;">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password" class="form-label">
                    <i class="bi bi-lock" style="color: #667eea; margin-right: 0.5rem;"></i>Password
                </label>
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                       name="password" required
                       placeholder="Enter your password">
                @error('password')
                    <div class="invalid-feedback d-block" style="color: #dc3545; font-size: 0.85rem; margin-top: 0.5rem;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-footer">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label" for="remember">Remember me</label>
                </div>
                <a href="#" class="forgot-password-link">Forgot password?</a>
            </div>

            <div class="d-grid gap-2 mb-4">
                <button type="submit" class="btn btn-login">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                </button>
            </div>
        </form>

        <div class="register-section">
            <p>Don't have an account? <a href="{{ route('register') }}">Create one now</a></p>
        </div>
    </div>
</div>
@endsection
