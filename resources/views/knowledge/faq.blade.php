@extends('layouts.app')

@section('content')
<x-page-header 
    :back-url="url('/')"
    title="Common Problems"
/>

<div class="container">
    <div class="accordion" id="faqAccordion">
        <!-- What are futures -->
        <div class="accordion-item border-0 mb-3">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed bg-light rounded-3" type="button" data-bs-toggle="collapse" data-bs-target="#futures">
                    What are futures
                </button>
            </h2>
            <div id="futures" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                    <p>Options are financial instruments that provide users with exposure to the price of an underlying asset while limiting their exposure to downside risk. It offers buyers the opportunity to go long (call options) or short (put options) in BTC or ETH.</p>
                    
                    <p>Newcomers to options contracts can learn about:</p>
                    
                    <p>A put option represents the right (not the obligation) to sell an asset at a predetermined price within a certain period of time. This gives you "short" exposure, as if the price of the underlying asset had fallen, you retain the right to sell the asset at a higher price (called the strike price) and make a profit.</p>
                    
                    <p>A call option represents the holder's right (not the obligation) to purchase an asset at a predetermined price within a certain period of time. This gives you "long" exposure, as if the underlying asset rises in price, you retain the right to purchase the asset at a lower price and make a profit</p>
                </div>
            </div>
        </div>

        <!-- What can be done on the platform -->
        <div class="accordion-item border-0 mb-3">
            <h2 class="accordion-header">
                    <button class="accordion-button collapsed bg-light rounded-3" type="button" data-bs-toggle="collapse" data-bs-target="#platform">
                    What can be done on the Crypto Nest futures trading platform?
                </button>
            </h2>
            <div id="platform" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                    <p>You can perform three main operations on this platform:</p>
                    <ul>
                        <li>Futures trading</li>
                        <li>Arbitrage trading</li>
                        <li>Mining trading</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Contract trading rules -->
        <div class="accordion-item border-0 mb-3">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed bg-light rounded-3" type="button" data-bs-toggle="collapse" data-bs-target="#rules">
                    What are the contract trading rules?
                </button>
            </h2>
            <div id="rules" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                    <h6>Transaction Type</h6>
                    <p>Transaction types are divided into two directions: long position opening (buying) and short position opening (selling):</p>
                    
                    <p><strong>Buying long (bullish)</strong> means that you believe that the current index is likely to rise and hope to buy a certain number of new contracts at the price you set or the current market price.</p>
                    
                    <p><strong>Short selling (position)</strong> means that you believe that the current index is likely to fall and hope to sell a certain number of new contracts at a price you set or the current market price.</p>
                    
                    <h6 class="mt-3">Ordering method</h6>
                    <ul>
                        <li><strong>Limit order:</strong> You need to specify the price and quantity of the order yourself</li>
                        <li><strong>Market order:</strong> You only need to set the order quantity, and the price will be the current market price</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Safety -->
        <div class="accordion-item border-0 mb-3">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed bg-light rounded-3" type="button" data-bs-toggle="collapse" data-bs-target="#safety">
                    Is it safe?
                </button>
            </h2>
            <div id="safety" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                    <p>We work very hard to ensure every aspect of the platform. We also conduct professional audits of all smart contracts. From the ground up, we designed Crypto Nest to be as powerful as possible: from the price prediction mechanism to the liquidation mechanism to the underlying smart contracts.</p>
                </div>
            </div>
        </div>

        <!-- Success in trading -->
        <div class="accordion-item border-0 mb-3">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed bg-light rounded-3" type="button" data-bs-toggle="collapse" data-bs-target="#success">
                    How can you succeed in digital currency trading?
                </button>
            </h2>
            <div id="success" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                    <p>After investors understand the rules of digital currency trading, they may also want to choose to invest, but they also want to truly gain profits during the investment process. First, you should do your homework, and you also need to rely on common sense. Many people may not understand this technology at all, but you should also know something about virtual currencies. For example, you can pay attention to the platform's digital currency exchange. The platform itself will have many virtual currencies, and you can learn about the knowledge in between. In addition, in the process of choosing investment, you must choose the right time, because it is also very critical in the investment process. Only when the time is ripe can we truly achieve our goals and bring more benefits to investors.</p>

                    <p>Everyone wants to gain wealth, and everyone wants to have a successful career. However, on the road of investment, some people may seem to be very happy, but in fact they may have unacceptable sadness, so before you choose to invest. First, we should understand the rules of digital currency trading. The digital currency itself can be traded without interruption for 24 hours. Therefore, when we make decisions, we must first maintain rationality and control. This is also an important content.</p>
                </div>
            </div>
        </div>

        <!-- Trading rules -->
        <div class="accordion-item border-0 mb-3">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed bg-light rounded-3" type="button" data-bs-toggle="collapse" data-bs-target="#tradingRules">
                    What are the rules for digital currency trading?
                </button>
            </h2>
            <div id="tradingRules" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                    <p>When you understand the rules of digital currency trading, you will find that it is relatively simple. The entire digital currency transaction is nothing more than transferring digital currency to another user. It is the output of the transaction, etc., then the final difference is equivalent to a reward.</p>

                    <p>First of all, the trading hours are 24 hours a day, all year round, and there is no limit on the rise or fall. You should know that there is a 10% limit on the rise and fall of stocks, but there is no limit on virtual currencies. For example, on May 28, Bitcoin's single-day increase directly exceeded 20%.</p>

                    <p>In addition, digital currency trading rules also include t plus 0 transactions, that is, buying on the same day and selling on the same day. Cash can also be withdrawn at any time, and the liquidity of funds is relatively high.</p>

                    <p>The rules of digital currency trading are actually relatively simple. For example, through basic principles such as current price trading and market price trading, you can not only enjoy price priority, but also time priority. A higher purchase price will definitely take precedence over a lower purchase price. When the order prices are exactly the same, the order can be placed relatively early and the transaction can naturally be executed quickly.</p>
                </div>
            </div>
        </div>

        <!-- Blockchain -->
        <div class="accordion-item border-0 mb-3">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed bg-light rounded-3" type="button" data-bs-toggle="collapse" data-bs-target="#blockchain">
                    Is it entirely on the blockchain?
                </button>
            </h2>
            <div id="blockchain" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                    <p>We use a hybrid model decentralized exchange with off-chain matching and on-chain settlement. It requires no gas to create or cancel orders, but all token transfers are still processed on-chain. This is a more friendly structure for market makers and is designed to promote excellent liquidity.</p>
                </div>
            </div>
        </div>

        <!-- Who can use -->
        <div class="accordion-item border-0 mb-3">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed bg-light rounded-3" type="button" data-bs-toggle="collapse" data-bs-target="#whoCanUse">
                    Who can use the decentralized futures trading platform?
                </button>
            </h2>
            <div id="whoCanUse" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                    <p>When our platform first launches, we will be available to everyone. There is currently no centralized process for account creation and no planned restrictions on our users.</p>
                </div>
            </div>
        </div>

        <!-- Credit score -->
        <div class="accordion-item border-0 mb-3">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed bg-light rounded-3" type="button" data-bs-toggle="collapse" data-bs-target="#creditScore">
                    What is a credit score?
                </button>
            </h2>
            <div id="creditScore" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                    <p>Refers to a statistical and evaluation system that gives different scores based on participants' trust plans, number of option transactions, financial status and other information, and is used to determine whether to approve withdrawals or credit lines.</p>
                    
                    <p>Credit score levels:</p>
                    <ul>
                        <li>580 points-620 points: Maximum weekly withdrawal is $10,000 USD or coin equivalent.</li>
                        <li>621 points-650 points: Maximum weekly withdrawal is $50,000 USD or coin equivalent.</li>
                        <li>651 points-680 points: The maximum weekly withdrawal is US$100,000 or its equivalent.</li>
                        <li>681 points-720 points: The maximum weekly withdrawal is US$500,000 or its equivalent.</li>
                        <li>721 points: Unlimited withdrawals and above.</li>
                    </ul>
                    
                    <p>Credit scores can be obtained from the bank providing credit or from Crypto Nest's internal system</p>
                </div>
            </div>
        </div>

        <!-- Custody fees -->
        <div class="accordion-item border-0 mb-3">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed bg-light rounded-3" type="button" data-bs-toggle="collapse" data-bs-target="#custodyFees">
                    What are custody fees?
                </button>
            </h2>
            <div id="custodyFees" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                    <p>Crypto Nest's custody fees refer to the various fees that users need to pay when trading or managing digital assets on decentralized exchanges. These fees can cover multiple aspects, as follows:</p>

                    <ol>
                        <li><strong>Asset custody fees:</strong> Although Crypto Nest does not store user assets centrally by nature, in some cases, users may choose to use custody services to ensure the safety of their assets. These custody service providers charge a certain custody fee, usually based on the total value of the assets or a fixed amount.</li>
                        
                        <li><strong>Transaction fees:</strong> Users usually need to pay transaction fees when trading on Crypto Nest. These fees are used to compensate liquidity providers, maintain the operation and technical support of the platform. Transaction fees may vary depending on the transaction volume or asset type.</li>
                        
                        <li><strong>Withdrawal fees:</strong> When users withdraw assets from Crypto Nest, withdrawal fees may be incurred. These fees are used to cover network fees and other related costs.</li>
                        
                        <li><strong>Service fees:</strong> If users choose to participate in additional services such as trust plans, asset management or advisory services, additional custody fees may be incurred. These fees are usually related to the content and complexity of the services provided.</li>
                        
                        <li><strong>Other fees:</strong> In some cases, Crypto Nest may charge other related fees, such as transaction report generation fees or compliance review fees.</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .accordion-button:not(.collapsed) {
        background-color: var(--bs-primary) !important;
        color: white !important;
    }
    .accordion-button:focus {
        box-shadow: none;
    }
</style>
@endpush