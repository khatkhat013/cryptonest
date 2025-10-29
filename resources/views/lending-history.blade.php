@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Header Card -->
    <div class="card bg-primary text-white border-0 mb-4" style="border-radius: 20px;">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <a href="{{ url('/lending') }}" class="text-white">
                    <i class="bi bi-arrow-left fs-4"></i>
                </a>
                <h3 class="mb-0 text-white">Lending History</h3>
                <div style="width: 24px;"></div> <!-- Spacer for centering -->
            </div>
        </div>
    </div>

    <!-- History List -->
    <div class="card shadow-sm" style="border-radius: 15px;">
        <div class="card-body p-4">
            <div class="mb-4 d-flex justify-content-center">
                <div class="toggle-wrap p-1 d-inline-flex" style="border-radius:999px;">
                    <button id="filterPending" class="toggle-btn active">Pending</button>
                    <button id="filterComplete" class="toggle-btn">Complete</button>
                </div>
            </div>

            <div id="historyList">
                <div class="text-center text-muted py-5">
                    <i class="bi bi-clock-history fs-1 mb-3 d-block"></i>
                    <p>Loading...</p>
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const pendingBtn = document.getElementById('filterPending');
                    const completeBtn = document.getElementById('filterComplete');
                    const list = document.getElementById('historyList');

                    function setActive(button) {
                        pendingBtn.classList.remove('active');
                        completeBtn.classList.remove('active');
                        button.classList.add('active');
                        // update aria-pressed for accessibility
                        pendingBtn.setAttribute('aria-pressed', pendingBtn.classList.contains('active'));
                        completeBtn.setAttribute('aria-pressed', completeBtn.classList.contains('active'));
                    }

                    function renderItems(items) {
                        if (!items.length) {
                            list.innerHTML = '<div class="text-center text-muted py-5"><i class="bi bi-clock-history fs-1 mb-3 d-block"></i><p>No lending history yet</p></div>';
                            return;
                        }

                        const html = items.map(item => {
                            const created = new Date(item.created_at).toLocaleString();
                            const status = item.status === 'approved' ? 'Complete' : item.status.charAt(0).toUpperCase() + item.status.slice(1);
                            return `
                                <div class="mb-3 p-3 border rounded-3 bg-white bg-opacity-5">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <div class="small text-muted">Amount</div>
                                            <div class="fw-medium">${item.borrowing_amount} USDT</div>
                                        </div>
                                        <div class="text-end">
                                            <div class="small text-muted">Period</div>
                                            <div class="fw-medium">${item.credit_period} days</div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between mt-2 small text-muted">
                                        <div>${created}</div>
                                        <div>${status}</div>
                                    </div>
                                </div>
                            `;
                        }).join('');

                        list.innerHTML = html;
                    }

                    function fetchList(status) {
                        list.innerHTML = '<div class="text-center text-muted py-5"><i class="bi bi-clock-history fs-1 mb-3 d-block"></i><p>Loading...</p></div>';
                        fetch(`/lending/list?status=${status}`, { headers: {'X-Requested-With': 'XMLHttpRequest'} })
                            .then(r => r.json())
                            .then(json => {
                                if (json.success) {
                                    renderItems(json.data);
                                } else {
                                    list.innerHTML = '<div class="text-danger">Failed to load</div>';
                                }
                            })
                            .catch(err => {
                                console.error(err);
                                list.innerHTML = '<div class="text-danger">Failed to load</div>';
                            });
                    }

                    pendingBtn.addEventListener('click', function() { setActive(this); fetchList('pending'); });
                    completeBtn.addEventListener('click', function() { setActive(this); fetchList('complete'); });

                    // Initial load
                    fetchList('pending');
                });
            </script>
            <style>
                .toggle-wrap{
                    background: #0d6efd; /* blue background */
                    border-radius: 999px;
                    padding: 4px;
                }
                .toggle-btn{
                    border: none;
                    background: transparent;
                    color: #cfe3ff;
                    padding: 8px 18px;
                    border-radius: 999px;
                    font-weight: 500;
                }
                .toggle-btn.active{
                    background: #0b1220; /* dark pill */
                    color: #fff;
                    box-shadow: 0 4px 8px rgba(11,18,32,0.12);
                }
            </style>
        </div>
    </div>
</div>
@endsection