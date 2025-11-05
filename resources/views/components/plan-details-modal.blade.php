<!-- Plan Details Modal -->
@php
    // Precompute user's total starts per plan so the modal can disable Join when limits reached
    $planStartsMap = [];
    $plansCfg = config('arbitrage.plans', []);
    if (auth()->check() && \Illuminate\Support\Facades\Schema::hasTable('ai_arbitrage_plans')) {
        foreach ($plansCfg as $pname => $pcfg) {
            $planStartsMap[$pname] = \Illuminate\Support\Facades\DB::table('ai_arbitrage_plans')
                ->where('user_id', auth()->id())
                ->where('plan_name', $pname)
                ->count();
        }
    }
@endphp
<div class="modal fade" id="planDetailsModal" tabindex="-1" aria-labelledby="planDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header border-0 p-4 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 pt-0">
                <div class="text-center mb-4">
                    <h4 class="modal-title fw-bold mb-3" id="planDetailsModalLabel">Join AI Arbitrage</h4>
                    <div class="d-flex align-items-center justify-content-center gap-2">
                        <div class="plan-badge bg-primary bg-opacity-10 px-4 py-2 rounded-pill">
                            <span class="plan-name h5 mb-0 text-primary">A Plan</span>
                            <span class="plan-duration badge bg-white text-primary ms-2">1 Day</span>
                        </div>
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-6 text-center">
                        <div class="d-flex flex-column align-items-center bg-light rounded-4 p-3 h-100">
                            <h6 class="text-muted mb-2">Quantity</h6>
                            <div class="plan-quantity fw-bold">$500.00-2000.00</div>
                        </div>
                    </div>
                    <div class="col-6 text-center">
                        <div class="d-flex flex-column align-items-center bg-light rounded-4 p-3 h-100">
                            <h6 class="text-muted mb-2">Daily Revenue</h6>
                            @php $plans = config('arbitrage.plans', []); $modalPlan = $plans['A'] ?? null; @endphp
                            <div class="plan-revenue fw-bold text-success">{{ $modalPlan['revenue_label'] ?? '1.60-1.70%' }}</div>
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
                        <input type="hidden" name="plan_name" id="plan_name" value="A">
                        <input type="hidden" name="preorder" id="modal_preorder_input" value="0">
                        <input type="hidden" name="duration_days" id="duration_days" value="1">
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
                                    min="500"
                                    max="2000"
                                    step="0.01"
                                    required
                                
                                >
                                <span class="input-group-text bg-light border-0">USDT</span>
                                <div class="invalid-feedback" id="plan-quantity-invalid">Please enter an amount between 500 and 2000 USDT</div>
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

                <!-- removed duplicate non-submitting button to avoid confusion -->
            </div>
        </div>
    </div>
