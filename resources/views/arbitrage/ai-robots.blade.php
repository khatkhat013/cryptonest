@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Header Card -->
    <div class="card bg-primary text-white border-0 mb-4" style="border-radius: 20px;">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <a href="{{ url('/arbitrage') }}" class="text-white">
                    <i class="bi bi-arrow-left fs-4"></i>
                </a>
                <h3 class="mb-0 text-white">Introduction To Arbitrage</h3>
                <div></div>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="card shadow-sm" style="border-radius: 15px;">
        <div class="card-body p-4">
            <h4 class="mb-4">What is arbitrage?</h4>
            
            <div class="text-muted mb-4">
                <p>Due to the high volatility of digital currencies, there will be spreads in the currency values of various exchanges. Arbitrage is the purchase of an asset in a lower-priced market and then selling it at a higher price in another trading market. Buy low and sell high. The price difference in the middle is the profit. We call this operation "brick arbitrage"</p>
            </div>

            <div class="bg-light p-4 rounded-3 mb-4">
                <h5 class="mb-3">Example Scenario</h5>
                <p class="mb-0">Assume that the EOS/USDT trading pair: the currency price is 11, the currency price is 10, and the EOS price difference between the two exchanges is 1 US dollar. Suppose you hold 1 Huobi EOS and follow the principle of buying high and buying low. Selling 1 EOS on Huobi will earn 11 USDT, and buying 1 EOS on Huobi will cost 10 USDT. Buy one and sell one net profit. 1 USDT, the amount of EOS remains unchanged.</p>
            </div>

            <div class="text-muted mb-4">
                <p>Although such price differences exist, manual arbitrage often involves a lot of uncertainty due to factors such as time-consuming manual operations, poor accuracy, and price changes. Use quantitative models to capture arbitrage opportunities and formulate arbitrage trading strategies. Programmed algorithms automatically issue trading instructions to the exchange to quickly and accurately capture opportunities and earn profits efficiently.</p>
            </div>

            <div class="card bg-primary text-white" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <h5 class="mb-3">Our AI arbitrage bot</h5>
                    <p class="mb-0">Our Al arbitrage robot can complete price screening on more than 200 exchanges around the world and automatically complete transactions, completely replacing manual operations. It has high work efficiency, safety and stability, and fast returns.</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .card {
        border: none;
    }
    .text-muted {
        line-height: 1.6;
    }
</style>
@endpush
@endsection
