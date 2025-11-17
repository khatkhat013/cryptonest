@extends('layouts.app')

@section('content')


<div class="container">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-sm">    
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-12 col-lg-9">
                            <div class="card shadow-sm my-4">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <a class="btn btn-primary btn-sm me-2" href="{{ asset('pdf/whitepaper.pdf') }}" target="_blank" rel="noopener">Open full PDF in new tab</a>
                                            <a class="btn btn-outline-secondary btn-sm" href="{{ asset('pdf/whitepaper.pdf') }}" download>Download</a>
                                        </div>
                                    </div>

                                    <div class="pdf-container" style="width:100%; height:75vh; min-height:560px; border-radius:6px; overflow:hidden; border:1px solid rgba(0,0,0,0.08)">
                                        <object data="{{ asset('pdf/whitepaper.pdf') }}" type="application/pdf" width="100%" height="100%">
                                            <p class="text-center small py-4">Your browser does not support embedded PDFs. You can <a href="{{ asset('pdf/whitepaper.pdf') }}" target="_blank" rel="noopener">open the PDF in a new tab</a> or <a href="{{ asset('pdf/whitepaper.pdf') }}" download>download it</a> instead.</p>
                                        </object>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @push('styles')
                <style>
                    .pdf-container { background: #f8f9fa; }
                    @media (max-width: 767px) { .pdf-container { height: 65vh; min-height:420px; } }
                </style>
                @endpush

                @endsection
            </div>
