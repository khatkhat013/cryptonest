<nav class="navbar fixed-top">
    <div class="container-fluid position-relative">
        <!-- Left actions -->
        <div class="d-flex align-items-center">
            @auth
                <button class="btn-action" type="button" aria-label="notifications">
                    <i class="bi bi-bell-fill"></i>
                </button>
            @endauth
        </div>

        <!-- Centered brand (use logo image if available) -->
        <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
            @php
                $logoSrc = null;
                if (file_exists(public_path('images/cryptonest.png'))) {
                    $logoSrc = asset('images/cryptonest.png');
                } elseif (file_exists(public_path('images/cryptonest.jpg'))) {
                    $logoSrc = asset('images/cryptonest.jpg');
                } elseif (file_exists(public_path('images/cryptonest.svg'))) {
                    $logoSrc = asset('images/cryptonest.svg');
                }
            @endphp
            @if ($logoSrc)
                <img src="{{ $logoSrc }}" alt="Crypto Nest" class="site-logo me-2"> 
            @endif
            <span class="d-none d-sm-inline">Crypto Nest</span>
        </a>

        <!-- Right actions -->
        <div class="d-flex align-items-center gap-2">
            <button class="btn-action" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarCanvas" aria-controls="sidebarCanvas" aria-label="Toggle menu">
                <i class="bi bi-list fs-4"></i>
            </button>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="sidebarCanvas" aria-labelledby="sidebarLabel">
        <div class="offcanvas-header justify-content-between align-items-center border-bottom">
            <h5 class="offcanvas-title mb-0 d-flex align-items-center" id="sidebarLabel">
                @php
                    $sidebarLogo = null;
                    if (file_exists(public_path('images/cryptonest.png'))) {
                        $sidebarLogo = asset('images/cryptonest.png');
                    } elseif (file_exists(public_path('images/cryptonest.jpg'))) {
                        $sidebarLogo = asset('images/cryptonest.jpg');
                    } elseif (file_exists(public_path('images/cryptonest.svg'))) {
                        $sidebarLogo = asset('images/cryptonest.svg');
                    }
                @endphp
                @if ($sidebarLogo)
                    <img src="{{ $sidebarLogo }}" alt="Crypto Nest" class="site-logo sidebar-logo me-2">
                @endif
                <span>Crypto Nest</span>
            </h5>
            <button type="button" class="btn-close text-reset ms-3" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        
        <div class="offcanvas-body pt-3">
            <!-- User Info -->
            @auth
                <div class="user-info mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="d-flex align-items-center">
                            <div class="user-avatar">
                                <i class="bi bi-person-circle fs-1"></i>
                            </div>
                            <div class="ms-2">
                                <div class="fw-bold">{{ Auth::user()->name }}</div>
                                @php
                                    $displayUid = optional(Auth::user())->user_id ?: Auth::id();
                                @endphp
                                <div class="text-muted small">UID: {{ str_pad($displayUid, 6, '0', STR_PAD_LEFT) }}</div>
                            </div>
                        </div>
                        <div class="credit-section">
                            <div class="credit-badge">
                                <i class="bi bi-coin me-1"></i>
                                {{ number_format(600) }}
                            </div>
                            <button class="btn btn-link text-decoration-none p-0 ms-1" type="button" title="Credit Info">
                                <i class="bi bi-info-circle text-muted"></i>
                            </button>
                        </div>
                    </div>
                    <div class="d-grid gap-2">
                        <a href="#" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-gear me-1"></i> Account Settings
                        </a>
                    </div>
                </div>
            @else
                <div class="login-prompt mb-4">
                    <div class="text-center mb-3">
                        <i class="bi bi-shield-lock fs-1 text-primary"></i>
                        <h5 class="mt-2">Welcome to Crypto Nest</h5>
                        <p class="text-muted small">Sign in to access all features</p>
                    </div>
                    <div class="row g-2 px-4">
                        <div class="col-6">
                            <a href="{{ route('login') }}" class="btn btn-light py-2 w-100">
                                Sign In
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('register') }}" class="btn btn-primary py-2 w-100">
                                Create Account
                            </a>
                        </div>
                    </div>
                </div>
            @endauth

            <!-- Navigation -->
            <div class="function-label">Function</div>
            <div class="list-group">
                @auth
                    <a href="{{ url('/wallets') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                        <i class="bi bi-wallet2 me-2"></i> Wallets
                    </a>
                    <a href="{{ url('/transaction') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                        <i class="bi bi-arrow-left-right me-2"></i> Transaction
                    </a>
                    <a href="{{ url('/arbitrage') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                        <i class="bi bi-graph-up-arrow me-2"></i> Arbitrage
                    </a>
                    <a href="{{ url('/mining') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                        <i class="bi bi-cpu me-2"></i> Mining
                    </a>
                    <a href="{{ url('/lending') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                        <i class="bi bi-piggy-bank me-2"></i> Assisted Lending
                    </a>
                    <a href="{{ url('/trade/orders') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                        <i class="bi bi-card-list me-2"></i> Trade Orders
                    </a>
                    <a href="{{ url('/financial/record') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                        <i class="bi bi-journal-text me-2"></i> Financial Record
                    </a>
                @endauth

                <!-- Knowledge section - always visible -->
                <div class="list-group-item">
                    <a href="#knowledgeSubmenu" data-bs-toggle="collapse" class="d-flex align-items-center text-decoration-none text-dark">
                        <i class="bi bi-book me-2"></i>
                        <span class="flex-grow-1">Knowledge</span>
                        <i class="bi bi-chevron-down"></i>
                    </a>
                    <div class="collapse" id="knowledgeSubmenu">
                        <div class="list-group mt-2">
                            <a href="{{ url('/knowledge/faq') }}" class="list-group-item list-group-item-action border-0 ps-4">
                                Frequently Asked Questions
                            </a>
                            <a href="{{ asset('pdf/whitepaper.pdf') }}" target="_blank" rel="noopener" class="list-group-item list-group-item-action border-0 ps-4">
                                White Paper
                            </a>
                                    <a href="{{ url('/knowledge/service-agreement') }}" class="list-group-item list-group-item-action border-0 ps-4">
                                                Service Agreement
                                            </a>
                                    <a href="{{ asset('pdf/regulatorylicense.pdf') }}" target="_blank" rel="noopener" class="list-group-item list-group-item-action border-0 ps-4">
                                        Regulatory License
                                    </a>
                        </div>
                    </div>
                </div>

                @auth
                    <!-- Logout option for authenticated users -->
                    <form action="{{ route('logout') }}" method="POST" class="mt-4">
                        @csrf
                        <button type="submit" class="list-group-item list-group-item-action d-flex align-items-center text-danger">
                            <i class="bi bi-box-arrow-right me-2"></i> Sign Out
                        </button>
                    </form>
                @endauth
            </div>
        </div>
    </div>
