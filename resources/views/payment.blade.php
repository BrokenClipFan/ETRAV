<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Method - ETRAV</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', system-ui, sans-serif; }
        .payment-card { border: 2px solid transparent; cursor: pointer; transition: 0.2s; border-radius: 12px; }
        .payment-card:hover { border-color: #0d6efd; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(13, 110, 253, 0.15); }
        .payment-card.active { border-color: #0d6efd; background-color: #f0f7ff; }
        .navbar-brand img { height: 38px; object-fit: contain; }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-md navbar-light bg-white border-bottom sticky-top py-2 shadow-sm">
        <div class="container px-4">
            <a class="navbar-brand fw-bold text-dark d-flex align-items-center gap-2" href="{{ route('home') }}">
                <img src="{{ asset('storage/logotext.png') }}" alt="ETRAV Logo">
            </a>
            <div class="ms-auto d-flex align-items-center">
                <span class="text-muted fw-medium me-3"><i class="bi bi-person-circle me-1"></i> {{ $user->name }}</span>
                <a href="{{ route('bookings.view') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">Cancel</a>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center mb-4">
                    <h2 class="fw-bold text-dark mb-1">Complete Your Booking</h2>
                    <p class="text-muted">Pay the 25% deposit to confirm your reservation.</p>
                </div>

                <div class="row g-4">
                    <!-- Left: Payment Form -->
                    <div class="col-md-7">
                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="card-body p-4">
                                <h5 class="fw-bold mb-4"><i class="bi bi-credit-card-2-front text-primary me-2"></i>Select Payment Method</h5>
                                
                                <form action="{{ url('booking/'.$booking->id.'/pay') }}" method="POST" enctype="multipart/form-data" id="paymentForm">
                                    @csrf
                                    <!-- Payment Options -->
                                    <div class="row g-3 mb-4">
                                        <div class="col-6">
                                            <div class="card payment-card active" onclick="selectMethod('gcash', this)">
                                                <div class="card-body text-center p-3">
                                                    <h5 class="mb-1 text-primary fw-bold">GCash</h5>
                                                    <small class="text-muted">E-Wallet</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="card payment-card" onclick="selectMethod('maya', this)">
                                                <div class="card-body text-center p-3">
                                                    <h5 class="mb-1 text-success fw-bold">Maya</h5>
                                                    <small class="text-muted">E-Wallet</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="card payment-card" onclick="selectMethod('bank', this)">
                                                <div class="card-body text-center p-3">
                                                    <h5 class="mb-1 text-secondary fw-bold">Bank Transfer</h5>
                                                    <small class="text-muted">BDO / BPI / UnionBank</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <input type="hidden" name="payment_method" id="paymentMethod" value="gcash">

                                    <!-- Instructions Box -->
                                    <div class="bg-light p-3 rounded-3 mb-4 border text-center" id="paymentInstructions">
                                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=ETRAV-GCash-Payment" alt="QR Code" class="img-fluid rounded mb-3 shadow-sm border" style="max-width: 130px;">
                                        <div class="text-start">
                                            <p class="mb-1 small fw-bold">GCash Account Details:</p>
                                            <p class="mb-1 small">Account Name: <strong>ETRAV Tours</strong></p>
                                            <p class="mb-0 small">Account Number: <strong class="text-primary fs-6">0912 345 6789</strong></p>
                                        </div>
                                    </div>

                                    <!-- Reference Number -->
                                    <div class="mb-3">
                                        <label class="form-label fw-medium small text-muted">Reference Number</label>
                                        <input type="text" name="reference_number" class="form-control" placeholder="e.g. 100012345678" required>
                                    </div>

                                    <!-- Proof of Payment -->
                                    <div class="mb-4">
                                        <label class="form-label fw-medium small text-muted">Upload Proof of Payment (Optional)</label>
                                        <input type="file" name="proof_of_payment" class="form-control form-control-sm" accept="image/*">
                                        <div class="form-text" style="font-size: 11px;">Max file size: 2MB. JPG, PNG formats only.</div>
                                    </div>

                                    <button type="submit" class="btn btn-success w-100 rounded-pill py-3 fw-bold fs-5 shadow-sm" id="submitBtn">
                                        Confirm Payment
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Summary -->
                    <div class="col-md-5">
                        <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: 80px;">
                            <div class="card-body p-4">
                                <h5 class="fw-bold mb-4"><i class="bi bi-receipt text-success me-2"></i>Summary</h5>
                                
                                <div class="d-flex justify-content-between mb-3 border-bottom pb-3">
                                    <span class="text-muted">Total Package Price</span>
                                    <span class="fw-bold">₱{{ number_format($booking->quoted_price ?? $booking->total_price, 2) }}</span>
                                </div>
                                
                                <div class="bg-warning-subtle p-3 rounded-3 border border-warning">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-warning-emphasis fw-bold small">Required Deposit (25%)</span>
                                        <span class="fs-4 fw-bold text-dark">₱{{ number_format($booking->deposit_amount, 2) }}</span>
                                    </div>
                                    <p class="small text-muted mb-0 lh-sm" style="font-size: 12px;">This deposit guarantees your reservation. The remaining balance will be collected on the day of the tour.</p>
                                </div>

                                <div class="mt-4 pt-3 border-top">
                                    <p class="small text-muted mb-1"><i class="bi bi-geo-alt-fill text-danger me-1"></i> Pickup: {{ $booking->pickup_place_name }}</p>
                                    <p class="small text-muted mb-0"><i class="bi bi-calendar-event-fill text-primary me-1"></i> Date: {{ \Carbon\Carbon::parse($booking->pickup_datetime)->format('M d, Y h:i A') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const instructions = {
            'gcash': `<img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=ETRAV-GCash-Payment" alt="QR Code" class="img-fluid rounded mb-3 shadow-sm border" style="max-width: 130px;"><div class="text-start"><p class="mb-1 small fw-bold">GCash Account Details:</p><p class="mb-1 small">Account Name: <strong>ETRAV Tours</strong></p><p class="mb-0 small">Account Number: <strong class="text-primary fs-6">0912 345 6789</strong></p></div>`,
            'maya': `<img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=ETRAV-Maya-Payment" alt="QR Code" class="img-fluid rounded mb-3 shadow-sm border" style="max-width: 130px;"><div class="text-start"><p class="mb-1 small fw-bold">Maya Account Details:</p><p class="mb-1 small">Account Name: <strong>ETRAV Tours</strong></p><p class="mb-0 small">Account Number: <strong class="text-success fs-6">0998 765 4321</strong></p></div>`,
            'bank': `<img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=ETRAV-Bank-Payment" alt="QR Code" class="img-fluid rounded mb-3 shadow-sm border" style="max-width: 130px;"><div class="text-start"><p class="mb-1 small fw-bold">BDO Bank Transfer:</p><p class="mb-1 small">Account Name: <strong>ETRAV Corp</strong></p><p class="mb-0 small">Account Number: <strong class="text-secondary fs-6">001234567890</strong></p></div>`
        };

        function selectMethod(method, element) {
            document.querySelectorAll('.payment-card').forEach(card => card.classList.remove('active'));
            element.classList.add('active');
            document.getElementById('paymentMethod').value = method;
            document.getElementById('paymentInstructions').innerHTML = instructions[method];
        }

        document.getElementById('paymentForm').addEventListener('submit', function(e) {
            const btn = document.getElementById('submitBtn');
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
            btn.disabled = true;
        });
    </script>
</body>
</html>
