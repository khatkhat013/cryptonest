@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Header Card -->
    <div class="card bg-primary text-white border-0 mb-4" style="border-radius: 20px;">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <a href="{{ url('/') }}" class="text-white">
                    <i class="bi bi-arrow-left fs-4"></i>
                </a>
                <h3 class="mb-0 text-white">Assisted Lending</h3>
                <a href="{{ url('/lending-history') }}" class="text-white">
                    <i class="bi bi-clock-history fs-4"></i>
                </a>
            </div>
            <div class="text-center">
                <div class="row g-4 justify-content-center">
                    <div class="col-md-6">
                        <div class="bg-white bg-opacity-10 rounded-4 p-4">
                            <h5 class="mb-2">Accumulated Currency (USDT)</h5>
                            <h3 class="mb-0">0.00USDT</h3>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="bg-white bg-opacity-10 rounded-4 p-4">
                            <h5 class="mb-2">Outstanding Amount</h5>
                            <h3 class="mb-0">≈ 0.00</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lending Form Card -->
    <div class="card shadow-sm mt-4" style="border-radius: 15px;">
        <div class="card-body p-4">
            <form id="lendingForm">
                <div class="row mb-3">
                    <div class="col-6">
                        <label class="text-muted">Borrowing</label>
                        <div class="input-group">
                            <input type="text" name="borrowing_amount" class="form-control" placeholder="Please enter the amount" required>
                            <span class="input-group-text bg-light">USDT</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <label class="text-muted">Credit Period</label>
                        <select class="form-select" name="credit_period" required>
                            <option value="10" selected>10 days</option>
                            <option value="12">12 days</option>
                            <option value="15">15 days</option>
                            <option value="20">20 days</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Loan Information Card -->
    <div class="card shadow-sm mt-4" style="border-radius: 15px;">
        <div class="card-body p-4">
            <h5 class="mb-4">Loan Information</h5>
            <div class="row g-4">
                <div class="col-12">
                    <div class="d-flex align-items-center border-bottom pb-3 mb-3">
                        <div style="width: 180px;">
                            <span class="text-muted">Rate</span>
                        </div>
                        <div>
                            <span class="fw-medium">Daily</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center border-bottom pb-3 mb-3">
                        <div style="width: 180px;">
                            <span class="text-muted">Exchange Rate</span>
                        </div>
                        <div>
                            <span class="fw-medium">0.80%</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center border-bottom pb-3 mb-3">
                        <div style="width: 180px;">
                            <span class="text-muted">Interest</span>
                        </div>
                        <div>
                            <span class="fw-medium">0</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center border-bottom pb-3 mb-3">
                        <div style="width: 180px;">
                            <span class="text-muted">Reimbursement Method</span>
                        </div>
                        <div>
                            <span class="fw-medium">Repayment of principal and interest at maturity</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div style="width: 180px;">
                            <span class="text-muted">Lender</span>
                        </div>
                        <div>
                            <span class="fw-medium">Morgan Stanley</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Document Upload Card -->
    <div class="card shadow-sm mt-4" style="border-radius: 15px;">
        <div class="card-body p-4">
            <h5 class="mb-4">Credit Loan (image upload)</h5>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="bg-light rounded-3 p-3">
                        <label class="form-label mb-2">Housing Information</label>
                        <!-- Preview Image -->
                        <div class="preview-container mb-3 d-none">
                            <img id="housing_preview" class="img-preview" alt="Housing Information Preview">
                        </div>
                        <!-- Upload Box -->
                        <div class="bg-light rounded-3 p-3 position-relative">
                            <input type="file" name="housing_info" class="form-control form-control-lg opacity-0" style="height: 80px;" 
                                form="lendingForm" required accept="image/*" onchange="previewImage(this, 'housing_preview')">
                            <div class="position-absolute top-50 start-50 translate-middle text-center">
                                <i class="bi bi-camera-fill text-primary fs-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="bg-light rounded-3 p-3">
                        <label class="form-label mb-2">Proof of Income (Employment)</label>
                        <!-- Preview Image -->
                        <div class="preview-container mb-3 d-none">
                            <img id="income_preview" class="img-preview" alt="Income Proof Preview">
                        </div>
                        <!-- Upload Box -->
                        <div class="bg-light rounded-3 p-3 position-relative">
                            <input type="file" name="income_proof" class="form-control form-control-lg opacity-0" style="height: 80px;" 
                                form="lendingForm" required accept="image/*" onchange="previewImage(this, 'income_preview')">
                            <div class="position-absolute top-50 start-50 translate-middle text-center">
                                <i class="bi bi-camera-fill text-primary fs-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="bg-light rounded-3 p-3">
                        <label class="form-label mb-2">Bank Details</label>
                        <!-- Preview Image -->
                        <div class="preview-container mb-3 d-none">
                            <img id="bank_preview" class="img-preview" alt="Bank Details Preview">
                        </div>
                        <!-- Upload Box -->
                        <div class="bg-light rounded-3 p-3 position-relative">
                            <input type="file" name="bank_details" class="form-control form-control-lg opacity-0" style="height: 80px;" 
                                form="lendingForm" required accept="image/*" onchange="previewImage(this, 'bank_preview')">
                            <div class="position-absolute top-50 start-50 translate-middle text-center">
                                <i class="bi bi-camera-fill text-primary fs-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="bg-light rounded-3 p-3">
                        <label class="form-label mb-2">Proof of Identity</label>
                        <!-- Preview Image -->
                        <div class="preview-container mb-3 d-none">
                            <img id="identity_preview" class="img-preview" alt="Identity Proof Preview">
                        </div>
                        <!-- Upload Box -->
                        <div class="bg-light rounded-3 p-3 position-relative">
                            <input type="file" name="identity_proof" class="form-control form-control-lg opacity-0" style="height: 80px;" 
                                form="lendingForm" required accept="image/*" onchange="previewImage(this, 'identity_preview')">
                            <div class="position-absolute top-50 start-50 translate-middle text-center">
                                <i class="bi bi-camera-fill text-primary fs-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                
                    </div>
                </div>
            </div>

    <!-- Agreement Section -->
    <div class="card shadow-sm mt-4" style="border-radius: 15px;">
        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-12">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="form-check m-0">
                            <input type="checkbox" class="form-check-input" id="agreementCheck">
                        </div>
                        <label for="agreementCheck" class="form-check-label">
                            I have read and agree 
                            <a href="#" class="text-primary text-decoration-none">Token Application Agreement</a>
                        </label>
                    </div>
                    <p class="text-muted small mb-4">Please be sure to pay on time, if there is a malicious expectation will be frozen trading account.</p>
                    
                    <button type="submit" form="lendingForm" class="btn btn-primary w-100 py-3" style="border-radius: 12px;" onclick="submitLendingForm(event)">
                        Apply Now
                    </button>

                    <script>
                    function submitLendingForm(event) {
                        event.preventDefault();
                        
                        // Check if agreement is checked
                        if (!document.getElementById('agreementCheck').checked) {
                            alert('Please accept the Token Application Agreement');
                            return;
                        }

                        // Get form data
                        const form = document.getElementById('lendingForm');
                        const formData = new FormData(form);
                        
                        // Add file input data and ensure they're selected
                        const requiredFiles = ['housing_info', 'income_proof', 'bank_details', 'identity_proof'];
                        for (const fileType of requiredFiles) {
                            const fileInput = document.querySelector(`input[name="${fileType}"]`);
                            if (!fileInput.files.length) {
                                alert(`Please select a file for ${fileInput.previousElementSibling.textContent}`);
                                return;
                            }
                            formData.append(fileType, fileInput.files[0]);
                        }

                        // Add CSRF token
                        formData.append('_token', '{{ csrf_token() }}');

                        // Show loading state
                        const submitButton = event.target;
                        const originalText = submitButton.textContent;
                        submitButton.disabled = true;
                        submitButton.textContent = 'Submitting...';

                        // Submit the form
                        fetch('/lending/submit', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Application submitted successfully!');
                                // reset form and hide previews
                                form.reset();
                                ['housing_preview','income_preview','bank_preview','identity_preview'].forEach(id => {
                                    const img = document.getElementById(id);
                                    if (img) {
                                        img.src = '';
                                        img.parentElement.classList.add('d-none');
                                    }
                                });
                                submitButton.disabled = false;
                                submitButton.textContent = originalText;
                                window.location.href = '/lending-history';
                            } else {
                                alert(data.message || 'Submission failed. Please try again.');
                                submitButton.disabled = false;
                                submitButton.textContent = originalText;
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('An error occurred. Please try again.');
                            submitButton.disabled = false;
                            submitButton.textContent = originalText;
                        });
                    }
                    </script>
                </div>
            </div>
        </div>
    </div>

    <style>
        .preview-container {
            width: 100%;
            height: 150px;
            border-radius: 10px;
            overflow: hidden;
            background-color: #f8f9fa;
        }

        .img-preview {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
    </style>

    <script>
        function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);
            const previewContainer = preview.parentElement;
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    previewContainer.classList.remove('d-none');
                };
                
                reader.readAsDataURL(input.files[0]);
            } else {
                previewContainer.classList.add('d-none');
            }
        }
    </script>
</div>
@endsection