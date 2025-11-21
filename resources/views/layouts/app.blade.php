<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Crypto Nest') }}</title>
    <!-- Favicon / Site icon: prefer logo in public/images. Use jpg fallback and include shortcut icon to override cached root favicon.ico -->
    <link rel="icon" type="image/jpeg" href="{{ asset('images/cryptonest.jpg') }}?v=2">
    <link rel="shortcut icon" href="{{ asset('images/cryptonest.jpg') }}?v=2">
    <link rel="apple-touch-icon" href="{{ asset('images/cryptonest.jpg') }}?v=2">
    <meta name="theme-color" content="#f59e0b">
    <script>
        // expose a small runtime flag so client JS can decide whether to call Binance (set in .env)
        window.APP_CONFIG = {
            // encode and escape to avoid injecting unexpected HTML. This will be parsed by client as JSON.
            allowBinanceClient: JSON.parse("{!! e(json_encode((bool) env('ALLOW_BINANCE_CLIENT', false))) !!}")
        };
    </script>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
    
    <style>
        :root {
            /* Main colors */
            --primary: #0052cc;
            --primary-light: #0066ff;
            --primary-dark: #004099;
            --primary-bg: rgba(0, 82, 204, 0.1);
            --success: #22c55e;
            
            /* Light mode */
            --bg-main: #f8f9fa;
            --bg-surface: #ffffff;
            --text: #212529;
            --text-muted: rgba(33, 37, 41, 0.75);
            --border: rgba(0, 0, 0, 0.125);
            --shadow: 0 2px 4px rgba(0,0,0,0.075);
        }

        body.dark {
            /* Dark mode */
            --bg-main: #0a1120;
            --bg-surface: #111827;
            --text: #e9ecef;
            --text-muted: rgba(233, 236, 239, 0.75);
            --border: rgba(255, 255, 255, 0.125);
            --shadow: 0 2px 4px rgba(0,0,0,0.25);
            
            /* Adjust primary for dark mode */
            --primary: #0066ff;
            --primary-light: #3385ff;
            --primary-bg: rgba(0, 102, 255, 0.15);
        }

        body {
            background-color: var(--bg-main);
            color: var(--text);
            min-height: 100vh;
        }

        /* Header */
        .navbar {
            background-color: var(--bg-surface);
            box-shadow: var(--shadow);
            padding: 0.75rem 1rem;
            height: 64px;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.25rem;
            color: var(--primary) !important;
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
        }

        .btn-action {
            color: var(--primary) !important;
        }

        .btn-action:hover {
            color: var(--primary-light) !important;
            background-color: rgba(0, 102, 255, 0.1);
        }

        /* dark-mode specific hover styles removed - site is light-mode only */

        /* Sidebar */
        .offcanvas {
            background-color: var(--bg-surface);
            border-right: 1px solid var(--border);
            width: 280px;
        }

        .offcanvas-header {
            border-bottom: 1px solid var(--border);
            padding: 1rem;
        }

        .credit-badge {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: var(--text);
            padding: 0.35rem 0.75rem;
            border-radius: 50rem;
            font-weight: 600;
            font-size: 0.875rem;
            display: inline-block;
            box-shadow: 0 2px 4px rgba(40, 167, 69, 0.2);
        }

        .user-id {
            color: var(--text-muted);
            font-size: 0.875rem;
            margin-bottom: 1.5rem;
            padding: 0.5rem;
            border-radius: 0.5rem;
            background-color: rgba(0,0,0,0.03);
        }

        /* removed dark-mode user-id override */

        .function-label {
            color: var(--text-muted);
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 0.75rem;
            padding: 0 0.5rem;
        }

        /* List items */
        .list-group-item {
            background-color: transparent;
            border: none;
            color: var(--text);
            padding: 0.75rem 1rem;
            margin-bottom: 0.25rem;
            border-radius: 0.5rem;
            transition: all 0.15s ease-in-out;
        }

        .list-group-item:hover {
            background-color: rgba(0,0,0,0.05);
            transform: translateX(4px);
        }

        /* removed dark-mode list hover override */

        .list-group-item i {
            color: var(--text-muted);
            transition: color 0.15s ease-in-out;
        }

        .list-group-item:hover i {
            color: var(--text);
        }

        /* Action buttons */
        .btn-action {
            width: 40px;
            height: 40px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.5rem;
            background: transparent;
            border: none;
            color: var(--primary);
            transition: all 0.15s ease-in-out;
        }

        .btn-action:hover {
            background-color: var(--primary-bg);
            color: var(--primary-dark);
        }

        /* removed dark-mode btn-action hover override */

        /* Layout */
        .container-main {
            padding-top: 80px;
            max-width: 1200px;
        }

        @media (max-width: 576px) {
            .navbar-brand {
                font-size: 1.1rem;
            }
            .offcanvas {
                width: 260px;
            }
        }
    </style>

    @stack('styles')
