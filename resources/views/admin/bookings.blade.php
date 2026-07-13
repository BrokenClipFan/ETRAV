<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Booking Manifests</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body {
            background-color: #f4f6f9;
            font-family: system-ui, -apple-system, sans-serif;
        }
        .manifest-card {
            transition: all 0.2s ease;
            border: 1px solid #e3e8ef;
        }
        .manifest-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.04) !important;
        }
        .timeline-dot {
            position: relative;
            padding-left: 20px;
        }
        .timeline-dot::before {
            content: '';
            position: absolute;
            left: 0;
            top: 6px;
            width: 8px;
            height: 8px;
            background-color: #0d6efd;
            border-radius: 50%;
        }
        .timeline-dot:not(:last-child)::after {
            content: '';
            position: absolute;
            left: 3px;
            top: 14px;
            width: 2px;
            height: 16px;
            background-color: #e2e8f0;
        }
    </style>
</head>
<body>

    <!-- NAVIGATION BAR -->
    <nav class="navbar navbar-expand-md navbar-light bg-white border-bottom py-3" style="height: 65px;">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold text-dark d-flex align-items-center gap-2" href="#">
                <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" style="height: 36px; width: 36px;" class="text-primary"><path d="M24 4C12.95 4 4 12.95 4 24s8.95 20 20 20 20-8.95 20-20S35.05 4 24 4zm2 32h-4v-4h4v4zm0-8h-4V12h4v16z" fill="currentColor"/></svg>
                <span class="fs-5 fw-semibold tracking-wider">Admin Dashboard</span>
            </a>
            <div class="ms-auto">
                <span class="text-muted small fw-medium me-3">Admin Mode</span>
                <a href="#" class="btn btn-sm btn-outline-secondary rounded-pill px-3">Go to User View</a>
            </div>
        </div>
    </nav>

    <!-- MAIN CONTAINER -->
    <div class="container-fluid p-4">
        
        <!-- HEADER BLOCK -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
            <div>
                <h4 class="fw-bold text-dark mb-1">🎟️ User Booking Manifests</h4>
                <p class="text-muted small mb-0">Review custom passenger configurations, pricing calculations, and itinerary allocations.</p>
            </div>
            <!-- Live Filter Tabs -->
            <div class="bg-white border rounded-3 p-1 shadow-sm d-flex gap-1 flex-wrap">
                <button class="btn btn-sm btn-primary rounded-2 px-3">All Bookings</button>
                <button class="btn btn-sm btn-light text-muted rounded-2 px-3">Pending</button>
                <button class="btn btn-sm btn-light text-muted rounded-2 px-3">Confirmed</button>
            </div>
        </div>

        <!-- BOOKING MANIFEST CARDS GRID -->
        <div class="row g-3">
            
            <!-- BOOKING CARD 1: PENDING ORDER (With added Edit Button & Modal link) -->
            <div class="col-12 col-xl-6">
                <div class="bg-white rounded-4 p-4 shadow-sm manifest-card h-100 d-flex flex-column justify-content-between">
                    <div>
                        <!-- Top Header Context -->
                        <div class="d-flex justify-content-between align-items-start mb-3 border-bottom pb-3">
                            <div>
                                <span class="text-muted font-monospace small d-block">MANIFEST #BKG-9082</span>
                                <h5 class="fw-bold text-dark mb-0">South Cebu Adventure</h5>
                            </div>
                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-3 py-1.5 fw-semibold" style="font-size: 11px;">🟡 Pending Approval</span>
                        </div>

                        <!-- Main Details Grid Split -->
                        <div class="row g-3 mb-3">
                            <!-- Lead Passenger Info -->
                            <div class="col-12 col-sm-6 border-end-sm">
                                <span class="text-muted small text-uppercase fw-bold d-block mb-1" style="font-size: 10px;">Lead Passenger</span>
                                <div class="fw-bold text-dark">Juan Dela Cruz</div>
                                <span class="text-muted small d-block"><i class="bi bi-envelope me-1"></i>juan@email.com</span>
                                <span class="text-muted small d-block"><i class="bi bi-telephone me-1"></i>+63 917 123 4567</span>
                            </div>
                            <!-- Travel Context Dates -->
                            <div class="col-12 col-sm-6">
                                <span class="text-muted small text-uppercase fw-bold d-block mb-1" style="font-size: 10px;">Schedule Target</span>
                                <div class="fw-semibold text-dark"><i class="bi bi-calendar-event me-1"></i>July 24, 2026</div>
                                <span class="text-muted small d-block"><i class="bi bi-people me-1"></i>Group Size: <b>5 Passengers</b></span>
                            </div>
                        </div>

                        <!-- Split Pricing Math Sheet -->
                        <div class="bg-light rounded-3 p-3 mb-3 border font-monospace">
                            <span class="text-muted small text-uppercase fw-bold d-block mb-2 font-sans" style="font-size: 10px; font-family: system-ui, sans-serif;">Cost Ledger Breakdown</span>
                            <div class="d-flex justify-content-between small text-secondary mb-1">
                                <span>Base Package Price:</span>
                                <span>₱1,500.00</span>
                            </div>
                            <div class="d-flex justify-content-between small text-secondary mb-2 border-bottom pb-2">
                                <span>Passenger Head Add-on (5 × ₱200):</span>
                                <span>₱1,000.00</span>
                            </div>
                            <div class="d-flex justify-content-between fw-bold text-dark fs-6">
                                <span>Total Gross Cost:</span>
                                <span class="text-primary">₱2,500.00</span>
                            </div>
                        </div>

                        <!-- Itinerary Node Summary Tracker -->
                        <div class="mb-3">
                            <span class="text-muted small text-uppercase fw-bold d-block mb-2" style="font-size: 10px;">Route Pinned Itinerary</span>
                            <div class="d-flex flex-column small text-secondary">
                                <div class="timeline-dot fw-medium text-dark">Oslob Whale Shark Watching</div>
                                <div class="timeline-dot fw-medium text-dark">Tumalog Falls Paradise</div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Control Strip (Includes Edit Settings, Reject, and Confirm) -->
                    <div class="d-flex gap-2 pt-3 border-top mt-auto">
                        <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-medium" data-bs-toggle="modal" data-bs-target="#editManifestModal">
                            <i class="bi bi-sliders me-1"></i> Edit Settings
                        </button>
                        <button type="button" class="btn btn-light btn-sm rounded-pill px-3 fw-medium text-danger border"><i class="bi bi-x-circle me-1"></i> Reject</button>
                        <button type="button" class="btn btn-primary btn-sm rounded-pill flex-fill fw-medium shadow-sm"><i class="bi bi-check2-circle me-1"></i> Confirm Booking</button>
                    </div>
                </div>
            </div>

            <!-- BOOKING CARD 2: CONFIRMED ORDER -->
            <div class="col-12 col-xl-6">
                <div class="bg-white rounded-4 p-4 shadow-sm manifest-card h-100 d-flex flex-column justify-content-between">
                    <div>
                        <!-- Top Header Context -->
                        <div class="d-flex justify-content-between align-items-start mb-3 border-bottom pb-3">
                            <div>
                                <span class="text-muted font-monospace small d-block">MANIFEST #BKG-8411</span>
                                <h5 class="fw-bold text-dark mb-0">Highland Escape Bundle</h5>
                            </div>
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1.5 fw-semibold" style="font-size: 11px;">🟢 Confirmed / Paid</span>
                        </div>

                        <!-- Main Details Grid Split -->
                        <div class="row g-3 mb-3">
                            <!-- Lead Passenger Info -->
                            <div class="col-12 col-sm-6 border-end-sm">
                                <span class="text-muted small text-uppercase fw-bold d-block mb-1" style="font-size: 10px;">Lead Passenger</span>
                                <div class="fw-bold text-dark">Maria Santos</div>
                                <span class="text-muted small d-block"><i class="bi bi-envelope me-1"></i>maria.s@email.com</span>
                                <span class="text-muted small d-block"><i class="bi bi-telephone me-1"></i>+63 918 765 4321</span>
                            </div>
                            <!-- Travel Context Dates -->
                            <div class="col-12 col-sm-6">
                                <span class="text-muted small text-uppercase fw-bold d-block mb-1" style="font-size: 10px;">Schedule Target</span>
                                <div class="fw-semibold text-dark"><i class="bi bi-calendar-event me-1"></i>July 29, 2026</div>
                                <span class="text-muted small d-block"><i class="bi bi-people me-1"></i>Group Size: <b>3 Passengers</b></span>
                            </div>
                        </div>

                        <!-- Split Pricing Math Sheet -->
                        <div class="bg-light rounded-3 p-3 mb-3 border font-monospace">
                            <span class="text-muted small text-uppercase fw-bold d-block mb-2 font-sans" style="font-size: 10px; font-family: system-ui, sans-serif;">Cost Ledger Breakdown</span>
                            <div class="d-flex justify-content-between small text-secondary mb-1">
                                <span>Base Package Price:</span>
                                <span>₱2,200.00</span>
                            </div>
                            <div class="d-flex justify-content-between small text-secondary mb-2 border-bottom pb-2">
                                <span>Passenger Head Add-on (3 × ₱350):</span>
                                <span>₱1,050.00</span>
                            </div>
                            <div class="d-flex justify-content-between fw-bold text-dark fs-6">
                                <span>Total Gross Cost:</span>
                                <span class="text-success">₱3,250.00</span>
                            </div>
                        </div>

                        <!-- Itinerary Node Summary Tracker -->
                        <div class="mb-3">
                            <span class="text-muted small text-uppercase fw-bold d-block mb-2" style="font-size: 10px;">Route Pinned Itinerary</span>
                            <div class="d-flex flex-column small text-secondary">
                                <div class="timeline-dot fw-medium text-dark">Sirao Flower Garden Oasis</div>
                                <div class="timeline-dot fw-medium text-dark">Temple of Leah Lookout</div>
                                <div class="timeline-dot fw-medium text-dark">Tops Lookout Viewpoint</div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Management System Trigger Interface -->
                    <div class="d-flex gap-2 pt-3 border-top mt-auto">
                        <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill flex-fill fw-medium"><i class="bi bi-printer me-1"></i> Print Manifest</button>
                        <button type="button" class="btn btn-success btn-sm rounded-pill flex-fill fw-medium text-white shadow-sm"><i class="bi bi-patch-check me-1"></i> Mark as Completed</button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- EDIT BOOKING PARAMETERS MODAL -->
    <div class="modal fade" id="editManifestModal" tabindex="-1" aria-labelledby="editManifestModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header bg-light border-bottom px-4 py-3">
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="editManifestModalLabel">🛠️ Adjust Manifest Settings</h5>
                        <small class="text-muted">Modify live variables to fit custom user alignment requests.</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="#" method="POST">
                    <div class="modal-body p-4">
                        
                        <!-- Passenger Amount Custom Control -->
                        <div class="mb-4">
                            <label class="form-label text-dark fw-bold small mb-1">Adjust Passenger Count</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-people"></i></span>
                                <input type="number" name="edit_passengers" class="form-control rounded-end-3" value="5" min="1" required>
                            </div>
                            <div class="form-text text-muted small">Changing passenger value automatically recalibrates total head calculations.</div>
                        </div>

                        <!-- Itinerary Spot Panel List -->
                        <div>
                            <label class="form-label text-dark fw-bold small mb-2">Manage Selected Itinerary Spots</label>
                            <div class="d-flex flex-column gap-2">
                                
                                <!-- Spot 1 -->
                                <div class="d-flex justify-content-between align-items-center p-2.5 bg-light rounded-3 border">
                                    <div class="d-flex align-items-center gap-2 text-truncate">
                                        <i class="bi bi-geo-alt-fill text-danger small"></i>
                                        <span class="small fw-semibold text-dark text-truncate">Oslob Whale Shark Watching</span>
                                    </div>
                                    <button type="button" class="btn btn-link p-0 text-danger text-decoration-none small fw-medium" onclick="this.parentElement.remove()">
                                        <i class="bi bi-dash-circle me-1"></i>Remove
                                    </button>
                                </div>

                                <!-- Spot 2 -->
                                <div class="d-flex justify-content-between align-items-center p-2.5 bg-light rounded-3 border">
                                    <div class="d-flex align-items-center gap-2 text-truncate">
                                        <i class="bi bi-geo-alt-fill text-danger small"></i>
                                        <span class="small fw-semibold text-dark text-truncate">Tumalog Falls Paradise</span>
                                    </div>
                                    <button type="button" class="btn btn-link p-0 text-danger text-decoration-none small fw-medium" onclick="this.parentElement.remove()">
                                        <i class="bi bi-dash-circle me-1"></i>Remove
                                    </button>
                                </div>

                            </div>
                        </div>

                    </div>
                    <div class="modal-footer bg-light border-top px-4 py-3">
                        <button type="button" class="btn btn-light btn-sm rounded-pill px-3 fw-semibold text-muted" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 fw-semibold shadow-sm">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap Core Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>