@extends('layouts.app')

@section('content')
<div class="container" style="padding-top:5px;">
    <div class="card">
        <div class="card-body p-0">
            <div class="d-flex justify-content-end p-3">
                <a class="btn btn-outline-primary btn-sm me-2" href="{{ asset('pdf/regulatorylicense.pdf') }}" target="_blank" rel="noopener">Open full PDF in new tab</a>
                <a class="btn btn-outline-secondary btn-sm" href="{{ asset('pdf/regulatorylicense.pdf') }}" download>Download PDF</a>
            </div>
            <div style="width:100%; min-height:720px;">
                <object data="{{ asset('pdf/regulatorylicense.pdf') }}" type="application/pdf" width="100%" height="720">
                    <p class="small text-center py-4">Your browser does not support embedded PDFs. You can <a href="{{ asset('pdf/regulatorylicense.pdf') }}" target="_blank" rel="noopener">open the PDF in a new tab</a> or <a href="{{ asset('pdf/regulatorylicense.pdf') }}" download>download it</a> instead.</p>
                </object>
            </div>
        </div>
    </div>
</div>
@endsection
