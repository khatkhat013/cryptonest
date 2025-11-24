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
        @endif
    @endif

    <!-- Statistics Cards -->
    <style>
        /* Modern gradient stats cards */
        .stats-card {
            position: relative;
            color: #fff !important;
            overflow: hidden;
            border: 0;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            min-height: 140px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
        }

        .stats-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .stats-card .card-body { 
            padding: 1.75rem; 
            display: flex; 
            flex-direction: column; 
            align-items: center; 
            justify-content: center;
            position: relative;
            z-index: 2;
        }

        /* Animated background shapes */
        .stats-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200px;
            height: 200px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
            z-index: 0;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(-30px, -30px) scale(1.1); }
        }

        /* Circular icon background in the corner */
        .stats-card .stats-icon {
            position: absolute;
            right: 1rem;
            top: 1rem;
            opacity: 0.2;
            font-size: 3.5rem;
            width: 70px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: rgba(255,255,255,0.1);
            z-index: 1;
        }

        .stats-card .label { 
            font-size: 0.95rem; 
            opacity: 0.95; 
            margin-bottom: 0.5rem;
            font-weight: 500;
            letter-spacing: 0.5px;
        }
        
        .stats-card .value { 
            font-size: 2.2rem; 
            font-weight: 800; 
            margin-bottom: 0.5rem;
            letter-spacing: -1px;
        }

        /* Modern gradient backgrounds */
        .stats-card.bg-primary { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .stats-card.bg-success { 
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        
        .stats-card.bg-warning { 
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        
        .stats-card.bg-info { 
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }

        .stats-card .muted-note { 
            font-size: 0.85rem; 
            opacity: 0.9;
            margin-top: 0.25rem;
        }

        .stats-card .badge {
            font-size: 0.75rem;
            padding: 0.4rem 0.75rem;
            border-radius: 20px;
        }

        @media (max-width: 576px) {
            .stats-card { 
                min-height: 120px;
                border-radius: 12px;
            }
            
            .stats-card .stats-icon { 
                font-size: 2.5rem; 
                width: 60px; 
                height: 60px; 
                right: 0.75rem; 
                top: 0.75rem; 
            }
            
            .stats-card .value { 
                font-size: 1.8rem;
            }
            
            .stats-card .label { 
                font-size: 0.9rem;
            }

            .stats-card:hover {
                transform: translateY(-4px);
            }
        }
    </style>
    <div class="row">
        <div class="col-6 col-md-3">
            <a href="/admin/deposits" class="text-white" style="text-decoration:none;">
            <div class="card stats-card bg-primary text-white">
                <div class="card-body text-center">
                    <div class="stats-icon"><i class="bi bi-arrow-down-circle-fill"></i></div>
                    <div class="label">Deposits</div>
                    <div class="value">{{ number_format($depositsCount ?? 0) }}</div>
                    @if(isset($depositsNew) && $depositsNew > 0)
                        <div class="mt-1"><span class="badge rounded-pill bg-white text-dark">New {{ $depositsNew }}</span></div>
                    @else
                        <div class="muted-note mt-1">No new deposits</div>
                    @endif
                </div>
            </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="/admin/withdraws" class="text-white" style="text-decoration:none;">
            <div class="card stats-card bg-success text-white">
                <div class="card-body text-center">
                    <div class="stats-icon"><i class="bi bi-wallet2"></i></div>
                    <div class="label">Withdrawals</div>
                    <div class="value">{{ number_format($withdrawalsCount ?? 0) }}</div>
                    @if(isset($withdrawalsNew) && $withdrawalsNew > 0)
                        <div class="mt-1"><span class="badge rounded-pill bg-white text-dark">New {{ $withdrawalsNew }}</span></div>
                    @else
                        <div class="muted-note mt-1">No new withdrawals</div>
                    @endif
                </div>
            </div>
            </a>
        </div>
        <div class="col-6 col-md-3 mt-1">
            <a href="/admin/trading" class="text-white" style="text-decoration:none;">
            <div class="card stats-card bg-warning text-white">
                <div class="card-body text-center">
                    <div class="stats-icon"><i class="bi bi-graph-up"></i></div>
                    <div class="label">Trading</div>
                    <div class="value">{{ number_format($tradesCount ?? 0) }}</div>
                    @if(isset($tradesNew) && $tradesNew > 0)
                        <div class="mt-1"><span class="badge rounded-pill bg-white text-dark">New {{ $tradesNew }}</span></div>
                    @else
                        <div class="muted-note mt-1">No new trades</div>
                    @endif
                </div>
            </div>
            </a>
        </div>
        <div class="col-6 col-md-3 mt-1">
            <a href="/admin/ai-arbitrage" class="text-white" style="text-decoration:none;">
            <div class="card stats-card bg-info text-white">
                <div class="card-body text-center">
                    <div class="stats-icon"><i class="bi bi-robot"></i></div>
                    <div class="label">AI Arbitrage</div>
                    <div class="value">{{ number_format($aiArbCount ?? 0) }}</div>
                    @if(isset($aiArbNew) && $aiArbNew > 0)
                        <div class="mt-1"><span class="badge rounded-pill bg-white text-dark">New {{ $aiArbNew }}</span></div>
                    @else
                        <div class="muted-note mt-1">No new plans</div>
                    @endif
                </div>
            </div>
            </a>
        </div>
    </div>
    <!-- Charts removed per request -->

    <!-- Quick User Assignment Widget -->
    <div class="row mt-5">
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden; display: flex; flex-direction: column; height: 100%;">
                <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; padding: 1.5rem;">
                    <h5 class="card-title mb-0 text-white" style="font-weight: 700; font-size: 1.1rem;">
                        <i class="bi bi-person-check me-2"></i>User ကို Admin ချိတ်ဆက်ခြင်း
                    </h5>
                </div>
                <div class="card-body p-4" style="flex-grow: 1; display: flex; flex-direction: column;">
                    <form id="quickAssignForm" style="display: flex; flex-direction: column; flex-grow: 1;">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="quick_uid" class="form-label" style="font-weight: 600; font-size: 0.95rem; color: #495057;">
                                <i class="bi bi-key me-2" style="color: #667eea;"></i>User UID (6 digits)
                            </label>
                            <input 
                                type="text" 
                                class="form-control" 
                                id="quick_uid" 
                                name="uid" 
                                placeholder="ဥပမာ: 342016"
                                pattern="^\d{6}$"
                                {{ $currentAdmin->role_id === config('roles.normal_id', 1) ? 'disabled' : '' }}
                                required
                                style="border-radius: 10px; border: 1.5px solid #e0e0e0; padding: 0.75rem 1rem; font-size: 0.95rem;"
                            >
                            <small class="text-muted d-block mt-2">သင့် Customer များအကောင့်ပြုလုပ်ပြီးနောက် ရရှိလာသည့် UID ကို ထည့်သွင်းရမည် ဖြစ်သည်။ သတိပြုရန်!  သင့် Customer ရဲ့ UID အမှန်ကိုသာ မှန်ကန်စွာ ထည့်သွင်းအသုံးပြုရန် အထူး လိုက်နာရပါမည်။ </small>
                        </div>

                        <div class="form-group mb-4">
                            <label for="quick_admin" class="form-label" style="font-weight: 600; font-size: 0.95rem; color: #495057;">
                                <i class="bi bi-telegram me-2" style="color: #667eea;"></i>Admin Telegram Username
                            </label>
                            <div class="input-group">
                                <span class="input-group-text" style="border-radius: 10px 0 0 10px; border: 1.5px solid #e0e0e0; background-color: #f8f9fa; border-right: none;">@</span>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    id="quick_admin" 
                                    name="telegram_username" 
                                    placeholder="admin registration မှ username"
                                    {{ $currentAdmin->role_id === config('roles.normal_id', 1) ? 'disabled' : '' }}
                                    readonly
                                    required
                                    style="border-radius: 0 10px 10px 0; border: 1.5px solid #e0e0e0; padding: 0.75rem 1rem; font-size: 0.95rem; background-color: #f8f9fa;"
                                >
                            </div>
                            <small class="text-muted d-block mt-2">Admin အကောင့်တွင် သင်ထည့်သွင်းထားသည့် Telegram Username ဖြစ်သည်။</small>
                        </div>

                        <button type="submit" class="btn w-100 mt-auto" {{ $currentAdmin->role_id === config('roles.normal_id', 1) ? 'disabled' : '' }} 
                                title="{{ $currentAdmin->role_id === config('roles.normal_id', 1) ? 'Normal role users cannot assign users' : '' }}"
                                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; border-radius: 10px; padding: 0.85rem 1.5rem; color: white; font-weight: 600; font-size: 0.95rem; transition: all 0.3s ease;">
                            <span id="quickSubmitText">
                                <i class="bi bi-check-circle me-2"></i>Assign လုပ်ခြင်း
                            </span>
                            <span id="quickSpinner" class="spinner-border spinner-border-sm ms-2" style="display:none;"></span>
                        </button>

                        <div id="quickResultAlert" style="display:none; border-radius: 10px; margin-top: 1rem;" class="alert alert-sm mb-0" role="alert"></div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Assignment Info Card -->
        <div class="col-12 col-lg-6 mt-4 mt-lg-0">
            <div class="card border-0 shadow-sm" style="border-radius: 16px; background: #ffffff; overflow: hidden; display: flex; flex-direction: column; height: 100%;">
                <div class="card-header border-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 1.5rem;">
                    <h5 class="card-title mb-0 text-white" style="font-weight: 700; font-size: 1.1rem;">
                        <i class="bi bi-info-circle me-2"></i>Admin အနေနဲ့ အသုံးပြုရန် အမြန်လမ်းညွှန်ချက်များ
                    </h5>
                </div>
                <div class="card-body p-4 pt-0" style="color: #333; font-size: 0.85rem; flex-grow: 1;">
                    <ol style="padding-left: 1.5rem; color: #444; line-height: 1.8; margin-bottom: 0;">
                        <li class="mb-3">
                            <strong style="color: #222;">Admin အကောင့်တစ်ခုကို Register လုပ်ထားရန်လိုအပ်သည်။</strong><br> Admin အကောင့် Register လုပ်သည့်အချိန်မှာ Telegram username and Wallet address မရှိရင် ထည့်သွင်းစရာမလိုအပ်ပါဘူး။
                        </li>
                        <li class="mb-3">
                            <strong style="color: #222;">Admin အကောင့်ကို အပြည့်အဝ အသုံးပြုနိုင်ရန် Plan တစ်ခုကို ဝယ်ယူရန်လိုအပ်သည်။</strong><br> Telegram Username က သင့်ရဲ့ ဖောက်သည်တွေအား Customer Support အနေနဲ့ အသုံးပြုနိုင်ရန်ဖြစ်သည်။
                        </li>
                        <li class="mb-3">
                            <strong style="color: #222;">Plan ဝယ်ယူပြီးပါက သင့်ရဲ့ Customer များကို User Portal မှာ Register လုပ်ခိုင်းပြီး ငွေလက်ခံရန် နဲ့ အခြား Platform လုပ်ဆောင်ချက်များကို စတင်အသုံးပြုနိုင်ပါပြီ။</strong>
                        </li>
                        <li class="mb-3">
                            <strong style="color: #222;">သင့်ရဲ့ Customer နဲ့ သင့်ရဲ့ Admin အကောင့်ကို ချိတ်ဆက်မှု ရရှိစေရန်</strong><br> User ကို Admin ချိတ်ဆက်ခြင်း နေရာတွင် သင့်ဖောက်သည်၏ UID ဂဏန်း(၆)လုံး နဲ့ သင့် အကောင့်ဖွင့်စဥ် ထည့်သွင်းခဲ့သည့် Telegram username ကို ဖြည့်သွင်းရန် လိုအပ်ပြီး Assign လုပ်ခြင်း Button ကို နှိပ်ခြင်းဖြင့် သင့်ဖောက်သည်နဲ့ ချိတ်ဆက်မှုရရှိပြီး သင့်ဖောက်သည်လုပ်ဆောင်ချက်များကို ထိန်းချုပ်နိုင်မည်ဖြစ်သည်။
                        </li>
                        <li class="mb-0">
                            <strong style="color: #222;">Deposits, Withdrawals, Trading, AI Arbitrage နေရာများတွင်</strong><br> သင့်ဖောက်သည် လုပ်ဆောင်သည့် အချက်အလက်များကို ကြည့်ရှုပြီး ပြန်လည်ပြင်ဆင်နိုင်ရန် အသုံးပြုနိုင်ပါသည်။
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
                <div class="card-header border-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 1.75rem;">
                    <h5 class="card-title mb-0 text-white" style="font-weight: 700; font-size: 1.15rem;">
                        <i class="bi bi-clock-history me-2"></i>Recent Activities
                    </h5>
                </div>
                <div class="card-body p-0">
                    <style>
                        /* Modern activities table styling */
                        .recent-activities-body { 
                            max-height: 560px; 
                            overflow-y: auto;
                        }
                        
                        .recent-activities-body table { 
                            margin-bottom: 0; 
                        }
                        
                        .recent-activities-body table thead th { 
                            position: sticky; 
                            top: 0; 
                            z-index: 3; 
                            background-color: #f8f9fa;
                            border-bottom: 2px solid #e9ecef;
                            font-weight: 700;
                            color: #495057;
                            padding: 1rem 0.75rem;
                            font-size: 0.9rem;
                        }

                        .recent-activities-body table tbody tr {
                            border-bottom: 1px solid #f0f0f0;
                            transition: background-color 0.2s ease;
                        }

                        .recent-activities-body table tbody tr:hover {
                            background-color: #fafbfc;
                        }

                        .recent-activities-body table tbody td {
                            padding: 1rem 0.75rem;
                            font-size: 0.9rem;
                            color: #495057;
                            vertical-align: middle;
                        }

                        /* Badge styling */
                        .recent-activities-body .badge {
                            padding: 0.5rem 0.85rem;
                            border-radius: 20px;
                            font-weight: 600;
                            font-size: 0.8rem;
                        }

                        /* Mobile responsive */
                        @media (max-width: 576px) {
                            .recent-activities-body { 
                                max-height: calc(100vh - 160px); 
                            }

                            .recent-activities-body table thead { 
                                display: none; 
                            }
                            
                            .recent-activities-body table, 
                            .recent-activities-body tbody, 
                            .recent-activities-body tr, 
                            .recent-activities-body td {
                                display: block;
                                width: 100%;
                            }

                            .recent-activities-body tr { 
                                margin-bottom: 0.75rem; 
                                border: 1px solid #e9ecef; 
                                border-radius: 10px; 
                                padding: 0.5rem; 
                                background-color: #fafbfc;
                            }

                            .recent-activities-body td {
                                padding: 0.6rem 0.75rem;
                                border: none;
                                display: flex;
                                justify-content: space-between;
                                align-items: center;
                                background: transparent;
                                gap: 0.5rem;
                                font-size: 0.85rem;
                            }

                            .recent-activities-body td[data-label]::before {
                                content: attr(data-label) ": ";
                                font-weight: 700;
                                color: #333;
                                margin-right: 0.5rem;
                                min-width: 80px;
                                color: #667eea;
                            }
                        }
                    </style>

                    <div class="table-responsive recent-activities-body">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 15%;">Transaction ID</th>
                                    <th style="width: 18%;">User</th>
                                    <th style="width: 12%;">Type</th>
                                    <th style="width: 20%;">Amount</th>
                                    <th style="width: 15%;">Status</th>
                                    <th style="width: 20%;">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentActivities ?? [] as $act)
                                <tr>
                                    <td data-label="Transaction ID">
                                        <code style="background-color: #f0f0f0; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.85rem;">{{ $act->tx_id ?? ($act->type[0] . str_pad($act->id,5,'0',STR_PAD_LEFT)) }}</code>
                                    </td>
                                    <td data-label="User">
                                        <div style="font-weight: 500; color: #333;">{{ $act->user?->name ?? $act->user?->email ?? '—' }}</div>
                                    </td>
                                    <td data-label="Type">
                                        @if($act->type === 'deposit')
                                            <span style="color: #43e97b; font-weight: 600;">📥 Deposit</span>
                                        @elseif($act->type === 'withdrawal')
                                            <span style="color: #f5576c; font-weight: 600;">📤 Withdrawal</span>
                                        @else
                                            <span style="color: #4facfe; font-weight: 600;">📊 Trade</span>
                                        @endif
                                    </td>
                                    <td data-label="Amount">
                                        @if($act->amount !== null)
                                            <span style="font-weight: 600; color: #1f4068;">{{ rtrim(rtrim(number_format($act->amount, 8, '.', ''), '0'), '.') }} <span style="color: #667eea;">{{ strtoupper($act->coin ?? '') }}</span></span>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td data-label="Status">
                                        @php
                                            $status = strtolower((string)($act->status ?? ''));
                                        @endphp
                                        @if(str_contains($status, 'comp') || $status === 'completed')
                                            <span class="badge bg-success" style="box-shadow: 0 2px 8px rgba(34, 197, 94, 0.2);">✓ {{ $act->status }}</span>
                                        @elseif(str_contains($status, 'pend') || $status === 'pending' || $status === 'open')
                                            <span class="badge bg-warning text-dark" style="box-shadow: 0 2px 8px rgba(245, 158, 11, 0.2);">⏱ {{ $act->status }}</span>
                                        @elseif(str_contains($status, 'fail') || $status === 'failed')
                                            <span class="badge bg-danger" style="box-shadow: 0 2px 8px rgba(239, 68, 68, 0.2);">✗ {{ $act->status }}</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $act->status }}</span>
                                        @endif
                                    </td>
                                    <td data-label="Date">
                                        <small class="text-muted">{{ optional($act->created_at)->format('Y-m-d H:i') }}</small>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox" style="font-size: 2rem; opacity: 0.5;"></i>
                                        <div class="mt-2">No recent activities found.</div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination centered below table --}}
                    @php $raPage = (int) request('page', 1); @endphp
                    <div class="d-flex flex-column align-items-center mt-4 pb-4">
                        <nav aria-label="Recent activities pagination">
                            <ul class="pagination mb-2" style="gap: 0.35rem;">
                                <li class="page-item {{ $raPage <= 1 ? 'disabled' : '' }}">
                                    <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => max(1, $raPage - 1)]) }}" style="border-radius: 8px;">« Previous</a>
                                </li>
                                <li class="page-item {{ $raPage == 1 ? 'active' : '' }}"><a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => 1]) }}" style="border-radius: 8px;">1</a></li>
                                <li class="page-item {{ $raPage == 2 ? 'active' : '' }}"><a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => 2]) }}" style="border-radius: 8px;">2</a></li>
                                <li class="page-item">
                                    <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => min(2, $raPage + 1)]) }}" style="border-radius: 8px;">Next »</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Plan Inquiries -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
                <div class="card-header border-0" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); padding: 1.75rem;">
                    <h5 class="card-title mb-0 text-white" style="font-weight: 700; font-size: 1.15rem;">
                        <i class="bi bi-bag-check-fill me-2"></i>Recent Plan Inquiries
                    </h5>
                </div>
                <div class="card-body p-4">
                    @if(isset($recentPlanInquiries) && $recentPlanInquiries->count())
                        <div class="table-responsive">
                            <style>
                                .plan-inquiries-table {
                                    margin-bottom: 0;
                                }
                                
                                .plan-inquiries-table thead th {
                                    background-color: #f8f9fa;
                                    border-bottom: 2px solid #e9ecef;
                                    font-weight: 700;
                                    color: #495057;
                                    padding: 1rem 0.75rem;
                                    font-size: 0.9rem;
                                }
                                
                                .plan-inquiries-table tbody tr {
                                    border-bottom: 1px solid #f0f0f0;
                                    transition: background-color 0.2s ease;
                                }
                                
                                .plan-inquiries-table tbody tr:hover {
                                    background-color: #fafbfc;
                                }
                                
                                .plan-inquiries-table tbody td {
                                    padding: 1rem 0.75rem;
                                    font-size: 0.9rem;
                                    color: #495057;
                                    vertical-align: middle;
                                }
                                
                                .plan-inquiries-table .screenshot-container {
                                    display: flex;
                                    gap: 0.5rem;
                                    flex-wrap: wrap;
                                }
                                
                                .plan-inquiries-table .screenshot-thumb {
                                    border-radius: 8px;
                                    overflow: hidden;
                                    border: 1px solid #e9ecef;
                                    transition: transform 0.2s ease, box-shadow 0.2s ease;
                                }
                                
                                .plan-inquiries-table .screenshot-thumb:hover {
                                    transform: scale(1.05);
                                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                                }
                                
                                .plan-inquiries-table .screenshot-thumb img {
                                    max-height: 45px;
                                    max-width: 80px;
                                    object-fit: cover;
                                    display: block;
                                }
                            </style>
                            
                            <table class="table table-hover plan-inquiries-table">
                                <thead>
                                    <tr>
                                        <th style="width: 15%;">Created</th>
                                        <th style="width: 15%;">Admin</th>
                                        <th style="width: 15%;">Plan</th>
                                        <th style="width: 18%;">Price</th>
                                        <th style="width: 15%;">Method</th>
                                        <th style="width: 22%;">Screenshots</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentPlanInquiries as $p)
                                        <tr>
                                            <td data-label="Created">
                                                <small class="text-muted">{{ $p->created_at->format('Y-m-d H:i') }}</small>
                                            </td>
                                            <td data-label="Admin">
                                                <span style="font-weight: 600; color: #333;">{{ $p->admin?->name ?? 'N/A' }}</span>
                                            </td>
                                            <td data-label="Plan">
                                                <span class="badge" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 0.5rem 0.85rem;">{{ $p->plan_name }}</span>
                                            </td>
                                            <td data-label="Price">
                                                <span style="font-weight: 700; color: #43e97b;">{{ $p->plan_price }}</span>
                                            </td>
                                            <td data-label="Method">
                                                @if($p->payment_method)
                                                    @if($p->payment_method === 'crypto')
                                                        <span class="badge bg-info" style="box-shadow: 0 2px 8px rgba(79, 172, 254, 0.2); padding: 0.5rem 0.85rem;">🪙 Crypto</span>
                                                    @elseif($p->payment_method === 'mobile_money')
                                                        <span class="badge bg-warning text-dark" style="box-shadow: 0 2px 8px rgba(245, 158, 11, 0.2); padding: 0.5rem 0.85rem;">📱 Mobile</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ ucfirst($p->payment_method) }}</span>
                                                    @endif
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td data-label="Screenshots">
                                                <div class="screenshot-container">
                                                    @if($p->crypto_screenshot)
                                                        <a href="{{ asset('storage/' . $p->crypto_screenshot) }}" target="_blank" class="screenshot-thumb">
                                                            <img src="{{ asset('storage/' . $p->crypto_screenshot) }}" title="Crypto Payment" />
                                                        </a>
                                                    @endif
                                                    @if($p->mobile_screenshot)
                                                        <a href="{{ asset('storage/' . $p->mobile_screenshot) }}" target="_blank" class="screenshot-thumb">
                                                            <img src="{{ asset('storage/' . $p->mobile_screenshot) }}" title="Mobile Payment" />
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- Pagination -->
                        <div class="mt-4 d-flex justify-content-center">
                            <nav aria-label="Recent plan inquiries pagination">
                                <ul class="pagination mb-0" style="gap: 0.35rem;">
                                    {{-- Previous Button --}}
                                    @if ($recentPlanInquiries->onFirstPage())
                                        <li class="page-item disabled">
                                            <span class="page-link" style="border-radius: 8px;">« Previous</span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $recentPlanInquiries->previousPageUrl() }}" style="border-radius: 8px;">« Previous</a>
                                        </li>
                                    @endif

                                    {{-- Page Numbers --}}
                                    @foreach ($recentPlanInquiries->getUrlRange(1, $recentPlanInquiries->lastPage()) as $page => $url)
                                        @if ($page == $recentPlanInquiries->currentPage())
                                            <li class="page-item active">
                                                <span class="page-link" style="border-radius: 8px;">{{ $page }}</span>
                                            </li>
                                        @else
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $url }}" style="border-radius: 8px;">{{ $page }}</a>
                                            </li>
                                        @endif
                                    @endforeach

                                    {{-- Next Button --}}
                                    @if ($recentPlanInquiries->hasMorePages())
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $recentPlanInquiries->nextPageUrl() }}" style="border-radius: 8px;">Next »</a>
                                        </li>
                                    @else
                                        <li class="page-item disabled">
                                            <span class="page-link" style="border-radius: 8px;">Next »</span>
                                        </li>
                                    @endif
                                </ul>
                            </nav>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-inbox" style="font-size: 3rem; opacity: 0.4; color: #999;"></i>
                            <div class="mt-3 text-muted" style="font-size: 0.95rem;">No recent plan inquiries.</div>
                        </div>
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
// Auto-fill current admin's Telegram username on page load
document.addEventListener('DOMContentLoaded', function() {
    const adminTelegramField = document.getElementById('quick_admin');
    if (adminTelegramField && !adminTelegramField.value) {
        // Get current admin's telegram username from data attribute
        const currentTelegram = '{{ $currentAdmin->telegram_username ?? '' }}';
        if (currentTelegram) {
            adminTelegramField.value = currentTelegram;
        }
    }
});

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
                // Re-populate telegram field after reset
                const adminTelegramField = document.getElementById('quick_admin');
                const currentTelegram = '{{ $currentAdmin->telegram_username ?? '' }}';
                if (currentTelegram) {
                    adminTelegramField.value = currentTelegram;
                }
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