</head>
<body>

    

    {{-- Include the header component once for all pages --}}

    <x-app-header />
    @yield('header-animation')

    <div class="container container-main my-4">
        @yield('content')
    </div>

        <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="{{ asset('js/plan-modal.js') }}"></script>
    <!-- DotLottie web component (used for success animations) -->
    <script src="https://unpkg.com/@lottiefiles/dotlottie-wc@0.8.5/dist/dotlottie-wc.js" type="module"></script>
</body>
</html>
    <!-- dark-mode toggle removed: site operates in single light mode -->


    <!-- Floating Support Button -->
    @php
        $supportLink = url('/support');
        if (auth()->check()) {
            $user = auth()->user();

            // 1) If the user has an assigned admin with a telegram username, use it
            if (!empty($user->assignedAdmin) && !empty($user->assignedAdmin->telegram_username)) {
                $username = ltrim($user->assignedAdmin->telegram_username, '@');
                $supportLink = 'https://t.me/' . $username;
            } else {
                // 2) Fallback: find any super-admin (role_id = 2) that has a telegram username
                $super = \App\Models\Admin::where('role_id', config('roles.super_id', 3))
                    ->whereNotNull('telegram_username')
                    ->where('telegram_username', '!=', '')
                    ->orderBy('id')
                    ->first();

                if ($super && !empty($super->telegram_username)) {
                    $username = ltrim($super->telegram_username, '@');
                    $supportLink = 'https://t.me/' . $username;
                }
            }
        }
    @endphp

    {{-- ensure default support link falls back to official Telegram if no admin username is available --}}
    @php
        if (empty($supportLink) || $supportLink === url('/support')) {
            $supportLink = 'https://t.me/CryptoNest_Support';
        }
    @endphp

    <a href="{{ $supportLink }}" class="support-btn" aria-label="Contact Support" target="_blank" rel="noopener noreferrer">
        <i class="fa-solid fa-headset"></i>
        <span class="support-tooltip">Contact Support</span>
    </a>

    <style>
        .support-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            background: var(--success);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text);
            box-shadow: 0 4px 12px rgba(34, 197, 94, 0.3);
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .support-btn:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 6px 16px rgba(34, 197, 94, 0.4);
            color: var(--text);
        }

        .support-btn i {
            font-size: 1.8rem;
        }

        .support-tooltip {
            position: absolute;
            background: var(--bg-surface);
            color: var(--text);
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.9rem;
            right: 70px;
            white-space: nowrap;
            opacity: 0;
            transform: translateX(10px);
            transition: all 0.3s ease;
            box-shadow: var(--shadow);
        }

        .support-btn:hover .support-tooltip {
            opacity: 1;
            transform: translateX(0);
        }

        @media (max-width: 768px) {
            .support-btn {
                bottom: 20px;
                right: 20px;
                width: 50px;
                height: 50px;
            }

            .support-btn i {
                font-size: 1.5rem;
            }
        }
    </style>

    @stack('scripts')
    
</body>
</html>
