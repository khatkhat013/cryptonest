@extends('layouts.app')

@section('content')
<div class="landing-page">
    <!-- Information Section -->
    <section class="info-section py-5" style="background: #f8f9fa;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="info-box p-5" style="background: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                        <p style="font-size: 16px; font-weight: bold; margin-bottom: 1.5rem; color: #333;">
                            မင်္ဂလာပါ။ Crypto Nest မှ ကြိုဆိုပါတယ်။
                        </p>
                        
                        <p style="font-size: 15px; line-height: 1.8; margin-bottom: 1rem; color: #333;">
                            Crypto Nest နှင့်ပက်သက်ပြီး User Portal နှင့် Admin Portal နှစ်ခုရှိပါတယ်။ User Portal က သင့်ရဲ့ Customer များအတွက် ဖြစ်ပြီး Admin Portal က သင့်ရဲ့ Customer များကို ထိန်းချုပ်နိုင်ရန်အတွက် ဖြစ်ပါတယ်။
                        </p>
                        
                        <p style="font-size: 15px; line-height: 1.8; margin-bottom: 1rem; color: #333;">
                            သင်နှစ်သက်သော plan တစ်ခုကို ရွေးချယ်ပြီးတဲ့အခါ Admin Portal တွင် အကောင့် ဖွင့်၍ လေ့လာနိုင်ပါတယ်။ သင်အကောင့်ဖွင့်ပြီး ငွေလွှဲပြီးတဲ့နောက်မှာ သင့်ရဲ့ Customer များ ကို Admin Dashboard မှတဆင့် သင့်ကိုယ်တိုင် ထိန်းချုပ်နိုင်မှာ ဖြစ်ပါတယ်။
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="features-section py-5">
        <div class="container">
            <h2 class="text-center mb-5">Crypton Nest ကို ဘာကြောင့် ရွေးချယ် အသုံးပြုရမည်နည်း။</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h5>လွယ်ကူရိုးရှင်းပြီး ဈေးနှုန်းသက်သာခြင်း</h5>
                        <p>အခြား Platform များကဲ့သို့ Web Server တစ်ခုလုံးကို ရောင်းချခြင်းမျိုးမဟုတ်တာကြောင့် ဈေးနှုန်းသက်သာစွာနဲ့ လစဥ် Plan များကိုရွေးချယ်ပြီး လိုအပ်မှသာ ဝယ်ယူအသုံးပြုနိုင်ခြင်း။</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-lock"></i>
                        </div>
                        <h5>လုံခြုံမှုနှင့် အာမခံ</h5>
                        <p>ခိုင်လုံသော ကုဒ်လုံခြုံမှု နည်းပညာဖြင့် သင်၏ အချက်အလက်များကို ပေါက်ကြားမှု မရှိစေရန်နဲ့ ဖြစ်လာနိုင်သည့် ဆိုးကျိုးများကိုတာဝန်ယူ ထိန်းသိမ်းပေးထားခြင်း။</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-robot"></i>
                        </div>
                        <h5>Admin Dashboard and Support </h5>
                        <p>သင့်ရဲ့ Customer များကို Admin Dashboard နှင့် Telegram အကောင့်မှတဆင့် Customer Support အနေနဲ့ ထိန်းချုပ်နိုင်မှာဖြစ်ပါတယ်။Customer Support အနေနဲ့ အသုံးပြုနိုင်ရန် Telegram အကောင့်တစ်ခုကို ကြိုတင်ပြင်ဆင်ထားရန်လိုအပ်နိုင်ပါတယ်။</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section class="pricing-section py-5" id="pricing">
        <div class="container">
            <h2 class="text-center mb-2">ရှင်းလင်းသော စျေးနှုန်းအဆင်ပြေ</h2>
            <p class="text-center text-muted mb-5">သင့်အတွက် သင့်လျှာ ဘေ်လ်ကို ရွေးချယ်ပါ</p>
            
            <div class="row g-4">
                <!-- Free Plan -->
                <div class="col-lg-4">
                    <div class="pricing-card">
                        <div class="pricing-header">
                            <h5 class="plan-name">အခမဲ့ သုံးစွဲ</h5>
                            <div class="price">
                                <span class="currency">₀</span>
                                <span class="amount"></span>
                            </div>
                            <p class="plan-duration">၃ ရက်အသုံးပြုခွင့်</p>
                        </div>
                        <div class="pricing-body">
                            <ul class="features-list">
                                <li><i class="fas fa-check"></i> အခြေခံ ကုန်သည်မှု</li>
                                <li><i class="fas fa-check"></i> နေ့တစ်ခါ ၅ ကြိမ်သာ ကုန်သည်မှု</li>
                                <li><i class="fas fa-check"></i> တစ်ခြိန်တည်း စျေးနှုန်း</li>
                                <li><i class="fas fa-check"></i> အခြေခံ အကူအညီ</li>
                                <li><i class="fas fa-check"></i> ဒေါ်လာ ကုန်သည်မှု</li>
                                <li><i class="fas fa-check"></i> ပြည်သူ့ ကြေးကျပ်</li>
                            </ul>
                        </div>
                        <div class="pricing-footer">
                            @auth
                                <button class="btn btn-outline-primary w-100" disabled>လက်ရှိ ဘေ်လ်</button>
                            @else
                                <a href="{{ route('register') }}" class="btn btn-outline-primary w-100">အခမဲ့ စတင်ပါ</a>
                            @endauth
                        </div>
                    </div>
                </div>

                <!-- 1 Month Plan -->
                <div class="col-lg-4">
                    <div class="pricing-card featured">
                        <div class="pricing-badge">အကြီးအကျယ်အသုံးပြုသူများ</div>
                        <div class="pricing-header">
                            <h5 class="plan-name">၁ လ သုံးစွဲခွင့်</h5>
                            <div class="price">
                                <span class="currency">၁</span>
                                <span class="amount">0,000</span>
                            </div>
                            <p class="plan-duration">မြန်မာ ကျပ် - လစ</p>
                        </div>
                        <div class="pricing-body">
                            <ul class="features-list">
                                <li><i class="fas fa-check"></i> အဆင့်မြင့် ကုန်သည်မှု</li>
                                <li><i class="fas fa-check"></i> အကန့်အသတ်မဲ့ ကုန်သည်မှု</li>
                                <li><i class="fas fa-check"></i> အကြီးအကျယ်အသုံးပြုသူ အကူအညီ</li>
                                <li><i class="fas fa-check"></i> ကုန်သည်မှု ခွဲခြမ်းစိတ်ဖြာမှု</li>
                                <li><i class="fas fa-check"></i> အီတီအေိုင်း အကူအညီ</li>
                                <li><i class="fas fa-check"></i> အုပ်ချုပ်ရန် ဒက်ရှ်ဘုတ်</li>
                            </ul>
                        </div>
                        <div class="pricing-footer">
                            @auth
                                <a href="{{ route('checkout', ['plan' => 'monthly']) }}" class="btn btn-primary w-100">အဆင့်မြှင့်တင်ပါ</a>
                            @else
                                <a href="{{ route('register') }}" class="btn btn-primary w-100">စတင်ပါ</a>
                            @endauth
                        </div>
                    </div>
                </div>

                <!-- 2 Months Plan -->
                <div class="col-lg-4">
                    <div class="pricing-card">
                        <div class="pricing-header">
                            <h5 class="plan-name">၂ လ သုံးစွဲခွင့်</h5>
                            <div class="price">
                                <span class="currency">၂</span>
                                <span class="amount">0,000</span>
                            </div>
                            <p class="plan-duration">မြန်မာ ကျပ် - သုံးလပတ်</p>
                        </div>
                        <div class="pricing-body">
                            <ul class="features-list">
                                <li><i class="fas fa-check"></i> ၁ လ တစ်ခါလုံး လုပ်ဆောင်ချက်</li>
                                <li><i class="fas fa-check"></i> ယူរိုပ အခြေခံ ကုန်သည်မှု</li>
                                <li><i class="fas fa-check"></i> ကြေးကျပ် သုံးစွဲခွင့်မှု စောင့်ကြည့်ရန်</li>
                                <li><i class="fas fa-check"></i> ဘုတ်အုပ်ချုပ်မှု ခွင့်ပြုချက်</li>
                                <li><i class="fas fa-check"></i> ကုန်သည်မှု မြန်ဆန်မှု စောင့်ကြည့်မှု</li>
                                <li><i class="fas fa-check"></i><strong>+ ၇ ရက် ထပ်လည်း</strong></li>
                            </ul>
                        </div>
                        <div class="pricing-footer">
                            @auth
                                <a href="{{ route('checkout', ['plan' => 'two-months']) }}" class="btn btn-outline-primary w-100">အဆင့်မြှင့်တင်ပါ</a>
                            @else
                                <a href="{{ route('register') }}" class="btn btn-outline-primary w-100">စတင်ပါ</a>
                            @endauth
                        </div>
                    </div>
                </div>

                <!-- 3 Months Plan -->
                <div class="col-lg-4">
                    <div class="pricing-card">
                        <div class="pricing-header">
                            <h5 class="plan-name">၃ လ သုံးစွဲခွင့်</h5>
                            <div class="price">
                                <span class="currency">၃</span>
                                <span class="amount">0,000</span>
                            </div>
                            <p class="plan-duration">မြန်မာ ကျပ် - သုံးလတစ်ကြိမ်</p>
                        </div>
                        <div class="pricing-body">
                            <ul class="features-list">
                                <li><i class="fas fa-check"></i> ၂ လ တစ်ခါလုံး လုပ်ဆောင်ချက်</li>
                                <li><i class="fas fa-check"></i> အဆင့်မြင့် မြန်နှုန်း ခွဲခြမ်းစိတ်ဖြာမှု</li>
                                <li><i class="fas fa-check"></i> API ချိတ်ဆက်မှု</li>
                                <li><i class="fas fa-check"></i> အီတီအေိုင်း ကုန်သည်မှု အကူအညီ</li>
                                <li><i class="fas fa-check"></i> အုပ်ချုပ်ရန် ဘုတ်များ ခွင့်ပြုချက်</li>
                                <li><i class="fas fa-check"></i><strong>+ ၁၅ ရက် ထပ်လည်း</strong></li>
                            </ul>
                        </div>
                        <div class="pricing-footer">
                            @auth
                                <a href="{{ route('checkout', ['plan' => 'three-months']) }}" class="btn btn-outline-primary w-100">အဆင့်မြှင့်တင်ပါ</a>
                            @else
                                <a href="{{ route('register') }}" class="btn btn-outline-primary w-100">စတင်ပါ</a>
                            @endauth
                        </div>
                    </div>
                </div>

                <!-- 5 Months Plan -->
                <div class="col-lg-4">
                    <div class="pricing-card">
                        <div class="pricing-header">
                            <h5 class="plan-name">၅ လ သုံးစွဲခွင့်</h5>
                            <div class="price">
                                <span class="currency">၅</span>
                                <span class="amount">0,000</span>
                            </div>
                            <p class="plan-duration">မြန်မာ ကျပ် - အများကြီး</p>
                        </div>
                        <div class="pricing-body">
                            <ul class="features-list">
                                <li><i class="fas fa-check"></i> ၃ လ တစ်ခါလုံး လုပ်ဆောင်ချက်</li>
                                <li><i class="fas fa-check"></i> အီတီအေိုင်း နည်းပညာ အကူအညီ</li>
                                <li><i class="fas fa-check"></i> အီတီအေိုင်း ကုန်သည်မှု အကူအညီ</li>
                                <li><i class="fas fa-check"></i> အီတီအေိုင်း သုံးစွဲခွင့် ကုန်သည်မှု</li>
                                <li><i class="fas fa-check"></i> အီတီအေိုင်း ကုန်သည်မှု ခွဲခြမ်းစိတ်ဖြာမှု</li>
                                <li><i class="fas fa-check"></i><strong>+ ၃၀ ရက် ထပ်လည်း</strong></li>
                            </ul>
                        </div>
                        <div class="pricing-footer">
                            @auth
                                <a href="{{ route('checkout', ['plan' => 'five-months']) }}" class="btn btn-outline-primary w-100">အဆင့်မြှင့်တင်ပါ</a>
                            @else
                                <a href="{{ route('register') }}" class="btn btn-outline-primary w-100">စတင်ပါ</a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section py-5">
        <div class="container">
            <div class="text-center">
                <h2>ကုန်သည်မှု စတင်ရန် အဆင်သည်လျှင်</h2>
                <p class="lead mb-4">ကြေးကျပ်နက် ပေါ်တွင် ကုန်သည်မှုလုပ်သူ အများအပြားများထဲ ခွင်းဝင်ပါ</p>
                @auth
                    <a href="{{ route('home') }}" class="btn btn-primary btn-lg">ဒက်ရှ်ဘုတ်သို့ သွားပါ</a>
                @else
                    <a href="{{ route('register') }}" class="btn btn-primary btn-lg">အကောင်တ ဖန်တီးပါ</a>
                @endauth
            </div>
        </div>
    </section>
