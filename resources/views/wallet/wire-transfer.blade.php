@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    <!-- Header -->
    <div class="bg-primary text-white position-relative" style="border-radius: 0 0 25px 25px;">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center pt-3">
                <a href="{{ url('/wallets') }}" class="text-white text-decoration-none">
                    <i class="bi bi-arrow-left fs-4"></i>
                </a>
                <h5 class="mb-0 text-center flex-grow-1">Wire Transfers</h5>
                <div class="invisible">
                    <i class="bi bi-arrow-left fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="container mt-4">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h4 class="card-title mb-4">Wire Transfers</h4>

                <div class="alert alert-info mb-4" role="alert">
                    <p class="mb-3">Before initiating a wire transfer, please contact our customer service team to obtain the correct wire transfer information. This is to ensure that your funds are safely deposited!</p>
                </div>

                <div class="card bg-light border-0 mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Wire transfer processing time:</h5>
                        <p class="card-text mb-0">Wire transfer processing time varies according to the processing time of different banking systems around the world, please provide proof of the wire transfer to our customer service team in time after the wire transfer in order to make inquiries and speed up the processing time!</p>
                    </div>
                </div>

                <div class="alert alert-warning mb-4" role="alert">
                    <h5 class="alert-heading">Assistance in the wire transfer process:</h5>
                    <p class="mb-0">If you encounter any problems in the wire transfer process, please contact our customer service team for assistance.</p>
                </div>

                <div class="alert alert-primary mb-4" role="alert">
                    <p class="mb-0">If you encounter any problems or questions during the wire transfer process, please feel free to contact our customer service team, we are always ready to provide you with assistance and support to ensure that your wire transfer to the account in a timely manner!</p>
                </div>

                <div class="card bg-light border-0">
                    <div class="card-body">
                        <h5 class="card-title">After the wire transfer is completed:</h5>
                        <p class="card-text mb-0">To complete a wire transfer, you will need to provide proof of the wire transfer as well as provide proof of identity and an account ID, this is to ensure that we receive the funds to send you to your trading account.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection