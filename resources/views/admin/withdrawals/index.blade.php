@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mt-4">
        <div class="col-12">
            @include('admin.withdrawals._table')
        </div>
    </div>

    @include('admin.withdrawals._modal')
</div>
@endsection