</div>

@push('styles')
<style>
    .landing-page {
        background: white;
        color: #333;
    }

    .hero-section {
        padding: 80px 0;
        color: #333;
        min-height: 100vh;
        display: flex;
        align-items: center;
        background: white;
    }

    .hero-title {
        font-size: 48px;
        font-weight: 700;
        margin-bottom: 20px;
        color: white;
    }

    .hero-subtitle {
        font-size: 20px;
        margin-bottom: 30px;
        color: rgba(255, 255, 255, 0.9);
    }

    .hero-buttons {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
    }

    .hero-buttons .btn {
        padding: 12px 30px;
        font-size: 16px;
        font-weight: 600;
    }

    .features-section {
        background: #f8f9fa;
    }

    /* Make feature cards consistent and more readable */
    .features-section .row {
        align-items: stretch;
    }

    .feature-card {
        background: rgba(0,0,0,0.55);
        padding: 32px;
        border-radius: 12px;
        text-align: left;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.35);
        transition: transform 0.28s ease, box-shadow 0.28s ease;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .feature-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 14px 40px rgba(0, 0, 0, 0.45);
    }

    .feature-icon {
        font-size: 46px;
        color: #667eea;
        margin-bottom: 18px;
        display: block;
        text-align: center;
    }

    .feature-card h5 {
        font-size: 20px;
        font-weight: 700;
        color: #fff;
        margin: 0 0 12px 0;
        text-align: center;
    }

    .feature-card p {
        color: rgba(255,255,255,0.92);
        font-size: 15px;
        line-height: 1.9;
        margin: 0;
        text-align: left;
        flex: 1 1 auto;
    }

    .pricing-section {
        background: white;
    }

    .pricing-card {
        background: white;
        border: 1px solid #e0e0e0;
        border-radius: 10px;
        overflow: hidden;
        transition: all 0.3s ease;
        position: relative;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .pricing-card:hover {
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        transform: translateY(-5px);
    }

    .pricing-card.featured {
        border-color: #667eea;
        box-shadow: 0 10px 40px rgba(102, 126, 234, 0.2);
        transform: scale(1.02);
    }

    .pricing-badge {
        position: absolute;
        top: 20px;
        right: 20px;
        background: #667eea;
        color: white;
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .pricing-header {
        padding: 30px 25px;
        background: #f8f9fa;
        text-align: center;
    }

    .plan-name {
        font-size: 22px;
        font-weight: 700;
        margin-bottom: 15px;
        color: #333;
    }

    .price {
        margin-bottom: 10px;
    }

    .currency {
        font-size: 18px;
        color: #667eea;
        font-weight: 600;
    }

    .amount {
        font-size: 36px;
        font-weight: 700;
        color: #333;
    }

    .plan-duration {
        font-size: 13px;
        color: #666;
        margin: 0;
    }

    .pricing-body {
        padding: 25px;
        flex-grow: 1;
    }

    .features-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .features-list li {
        padding: 10px 0;
        color: #555;
        border-bottom: 1px solid #f0f0f0;
        font-size: 14px;
    }

    .features-list li:last-child {
        border-bottom: none;
    }

    .features-list i {
        color: #28a745;
        margin-right: 10px;
        font-weight: 600;
    }

    .pricing-footer {
        padding: 20px 25px;
        border-top: 1px solid #e0e0e0;
    }

    .pricing-footer .btn {
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .pricing-footer .btn:disabled {
        cursor: not-allowed;
        opacity: 0.6;
    }

    .cta-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .cta-section h2 {
        font-size: 36px;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .cta-section .lead {
        color: rgba(255, 255, 255, 0.9);
    }

    @media (max-width: 768px) {
        .hero-title {
            font-size: 36px;
        }

        .hero-subtitle {
            font-size: 16px;
        }

        .pricing-card.featured {
            transform: scale(1);
        }

        .hero-image {
            display: none;
        }
    }
</style>
@endpush
@endsection
