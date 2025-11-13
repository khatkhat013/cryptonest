@extends('layouts.admin')
@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h3 class="mb-0">Edit AI Arbitrage Plan #{{ data_get($plan, 'id', '—') }}</h3>
        <div>
            <button type="submit" form="ai-arb-edit-form" class="btn btn-success me-2">
                <i class="bi bi-check-circle"></i> Save Changes
            </button>
            <a href="{{ route('admin.ai.arbitrage.index') }}" class="btn btn-secondary">
                <i class="bi bi-x-circle"></i> Cancel
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            <form id="ai-arb-edit-form" method="POST" action="{{ url('/admin/ai-arbitrage/' . $plan->id . '/update') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">ID</label>
                        <input class="form-control" value="{{ $plan->id }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">User</label>
                        <input class="form-control" value="{{ data_get($plan, 'user_name', '—') }}" readonly>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Plan Name</label>
                        <input name="plan_name" class="form-control" value="{{ data_get($plan,'plan_name', data_get($plan,'plan', data_get($plan,'name',''))) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control">
                            @php $current = strtolower(data_get($plan, 'status', 'pending')); @endphp
                            <option value="pending" {{ $current=='pending' ? 'selected' : '' }}>Pending</option>
                            <option value="active" {{ $current=='active' ? 'selected' : '' }}>Active</option>
                            <option value="completed" {{ $current=='completed' ? 'selected' : '' }}>Completed</option>
                            <option value="failed" {{ $current=='failed' ? 'selected' : '' }}>Failed</option>
                            <option value="cancelled" {{ $current=='cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Amount</label>
                        <input name="amount" type="text" class="form-control" value="{{ data_get($plan,'amount', data_get($plan,'quantity','')) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Profit Rate (%)</label>
                        <input name="profit_rate" type="text" class="form-control" value="{{ data_get($plan,'profit_rate', data_get($plan,'daily_revenue_percentage', data_get($plan,'profit',''))) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Duration Hours</label>
                        @php
                            $durationHoursVal = data_get($plan, 'duration_hours');
                            if (is_null($durationHoursVal)) {
                                $dd = data_get($plan, 'duration_days');
                                $durationHoursVal = $dd ? (intval($dd) * 24) : '';
                            }
                        @endphp
                        <input name="duration_hours" type="number" class="form-control" value="{{ $durationHoursVal }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Started At</label>
                        @php
                            // Safely pick any available started timestamp and format for datetime-local
                            $startedRaw = data_get($plan, 'started_at');
                            if (is_null($startedRaw)) $startedRaw = data_get($plan, 'started');
                            if (is_null($startedRaw)) $startedRaw = data_get($plan, 'created_at');
                            if (is_null($startedRaw)) $startedRaw = data_get($plan, 'updated_at');
                            $startedVal = '';
                            if ($startedRaw) {
                                try {
                                    $ts = strtotime($startedRaw);
                                    if ($ts !== false) $startedVal = date('Y-m-d\\TH:i', $ts);
                                } catch (\Throwable $e) {
                                    $startedVal = '';
                                }
                            }
                        @endphp
                        <input name="started_at" type="datetime-local" class="form-control" value="{{ $startedVal }}">
                    </div>

                </div>

                {{-- Buttons moved to header to match Trading edit layout --}}
            </form>

            {{-- Delete button removed per request --}}
        </div>
    </div>
</div>
@endsection
