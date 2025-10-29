@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mt-4">
        <div class="col-12">
            @include('admin.deposits._table')
        </div>
    </div>

    @include('admin.deposits._modal')
</div>
@endsection