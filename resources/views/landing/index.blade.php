@extends('layouts.app')

@section('content')
<div class="landing-page">
    <section class="hero-section text-center text-white d-flex align-items-center" style="background: linear-gradient(135deg, #1f4068 0%, #162447 100%); min-height: 80vh; padding: 100px 0;">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 mx-auto">
                    <h1 class="hero-title display-4 fw-bold mb-4" style="color: #a4e063;">
                        👋 Crypto Nest မှ ကြိုဆိုပါတယ်!
                    </h1>
                    
                    <p class="hero-subtitle lead mb-5 px-lg-5" style="font-size: 1.3rem; line-height: 2; font-weight: 400; color: rgba(255, 255, 255, 0.9);">
                        Crypto Nest တွင် **User Portal** (သင့်ဖောက်သည်များအတွက်) နှင့် **Admin Portal** (သင့်အတွက်) ဟူ၍ Portal နှစ်မျိုး ပါဝင်ပါတယ်။ Admin Portal သည် သင့်ရဲ့ Customer များကို လွယ်ကူထိရောက်စွာ ထိန်းချုပ်စီမံနိုင်ရန် အဓိကထား၍ ဖန်တီးထားပါသည်။
                        <br><br>
                        သင်နှစ်သက်သော Plan ကို ရွေးချယ်ပြီး Admin Portal တွင် အကောင့်ဖွင့်ပါ။ ငွေလွှဲပြီးတာနဲ့ သင့် Customer များအား Admin Dashboard မှတဆင့် စတင်စီမံခန့်ခွဲနိုင်ပါပြီ။
                    </p>
                    
                    <div class="hero-buttons d-flex justify-content-center gap-3 flex-wrap">
                        @auth
                            <a href="{{ Auth::guard('admin')->check() ? route('admin.dashboard') : '/' }}" class="btn btn-warning btn-lg">
                                <i class="fas fa-tachometer-alt me-2"></i> ဒက်ရှ်ဘုတ်သို့ သွားပါ
                            </a>
                        @else
                            <a href="{{ Route::currentRouteName() === 'info' ? route('admin.register') : route('register') }}" class="btn btn-warning btn-lg">
                                <i class="fas fa-user-plus me-2"></i> အကောင့် စတင်ဖွင့်ပါ
                            </a>
                        @endauth
                        <a href="#pricing" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-dollar-sign me-2"></i> Plan များကို ကြည့်ရှုပါ
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="info-section py-5" style="background: #f8f9fa;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="info-box p-lg-5 p-4 text-center" style="background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05);">
                        <h2 class="h4 fw-bold mb-4" style="color: #1f4068;">
                            ✨ ပလက်ဖောင်း အကျဉ်းချုပ်
                        </h2>
                        
                        <p class="text-secondary" style="font-size: 16px; line-height: 1.9; margin-bottom: 0;">
                            Crypto Nest သည် သင့်အား သင့်ကိုယ်ပိုင် Customer များကို အပြည့်အဝ ထိန်းချုပ်စီမံခွင့် ပေးထားသော ဝန်ဆောင်မှု Platform တစ်ခုဖြစ်သည်။ စျေးနှုန်းသက်သာပြီး လုံခြုံမှုအပြည့်ဖြင့် သင့်လုပ်ငန်းကို အဆင့်မြှင့်တင်လိုက်ပါ။
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="features-section py-5" style="background: #1f4068;">
        <div class="container">
            <h2 class="text-center mb-5 text-white fw-bold">
                <span style="color: #a4e063;">Crypto Nest</span> ကို ဘာကြောင့် ရွေးချယ် အသုံးပြုသင့်သလဲ?
            </h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card shadow-lg" style="background: white; border: 1px solid #162447;">
                        <div class="feature-icon text-center">
                            <i class="fas fa-tags" style="color: #a4e063;"></i>
                        </div>
                        <h5 class="text-center" style="color: #1f4068;">လွယ်ကူရိုးရှင်းပြီး ဈေးနှုန်းသက်သာခြင်း</h5>
                        <p class="text-center text-secondary">
                            အခြား Platform များကဲ့သို့ Web Server တစ်ခုလုံးကို ဝယ်စရာမလိုဘဲ၊ ဈေးနှုန်းသက်သာသော လစဥ် Plan များကို ရွေးချယ်ပြီး လိုအပ်မှသာ ဝယ်ယူအသုံးပြုနိုင်ခြင်း။
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card shadow-lg" style="background: white; border: 1px solid #162447;">
                        <div class="feature-icon text-center">
                            <i class="fas fa-shield-alt" style="color: #a4e063;"></i>
                        </div>
                        <h5 class="text-center" style="color: #1f4068;">လုံခြုံမှုနှင့် အာမခံချက်</h5>
                        <p class="text-center text-secondary">
                            ခိုင်မာသော ကုဒ်လုံခြုံမှု နည်းပညာဖြင့် သင်၏ အချက်အလက်များကို ပေါက်ကြားမှု မရှိစေရန် အာမခံပြီး ဖြစ်လာနိုင်သည့် ဆိုးကျိုးများကို တာဝန်ယူ ထိန်းသိမ်းပေးခြင်း။
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card shadow-lg" style="background: white; border: 1px solid #162447;">
                        <div class="feature-icon text-center">
                            <i class="fas fa-headset" style="color: #a4e063;"></i>
                        </div>
                        <h5 class="text-center" style="color: #1f4068;">Admin Dashboard & Customer Support</h5>
                        <p class="text-center text-secondary">
                            သင့်ဖောက်သည်များအား Admin Dashboard မှတဆင့် စီမံခန့်ခွဲနိုင်သလို၊ Telegram အကောင့်ဖြင့်လည်း Customer Support ပေးနိုင်ရန် အထောက်အကူပြုထားခြင်း။
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="pricing-section py-5" id="pricing">
        <div class="container">
            <h2 class="text-center mb-3 fw-bold" style="color: #1f4068;">💰 ရှင်းလင်းသော စျေးနှုန်း Plan များ</h2>
            <p class="text-center text-muted mb-5 lead">သင့်လုပ်ငန်းအတွက် အသင့်တော်ဆုံး Plan ကို ရွေးချယ်ပါ</p>
            
            <div class="row g-4 justify-content-center">
                
                <div class="col-lg-4 col-md-6">
                    <div class="pricing-card">
                        <div class="pricing-header bg-light">
                            <h5 class="plan-name text-dark">အစမ်းသုံးခွင့် (Free Trial)</h5>
                            <div class="price mb-2">
                                <span class="amount">0</span>
                                <span class="currency h5">ကျပ်</span>
                            </div>
                            <p class="plan-duration text-muted">Admin ထံမှ တောင်းခံပြီး အသုံးပြုနိုင်</p>
                        </div>
                        <div class="pricing-body">
                            <ul class="features-list">
                                <li><i class="fas fa-check text-success me-2"></i> API ချိတ်ဆက်မှု</li>
                                <li><i class="fas fa-check text-success me-2"></i> အဆင့်မြင့် ကုန်သည်မှု</li>
                                <li><i class="fas fa-check text-success me-2"></i> အကန့်အသတ်မဲ့ ကုန်သည်မှု</li>
                                <li><i class="fas fa-check text-success me-2"></i> အသုံးပြုသူ အကူအညီ</li>
                                <li><i class="fas fa-check text-success me-2"></i> အုပ်ချုပ်ရန် Dashboard ခွင့်ပြုချက်</li>
                            </ul>
                        </div>
                        <div class="pricing-footer bg-light p-3">
                            @auth
                                <button class="btn btn-outline-secondary w-100" disabled>လက်ရှိ Plan</button>
                            @else
                                <button class="btn btn-outline-success w-100 fw-bold payment-trigger" data-plan="free" data-mmk="0" data-usd="0" data-name="Free Trial" data-bs-toggle="modal" data-bs-target="#paymentModal">အခမဲ့ စတင်ပါ</button>
                            @endauth
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="pricing-card featured border-primary shadow-lg">
                        <div class="pricing-badge bg-danger">အသင့်တော်ဆုံး</div>
                        <div class="pricing-header" style="background: #e6e9f0;">
                            <h5 class="plan-name text-primary">၁ လ သုံးစွဲခွင့် (Standard)</h5>
                            <div class="price mb-2">
                                <span class="amount text-primary">၁,၀၀၀,၀၀၀</span>
                                <span class="currency h5 text-primary">ကျပ်</span>
                            </div>
                            <p class="plan-duration text-muted">တစ်လအတွက်</p>
                        </div>
                        <div class="pricing-body">
                            <ul class="features-list">
                                <li><i class="fas fa-check text-success me-2"></i> API ချိတ်ဆက်မှု</li>
                                <li><i class="fas fa-check text-success me-2"></i> အဆင့်မြင့် ကုန်သည်မှု</li>
                                <li><i class="fas fa-check text-success me-2"></i> အကန့်အသတ်မဲ့ ကုန်သည်မှု</li>
                                <li><i class="fas fa-check text-success me-2"></i> အသုံးပြုသူ အကူအညီ</li>
                                <li><i class="fas fa-check text-success me-2"></i> အုပ်ချုပ်ရန် Dashboard ခွင့်ပြုချက်</li>
                            </ul>
                        </div>
                        <div class="pricing-footer p-3" style="background: #e6e9f0;">
                            @auth
                                <a href="{{ route('admin.dashboard') }}" class="btn btn-primary w-100 fw-bold">အဆင့်မြှင့်တင်ပါ</a>
                            @else
                                <button class="btn btn-primary w-100 fw-bold payment-trigger" data-plan="monthly" data-mmk="1,000,000" data-usd="222" data-name="Standard" data-bs-toggle="modal" data-bs-target="#paymentModal">စတင်ပါ</button>
                            @endauth
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="pricing-card">
                        <div class="pricing-header bg-light">
                            <h5 class="plan-name text-dark">၂ လ သုံးစွဲခွင့် (Pro)</h5>
                            <div class="price mb-2">
                                <span class="amount" style="color: #667eea;">၂,၀၀၀,၀၀၀</span>
                                <span class="currency h5" style="color: #667eea;">ကျပ်</span>
                            </div>
                            <p class="plan-duration text-muted">ငွေပေးချေမှုတစ်ကြိမ်/ ၇ ရက် အပို</p>
                        </div>
                        <div class="pricing-body">
                            <ul class="features-list">
                                <li><i class="fas fa-check text-success me-2"></i> API ချိတ်ဆက်မှု</li>
                                <li><i class="fas fa-check text-success me-2"></i> အဆင့်မြင့် ကုန်သည်မှု</li>
                                <li><i class="fas fa-check text-success me-2"></i> အကန့်အသတ်မဲ့ ကုန်သည်မှု</li>
                                <li><i class="fas fa-check text-success me-2"></i> အသုံးပြုသူ အကူအညီ</li>
                                <li><i class="fas fa-check text-success me-2"></i> အုပ်ချုပ်ရန် Dashboard ခွင့်ပြုချက်</li>
                                <li class="bonus-feature"><i class="fas fa-gift me-2"></i> + ၇ ရက် အပို အခမဲ့</li>
                            </ul>
                        </div>
                        <div class="pricing-footer bg-light p-3">
                            @auth
                                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary w-100 fw-bold">အဆင့်မြှင့်တင်ပါ</a>
                            @else
                                <button class="btn btn-outline-primary w-100 fw-bold payment-trigger" data-plan="two-months" data-mmk="2,000,000" data-usd="444" data-name="Pro" data-bs-toggle="modal" data-bs-target="#paymentModal">စတင်ပါ</button>
                            @endauth
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="pricing-card">
                        <div class="pricing-header bg-light">
                            <h5 class="plan-name text-dark">၃ လ သုံးစွဲခွင့် (Advanced)</h5>
                            <div class="price mb-2">
                                <span class="amount" style="color: #764ba2;">၃,၀၀၀,၀၀၀</span>
                                <span class="currency h5" style="color: #764ba2;">ကျပ်</span>
                            </div>
                            <p class="plan-duration text-muted">ငွေပေးချေမှုတစ်ကြိမ်/ ၁၅ ရက် အပို</p>
                        </div>
                        <div class="pricing-body">
                            <ul class="features-list">
                                <li><i class="fas fa-check text-success me-2"></i> API ချိတ်ဆက်မှု</li>
                                <li><i class="fas fa-check text-success me-2"></i> အဆင့်မြင့် ကုန်သည်မှု</li>
                                <li><i class="fas fa-check text-success me-2"></i> အကန့်အသတ်မဲ့ ကုန်သည်မှု</li>
                                <li><i class="fas fa-check text-success me-2"></i> အသုံးပြုသူ အကူအညီ</li>
                                <li><i class="fas fa-check text-success me-2"></i> အုပ်ချုပ်ရန် Dashboard ခွင့်ပြုချက်</li>
                                <li class="bonus-feature"><i class="fas fa-gift me-2"></i> + ၁၅ ရက် အပို အခမဲ့</li>
                            </ul>
                        </div>
                        <div class="pricing-footer bg-light p-3">
                            @auth
                                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary w-100 fw-bold">အဆင့်မြှင့်တင်ပါ</a>
                            @else
                                <button class="btn btn-outline-secondary w-100 fw-bold payment-trigger" data-plan="three-months" data-mmk="3,000,000" data-usd="666" data-name="Advanced" data-bs-toggle="modal" data-bs-target="#paymentModal">စတင်ပါ</button>
                            @endauth
                        </div>
                    </div>
                </div>

                 <div class="col-lg-4 col-md-6">
                    <div class="pricing-card">
                        <div class="pricing-header bg-light">
                            <h5 class="plan-name text-dark">၅ လ သုံးစွဲခွင့် (Premium)</h5>
                            <div class="price mb-2">
                                <span class="amount" style="color: #1f4068;">၅,၀၀၀,၀၀၀</span>
                                <span class="currency h5" style="color: #1f4068;">ကျပ်</span>
                            </div>
                            <p class="plan-duration text-muted">ငွေပေးချေမှုတစ်ကြိမ်/ ၃၀ ရက် အပို</p>
                        </div>
                        <div class="pricing-body">
                            <ul class="features-list">
                                <li><i class="fas fa-check text-success me-2"></i> API ချိတ်ဆက်မှု</li>
                                <li><i class="fas fa-check text-success me-2"></i> အဆင့်မြင့် ကုန်သည်မှု</li>
                                <li><i class="fas fa-check text-success me-2"></i> အကန့်အသတ်မဲ့ ကုန်သည်မှု</li>
                                <li><i class="fas fa-check text-success me-2"></i> အုပ်ချုပ်ရန် Dashboard ခွင့်ပြုချက်</li>
                                <li><i class="fas fa-check text-success me-2"></i> အသုံးပြုသူ အကူအညီ</li>
                                <li class="bonus-feature"><i class="fas fa-gift me-2"></i> + ၃၀ ရက် အပို အခမဲ့</li>
                            </ul>
                        </div>
                        <div class="pricing-footer bg-light p-3">
                            @auth
                                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary w-100 fw-bold">အဆင့်မြှင့်တင်ပါ</a>
                            @else
                                <button class="btn btn-outline-secondary w-100 fw-bold payment-trigger" data-plan="five-months" data-mmk="5,000,000" data-usd="1110" data-name="Premium" data-bs-toggle="modal" data-bs-target="#paymentModal">စတင်ပါ</button>
                            @endauth
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="pricing-card border-success shadow-lg" style="border-width: 3px !important;">
                        <div class="pricing-badge bg-success">အကောင်းဆုံးရွေးချယ်မှု</div>
                        <div class="pricing-header" style="background: #d4edda;">
                            <h5 class="plan-name text-dark">၁၂ လ သုံးစွဲခွင့် (VIP)</h5>
                            <div class="price mb-2">
                                <span class="amount text-success">၁၀,၀၀၀,၀၀၀</span>
                                <span class="currency h5 text-success">ကျပ်</span>
                            </div>
                            <p class="plan-duration text-muted">ငွေပေးချေမှုတစ်ကြိမ်/ ၉၀ ရက် အပို</p>
                        </div>
                        <div class="pricing-body">
                            <ul class="features-list">
                                <li><i class="fas fa-check text-success me-2"></i> API ချိတ်ဆက်မှု</li>
                                <li><i class="fas fa-check text-success me-2"></i> အဆင့်မြင့် ကုန်သည်မှု</li>
                                <li><i class="fas fa-check text-success me-2"></i> အကန့်အသတ်မဲ့ ကုန်သည်မှု</li>
                                <li><i class="fas fa-check text-success me-2"></i> အုပ်ချုပ်ရန် Dashboard ခွင့်ပြုချက်</li>
                                <li><i class="fas fa-check text-success me-2"></i> အသုံးပြုသူ အကူအညီ</li>
                                <li class="bonus-feature text-dark fw-bold"><i class="fas fa-gift me-2"></i> + ၉၀ ရက် အပို အခမဲ့</li>
                            </ul>
                        </div>
                        <div class="pricing-footer p-3" style="background: #d4edda;">
                            @auth
                                <a href="{{ route('admin.dashboard') }}" class="btn btn-success w-100 fw-bold">အဆင့်မြှင့်တင်ပါ</a>
                            @else
                                <button class="btn btn-success w-100 fw-bold payment-trigger" data-plan="twelve-months" data-mmk="10,000,000" data-usd="2220" data-name="VIP" data-bs-toggle="modal" data-bs-target="#paymentModal">စတင်ပါ</button>
                            @endauth
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section py-5" style="background: linear-gradient(90deg, #a4e063 0%, #7d9d5f 100%);">
        <div class="container">
            <div class="text-center">
                <h2 class="text-dark fw-bold" style="font-size: 2rem;">Crypto Nest ဖြင့် စီမံခန့်ခွဲမှု စတင်လိုက်ပါ</h2>
                <p class="lead mb-4 text-dark">လုံခြုံစိတ်ချရသော Platform ပေါ်တွင် သင့်ဖောက်သည်များအား ထိန်းချုပ်လိုက်ပါ</p>
                @auth
                    <a href="{{ Auth::guard('admin')->check() ? route('admin.dashboard') : '/' }}" class="btn btn-primary btn-lg fw-bold" style="background: #1f4068; border-color: #1f4068;">
                        ဒက်ရှ်ဘုတ်သို့ သွားပါ
                    </a>
                @else
                    <a href="{{ Route::currentRouteName() === 'info' ? route('admin.register') : route('register') }}" class="btn btn-primary btn-lg fw-bold" style="background: #1f4068; border-color: #1f4068;">
                        အခမဲ့ အကောင့် ဖန်တီးပါ
                    </a>
                @endauth
            </div>
        </div>
    </section>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header" style="background: linear-gradient(135deg, #1f4068 0%, #162447 100%); color: white; border: none;">
                <h5 class="modal-title fw-bold w-100 text-center" id="paymentModalLabel">
                    <i class="fas fa-credit-card me-2"></i>ငွေပေးချေမှု အချက်အလက်
                </h5>
                <button type="button" class="btn-close btn-close-white modal-close-btn" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body p-4">
                <div class="alert alert-info d-flex align-items-center" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    <div>
                        <strong>ရွေးချယ်ထားသော Plan:</strong> <span id="selected-plan-name" class="text-primary fw-bold ms-2"></span>
                    </div>
                </div>

                <!-- Payment Tabs -->
                <ul class="nav nav-tabs mb-4 border-0 justify-content-center" id="paymentTabs" role="tablist" style="border-bottom: 2px solid #e0e0e0;">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold py-2 px-4" id="crypto-tab" data-bs-toggle="tab" data-bs-target="#crypto-payment" type="button" role="tab" aria-controls="crypto-payment" aria-selected="true">
                            <i class="fab fa-bitcoin me-2"></i> Crypto (USDT)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold py-2 px-4" id="mobile-tab" data-bs-toggle="tab" data-bs-target="#mobile-payment" type="button" role="tab" aria-controls="mobile-payment" aria-selected="false">
                            <i class="fas fa-mobile-alt me-2"></i> Mobile Money
                        </button>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content" id="paymentTabsContent">

                    <!-- Crypto Payment Tab (now the default active tab) -->
                    <div class="tab-pane fade show active" id="crypto-payment" role="tabpanel" aria-labelledby="crypto-tab">
                        <div class="alert alert-success mb-4 text-center" role="alert">
                            <h6 class="alert-heading fw-bold mb-2">
                                <i class="fab fa-bitcoin me-2"></i>ပေးချေရမည့်ပမာဏ
                            </h6>
                            <p class="h5 mb-0 text-dark text-center">
                                <strong id="usd-amount">222</strong> USDT (TRC20)
                            </p>
                        </div>

                        <div class="card border-0 bg-light mb-4">
                            <div class="card-body">
                                <h6 class="card-title fw-bold mb-3" style="color: #1f4068;">
                                    <i class="fas fa-wallet me-2"></i>Wallet အချက်အလက်
                                </h6>
                                <table class="w-100 payment-details">
                                    <tr>
                                        <td class="fw-bold py-2">Network:</td>
                                        <td class="py-2">
                                            <span class="badge bg-danger">TRC20</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold py-2">Wallet Address:</td>
                                        <td class="py-2">
                                            <code class="text-dark d-block text-break">TXycHE9DBY9abd5Cj9zgbErfmz4cShvFVN</code>
                                            <i class="far fa-copy copy-icon mt-1" role="button" data-text="TXycHE9DBY9abd5Cj9zgbErfmz4cShvFVN" title="ကူးယူပါ"></i>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="alert alert-info" role="alert">
                            <i class="fas fa-lightbulb me-2"></i>
                            Crypto ဖြင့် ပေးချေပါက <strong>10 သိန်း = 222 USDT</strong> ဖြင့် တွက်ချက်ထားသည့် အထူးဈေးနှုန်းဖြင့် ဝန်ဆောင်မှုပေးပါသည်!
                        </div>

                        <div class="d-grid gap-2">
                            <a href="https://t.me/cryptonest_support" target="_blank" rel="noopener" class="btn btn-info fw-bold py-2">
                                <i class="fab fa-telegram-plane me-2"></i>Admin သို့ ဆက်သွယ်မည်
                            </a>
                            <a href="/admin/login" class="btn btn-outline-dark fw-bold py-2">
                                <i class="fas fa-user-shield me-2"></i>Admin Login
                            </a>
                        </div>
                    </div>

                    

                    <!-- Mobile Money Tab -->
                    <div class="tab-pane fade" id="mobile-payment" role="tabpanel" aria-labelledby="mobile-tab">
                        <div class="alert alert-success mb-4 text-center" role="alert">
                            <h6 class="alert-heading fw-bold mb-2">
                                <i class="fas fa-mobile-alt me-2"></i>ပေးချေရမည့်ပမာဏ
                            </h6>
                            <p class="h5 mb-0 text-dark text-center">
                                <strong id="mmk-amount-mobile">1,000,000</strong> MMK
                            </p>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="card border-0 bg-light h-100">
                                    <div class="card-body">
                                        <h6 class="card-title fw-bold mb-3 d-flex align-items-center" style="color: #1f4068;">
                                            <img src="{{ asset('images/icons/kpay.png') }}" alt="KBZ Pay" class="provider-icon">
                                            KBZ Pay
                                        </h6>
                                        <table class="w-100 payment-details">
                                            <tr>
                                                <td class="fw-bold py-2">အမည်:</td>
                                                <td class="py-2">Myo Ko Aung</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold py-2">ဖုန်း:</td>
                                                <td class="py-2">
                                                    <code class="text-dark">09-950-569-539</code>
                                                    <i class="far fa-copy copy-icon ms-2" role="button" data-text="09-XXX-XXXXX" title="ကူးယူပါ"></i>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card border-0 bg-light h-100">
                                    <div class="card-body">
                                        <h6 class="card-title fw-bold mb-3 d-flex align-items-center" style="color: #1f4068;">
                                            <img src="{{ asset('images/icons/wavepay.jpg') }}" alt="Wave Money" class="provider-icon">
                                            Wave Money
                                        </h6>
                                        <table class="w-100 payment-details">
                                            <tr>
                                                <td class="fw-bold py-2">အမည်:</td>
                                                <td class="py-2">Myo Ko Aung</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold py-2">ဖုန်း:</td>
                                                <td class="py-2">
                                                    <code class="text-dark">09-950-569-539</code>
                                                    <i class="far fa-copy copy-icon ms-2" role="button" data-text="09-XXX-XXXXX" title="ကူးယူပါ"></i>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-warning mt-4" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>အရေးကြီး:</strong> ငွေလွှဲပြီးကြောင်းကို  Telegram တွင် Screenshot ဖြင့် Admin သို့ ပို့ပေးပါ။
                        </div>

                        <div class="d-grid gap-2">
                            <a href="https://t.me/cryptonest_support" target="_blank" rel="noopener" class="btn btn-info fw-bold py-2">
                                <i class="fab fa-telegram-plane me-2"></i>Admin သို့ ဆက်သွယ်မည်
                            </a>
                            <a href="/admin/login" class="btn btn-outline-dark fw-bold py-2">
                                <i class="fas fa-user-shield me-2"></i>Admin Login
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* ===== Global Styles ===== */
    .landing-page {
        background: white;
        color: #333;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .btn-warning {
        background-color: #a4e063;
        border-color: #a4e063;
        color: #1f4068;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-warning:hover {
        background-color: #7d9d5f;
        border-color: #7d9d5f;
        color: #1f4068;
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(164, 224, 99, 0.3);
    }

    /* ===== Hero Section ===== */
    .hero-title {
        font-size: 3.5rem;
        font-weight: 700;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        animation: fadeInDown 0.8s ease;
    }

    .hero-subtitle {
        font-size: 1.3rem;
        line-height: 1.8;
        color: rgba(255, 255, 255, 0.95);
        animation: fadeInUp 0.8s ease 0.2s backwards;
    }

    .hero-buttons {
        gap: 15px;
        animation: fadeInUp 0.8s ease 0.4s backwards;
    }

    .hero-buttons .btn {
        padding: 15px 35px;
        font-size: 16px;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .hero-buttons .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
    }

    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* ===== Information Section ===== */
    .info-box {
        transition: all 0.3s ease;
    }

    .info-box:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
    }

    /* ===== Features Section ===== */
    .features-section h2 {
        font-size: 2.5rem;
        font-weight: 700;
    }

    .feature-card {
        padding: 30px 25px;
        border-radius: 12px;
        height: 100%;
        background: white;
        border: 2px solid #e8e8e8;
        transition: all 0.35s ease;
    }

    .feature-card:hover {
        transform: translateY(-12px);
        box-shadow: 0 15px 40px rgba(31, 64, 104, 0.15);
        border-color: #a4e063;
    }

    .feature-icon {
        font-size: 50px;
        margin-bottom: 20px;
        transition: transform 0.3s ease;
    }

    .feature-card:hover .feature-icon {
        transform: scale(1.1) rotate(5deg);
    }

    .feature-card h5 {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 15px;
        color: #1f4068;
    }

    .feature-card p {
        font-size: 15px;
        line-height: 1.6;
    }

    /* ===== Pricing Section ===== */
    .pricing-section h2 {
        font-size: 2.5rem;
        font-weight: 700;
        color: #1f4068;
    }

    .pricing-card {
        border: 2px solid #e8e8e8;
        border-radius: 15px;
        overflow: hidden;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        height: 100%;
        position: relative;
    }

    .pricing-card:hover:not(.featured) {
        transform: translateY(-8px);
        box-shadow: 0 12px 25px rgba(0, 0, 0, 0.1);
        border-color: #a4e063;
    }

    .pricing-card.featured {
        box-shadow: 0 15px 45px rgba(31, 64, 104, 0.25);
        transform: scale(1.03);
        border-width: 3px;
        border-color: #0d6efd;
    }

    .pricing-badge {
        position: absolute;
        top: 0;
        right: 0;
        padding: 8px 18px;
        color: white;
        font-size: 0.85rem;
        font-weight: bold;
        border-bottom-left-radius: 12px;
        z-index: 10;
    }

    .pricing-header {
        padding: 30px 20px;
        text-align: center;
        border-bottom: 2px solid #f0f0f0;
    }

    .pricing-body {
        padding: 25px 20px;
        flex-grow: 1;
    }

    .plan-name {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1f4068;
    }

    .amount {
        font-size: 2.8rem;
        font-weight: bold;
        display: block;
    }

    .currency {
        font-size: 1rem;
        display: block;
        font-weight: 600;
    }

    .plan-duration {
        font-size: 14px;
        font-weight: 500;
    }

    .features-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .features-list li {
        padding: 12px 0;
        font-size: 15px;
        border-bottom: 1px solid #f0f0f0;
        color: #555;
        display: flex;
        align-items: center;
        transition: all 0.2s ease;
    }

    .features-list li:hover {
        color: #1f4068;
        padding-left: 5px;
    }

    .features-list li:last-child {
        border-bottom: none;
    }

    .features-list i {
        min-width: 25px;
    }

    .bonus-feature {
        font-weight: 600;
        color: #d9534f;
        background: #fff5f5;
        border-top: 2px solid #f0f0f0;
        padding-top: 15px !important;
    }

    .pricing-card.border-success .bonus-feature {
        color: #28a745;
        background: #f1fdf5;
    }

    .pricing-footer {
        padding: 20px;
        border-top: 1px solid #f0f0f0;
    }

    .pricing-footer .btn {
        padding: 12px 20px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .pricing-footer .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }

    /* ===== CTA Section ===== */
    .cta-section h2 {
        font-size: 2rem;
        font-weight: 700;
        color: #1f4068;
    }

    .cta-section .btn {
        padding: 14px 40px;
        font-size: 16px;
        transition: all 0.3s ease;
    }

    .cta-section .btn:hover {
        transform: scale(1.05);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    }

    /* ===== Modal Styles ===== */
    .modal-content {
        border-radius: 15px;
        border: 1px solid #e8e8e8;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
    }

    .modal-header {
        border-radius: 15px 15px 0 0;
        padding: 25px;
        border: none;
        position: relative;
    }

    .nav-tabs .nav-link {
        color: #1f4068;
        border: none;
        padding: 12px 20px;
        font-weight: 600;
        transition: all 0.3s ease;
        border-radius: 8px 8px 0 0;
    }

    .nav-tabs .nav-link:hover {
        color: #a4e063;
        background: #f8f9fa;
    }

    .nav-tabs .nav-link.active {
        color: white;
        background: linear-gradient(135deg, #1f4068 0%, #162447 100%);
        border: none;
    }

    .payment-details {
        border-collapse: separate;
        border-spacing: 0 8px;
    }

    .payment-details tr td {
        padding: 10px 0;
        border-bottom: 1px solid #e8e8e8;
        font-size: 14px;
    }

    .payment-details tr:last-child td {
        border-bottom: none;
    }

    .copy-icon {
        cursor: pointer;
        color: #1f4068;
        transition: all 0.2s ease;
    }

    .provider-icon {
        width: 28px;
        height: auto;
        object-fit: contain;
        margin-right: 10px;
        display: inline-block;
        vertical-align: middle;
    }

    .modal-close-btn {
        position: absolute;
        right: 18px;
        top: 18px;
        width: 1.6rem;
        height: 1.6rem;
        opacity: 0.95;
    }

    .copy-icon:hover {
        color: #a4e063;
        transform: scale(1.2);
    }

    /* ===== Responsive Design ===== */
    @media (max-width: 991px) {
        .hero-title {
            font-size: 2.5rem;
        }

        .hero-subtitle {
            font-size: 1.1rem;
        }

        .pricing-card.featured {
            transform: scale(1);
        }

        .pricing-section .row > div {
            margin-bottom: 1.5rem;
        }
    }

    @media (max-width: 767px) {
        .hero-title {
            font-size: 2rem;
        }

        .hero-subtitle {
            font-size: 1rem;
        }

        .amount {
            font-size: 2rem;
        }

        .hero-buttons {
            flex-direction: column;
        }

        .hero-buttons .btn {
            width: 100%;
        }

        .pricing-section h2 {
            font-size: 1.8rem;
        }

        .features-section h2 {
            font-size: 1.8rem;
        }
    }

    @media (max-width: 576px) {
        .modal-body {
            padding: 15px !important;
        }

        .pricing-card {
            margin-bottom: 1rem;
        }

        .feature-card {
            padding: 20px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle payment triggers for both checkout links and payment buttons
    const checkoutButtons = document.querySelectorAll('a[href*="checkout"], .payment-trigger');
    
    checkoutButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            // Only intercept if user is not authenticated and it's a checkout link
            const href = this.getAttribute('href');
            if (href && href.includes('checkout')) {
                const urlParams = new URL(href, window.location.origin).searchParams;
                const plan = urlParams.get('plan');
                
                // If user is not authenticated, show payment modal instead
                // For now, just show the modal (checkout links are only shown to authenticated users in Blade)
                e.preventDefault();
                showPaymentModal(plan);
            } else if (this.classList.contains('payment-trigger')) {
                e.preventDefault();
                const plan = this.getAttribute('data-plan');
                const mmk = this.getAttribute('data-mmk');
                const usd = this.getAttribute('data-usd');
                const name = this.getAttribute('data-name');
                
                updatePaymentModal(name, mmk, usd);
            }
        });
    });

    // Copy to Clipboard Functionality
    document.querySelectorAll('.copy-icon').forEach(icon => {
        icon.addEventListener('click', function(e) {
            e.preventDefault();
            const textToCopy = this.getAttribute('data-text');

            navigator.clipboard.writeText(textToCopy).then(() => {
                // Visual feedback
                const originalClass = this.className;
                this.className = 'fas fa-check text-success ms-2';
                
                setTimeout(() => {
                    this.className = originalClass;
                }, 1500);
            }).catch(err => {
                console.error('Failed to copy:', err);
                alert('Copy မအောင်မြင်ခဲ့ပါ။ ကျေးဇူးပြု၍ အလယ်အလတ်ကည့်ရှုပြီး ကူးယူပါ။');
            });
        });
    });

    // Add smooth scroll to pricing section
    document.querySelectorAll('a[href="#pricing"]').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const pricing = document.querySelector('#pricing');
            if (pricing) {
                pricing.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    // Add hover effects to pricing cards
    document.querySelectorAll('.pricing-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            if (!this.classList.contains('featured')) {
                this.style.transform = 'translateY(-8px)';
            }
        });

        card.addEventListener('mouseleave', function() {
            if (!this.classList.contains('featured')) {
                this.style.transform = 'translateY(0)';
            }
        });
    });
});

function updatePaymentModal(planName, mmk, usd) {
    document.getElementById('selected-plan-name').textContent = planName;
    document.getElementById('mmk-amount-mobile').textContent = mmk;
    document.getElementById('usd-amount').textContent = usd;
}

function showPaymentModal(plan) {
    const planPrices = {
        'free': { mmk: '0', usd: '0', name: 'Free Trial' },
        'monthly': { mmk: '1,000,000', usd: '222', name: 'Standard' },
        'two-months': { mmk: '2,000,000', usd: '444', name: 'Pro' },
        'three-months': { mmk: '3,000,000', usd: '666', name: 'Advanced' },
        'five-months': { mmk: '5,000,000', usd: '1110', name: 'Premium' },
        'twelve-months': { mmk: '10,000,000', usd: '2220', name: 'VIP' }
    };
    
    if (planPrices[plan]) {
        const planData = planPrices[plan];
        updatePaymentModal(planData.name, planData.mmk, planData.usd);
        const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
        modal.show();
    }
}
</script>
@endpush
@endsection