</div>
            <script>
                (function(){
                    const form = document.getElementById('planDetailsForm');
                    if (!form) return;

                    const qty = document.getElementById('plan-quantity');
                    const invalid = document.getElementById('plan-quantity-invalid');
                    const submitBtn = document.getElementById('plan-submit-btn');

                    // Prepare a small client-side map of user's starts and plan max limits
                    const _planStartsMap = @json($planStartsMap ?? []);
                    const _planCfgMap = @json(array_map(function($p){ return ['max_times' => isset($p['max_times']) ? intval($p['max_times']) : null, 'quantity_label' => $p['quantity_label'] ?? ($p['quantity'] ?? null)]; }, config('arbitrage.plans', [])));

                    // when modal is opened, populate it with the plan data from the clicked card
                    const modalEl = document.getElementById('planDetailsModal');
                    modalEl.addEventListener('show.bs.modal', function (evt) {
                        const trigger = evt.relatedTarget;
                        if (!trigger) return;
                        const plan = trigger.getAttribute('data-plan') || 'A';
                        const duration = trigger.getAttribute('data-duration') || '';
                        const quantityText = trigger.getAttribute('data-quantity') || '$500-2000';
                        const revenue = trigger.getAttribute('data-revenue') || '';

                        document.getElementById('plan_name').value = plan;
                        // If the trigger included a preorder flag (data-preorder), propagate it to the modal form
                        try {
                            const preorderFlag = trigger.getAttribute('data-preorder') ? 1 : 0;
                            const modalPre = document.getElementById('modal_preorder_input');
                            if (modalPre) modalPre.value = preorderFlag;
                            if (preorderFlag && submitBtn) {
                                // Show intent to preorder on the button
                                if (!submitBtn.dataset.origText) submitBtn.dataset.origText = submitBtn.innerHTML;
                                submitBtn.innerHTML = 'Pre-Order';
                            }
                        } catch (e) {}
                        // duration in days - extract number
                        const daysMatch = (duration || '').match(/(\d+)/);
                        document.getElementById('duration_days').value = daysMatch ? parseInt(daysMatch[1], 10) : 1;

                        // set placeholder and help text based on plan range
                        const qtyRange = quantityText.replace(/[^0-9\-]/g, ''); // e.g. 500-2000
                        const parts = qtyRange.split('-').map(p => parseFloat(p) || 0);
                        const min = parts[0] || 500;
                        const max = parts[1] || 2000;
                        const qtyInput = document.getElementById('plan-quantity');
                        qtyInput.min = min;
                        qtyInput.max = max;
                        qtyInput.placeholder = `Enter amount between ${min}-${max}`;
                        
                        document.getElementById('plan-quantity-invalid').textContent = `Please enter an amount between ${min} and ${max} USDT`;
                        // Check user's plan start limit and USDT balance; disable submit if necessary
                        const submitBtn = document.getElementById('plan-submit-btn');
                        try {
                            const planStarts = parseInt(_planStartsMap[plan] || 0, 10);
                            const planCfg = _planCfgMap[plan] || {};
                            const planMax = planCfg.max_times !== undefined ? planCfg.max_times : null;
                            if (planMax !== null && planStarts >= planMax) {
                                // disable the submit button and show helpful message (less prominent)
                                if (submitBtn) {
                                    submitBtn.disabled = true;
                                    submitBtn.dataset.origText = submitBtn.innerHTML;
                                    // replace content with plain text and make style less prominent
                                    submitBtn.textContent = 'Limit Reached';
                                    submitBtn.classList.remove('btn-primary');
                                    submitBtn.classList.add('btn-outline-secondary', 'disabled');
                                }
                                const invalidEl = document.getElementById('plan-quantity-invalid');
                                if (invalidEl) {
                                    invalidEl.textContent = `You have reached the maximum number of starts for plan ${plan} (${planStarts}/${planMax}).`;
                                    invalidEl.classList.add('d-block');
                                }
                                // still fetch balance in background but no need to re-enable
                            }
                        } catch (e) {
                            console.warn('Plan limit check failed', e);
                        }

                        // Fetch user's USDT balance and disable submit if below plan minimum
                        (async function(){
                            try {
                                const resp = await fetch('/api/wallet/balance/usdt', { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' });
                                if (!resp.ok) return;
                                const j = await resp.json().catch(() => null);
                                const bal = j && j.balance ? parseFloat(String(j.balance).replace(/,/g, '')) || 0 : 0;
                                // attach to modal for later checks
                                modalEl.__available_usdt_balance = bal;
                                if (bal < min) {
                                    // disable and show helpful message
                                    if (submitBtn) {
                                            submitBtn.disabled = true;
                                            submitBtn.dataset.origText = submitBtn.innerHTML;
                                            submitBtn.textContent = 'Insufficient balance';
                                            submitBtn.classList.remove('btn-primary');
                                            submitBtn.classList.add('btn-outline-secondary', 'disabled');
                                        }
                                    const invalidEl = document.getElementById('plan-quantity-invalid');
                                    if (invalidEl) {
                                        invalidEl.textContent = `Insufficient USDT balance. Available: ${bal.toFixed(2)} USDT (minimum ${min})`;
                                        invalidEl.classList.add('d-block');
                                    }
                                } else {
                                    const invalidEl = document.getElementById('plan-quantity-invalid');
                                    if (invalidEl) invalidEl.classList.remove('d-block');
                                    const submitBtn2 = document.getElementById('plan-submit-btn');
                                    if (submitBtn2) {
                                        // Only re-enable if we didn't already disable due to plan limit
                                        if (!(planCfg && planCfg.max_times !== undefined && parseInt(_planStartsMap[plan] || 0, 10) >= planCfg.max_times)) {
                                            submitBtn2.disabled = false;
                                            if (submitBtn2.dataset && submitBtn2.dataset.origText) {
                                                submitBtn2.innerHTML = submitBtn2.dataset.origText;
                                                // restore original styling
                                                submitBtn2.classList.remove('btn-outline-secondary', 'disabled');
                                                submitBtn2.classList.add('btn-primary');
                                                delete submitBtn2.dataset.origText;
                                            }
                                        }
                                    }
                                }
                            } catch (e) {
                                console.warn('Unable to fetch USDT balance for plan modal', e);
                            }
                        })();
                    });

                    function openModal() {
                        const modalEl = document.getElementById('planDetailsModal');
                        const bsModal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                        bsModal.show();
                    }

                    form.addEventListener('submit', async function(e){
                        e.preventDefault();

                        const min = parseFloat(qty.min || 500);
                        const max = parseFloat(qty.max || 2000);
                        const val = parseFloat(qty.value);
                        if (isNaN(val) || val < min || val > max) {
                            qty.classList.add('is-invalid');
                            if (invalid) invalid.textContent = `Amount must be between ${min} and ${max} USDT`;
                            qty.focus();
                            return;
                        }

                        // Ensure user's balance still covers the requested amount
                        try {
                            const respBal = await fetch('/api/wallet/balance/usdt', { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' });
                            if (respBal.ok) {
                                const jb = await respBal.json().catch(() => null);
                                const balNow = jb && jb.balance ? parseFloat(String(jb.balance).replace(/,/g, '')) || 0 : 0;
                                if (balNow < val) {
                                    alert(`Insufficient USDT balance. Available: ${balNow.toFixed(2)} USDT`);
                                    // reopen modal so user can adjust
                                    openModal();
                                    return;
                                }
                            }
                        } catch (e) {
                            console.warn('Balance check failed before submitting plan', e);
                        }

                        // hide modal immediately for UX
                        const modalEl = document.getElementById('planDetailsModal');
                        const bsModal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                        try { bsModal.hide(); } catch (err) { /* ignore */ }

                        // disable submit to prevent double submits
                        if (submitBtn) submitBtn.disabled = true;

                        // prepare fetch (include plan_name and duration_days)
                        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                        const planName = document.getElementById('plan_name')?.value || 'A';
                        const durationDays = document.getElementById('duration_days')?.value || 1;
                        try {
                            const preorderVal = document.getElementById('modal_preorder_input') ? document.getElementById('modal_preorder_input').value : 0;
                            const res = await fetch(form.action, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': token || ''
                                },
                                body: JSON.stringify({ quantity: val, plan_name: planName, duration_days: durationDays, preorder: preorderVal })
                            });

                            if (res.status === 419) {
                                // CSRF issue or session expired
                                alert('Session expired. Please refresh and try again.');
                                // reopen modal so user can retry after refresh
                                openModal();
                                return;
                            }

                            const data = await res.json().catch(() => null);

                            if (!res.ok || !data || (data.success === false)) {
                                const message = (data && (data.message || (data.errors && Object.values(data.errors)[0][0]))) || 'An error occurred';
                                alert(message);
                                // reopen modal so user can correct
                                openModal();
                                return;
                            }

                            // success: show Lottie overlay, then redirect after a short delay
                            const overlay = document.getElementById('arbitrage-success-overlay');
                            const lottieEl = document.getElementById('arbitrage-success-lottie');
                            if (lottieEl) {
                                // restart playback by re-setting src
                                const src = lottieEl.getAttribute('src');
                                lottieEl.setAttribute('src', '');
                                setTimeout(() => lottieEl.setAttribute('src', src), 50);
                            }
                            if (overlay) {
                                overlay.style.display = 'flex';
                            }

                            setTimeout(() => {
                                window.location.href = '{{ url('/arbitrage') }}';
                            }, 2200);

                        } catch (err) {
                            console.error(err);
                            alert('Network error. Please try again.');
                            openModal();
                        } finally {
                            if (submitBtn) submitBtn.disabled = false;
                        }
                    });
                })();
            </script>
            <!-- Success animation overlay (hidden until success) -->
            <div id="arbitrage-success-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:2000; align-items:center; justify-content:center;">
                <div style="width:320px; height:320px; display:flex; align-items:center; justify-content:center;">
                    <dotlottie-wc id="arbitrage-success-lottie" src="https://lottie.host/266c9a2c-6e00-466a-9e5d-768dbbb86059/2NCBTOo0Yu.lottie" style="width: 300px; height: 300px;" autoplay></dotlottie-wc>
                </div>
            </div>