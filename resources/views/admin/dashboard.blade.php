@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Admin Approval Status Alert -->
    @php
        $currentAdmin = Auth::guard('admin')->user();
    @endphp
    
    @if($currentAdmin)
        {{-- Show persistent, centered plan purchase banner for normal role users (not rejected) --}}
        @if($currentAdmin->role_id === config('roles.normal_id', 1) && !$currentAdmin->isRejected())
            <div class="d-flex justify-content-center align-items-center w-100 mt-2 mb-4">
                <div class="alert alert-info border-0 shadow-lg w-100" style="max-width: 900px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="d-flex align-items-center justify-content-between py-2 px-4 gap-3">
                        <div class="d-flex align-items-center gap-3 flex-grow-1">
                            <span class="display-5 lh-1 text-white flex-shrink-0"><i class="bi bi-bag-check-fill"></i></span>
                            <div>
                                <div class="fw-bold fs-5 text-white mb-0">အသုံးပြုနိုင်ရန် Plan တစ်ခုကို စတင် ဝယ်ယူရန် လိုအပ်ပါသည်။</div>
                            </div>
                        </div>
                        <a href="{{ route('info') }}" class="btn btn-light btn-lg fw-semibold px-4 flex-shrink-0 shadow-sm" style="white-space: nowrap;">
                            <i class="bi bi-cart-plus me-2"></i> ဝယ်ယူရန်
                        </a>
                    </div>
                </div>
            </div>
        @elseif($currentAdmin->role_id === config('roles.normal_id', 1) && $currentAdmin->isRejected())
            <div class="alert alert-danger border-2 border-danger shadow-sm rounded-3 d-flex align-items-center gap-3 p-3 mb-4" role="alert" style="max-width: 500px;">
                <span class="display-5 lh-1 text-danger"><i class="bi bi-x-circle-fill"></i></span>
                <div>
                    <h5 class="alert-heading mb-1">Account Rejected</h5>
                    <div class="mb-1 small">Your admin account has been rejected and you cannot edit records.</div>
                    @if($currentAdmin->rejection_reason)
                        <div class="text-danger small"><strong>Reason:</strong> {{ $currentAdmin->rejection_reason }}</div>
                    @endif
                </div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        {{-- Super admin sees the management alert --}}
        @elseif($currentAdmin->isSuperAdmin())
            <div class="alert alert-info border-2 border-primary shadow-sm rounded-3 d-flex align-items-center gap-3 p-3 mb-4" role="alert" style="max-width: 500px;">
                <span class="display-5 lh-1 text-primary"><i class="bi bi-check-circle-fill"></i></span>
                <div>
                    <h5 class="alert-heading mb-1">Site Owner - Admin Approval</h5>
                    <div class="mb-1 small">
                        <a href="{{ route('admin.admin_approval.index') }}" class="alert-link text-decoration-underline">
                            <i class="bi bi-shield-check"></i> Manage Admin Approvals
                        </a>
                    </div>
                </div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        {{-- Admin role users don't see any approval-related alerts --}}
        @endif
    @endif

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-md-3">
            <a href="/admin/deposits" class="text-white" style="text-decoration:none;">
            <div class="card stats-card bg-primary text-white">
                <div class="card-body text-center">
                    <h5 class="card-title mb-0">Deposits</h5>
                    <h2 class="mt-2 mb-0">{{ number_format($depositsCount ?? 0) }}</h2>
                    @if(isset($depositsNew) && $depositsNew > 0)
                        <div class="mt-1"><span class="badge rounded-pill bg-light text-dark">New {{ $depositsNew }}</span></div>
                    @endif
                    <div class="mt-2">
                        <i class="fas fa-download fa-2x"></i>
                    </div>
                </div>
            </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="/admin/withdraws" class="text-white" style="text-decoration:none;">
            <div class="card stats-card bg-success text-white">
                <div class="card-body text-center">
                    <h5 class="card-title mb-0">Withdraw</h5>
                    <h2 class="mt-2 mb-0">{{ number_format($withdrawalsCount ?? 0) }}</h2>
                    @if(isset($withdrawalsNew) && $withdrawalsNew > 0)
                        <div class="mt-1"><span class="badge rounded-pill bg-light text-dark">New {{ $withdrawalsNew }}</span></div>
                    @endif
                    <div class="mt-2">
                        <i class="fas fa-arrow-circle-down fa-2x"></i>
                    </div>
                </div>
            </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="/admin/trading" class="text-white" style="text-decoration:none;">
            <div class="card stats-card bg-warning text-white">
                <div class="card-body text-center">
                    <h5 class="card-title mb-0">Trading</h5>
                    <h2 class="mt-2 mb-0">{{ number_format($tradesCount ?? 0) }}</h2>
                    @if(isset($tradesNew) && $tradesNew > 0)
                        <div class="mt-1"><span class="badge rounded-pill bg-light text-dark">New {{ $tradesNew }}</span></div>
                    @endif
                    <div class="mt-2">
                        <i class="fas fa-chart-line fa-2x"></i>
                    </div>
                </div>
            </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="/admin/ai-arbitrage" class="text-white" style="text-decoration:none;">
            <div class="card stats-card bg-info text-white">
                <div class="card-body text-center">
                    <h5 class="card-title mb-0">AI Arbitrage</h5>
                    <h2 class="mt-2 mb-0">{{ number_format($aiArbCount ?? 0) }}</h2>
                    @if(isset($aiArbNew) && $aiArbNew > 0)
                        <div class="mt-1"><span class="badge rounded-pill bg-light text-dark">New {{ $aiArbNew }}</span></div>
                    @endif
                    <div class="mt-2">
                        <i class="fas fa-robot fa-2x"></i>
                    </div>
                </div>
            </div>
            </a>
        </div>
    </div>
    <!-- Charts removed per request -->

    <!-- Quick User Assignment Widget -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">👤 User ကို Admin ချိတ်ဆက်ခြင်း</h5>
                </div>
                <div class="card-body">
                    <form id="quickAssignForm">
                        @csrf
                        <div class="form-group mb-2">
                            <label for="quick_uid" class="form-label small">User UID (6 digits):</label>
                            <input 
                                type="text" 
                                class="form-control form-control-sm" 
                                id="quick_uid" 
                                name="uid" 
                                placeholder="ဥပမာ: 342016"
                                pattern="^\d{6}$"
                                {{ $currentAdmin->role_id === config('roles.normal_id', 1) ? 'disabled' : '' }}
                                required
                            >
                            <small class="text-muted">User ရဲ့ registration ခြင်းမှ UID</small>
                        </div>

                        <div class="form-group mb-3">
                            <label for="quick_admin" class="form-label small">Admin Telegram Username:</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">@</span>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    id="quick_admin" 
                                    name="telegram_username" 
                                    placeholder="admin registration မှ username"
                                    {{ $currentAdmin->role_id === config('roles.normal_id', 1) ? 'disabled' : '' }}
                                    required
                                >
                            </div>
                            <small class="text-muted">Admin account registration အချိန် သိမ်းဆည်းခဲ့တဲ့ username</small>
                        </div>

                        <button type="submit" class="btn btn-sm btn-primary w-100" {{ $currentAdmin->role_id === config('roles.normal_id', 1) ? 'disabled' : '' }} title="{{ $currentAdmin->role_id === config('roles.normal_id', 1) ? 'Normal role users cannot assign users' : '' }}">
                            <span id="quickSubmitText">✓ Assign လုပ်ခြင်း</span>
                            <span id="quickSpinner" class="spinner-border spinner-border-sm ms-2" style="display:none;"></span>
                        </button>

                        <div id="quickResultAlert" style="display:none;" class="alert alert-sm mt-2 mb-0" role="alert"></div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Assignment Info Card -->
        <div class="col-md-6">
            <div class="card bg-light">
                <div class="card-header">
                    <h5 class="card-title mb-0">ℹ️ အသုံးပြုမည့် အချက်များ</h5>
                </div>
                <div class="card-body small">
                    <p class="mb-2"><strong>📋 လုပ်ဆောင်မည့် အဆင့်များ:</strong></p>
                    <ol class="mb-3">
                        <li>User သည် website သည့်ယ်တွင် register လုပ်ခြင်း</li>
                        <li>User ရဲ့ UID ကို မှတ်သားခြင်း (success page သည့်ယ်တွင်)</li>
                        <li>Admin ရဲ့ telegram username ဖြည့်သွင်းခြင်း</li>
                        <li>"✓ Assign လုပ်ခြင်း" ကို နှိပ်ခြင်း</li>
                    </ol>
                    
                    <p class="mb-2"><strong>✅ ရလဒ်:</strong></p>
                    <ul class="mb-0">
                        <li>User သည် အဲ့ဒီ admin ကိုချိတ်ဆက်သည်</li>
                        <li>Database အပ်ဒေတ်သည်</li>
                        <li>Success message ပြသည်</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recent Activities</h5>
                </div>
                <div class="card-body">
                    <style>
                        /* Limit the height of the recent activities table and make only the body scrollable */
                        /* Increase desktop height slightly for more visible rows */
                        .recent-activities-body { max-height: 560px; overflow-y: auto; }
                        .recent-activities-body table thead th { position: sticky; top: 0; z-index: 3; background-color: #fff; }
                        /* On small devices, allow the list to expand to fill remaining viewport height */
                        @media (max-width: 576px) {
                            .recent-activities-body { max-height: calc(100vh - 160px); }
                        }
                    </style>

                    <div class="table-responsive recent-activities-body">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Transaction ID</th>
                                    <th>User</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentActivities ?? [] as $act)
                                <tr>
                                    <td>{{ $act->tx_id ?? ($act->type[0] . str_pad($act->id,5,'0',STR_PAD_LEFT)) }}</td>
                                    <td>{{ $act->user?->name ?? $act->user?->email ?? '—' }}</td>
                                    <td>
                                        @if($act->type === 'deposit')
                                            Deposit
                                        @elseif($act->type === 'withdrawal')
                                            Withdrawal
                                        @else
                                            Trade
                                        @endif
                                    </td>
                                    <td>
                                        @if($act->amount !== null)
                                            {{ rtrim(rtrim(number_format($act->amount, 8, '.', ''), '0'), '.') }} {{ strtoupper($act->coin ?? '') }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $status = strtolower((string)($act->status ?? ''));
                                        @endphp
                                        @if(str_contains($status, 'comp') || $status === 'completed')
                                            <span class="badge bg-success">{{ $act->status }}</span>
                                        @elseif(str_contains($status, 'pend') || $status === 'pending' || $status === 'open')
                                            <span class="badge bg-warning">{{ $act->status }}</span>
                                        @elseif(str_contains($status, 'fail') || $status === 'failed')
                                            <span class="badge bg-danger">{{ $act->status }}</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $act->status }}</span>
                                        @endif
                                    </td>
                                    <td>{{ optional($act->created_at)->format('Y-m-d H:i') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No recent activities found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination centered below table --}}
                    @php $raPage = (int) request('page', 1); @endphp
                    <div class="d-flex flex-column align-items-center mt-4">
                        <nav aria-label="Recent activities pagination">
                            <ul class="pagination mb-2">
                                <li class="page-item {{ $raPage <= 1 ? 'disabled' : '' }}">
                                    <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => max(1, $raPage - 1)]) }}">« Previous</a>
                                </li>
                                <li class="page-item {{ $raPage == 1 ? 'active' : '' }}"><a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => 1]) }}">1</a></li>
                                <li class="page-item {{ $raPage == 2 ? 'active' : '' }}"><a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => 2]) }}">2</a></li>
                                <li class="page-item">
                                    <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => min(2, $raPage + 1)]) }}">Next »</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Plan Inquiries -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recent Plan Inquiries</h5>
                </div>
                <div class="card-body">
                    @if(isset($recentPlanInquiries) && $recentPlanInquiries->count())
                        <div class="table-responsive">
                            <table class="table table-hover table-sm align-middle">
                                <thead>
                                    <tr>
                                        <th>Created</th>
                                        <th>Admin</th>
                                        <th>Plan</th>
                                        <th>Price</th>
                                        <th>Method</th>
                                        <th>Screenshots</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentPlanInquiries as $p)
                                        <tr>
                                            <td>{{ $p->created_at->format('Y-m-d H:i') }}</td>
                                            <td>{{ $p->admin?->name ?? 'N/A' }}</td>
                                            <td>{{ $p->plan_name }}</td>
                                            <td>{{ $p->plan_price }}</td>
                                            <td>{{ $p->payment_method ?? '-' }}</td>
                                            <td>
                                                @if($p->crypto_screenshot)
                                                    <a href="{{ asset('storage/' . $p->crypto_screenshot) }}" target="_blank">
                                                        <img src="{{ asset('storage/' . $p->crypto_screenshot) }}" style="max-height:40px; max-width:80px; object-fit:cover; border-radius:4px;" />
                                                    </a>
                                                @endif
                                                @if($p->mobile_screenshot)
                                                    <a href="{{ asset('storage/' . $p->mobile_screenshot) }}" target="_blank" class="ms-2">
                                                        <img src="{{ asset('storage/' . $p->mobile_screenshot) }}" style="max-height:40px; max-width:80px; object-fit:cover; border-radius:4px;" />
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-muted">No recent plan inquiries.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Deposits removed per request --}}

        {{-- Deposit modal and related controls removed per request --}}
