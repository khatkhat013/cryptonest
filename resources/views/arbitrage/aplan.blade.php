@extends('layouts.app')

@section('content')
@php
    $plan = strtoupper(request()->query('plan', 'A'));
    $plans = config('arbitrage.plans', []);
    $cfg = $plans[$plan] ?? ($plans['A'] ?? ['duration' => '1 Day','quantity_label' => '$500-2000','revenue_label' => '1.60-1.70%']);
@endphp

<div class="container">
    <nav style="background:#1976d2; border-radius:20px; padding:8px 12px; display:flex; align-items:center; justify-content:space-between; color:white; margin-bottom:16px;">
        <a href="{{ url('/arbitrage') }}" style="color:white; text-decoration:none; display:flex; align-items:center; gap:8px;">
            <i class="bi bi-arrow-left" style="font-size:18px;"></i>
        </a>
        <div style="flex:1; text-align:center; font-weight:600;">{{ $plan }} Plan</div>
        <a href="{{ url('/arbitrage/custody-order') }}" style="color:white; text-decoration:none; display:flex; align-items:center; gap:8px;">
            <i class="bi bi-clock-history" style="font-size:18px;"></i>
        </a>
    </nav>
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-body p-4 pt-0">
                <div class="text-center mb-4">
                    <h4 class="modal-title fw-bold mb-3">Join AI Arbitrage</h4>
                    <div class="d-flex align-items-center justify-content-center gap-2">
                        <div class="plan-badge bg-primary bg-opacity-10 px-4 py-2 rounded-pill">
                            <span class="plan-name h5 mb-0 text-primary">{{ $plan }} Plan</span>
                            <span class="plan-duration badge bg-white text-primary ms-2">{{ $cfg['duration'] }}</span>
                        </div>
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-6 text-center">
                        <div class="d-flex flex-column align-items-center bg-light rounded-4 p-3 h-100">
                            <h6 class="text-muted mb-2">Quantity</h6>
                            <div class="plan-quantity fw-bold">{{ $cfg['quantity_label'] ?? ($cfg['quantity'] ?? '') }}</div>
                        </div>
                    </div>
                    <div class="col-6 text-center">
                        <div class="d-flex flex-column align-items-center bg-light rounded-4 p-3 h-100">
                            <h6 class="text-muted mb-2">Daily Revenue</h6>
                            <div class="plan-revenue fw-bold text-success">{{ $cfg['revenue_label'] ?? ($cfg['revenue'] ?? '') }}</div>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <h6 class="text-muted d-flex align-items-center mb-3">
                        <i class="bi bi-currency-exchange me-2"></i>
                        Supported Currencies
                    </h6>
                    <div class="d-flex align-items-center gap-2 mb-4">
                        @php $arbSymbols = ['btc','eth','usdt','doge','xrp','bnb']; @endphp
                        @foreach($arbSymbols as $s)
                            @php $local = public_path('images/icons/' . $s . '.svg'); @endphp
                            @if(file_exists($local))
                                <img src="{{ asset('images/icons/' . $s . '.svg') }}" alt="{{ strtoupper($s) }}" width="24" height="24">
                            @else
                                <img src="{{ asset('images/icons/' . $s . '.svg') }}" alt="{{ strtoupper($s) }}" width="24" height="24">
                            @endif
                        @endforeach
                    </div>

                    <form id="planDetailsForm" method="POST" action="{{ url('/arbitrage') }}">
                        @csrf
                        <input type="hidden" name="plan_name" id="plan_name" value="{{ $plan }}">
                        <input type="hidden" name="duration_days" id="duration_days" value="{{ preg_replace('/\D+/','',$cfg['duration']) }}">
                        <div class="custody-input mb-4">
                            <label for="plan-quantity" class="form-label d-flex align-items-center mb-2">
                                <i class="bi bi-wallet2 me-2"></i>
                                Custody Quantity
                            </label>
                            <div class="input-group input-group-lg has-validation">
                                <input
                                    type="number"
                                    name="quantity"
                                    id="plan-quantity"
                                    class="form-control form-control-lg bg-light border-0"
                                    placeholder="Enter amount"
                                    step="0.01"
                                    required
                                
                                >
                                <span class="input-group-text bg-light border-0">USDT</span>
                                <div class="invalid-feedback" id="plan-quantity-invalid">Please enter an amount within range</div>
                            </div>
                            
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100 rounded-3 d-flex align-items-center justify-content-center gap-2" id="plan-submit-btn">
                            <i class="bi bi-rocket-takeoff"></i>
                            <span>Join Now</span>
                        </button>
                    </form>
                </div>

                <div class="features-section bg-light rounded-4 p-4 mb-4">
                    <h6 class="text-center d-flex align-items-center justify-content-center gap-2 mb-4">
                        <i class="bi bi-shield-check text-primary"></i>
                        Currently Hosting
                    </h6>
                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-wallet2 text-primary"></i>
                            <span>Daily earnings are sent to your USDT wallet.</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-robot text-primary"></i>
                            <span>Artificial Intelligence works 24 hours</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success overlay (copied from modal) -->
    <div id="arbitrage-success-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:2000; align-items:center; justify-content:center;">
        <div style="width:320px; height:320px; display:flex; align-items:center; justify-content:center;">
            <dotlottie-wc id="arbitrage-success-lottie" src="https://lottie.host/266c9a2c-6e00-466a-9e5d-768dbbb86059/2NCBTOo0Yu.lottie" style="width: 300px; height: 300px;" autoplay></dotlottie-wc>
        </div>
    </div>

    <!-- Centered alert modal for errors/info -->
    <div class="modal fade" id="arbitrageAlertModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="arbitrageAlertModalTitle">Notice</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="arbitrageAlertModalBody" class="mb-0">Something went wrong</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    (function(){
        const cfgMap = {
            @foreach($plans as $k => $p)
                @php
                    // prefer quantity_label from config; fall back to min-max or legacy 'quantity'
                    $qtyLabel = $p['quantity_label'] ?? (isset($p['min']) && isset($p['max']) ? ($p['min'].'-'.$p['max']) : ($p['quantity'] ?? '500-2000'));
                    $qtyClean = preg_replace('/[^0-9\-]/','', $qtyLabel);
                @endphp
                '{{ $k }}': { qty: '{{ $qtyClean }}' },
            @endforeach
        };

        const plan = '{{ $plan }}';
        const cfg = cfgMap[plan] || cfgMap['A'];
    const parts = (cfg.qty || '500-2000').split('-').map(x => parseFloat(x)||0);
    const min = parts[0] || 500;
    const max = parts[1] || 2000;

        const form = document.getElementById('planDetailsForm');
        const qty = document.getElementById('plan-quantity');
        const invalid = document.getElementById('plan-quantity-invalid');
        const submitBtn = document.getElementById('plan-submit-btn');

        qty.min = min; qty.max = max; qty.placeholder = `Enter amount between ${min}-${max}`;

        form.addEventListener('submit', async function(e){
            e.preventDefault();
            const val = parseFloat(qty.value);
            if (isNaN(val) || val < min || val > max) {
                qty.classList.add('is-invalid');
                invalid.textContent = `Amount must be between ${min} and ${max} USDT`;
                return;
            }

            // hide form button to show progress
            submitBtn.disabled = true;

            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            try {
                const res = await fetch(form.action, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token || '' },
                    body: JSON.stringify({ quantity: val, plan_name: plan, duration_days: document.getElementById('duration_days')?.value || 1 })
                });
                const data = await res.json().catch(()=>null);
                if (!res.ok) {
                    const msg = (data && data.message) || 'Error creating plan';
                    showArbitrageAlert('Plan creation failed', msg);
                    submitBtn.disabled = false;
                    return;
                }

                // show overlay and start animation immediately without an extra blank frame
                const overlay = document.getElementById('arbitrage-success-overlay');
                const lottieEl = document.getElementById('arbitrage-success-lottie');
                if (overlay) overlay.style.display = 'flex';
                if (lottieEl) {
                    try {
                        // prefer a play() method if the webcomponent exposes it
                        if (typeof lottieEl.play === 'function') {
                            lottieEl.play();
                        } else {
                            // fallback: replace the node with a clone to restart the animation instantly
                            const clone = lottieEl.cloneNode(true);
                            lottieEl.parentNode.replaceChild(clone, lottieEl);
                        }
                    } catch (e) {
                        console.debug('Failed to restart lottie animation cleanly', e);
                    }
                }
                // after a short delay redirect back to the list
                setTimeout(()=> location.href = '{{ url('/arbitrage') }}', 2200);

            } catch (err) {
                console.error(err);
                showArbitrageAlert('Network error', 'Please check your connection and try again.');
                submitBtn.disabled = false;
            }
        });
    })();
    
    function showArbitrageAlert(title, message) {
        try {
            const titleEl = document.getElementById('arbitrageAlertModalTitle');
            const bodyEl = document.getElementById('arbitrageAlertModalBody');
            if (titleEl) titleEl.textContent = title || 'Notice';
            if (bodyEl) bodyEl.textContent = message || '';
            const modalEl = document.getElementById('arbitrageAlertModal');
            if (modalEl && typeof bootstrap !== 'undefined') {
                const bsModal = new bootstrap.Modal(modalEl);
                bsModal.show();
            } else if (modalEl) {
                // fallback: make it visible if bootstrap not present
                modalEl.style.display = 'block';
            } else {
                alert(message || title || 'Notice');
            }
        } catch (e) {
            console.error('showArbitrageAlert error', e);
            alert(message || title || 'Notice');
        }
    }
</script>
@endpush
