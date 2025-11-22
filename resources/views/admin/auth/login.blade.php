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
        max-width: 450px;
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

    /* Form check (remember me) */
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

    /* Invalid feedback */
    .invalid-feedback {
        font-size: 0.85rem;
        color: #dc3545;
        margin-top: 0.5rem;
        display: block;
    }

    /* Auth footer */
    .auth-footer {
        text-align: center;
        margin-top: 2rem;
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

        .btn-login {
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
            <h4 class="auth-title">Admin Login</h4>
            <p class="auth-subtitle">Welcome back to your admin dashboard</p>
        </div>

        <form method="POST" action="{{ route('admin.login') }}">
            @csrf

            <div class="mb-4">
                <label for="email" class="form-label">
                    <i class="bi bi-envelope" style="color: #667eea; margin-right: 0.5rem;"></i>Email Address
                </label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                       id="email" name="email" value="{{ old('email') }}" required autofocus
                       placeholder="admin@example.com">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password" class="form-label">
                    <i class="bi bi-lock" style="color: #667eea; margin-right: 0.5rem;"></i>Password
                </label>
                <div class="input-group">
                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                           id="password" name="password" required
                           placeholder="Enter your password">
                    <button class="btn" type="button" id="togglePassword" title="Toggle password visibility">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
                @error('password')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label" for="remember">
                        Remember me for 7 days
                    </label>
                </div>
            </div>

            <div class="d-grid gap-2 mb-4">
                <button type="submit" class="btn btn-login">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Login
                </button>
            </div>
        </form>

        <div class="auth-footer">
            <p>Don't have an admin account? <a href="{{ route('admin.register') }}">Register here</a></p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
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
</script>
@endpush