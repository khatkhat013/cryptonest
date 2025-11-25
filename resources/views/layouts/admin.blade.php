<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - Admin Dashboard</title>
    
    <!-- Favicon / Site icon -->
    <link rel="icon" type="image/jpeg" href="{{ asset('images/cryptonest.jpg') }}?v=2">
    <link rel="shortcut icon" href="{{ asset('images/cryptonest.jpg') }}?v=2">
    <link rel="apple-touch-icon" href="{{ asset('images/cryptonest.jpg') }}?v=2">
    <meta name="theme-color" content="#f59e0b">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        :root {
            --sidebar-width: 280px;
        }

        body {
            background-color: #f8f9fa;
        }

        /* Sidebar Styles */
        .admin-sidebar {
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            background-color: #ffffff;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
            z-index: 1000;
            overflow-y: auto;
        }

        .admin-content {
            margin-left: var(--sidebar-width);
            padding: 2rem;
            min-height: 100vh;
        }

        .brand-section {
            padding: 1.5rem;
            border-bottom: 1px solid #eee;
        }

        .brand-section .brand-logo {
            font-size: 1.5rem;
            font-weight: 600;
            color: #0d6efd;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-section {
            padding: 1rem 0;
        }

        .nav-section-title {
            padding: 0.5rem 1.5rem;
            font-size: 0.75rem;
            text-transform: uppercase;
            color: #6c757d;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .nav-link {
            padding: 0.75rem 1.5rem;
            color: #495057;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.2s ease;
        }

        .nav-link:hover, 
        .nav-link.active {
            background-color: #e9ecef;
            color: #0d6efd;
        }

        .nav-link i {
            font-size: 1.1rem;
            width: 1.5rem;
            text-align: center;
        }

        /* Stats Card Styles */
        .stats-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
        }

        /* Table Styles */
        .table-responsive {
            border-radius: 0.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
        }

        /* Alert wrapper: keep alerts narrower and centered */
        .alert-wrapper {
            padding: 1rem 0;
        }

        .alert-wrapper .alert {
            max-width: 1100px;
            margin: 0.5rem auto;
            border-radius: 0.5rem;
            padding: 0.9rem 1.25rem;
        }

        .table {
            margin-bottom: 0;
        }

        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
        }

        .table td {
            vertical-align: middle;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        @media (max-width: 768px) {
            .admin-sidebar {
                position: fixed;
                top: 0;
                left: 0;
                width: var(--sidebar-width);
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                z-index: 1050;
            }

            .admin-sidebar.show {
                transform: translateX(0);
            }

            .admin-content {
                margin-left: 0;
            }

            .navbar-toggler {
                display: block;
            }
        }
        /* Backdrop for mobile off-canvas sidebar */
        .admin-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.35);
            z-index: 900;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.25s ease, visibility 0.25s ease;
        }

        .admin-backdrop.show {
            opacity: 1;
            visibility: visible;
        }

        /* Mobile fixed toggle button */
        .navbar-toggler {
            display: none; /* shown only via media query above */
            background: transparent;
            border: 0;
            font-size: 1.25rem;
            padding: .4rem .6rem;
            color: #0d6efd;
        }

        /* Mobile topbar */
        .admin-topbar {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 64px;
            background: #fff;
            border-bottom: 2px solid #f0f0f0;
            z-index: 1100;
            align-items: center;
            justify-content: center;
        }

        .admin-topbar .topbar-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            padding: 0 1rem;
        }

        .admin-topbar .logo-icon {
            font-size: 1.75rem;
            color: #0d6efd;
            text-decoration: none;
        }

        .admin-topbar .topbar-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #0d6efd;
            flex: 1;
            text-align: center;
        }

        .admin-topbar .spacer {
            flex: 1;
        }

        .admin-topbar .navbar-toggler {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: transparent;
            border: none;
            padding: 0.5rem 0.75rem;
            color: #0d6efd;
            font-size: 1.5rem;
            cursor: pointer;
            margin-left: auto;
        }

        .admin-topbar .navbar-toggler:hover {
            color: #0056b3;
        }

        @media (max-width: 768px) {
            .admin-topbar {
                display: flex;
            }

            .navbar-toggler {
                position: relative;
                left: auto;
                top: auto;
                background: transparent;
                border: none;
                box-shadow: none;
                padding: 0.5rem;
                color: #0d6efd;
                font-size: 1.5rem;
            }

            .navbar-toggler i {
                line-height: 1;
            }

            /* Slightly reduce admin-content padding on small screens */
            .admin-content {
                padding: 1rem;
                padding-top: 80px; /* make room for topbar */
                margin-left: 0;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    <div class="admin-sidebar">
        <div class="brand-section">
            <a href="{{ route('admin.dashboard') }}" class="brand-logo">
                <img src="{{ asset('images/cryptonest.jpg') }}" alt="Crypto Nest" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;">
                <span>Crypto Nest</span>
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">Main Navigation</div>
            <nav>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a href="{{ route('admin.dashboard') }}" 
                           class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i class="bi bi-speedometer2"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.users.index') }}" 
                           class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            <i class="bi bi-people"></i>
                            <span>Users Management</span>
                        </a>
                    </li>
                    @if(Auth::guard('admin')->check() && Auth::guard('admin')->user()->isSuperAdmin())
                    <li class="nav-item">
                        <a href="{{ route('admin.admins.index') }}" 
                           class="nav-link {{ request()->routeIs('admin.admins.*') ? 'active' : '' }}">
                            <i class="bi bi-shield-lock"></i>
                            <span>Admins Management</span>
                        </a>
                    </li>
                    @endif
                    <li class="nav-item">
                        <a href="{{ url('/info#plans') }}" 
                           class="nav-link">
                            <i class="bi bi-gem"></i>
                            <span>Plans</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.user-guide') }}"
                           class="nav-link {{ request()->routeIs('admin.user-guide') ? 'active' : '' }}">
                            <i class="bi bi-book"></i>
                            <span>User Guide</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">Settings</div>
            <nav>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a href="{{ route('admin.profile') }}" 
                           class="nav-link {{ request()->routeIs('admin.profile') ? 'active' : '' }}">
                            <i class="bi bi-person-circle"></i>
                            <span>My Profile</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <form action="{{ route('admin.logout') }}" method="POST" class="nav-link">
                            @csrf
                            <button type="submit" class="btn btn-link text-danger p-0 d-flex align-items-center gap-2">
                                <i class="bi bi-box-arrow-right"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    </li>
                </ul>
            </nav>
        </div>
    </div>

    <div class="admin-backdrop" aria-hidden="true"></div>

    <!-- Mobile Topbar -->
    <header class="admin-topbar">
        <div class="topbar-inner" style="justify-content: center;">
            <a href="{{ route('admin.dashboard') }}" class="logo-icon" style="display: flex; align-items: center; gap: 8px; text-decoration: none; color: inherit;">
                <img src="{{ asset('images/cryptonest.jpg') }}" alt="Crypto Nest" style="width: 28px; height: 28px; border-radius: 50%; object-fit: cover;">
                <span class="topbar-title" style="margin: 0;">Crypto Nest</span>
            </a>
            <button class="navbar-toggler" type="button" aria-label="Toggle navigation" aria-controls="admin-sidebar" style="position: absolute; right: 10px;">
                <i class="bi bi-list"></i>
            </button>
        </div>
    </header>

    <main class="admin-content">
        {{-- Session alerts are rendered via the partials.alerts include inside each page to avoid duplicates --}}

        @yield('content')
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mobile sidebar toggle
        const toggleBtn = document.querySelector('.navbar-toggler');
        const sidebar = document.querySelector('.admin-sidebar');
        const backdrop = document.querySelector('.admin-backdrop');

        function openSidebar() {
            sidebar.classList.add('show');
            backdrop.classList.add('show');
            sidebar.setAttribute('aria-hidden', 'false');
        }

        function closeSidebar() {
            sidebar.classList.remove('show');
            backdrop.classList.remove('show');
            sidebar.setAttribute('aria-hidden', 'true');
        }

        if (toggleBtn && sidebar) {
            toggleBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                if (sidebar.classList.contains('show')) {
                    closeSidebar();
                } else {
                    openSidebar();
                }
            });
        }

        // Backdrop click closes sidebar on mobile
        if (backdrop) {
            backdrop.addEventListener('click', function() {
                closeSidebar();
            });
        }

        // Close sidebar with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && sidebar.classList.contains('show')) {
                closeSidebar();
            }
        });

        // Close sidebar when resizing to larger widths
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                closeSidebar();
            }
        });

        // Handle alerts auto-close
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                const closeBtn = alert.querySelector('.btn-close');
                if (closeBtn) closeBtn.click();
            }, 5000);
        });
    });
    </script>

    @stack('scripts')
</body>
</html>