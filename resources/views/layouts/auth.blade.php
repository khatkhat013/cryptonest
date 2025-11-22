<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - Admin</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        :root {
            --primary: #0d6efd;
            --surface: #ffffff;
            --text: #212529;
        }

        [data-bs-theme="dark"] {
            --primary: #0d6efd;
            --surface: #212529;
            --text: #ffffff;
        }

        body {
            background: linear-gradient(135deg, #1f4068 0%, #162447 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .auth-box {
            background: rgba(255, 255, 255, 0.95);
            padding: 2.5rem;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 480px;
            backdrop-filter: blur(10px);
        }

        .auth-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .auth-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 1.5rem;
        }

        .auth-logo img {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            object-fit: cover;
        }

        .auth-logo-text {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1f4068;
            margin: 0;
        }

        .auth-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1f4068;
            margin-bottom: 0.5rem;
        }

        .auth-subtitle {
            font-size: 0.95rem;
            color: #6c757d;
            margin-bottom: 0;
        }

        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.1);
            background-color: #fff;
        }

        .form-label {
            font-weight: 600;
            color: #1f4068;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .input-group .input-group-text {
            background-color: #f8f9fa;
            border: 1px solid #e0e0e0;
            color: #6c757d;
            border-radius: 8px 0 0 8px;
        }

        .input-group .form-control {
            border-left: none;
            border-radius: 0 8px 8px 0;
        }

        .btn-primary {
            background: linear-gradient(135deg, #0d6efd 0%, #0d5dd9 100%);
            border: none;
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #0d5dd9 0%, #0c5ac7 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
        }

        .btn-light {
            background-color: #f0f0f0;
            border: 1px solid #e0e0e0;
            color: #1f4068;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-light:hover {
            background-color: #e8e8e8;
            border-color: #d0d0d0;
            color: #1f4068;
        }

        .btn-outline-secondary {
            border-color: #e0e0e0;
            color: #6c757d;
            border-radius: 0 8px 8px 0;
        }

        .btn-outline-secondary:hover {
            background-color: #f8f9fa;
            border-color: #e0e0e0;
            color: #1f4068;
        }

        .invalid-feedback {
            color: #dc3545;
            font-size: 0.85rem;
            margin-top: 0.25rem;
        }

        .form-check-input {
            border-radius: 4px;
            border: 1px solid #e0e0e0;
        }

        .form-check-input:checked {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        .form-check-label {
            color: #6c757d;
            margin-bottom: 0;
            font-size: 0.9rem;
        }

        .auth-footer {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e0e0e0;
        }

        .auth-footer p {
            margin: 0;
            color: #6c757d;
            font-size: 0.9rem;
        }

        .auth-footer a {
            color: #0d6efd;
            font-weight: 600;
            text-decoration: none;
        }

        .auth-footer a:hover {
            text-decoration: underline;
        }

        .card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            background-color: #f8f9fa;
        }

        .card-body {
            padding: 1rem;
        }

        .badge {
            padding: 0.4rem 0.6rem;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 6px;
        }

        @media (max-width: 576px) {
            .auth-box {
                padding: 1.5rem;
            }

            .auth-title {
                font-size: 1.5rem;
            }

            .auth-logo img {
                width: 40px;
                height: 40px;
            }

            .auth-logo-text {
                font-size: 1.25rem;
            }
        }

        .btn-action {
            background: none;
            border: none;
            color: var(--text);
            padding: 0.5rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.2s;
        }

        .btn-action:hover {
            background-color: rgba(0,0,0,0.1);
        }
    </style>

    @stack('styles')
</head>
<body>
    @yield('content')

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>