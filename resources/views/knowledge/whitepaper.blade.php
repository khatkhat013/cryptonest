@extends('layouts.app')

@section('content')
<x-page-header 
    :back-url="url('/')"
    title="White Paper"
/>

<div class="container">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-body p-5">
                    <h2 class="mb-4">Crypto-Nest White Paper</h2>
                    
                    <section class="mb-5">
                        <h3 class="h5 mb-3">Overview</h3>
                        <p>Crypto-Nest is a comprehensive cryptocurrency trading and investment platform designed to provide users with secure, efficient, and profitable opportunities in the digital asset market.</p>
                    </section>

                    <section class="mb-5">
                        <h3 class="h5 mb-3">Platform Features</h3>
                        <ul>
                            <li><strong>Futures Trading:</strong> Advanced derivatives trading with leverage and risk management tools</li>
                            <li><strong>Arbitrage Trading:</strong> Automated arbitrage opportunities across multiple trading pairs</li>
                            <li><strong>AI-Powered Plans:</strong> Intelligent investment strategies managed by AI algorithms</li>
                            <li><strong>User-Friendly Interface:</strong> Intuitive dashboard and trading tools for all experience levels</li>
                            <li><strong>Security:</strong> Enterprise-grade security protocols and user fund protection</li>
                        </ul>
                    </section>

                    <section class="mb-5">
                        <h3 class="h5 mb-3">Trading Operations</h3>
                        <p>The platform supports three primary operations:</p>
                        <ol>
                            <li><strong>Spot Trading:</strong> Buy and sell cryptocurrencies at market prices with immediate settlement</li>
                            <li><strong>Futures Trading:</strong> Trade cryptocurrency derivatives with configurable leverage</li>
                            <li><strong>AI Arbitrage:</strong> Automated profit generation through market inefficiencies</li>
                        </ol>
                    </section>

                    <section class="mb-5">
                        <h3 class="h5 mb-3">User Benefits</h3>
                        <ul>
                            <li>Low trading fees and competitive spreads</li>
                            <li>24/7 market access and trading capabilities</li>
                            <li>Multiple currency pair support (BTC, ETH, USDT, USDC, etc.)</li>
                            <li>Advanced charting and technical analysis tools</li>
                            <li>Real-time notifications and trade alerts</li>
                            <li>Dedicated customer support team</li>
                        </ul>
                    </section>

                    <section class="mb-5">
                        <h3 class="h5 mb-3">Risk Management</h3>
                        <p>Crypto-Nest implements comprehensive risk management features to protect user investments:</p>
                        <ul>
                            <li>Position size limits and stop-loss orders</li>
                            <li>Account balance verification and compliance checks</li>
                            <li>Real-time portfolio monitoring and alerts</li>
                            <li>Insurance coverage for user assets</li>
                        </ul>
                    </section>

                    <section>
                        <h3 class="h5 mb-3">Conclusion</h3>
                        <p>Crypto-Nest combines advanced trading technology with user-centric design to create a powerful platform for cryptocurrency trading and investment. Our commitment to security, transparency, and customer satisfaction makes us the ideal choice for both beginner and experienced traders.</p>
                    </section>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