</div>
@endsection

@push('scripts')
{{-- Charts removed per request: no dashboard chart scripts rendered --}}
<script>
// Quick Assign Form Handler
document.getElementById('quickAssignForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const uid = document.getElementById('quick_uid').value;
    const telegramUsername = document.getElementById('quick_admin').value;
    const submitBtn = e.target.querySelector('button[type="submit"]');
    const resultAlert = document.getElementById('quickResultAlert');
    const submitText = document.getElementById('quickSubmitText');
    const spinner = document.getElementById('quickSpinner');
    
    // Show loading state
    submitText.style.display = 'none';
    spinner.style.display = 'inline-block';
    submitBtn.disabled = true;
    
    try {
        // Get CSRF token from form
        const csrfToken = document.querySelector('form input[name="_token"]').value;
        
        const response = await fetch('/api/assignment/assign-user', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                uid: uid,
                telegram_username: telegramUsername
            })
        });
        
        const data = await response.json();
        
        // Show result
        resultAlert.classList.remove('alert-success', 'alert-danger');
        resultAlert.classList.add(response.ok ? 'alert-success' : 'alert-danger');
        
        let resultHtml = `<strong>${response.ok ? '✅ အလုပ်လုပ်သည်' : '❌ အမှားအရာ'}</strong><br>`;
        resultHtml += (data.message || 'Unknown error');
        
        if (data.user) {
            resultHtml += `<br><small>👤 User: ${data.user.name}</small>`;
        }
        if (data.admin) {
            resultHtml += `<br><small>👨‍💼 Admin: ${data.admin.name}</small>`;
        }
        
        resultAlert.innerHTML = resultHtml;
        resultAlert.style.display = 'block';
        
        // Clear form if successful
        if (response.ok) {
            setTimeout(() => {
                document.getElementById('quickAssignForm').reset();
                resultAlert.style.display = 'none';
            }, 2000);
        }
    } catch (error) {
        resultAlert.classList.remove('alert-success');
        resultAlert.classList.add('alert-danger');
        resultAlert.innerHTML = `<strong>❌ အမှားအရာ</strong><br>${error.message}`;
        resultAlert.style.display = 'block';
        console.error('Assignment error:', error);
    } finally {
        // Hide loading state
        submitText.style.display = 'inline';
        spinner.style.display = 'none';
        submitBtn.disabled = false;
    }
});
</script>
@endpush

{{-- Edit/Delete deposit scripts removed per request --}}