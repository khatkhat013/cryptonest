@extends('layouts.admin')
@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h3 class="mb-0">Edit Trade Order #{{ $trade->id }}</h3>
        <div>
            <button type="submit" form="trade-edit-form" class="btn btn-success me-2">
                <i class="bi bi-check-circle"></i> Save Changes
            </button>
            <a href="{{ route('admin.trading.index') }}" class="btn btn-secondary">
                <i class="bi bi-x-circle"></i> Cancel
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form id="trade-edit-form" method="POST" action="{{ route('admin.trading.update', $trade->id) }}">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">User</label>
                        <input type="text" class="form-control" value="{{ $trade->user?->name ?? $trade->user?->email ?? '—' }}" disabled>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Price Range %</label>
                        <input type="number" name="price_range_percent" class="form-control" value="{{ $trade->price_range_percent }}">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Symbol</label>
                        <input type="text" name="symbol" class="form-control" value="{{ $trade->symbol }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Delivery Seconds</label>
                        <input type="number" name="delivery_seconds" class="form-control" value="{{ $trade->delivery_seconds }}">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Direction</label>
                        <select name="direction" class="form-select">
                            <option value="up" @if($trade->direction=='up') selected @endif>Up</option>
                            <option value="down" @if($trade->direction=='down') selected @endif>Down</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Profit Amount</label>
                        <input type="number" step="0.00000001" name="profit_amount" class="form-control" value="{{ $trade->profit_amount }}">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Quantity</label>
                        <input type="number" step="0.00000001" name="purchase_quantity" class="form-control" value="{{ $trade->purchase_quantity }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Payout</label>
                        <input type="number" step="0.00000001" name="payout" class="form-control" value="{{ $trade->payout }}">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Purchase Price</label>
                        <input type="number" step="0.00000001" name="purchase_price" class="form-control" value="{{ $trade->purchase_price }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Result</label>
                        <input type="text" name="result" class="form-control" value="{{ $trade->result }}">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Initial Price</label>
                        <input type="number" step="0.00000001" name="initial_price" class="form-control" value="{{ $trade->initial_price }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Final Price</label>
                        <input type="number" step="0.00000001" name="final_price" class="form-control" value="{{ $trade->final_price }}">
                    </div>
                </div>

            </form>
        </div>
        
    </div>
</div>
@endsection
