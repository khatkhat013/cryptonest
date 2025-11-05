@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Header Card -->
    <div class="card bg-primary text-white border-0 mb-4" style="border-radius: 20px;">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <a href="{{ url('/') }}" class="text-white">
                    <i class="bi bi-arrow-left fs-4"></i>
                </a>
                <h3 class="mb-0 text-white">Arbitrage</h3>
                <a href="{{ route('transaction.index') }}?tab=mining" class="text-white" title="History">
                    <i class="bi bi-clock-history fs-4"></i>
                </a>
            </div>
            <div class="text-center">
                <h3 class="mb-3">US${{ number_format($totalEarned ?? 0, 2) }}</h3>
                <a href="{{ route('custody.order') }}" class="btn btn-outline-light rounded-pill px-4">
                    <i class="bi bi-lock-fill me-2"></i>
                    Custody Order
                </a>
            </div>
        </div>
    </div>

    <!-- Navigation Links -->
    <div class="arbitrage-card mb-4">
        <div class="list-group list-group-flush">
            <a href="{{ route('ai.robots') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center p-3">
                <span>How Artificial Intelligence Robots Work</span>
                <i class="bi bi-chevron-right"></i>
            </a>
        </div>
    </div>

    <!-- Investment Plans -->
    <!-- A Plan -->
    <div class="arbitrage-card mt-4">
        <div class="p-4">
            <!-- Card Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center">
                    <div class="plan-logo me-3 bg-primary bg-opacity-10">
                        <span class="h5 mb-0 text-primary fw-bold">A</span>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <h6 class="mb-0 fw-semibold">1 Day</h6>
                            <span class="badge bg-success bg-opacity-10 text-success px-2 py-1 rounded-pill small">A Plan</span>
                        </div>
                        <small class="text-muted">Quick investment</small>
                    </div>
                </div>
                @php
                    $cfg = config('arbitrage.plans.A', []);
                    $max = isset($cfg['max_times']) ? intval($cfg['max_times']) : 2;
                    $totalStarts = 0;
                    if (auth()->check() && \Illuminate\Support\Facades\Schema::hasTable('ai_arbitrage_plans')) {
                        // Count total starts (any status) so completed starts still count toward the limit
                        $totalStarts = \Illuminate\Support\Facades\DB::table('ai_arbitrage_plans')
                            ->where('user_id', auth()->id())
                            ->where('plan_name', 'A')
                            ->count();
                    }
                @endphp
                <span class="badge bg-warning text-white px-3 py-2 rounded-pill">{{ $totalStarts }}/{{ $max }} Times</span>
            </div>

            <!-- Card Body -->
            <div class="row g-4 mb-4">
                <div class="col-6 text-center">
                    <div class="d-flex flex-column align-items-center bg-light rounded-4 p-3">
                        <h6 class="text-muted mb-1">Quantity</h6>
                        <span class="fw-semibold fs-6">$500-2000</span>
                    </div>
                </div>
                <div class="col-6 text-center">
                    <div class="d-flex flex-column align-items-center bg-light rounded-4 p-3">
                        <h6 class="text-muted mb-1">Daily Revenue</h6>
                        <span class="fw-semibold fs-6 text-success">1.60-1.70%</span>
                    </div>
                </div>
            </div>

            <!-- Card Footer -->
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted mb-2">Currency</div>
                    <div class="d-flex gap-2">
                        @php
                            $arbSymbols = ['btc','eth','usdt','doge','xrp'];
                        @endphp
                        @foreach($arbSymbols as $s)
                            @php $local = public_path('images/icons/' . $s . '.svg'); @endphp
                            @if(file_exists($local))
                                <img src="{{ asset('images/icons/' . $s . '.svg') }}" alt="{{ strtoupper($s) }}" width="24" height="24">
                            @else
                                <img src="{{ asset('images/icons/' . $s . '.svg') }}" alt="{{ strtoupper($s) }}" width="24" height="24">
                            @endif
                        @endforeach
                    </div>
                </div>
                @php $disabledA = ($totalStarts >= $max); @endphp
                <a href="{{ $disabledA ? 'javascript:void(0);' : route('arbitrage.aplan') . '?plan=A' }}" class="btn {{ $disabledA ? 'btn-link text-muted disabled' : 'btn-link text-primary' }} d-flex align-items-center justify-content-center" {{ $disabledA ? 'aria-disabled=true' : '' }} style="text-decoration:none;">
                    <span class="me-2">{{ $disabledA ? 'Limit Reached' : 'Start' }}</span>
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- B Plan -->
    <div class="arbitrage-card mt-4">
        <div class="p-4">
            <!-- Card Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center">
                    <div class="plan-logo me-3 bg-primary bg-opacity-10">
                        <span class="h5 mb-0 text-primary fw-bold">B</span>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <h6 class="mb-0 fw-semibold">3 Day</h6>
                            <span class="badge bg-success bg-opacity-10 text-success px-2 py-1 rounded-pill small">B Plan</span>
                        </div>
                        <small class="text-muted">Medium term investment</small>
                    </div>
                </div>
                @php
                    $cfg = config('arbitrage.plans.B', []);
                    $max = isset($cfg['max_times']) ? intval($cfg['max_times']) : 2;
                    $totalStarts = 0;
                    if (auth()->check() && \Illuminate\Support\Facades\Schema::hasTable('ai_arbitrage_plans')) {
                        $totalStarts = \Illuminate\Support\Facades\DB::table('ai_arbitrage_plans')
                            ->where('user_id', auth()->id())
                            ->where('plan_name', 'B')
                            ->count();
                    }
                @endphp
                <span class="badge bg-warning text-white px-3 py-2 rounded-pill">{{ $totalStarts }}/{{ $max }} Times</span>
            </div>

            <!-- Card Body -->
            <div class="row g-4 mb-4">
                <div class="col-6 text-center">
                    <div class="d-flex flex-column align-items-center bg-light rounded-4 p-3">
                        <h6 class="text-muted mb-1">Quantity</h6>
                        <span class="fw-semibold fs-6">$2001-10000</span>
                    </div>
                </div>
                <div class="col-6 text-center">
                    <div class="d-flex flex-column align-items-center bg-light rounded-4 p-3">
                        <h6 class="text-muted mb-1">Daily Revenue</h6>
                        <span class="fw-semibold fs-6 text-success">1.90-2.10%</span>
                    </div>
                </div>
            </div>

            <!-- Card Footer -->
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted mb-2">Currency</div>
                    <div class="d-flex gap-2">
                        @php $arbSymbols = ['btc','eth','usdt','doge','xrp']; @endphp
                        @foreach($arbSymbols as $s)
                            @php $local = public_path('images/icons/' . $s . '.svg'); @endphp
                            @if(file_exists($local))
                                <img src="{{ asset('images/icons/' . $s . '.svg') }}" alt="{{ strtoupper($s) }}" width="24" height="24">
                            @else
                                <img src="{{ asset('images/icons/' . $s . '.svg') }}" alt="{{ strtoupper($s) }}" width="24" height="24">
                            @endif
                        @endforeach
                    </div>
                </div>
                @php $disabledB = ($totalStarts >= $max); @endphp
                <a href="{{ $disabledB ? 'javascript:void(0);' : route('arbitrage.aplan') . '?plan=B' }}" class="btn {{ $disabledB ? 'btn-link text-muted disabled' : 'btn-link text-primary' }} d-flex align-items-center" {{ $disabledB ? 'aria-disabled=true' : '' }} style="text-decoration:none;">
                    <span class="me-2">{{ $disabledB ? 'Limit Reached' : 'Start' }}</span>
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- C Plan -->
    <div class="arbitrage-card mt-4">
        <div class="p-4">
            <!-- Card Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center">
                    <div class="plan-logo me-3 bg-primary bg-opacity-10">
                        <span class="h5 mb-0 text-primary fw-bold">C</span>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <h6 class="mb-0 fw-semibold">3 Days</h6>
                            <span class="badge bg-success bg-opacity-10 text-success px-2 py-1 rounded-pill small">C Plan</span>
                        </div>
                        <small class="text-muted">Higher investment</small>
                    </div>
                </div>
                @php
                    $cfg = config('arbitrage.plans.C', []);
                    $max = isset($cfg['max_times']) ? intval($cfg['max_times']) : 2;
                    $totalStarts = 0;
                    if (auth()->check() && \Illuminate\Support\Facades\Schema::hasTable('ai_arbitrage_plans')) {
                        $totalStarts = \Illuminate\Support\Facades\DB::table('ai_arbitrage_plans')
                            ->where('user_id', auth()->id())
                            ->where('plan_name', 'C')
                            ->count();
                    }
                @endphp
                <span class="badge bg-warning text-white px-3 py-2 rounded-pill">{{ $totalStarts }}/{{ $max }} Times</span>
            </div>

            <!-- Card Body -->
            <div class="row g-4 mb-4">
                <div class="col-6 text-center">
                    <div class="d-flex flex-column align-items-center bg-light rounded-4 p-3">
                        <h6 class="text-muted mb-1">Quantity</h6>
                        <span class="fw-semibold fs-6">$10001-50000</span>
                    </div>
                </div>
                <div class="col-6 text-center">
                    <div class="d-flex flex-column align-items-center bg-light rounded-4 p-3">
                        <h6 class="text-muted mb-1">Daily Revenue</h6>
                        <span class="fw-semibold fs-6 text-success">2.20-2.70%</span>
                    </div>
                </div>
            </div>

            <!-- Card Footer -->
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted mb-2">Currency</div>
                    <div class="d-flex gap-2">
                        @php $arbSymbols = ['btc','eth','usdt','doge','xrp']; @endphp
                        @foreach($arbSymbols as $s)
                            @php $local = public_path('images/icons/' . $s . '.svg'); @endphp
                            @if(file_exists($local))
                                <img src="{{ asset('images/icons/' . $s . '.svg') }}" alt="{{ strtoupper($s) }}" width="24" height="24">
                            @else
                                <img src="{{ asset('images/icons/' . $s . '.svg') }}" alt="{{ strtoupper($s) }}" width="24" height="24">
                            @endif
                        @endforeach
                    </div>
                </div>
                @php $disabledC = ($totalStarts >= $max); @endphp
                <a href="{{ $disabledC ? 'javascript:void(0);' : route('arbitrage.aplan') . '?plan=C' }}" class="btn {{ $disabledC ? 'btn-link text-muted disabled' : 'btn-link text-primary' }} d-flex align-items-center" {{ $disabledC ? 'aria-disabled=true' : '' }} style="text-decoration:none;">
                    <span class="me-2">{{ $disabledC ? 'Limit Reached' : 'Start' }}</span>
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- D Plan -->
    <div class="arbitrage-card mt-4">
        <div class="p-4">
            <!-- Card Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center">
                    <div class="plan-logo me-3 bg-primary bg-opacity-10">
                        <span class="h5 mb-0 text-primary fw-bold">D</span>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <h6 class="mb-0 fw-semibold">7 Days</h6>
                            <span class="badge bg-success bg-opacity-10 text-success px-2 py-1 rounded-pill small">D Plan</span>
                        </div>
                        <small class="text-muted">Premium investment</small>
                    </div>
                </div>
                @php
                    $cfg = config('arbitrage.plans.D', []);
                    $max = isset($cfg['max_times']) ? intval($cfg['max_times']) : 3;
                    $totalStarts = 0;
                    if (auth()->check() && \Illuminate\Support\Facades\Schema::hasTable('ai_arbitrage_plans')) {
                        $totalStarts = \Illuminate\Support\Facades\DB::table('ai_arbitrage_plans')
                            ->where('user_id', auth()->id())
                            ->where('plan_name', 'D')
                            ->count();
                    }
                @endphp
                <span class="badge bg-warning text-white px-3 py-2 rounded-pill">{{ $totalStarts }}/{{ $max }} Times</span>
            </div>

            <!-- Card Body -->
            <div class="row g-4 mb-4">
                <div class="col-6 text-center">
                    <div class="d-flex flex-column align-items-center bg-light rounded-4 p-3">
                        <h6 class="text-muted mb-1">Quantity</h6>
                        <span class="fw-semibold fs-6">$50001-200000</span>
                    </div>
                </div>
                <div class="col-6 text-center">
                    <div class="d-flex flex-column align-items-center bg-light rounded-4 p-3">
                        <h6 class="text-muted mb-1">Daily Revenue</h6>
                        <span class="fw-semibold fs-6 text-success">2.80-3.30%</span>
                    </div>
                </div>
            </div>

            <!-- Card Footer -->
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted mb-2">Currency</div>
                    <div class="d-flex gap-2">
                        @php $arbSymbols = ['btc','eth','usdt','doge','xrp']; @endphp
                        @foreach($arbSymbols as $s)
                            @php $local = public_path('images/icons/' . $s . '.svg'); @endphp
                            @if(file_exists($local))
                                <img src="{{ asset('images/icons/' . $s . '.svg') }}" alt="{{ strtoupper($s) }}" width="24" height="24">
                            @else
                                <img src="{{ asset('images/icons/' . $s . '.svg') }}" alt="{{ strtoupper($s) }}" width="24" height="24">
                            @endif
                        @endforeach
                    </div>
                </div>
                @php $disabledD = ($totalStarts >= $max); @endphp
                <a href="{{ $disabledD ? 'javascript:void(0);' : route('arbitrage.aplan') . '?plan=D' }}" class="btn {{ $disabledD ? 'btn-outline-secondary disabled' : 'btn-primary' }}" {{ $disabledD ? 'aria-disabled=true' : '' }}>{{ $disabledD ? 'Limit Reached' : 'Pre-Order' }}</a>
            </div>
        </div>
    </div>

    <!-- E Plan -->
    <div class="arbitrage-card mt-4">
        <div class="p-4">
            <!-- Card Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center">
                    <div class="plan-logo me-3 bg-primary bg-opacity-10">
                        <span class="h5 mb-0 text-primary fw-bold">E</span>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <h6 class="mb-0 fw-semibold">10 Days</h6>
                            <span class="badge bg-success bg-opacity-10 text-success px-2 py-1 rounded-pill small">E Plan</span>
                        </div>
                        <small class="text-muted">Elite investment</small>
                    </div>
                </div>
                @php
                    $cfg = config('arbitrage.plans.E', []);
                    $max = isset($cfg['max_times']) ? intval($cfg['max_times']) : 5;
                    $totalStarts = 0;
                    if (auth()->check() && \Illuminate\Support\Facades\Schema::hasTable('ai_arbitrage_plans')) {
                        $totalStarts = \Illuminate\Support\Facades\DB::table('ai_arbitrage_plans')
                            ->where('user_id', auth()->id())
                            ->where('plan_name', 'E')
                            ->count();
                    }
                @endphp
                <span class="badge bg-warning text-white px-3 py-2 rounded-pill">{{ $totalStarts }}/{{ $max }} Times</span>
            </div>

            <!-- Card Body -->
            <div class="row g-4 mb-4">
                <div class="col-6 text-center">
                    <div class="d-flex flex-column align-items-center bg-light rounded-4 p-3">
                        <h6 class="text-muted mb-1">Quantity</h6>
                        <span class="fw-semibold fs-6">$200001-500000</span>
                    </div>
                </div>
                <div class="col-6 text-center">
                    <div class="d-flex flex-column align-items-center bg-light rounded-4 p-3">
                        <h6 class="text-muted mb-1">Daily Revenue</h6>
                        <span class="fw-semibold fs-6 text-success">3.50-5.50%</span>
                    </div>
                </div>
            </div>

            <!-- Card Footer -->
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted mb-2">Currency</div>
                    <div class="d-flex gap-2">
                        @php $arbSymbols = ['btc','eth','usdt','doge','xrp']; @endphp
                        @foreach($arbSymbols as $s)
                            @php $local = public_path('images/icons/' . $s . '.svg'); @endphp
                            @if(file_exists($local))
                                <img src="{{ asset('images/icons/' . $s . '.svg') }}" alt="{{ strtoupper($s) }}" width="24" height="24">
                            @else
                                <img src="{{ asset('images/icons/' . $s . '.svg') }}" alt="{{ strtoupper($s) }}" width="24" height="24">
                            @endif
                        @endforeach
                    </div>
                </div>
                @php $disabledE = ($totalStarts >= $max); @endphp
                <a href="{{ $disabledE ? 'javascript:void(0);' : route('arbitrage.aplan') . '?plan=E' }}" class="btn {{ $disabledE ? 'btn-outline-secondary disabled' : 'btn-primary' }}" {{ $disabledE ? 'aria-disabled=true' : '' }}>{{ $disabledE ? 'Limit Reached' : 'Pre-Order' }}</a>
            </div>
        </div>
    </div>

    <!-- VIP Plan -->
    <div class="arbitrage-card mt-4">
        <div class="p-4">
            <!-- Card Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center">
                    <div class="plan-logo me-3 bg-primary bg-opacity-10">
                        <span class="h5 mb-0 text-primary fw-bold">VIP</span>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <h6 class="mb-0 fw-semibold">15 Days</h6>
                            <span class="badge bg-success bg-opacity-10 text-success px-2 py-1 rounded-pill small">VIP Plan</span>
                        </div>
                        <small class="text-muted">VIP investment</small>
                    </div>
                </div>
                @php
                    $cfg = config('arbitrage.plans.VIP', []);
                    $max = isset($cfg['max_times']) ? intval($cfg['max_times']) : 7;
                    $totalStarts = 0;
                    if (auth()->check() && \Illuminate\Support\Facades\Schema::hasTable('ai_arbitrage_plans')) {
                        $totalStarts = \Illuminate\Support\Facades\DB::table('ai_arbitrage_plans')
                            ->where('user_id', auth()->id())
                            ->where('plan_name', 'VIP')
                            ->count();
                    }
                @endphp
                <span class="badge bg-warning text-white px-3 py-2 rounded-pill">{{ $totalStarts }}/{{ $max }} Times</span>
            </div>

            <!-- Card Body -->
            <div class="row g-4 mb-4">
                <div class="col-6">
                    <div class="d-flex flex-column">
                        <span class="text-muted mb-2">Quantity</span>
                        <span class="fw-semibold fs-5">$500001-3000000</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="d-flex flex-column">
                        <span class="text-muted mb-2">Daily Revenue</span>
                        <span class="fw-semibold fs-5 text-success">5.50-8.50%</span>
                    </div>
                </div>
            </div>

            <!-- Card Footer -->
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted mb-2">Currency</div>
                    <div class="d-flex gap-2">
                        @php $arbSymbols = ['btc','eth','usdt','doge','xrp']; @endphp
                        @foreach($arbSymbols as $s)
                            @php $local = public_path('images/icons/' . $s . '.svg'); @endphp
                            @if(file_exists($local))
                                <img src="{{ asset('images/icons/' . $s . '.svg') }}" alt="{{ strtoupper($s) }}" width="24" height="24">
                            @else
                                <img src="{{ asset('images/icons/' . $s . '.svg') }}" alt="{{ strtoupper($s) }}" width="24" height="24">
                            @endif
                        @endforeach
                    </div>
                </div>
                @php $disabledVIP = ($totalStarts >= $max); @endphp
                <a href="{{ $disabledVIP ? 'javascript:void(0);' : route('arbitrage.aplan') . '?plan=VIP' }}" class="btn {{ $disabledVIP ? 'btn-outline-secondary disabled' : 'btn-primary' }}" {{ $disabledVIP ? 'aria-disabled=true' : '' }}>{{ $disabledVIP ? 'Limit Reached' : 'Pre-Order' }}</a>
            </div>
        </div>
    </div>

    <!-- Crypto Nest Plan -->
    <div class="arbitrage-card mt-4">
        <div class="p-4">
            <!-- Card Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center">
                    <div class="plan-logo me-3 bg-primary bg-opacity-10">
                        <span class="h5 mb-0 text-primary fw-bold">CN</span>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <h6 class="mb-0 fw-semibold">20 Days</h6>
                            <span class="badge bg-success bg-opacity-10 text-success px-2 py-1 rounded-pill small">Crypto Nest</span>
                        </div>
                        <small class="text-muted">Ultimate investment</small>
                    </div>
                </div>
                @php
                    $cfg = config('arbitrage.plans.CN', []);
                    $max = isset($cfg['max_times']) ? intval($cfg['max_times']) : 9;
                    $totalStarts = 0;
                    if (auth()->check() && \Illuminate\Support\Facades\Schema::hasTable('ai_arbitrage_plans')) {
                        $totalStarts = \Illuminate\Support\Facades\DB::table('ai_arbitrage_plans')
                            ->where('user_id', auth()->id())
                            ->where('plan_name', 'CN')
                            ->count();
                    }
                @endphp
                <span class="badge bg-warning text-white px-3 py-2 rounded-pill">{{ $totalStarts }}/{{ $max }} Times</span>
            </div>

            <!-- Card Body -->
            <div class="row g-4 mb-4">
                <div class="col-6 text-center">
                    <div class="d-flex flex-column align-items-center bg-light rounded-4 p-3">
                        <h6 class="text-muted mb-1">Quantity</h6>
                        <span class="fw-semibold fs-6">$3000001-10000000</span>
                    </div>
                </div>
                <div class="col-6 text-center">
                    <div class="d-flex flex-column align-items-center bg-light rounded-4 p-3">
                        <h6 class="text-muted mb-1">Daily Revenue</h6>
                        <span class="fw-semibold fs-6 text-success">6.50-10.00%</span>
                    </div>
                </div>
            </div>

            <!-- Card Footer -->
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted mb-2">Currency</div>
                    <div class="d-flex gap-2">
                        @php $arbSymbols = ['btc','eth','usdt','doge','xrp','bnb']; @endphp
                        @foreach($arbSymbols as $s)
                            @php $local = public_path('images/icons/' . $s . '.svg'); @endphp
                            @if(file_exists($local))
                                <img src="{{ asset('images/icons/' . $s . '.svg') }}" alt="{{ strtoupper($s) }}" width="24" height="24">
                            @else
                                <img src="{{ asset('images/coins/' . $s . '.png') }}" alt="{{ strtoupper($s) }}" width="24" height="24">
                            @endif
                        @endforeach
                    </div>
                </div>
                @php $disabledCN = ($totalStarts >= $max); @endphp
                <a href="{{ $disabledCN ? 'javascript:void(0);' : route('arbitrage.aplan') . '?plan=CN' }}" class="btn {{ $disabledCN ? 'btn-outline-secondary disabled' : 'btn-primary' }}" {{ $disabledCN ? 'aria-disabled=true' : '' }}>{{ $disabledCN ? 'Limit Reached' : 'Pre-Order' }}</a>
            </div>
        </div>
    </div>

    <!-- AI Intelligent Mining Section -->
    <div class="card mt-4 mb-4" style="border-radius: 20px;">
        <div class="card-body p-4">
            <div class="text-center mb-4">
                <h4 class="mb-2 text-dark fw-bold">AI Intelligent Mining</h4>
                <p class="mb-4 text-primary fw-semibold">Buy Low, Sell High</p>
            </div>
            <div class="ratio ratio-16x9 rounded overflow-hidden">
                <iframe 
                    src="https://www.youtube.com/embed/VIDEO_ID" 
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                    allowfullscreen>
                </iframe>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.arbitrage-card {
    background: white;
    border-radius: 1rem;
    overflow: hidden;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
}

.plan-logo {
    width: 40px;
    height: 40px;
    background: rgba(59, 130, 246, 0.1);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.badge.bg-primary-subtle {
    background-color: rgba(59, 130, 246, 0.1) !important;
    font-weight: 500;
}

.btn-link {
    text-decoration: none;
}

.btn-link:hover {
    transform: translateX(4px);
    transition: transform 0.2s ease;
}

.bg-light {
    background-color: #f8f9fa !important;
    transition: all 0.3s ease;
}

.rounded-4 {
    border-radius: 1rem !important;
}

.arbitrage-card .row.g-4 > div:hover .bg-light {
    background-color: #f0f2f5 !important;
    transform: translateY(-2px);
}

.fs-6 {
    font-size: 0.9rem !important;
}

/* removed dark-mode styles (site is light-mode only) */
</style>
@endpush