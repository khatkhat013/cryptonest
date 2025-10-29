@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2 class="text-center mb-4">Join AI Arbitrage - A Plan</h2>

            <div class="card shadow-sm mb-4">
                <div class="card-body p-4">
                    <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#planModal">
                        Get Started
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Plan Modal -->
<div class="modal fade" id="planModal" tabindex="-1" aria-labelledby="planModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="planModalLabel">Join AI Arbitrage Plan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="planForm" method="POST" action="{{ route('arbitrage.store') }}" autocomplete="off">
                    @csrf
                    <div class="mb-4">
                        <label for="custody-quantity" class="form-label">Custody Quantity</label>
                        <div class="input-group has-validation">
                            <input type="number" class="form-control" name="quantity" id="custody-quantity" min="500" max="2000" step="0.01" required placeholder="Enter amount">
                            <span class="input-group-text">USDT</span>
                            <div class="invalid-feedback" id="custody-quantity-invalid">Please enter an amount between 500 and 2000 USDT</div>
                        </div>
                        <div class="form-text" id="custody-quantity-help">Enter amount between 500-2000 USDT</div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100" id="submitBtn">Join Now</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(() => {
    const qtyEl = document.getElementById('custody-quantity');
    const form = document.getElementById('planForm');

    if (!form || !qtyEl) return;

    function setInvalid(msg) {
        qtyEl.classList.add('is-invalid');
        const fb = document.getElementById('custody-quantity-invalid');
        if (fb) fb.textContent = msg;
    }
    function clearInvalid() { qtyEl.classList.remove('is-invalid'); }

    form.addEventListener('submit', function(e) {
        const min = parseFloat(qtyEl.min || 500);
        const max = parseFloat(qtyEl.max || 2000);
        const v = parseFloat(qtyEl.value);
        if (isNaN(v) || v < min || v > max) {
            e.preventDefault();
            setInvalid(`Amount must be between ${min} and ${max} USDT`);
            qtyEl.focus();
            return;
        }

        // All good: hide modal, then let the form submit normally
        const modalEl = document.getElementById('planModal');
        const bsModal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        bsModal.hide();
        // allow default submit to proceed (no e.preventDefault())
    });
})();
</script>
@endpush

@endsection