</nav>

<style>
/* Update existing styles */
.credit-badge {
    background: linear-gradient(135deg, var(--success), #20c997);
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 50rem;
    font-size: 0.875rem;
    display: inline-flex;
    align-items: center;
    box-shadow: 0 2px 4px rgba(40, 167, 69, 0.2);
}

.credit-section {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.credit-section .btn-link {
    font-size: 0.875rem;
}

/* New styles for user info section */
.user-info {
    padding: 1rem;
    background-color: var(--bg-surface);
    border-radius: 0.5rem;
    border: 1px solid var(--border);
}

.user-avatar {
    color: var(--primary);
    line-height: 1;
}

.login-prompt {
    padding: 2rem 0;
}

.login-prompt .btn {
    border-radius: 8px;
    font-weight: 500;
}

.login-prompt .btn-light {
    background-color: var(--bg-surface);
    border: 1px solid var(--border);
    color: var(--text);
}

.login-prompt .btn-light:hover {
    background-color: var(--bg-surface-hover);
}

/* Enhance list group items */
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
    background-color: rgba(var(--primary-rgb), 0.1);
    color: var(--primary);
    transform: translateX(4px);
}

.list-group-item i {
    transition: transform 0.15s ease-in-out;
}

.list-group-item:hover i {
    transform: scale(1.1);
}

/* Knowledge submenu enhancements */
#knowledgeSubmenu .list-group-item {
    padding-left: 2.5rem;
    font-size: 0.9rem;
}

/* Sign out button styling */
.text-danger {
    color: #dc3545 !important;
}

.text-danger:hover {
    background-color: rgba(220, 53, 69, 0.1) !important;
    color: #dc3545 !important;
}

/* Sidebar header enhancements */
.offcanvas-header {
    padding: 1rem;
    border-bottom: 1px solid var(--border);
}

.offcanvas-header .btn-close {
    margin-left: 1rem;
    padding: 0.5rem;
}

/* Logo styles */
.site-logo {
    height: 34px;
    width: 34px;
    object-fit: cover;
    border-radius: 50%;
    border: 2px solid rgba(255,255,255,0.06);
    background: rgba(255,255,255,0.03);
}

.sidebar-logo {
    height: 28px;
    width: 28px;
}
</style>
