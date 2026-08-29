<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Booking Manifests</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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

        /* DYNAMIC POS RECEIPT PRINT STYLING */
        @media print {
            /* Configure the page size targeting standard 80mm roll printer widths */
            @page {
                size: 80mm auto;
                margin: 0mm;
            }
            
            body * {
                visibility: hidden;
                background: #fff !important;
                color: #000 !important;
            }
            
            #printTargetSection, #printTargetSection * {
                visibility: visible;
            }
            
            #printTargetSection {
                position: absolute;
                left: 0;
                top: 0;
                width: 74mm; /* Adjusted viewport to fit within 80mm boundaries */
                padding: 3mm;
                font-family: 'Courier New', Courier, monospace !important; /* Authentic ticket layout font */
                font-size: 12px !important;
                line-height: 1.4;
            }
            
            /* Remove action rows and UI controls completely */
            .no-print, .modal, .btn, .border-top, form {
                display: none !important;
            }
            
            /* Restructure the layout card to appear clean and flat on tape */
            .manifest-card {
                border: none !important;
                padding: 0 !important;
                margin: 0 !important;
                box-shadow: none !important;
                background: transparent !important;
            }
            
            /* Flatten horizontal split layouts down to stack cleanly on vertical receipt strip */
            .row {
                display: block !important;
                margin: 0 !important;
            }
            .col-12, .col-sm-6 {
                width: 100% !important;
                padding: 0 !important;
                margin-bottom: 8px !important;
                border: none !important;
            }
            
            /* Receipt divider styling */
            .border-bottom {
                border-bottom: 1px dashed #000 !important;
                padding-bottom: 6px !important;
                margin-bottom: 8px !important;
            }
            
            .bg-light {
                background: transparent !important;
                border: 1px dashed #000 !important;
                padding: 6px !important;
            }

            .badge {
                border: 1px solid #000 !important;
                color: #000 !important;
                background: transparent !important;
                padding: 2px 4px !important;
                font-size: 10px !important;
            }
        }
    </style>
</head>
<body>
    <div class="no-print">
        @include('admin.layouts.nav')
    </div>

    <div class="container-fluid p-4">
        
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3 no-print">
            <div>
                <h4 class="fw-bold text-dark mb-1">🎟️ User Booking Manifests</h4>
                <p class="text-muted small mb-0">Review custom passenger configurations, pricing calculations, and itinerary allocations.</p>
            </div>
            <div class="bg-white border rounded-3 p-1 shadow-sm d-flex gap-1 flex-wrap">
                <button class="btn btn-sm btn-primary rounded-2 px-3 filter-btn" data-filter="all">All Bookings</button>
                <button class="btn btn-sm btn-light text-muted rounded-2 px-3 filter-btn" data-filter="pending">Pending</button>
                <button class="btn btn-sm btn-light text-muted rounded-2 px-3 filter-btn" data-filter="confirmed">Confirmed</button>
                <button class="btn btn-sm btn-light text-muted rounded-2 px-3 filter-btn" data-filter="completed">Completed</button>
            </div>
        </div>

        <div class="row g-3" id="manifestGrid">
            @forelse($bookings as $booking)
                <div class="col-12 col-xl-6 booking-card-item" data-status="{{ $booking->status }}" id="booking-card-{{ $booking->id }}">
                    <div class="bg-white rounded-4 p-4 shadow-sm manifest-card h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex justify-content-between align-items-start mb-3 border-bottom pb-3">
                                <div>
                                    <span class="text-muted font-monospace small d-block">MANIFEST #BKG-{{ $booking->id }}</span>
                                    <h5 class="fw-bold text-dark mb-0">{{ $booking->package->name ?? 'Custom Package Bundle' }}</h5>
                                </div>
                                
                                @if($booking->status === 'pending')
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-3 py-1.5 fw-semibold" style="font-size: 11px;">
                                        🟡 Pending Approval
                                    </span>
                                @elseif($booking->status === 'confirmed')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1.5 fw-semibold" style="font-size: 11px;">
                                        🟢 Confirmed / Paid
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-3 py-1.5 fw-semibold" style="font-size: 11px;">
                                        ⚫ {{ ucfirst($booking->status) }}
                                    </span>
                                @endif
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-12 col-sm-6 border-end">
                                    <span class="text-muted small text-uppercase fw-bold d-block mb-1" style="font-size: 10px;">Lead Passenger</span>
                                    <div class="fw-bold text-dark">{{ $booking->user->name ?? 'Unknown Client' }}</div>
                                    <span class="text-muted small d-block"><i class="bi bi-envelope me-1"></i>{{ $booking->user->email ?? 'N/A' }}</span>
                                    <span class="text-muted small d-block"><i class="bi bi-telephone me-1"></i>{{ $booking->user->phone ?? 'No Phone' }}</span>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <span class="text-muted small text-uppercase fw-bold d-block mb-1" style="font-size: 10px;">Schedule Target</span>
                                    <div class="fw-semibold text-dark">
                                        <i class="bi bi-calendar-event me-1"></i>{{ date('F d, Y', strtotime($booking->pickup_datetime)) }}
                                    </div>
                                    <span class="text-muted small d-block">
                                        <i class="bi bi-people me-1"></i>Group Size: <b>{{ $booking->pax }} Passengers</b>
                                    </span>
                                </div>
                            </div>

                            <div class="bg-light rounded-3 p-3 mb-3 border font-monospace">
                                <span class="text-muted small text-uppercase fw-bold d-block mb-2 text-dark" style="font-size: 10px; font-family: system-ui, sans-serif;">Cost Ledger Breakdown</span>
                                <div class="d-flex justify-content-between small text-secondary mb-1">
                                    <span>Base Package Price:</span>
                                    <span>₱{{ number_format($booking->package->package_price ?? 0, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between small text-secondary mb-2 border-bottom pb-2">
                                    <span>Passenger Add-on:</span>
                                    <span>₱{{ number_format(($booking->package->perhead_price ?? 0) * $booking->pax, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between fw-bold text-dark fs-6">
                                    <span>Total Gross Cost:</span>
                                    <span class="{{ $booking->status === 'pending' ? 'text-primary' : 'text-success' }}">
                                        ₱{{ number_format($booking->total_price, 2) }}
                                    </span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <span class="text-muted small text-uppercase fw-bold d-block mb-2" style="font-size: 10px;">Route Pinned Itinerary</span>
                                <div class="d-flex flex-column small text-secondary">
                                    @forelse($booking->places as $place)
                                        <div class="timeline-dot fw-medium text-dark">${{ $place->name }}</div>
                                    @empty
                                        <div class="text-muted small italic">No locations bound to this trip template.</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2 pt-3 border-top mt-auto no-print">
                            @if($booking->status === 'pending')
                                <button type="button" 
                                        class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-medium edit-manifest-btn" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editManifestModal" 
                                        data-id="{{ $booking->id }}"
                                        data-pax="{{ $booking->pax }}"
                                        data-places="{{ json_encode($booking->places) }}">
                                    <i class="bi bi-sliders me-1"></i> Edit Settings
                                </button>
                                <button type="button" class="btn btn-light btn-sm rounded-pill px-3 fw-medium text-danger border"><i class="bi bi-x-circle me-1"></i> Reject</button>
                                <form action="{{ route('admin.booking.update', $booking->id) }}" method="POST" class="flex-fill m-0 d-grid">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-sm rounded-pill fw-medium shadow-sm w-100"><i class="bi bi-check2-circle me-1"></i> Confirm Booking</button>
                                </form>
                            @else
                                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill flex-fill fw-medium" onclick="printReceiptManifest('{{ $booking->id }}')">
                                    <i class="bi bi-printer me-1"></i> Print Receipt
                                </button>
                                
                                @if($booking->status !== 'completed')
                                    <form action="{{ route('admin.booking.update.complete', $booking->id) }}" method="POST" class="flex-fill m-0 d-grid complete-action-form">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm rounded-pill fw-medium text-white shadow-sm w-100"><i class="bi bi-patch-check me-1"></i> Mark as Completed</button>
                                    </form>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5 bg-white rounded-4 border" id="noBookingsAlert">
                    <i class="bi bi-folder-x fs-1 text-muted opacity-50 d-block mb-2"></i>
                    <h6 class="text-muted">No processing workflows match your collection parameters right now.</h6>
                </div>
            @endforelse
            
            <div class="col-12 text-center py-5 bg-white rounded-4 border d-none no-print" id="jsEmptyAlert">
                <i class="bi bi-folder-x fs-1 text-muted opacity-50 d-block mb-2"></i>
                <h6 class="text-muted">No entries match this status filter.</h6>
            </div>
        </div>
    </div>

    <div id="printTargetSection"></div>

    <div class="modal fade no-print" id="editManifestModal" tabindex="-1" aria-labelledby="editManifestModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header bg-light border-bottom px-4 py-3">
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="editManifestModalLabel">🛠️ Adjust Manifest Settings</h5>
                        <small class="text-muted">Modify live variables to fit custom user alignment requests.</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editManifestForm" action="#" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4">
                        
                        <div class="mb-4">
                            <label class="form-label text-dark fw-bold small mb-1">Adjust Passenger Count</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-people"></i></span>
                                <input type="number" id="modalPaxInput" name="edit_passengers" class="form-control rounded-end-3" value="1" min="1" required>
                            </div>
                        </div>

                        <div>
                            <label class="form-label text-dark fw-bold small mb-2">Manage Selected Itinerary Spots</label>
                            <div class="d-flex flex-column gap-2" id="modalPlacesContainer"></div>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @include('layouts.notification')

    <script>
        // --- 3. PRINT RECEIPT MANIFEST DISPATCH FUNCTION ---
        function printReceiptManifest(id) {
            const cardElement = document.getElementById(`booking-card-${id}`);
            if (!cardElement) return;

            const printSandbox = document.getElementById('printTargetSection');
            
            // Clone content into the receipt printing track
            printSandbox.innerHTML = `
                <div style="text-align: center; margin-bottom: 12px;">
                    <h4 style="margin: 0; font-weight: bold;">ETRAV TOURS INC</h4>
                    <small>Official Booking Receipt</small>
                    <div style="border-bottom: 1px dashed #000; margin-top: 8px;"></div>
                </div>
                ${cardElement.innerHTML}
                <div style="text-align: center; margin-top: 15px; font-size: 10px;">
                    <div style="border-top: 1px dashed #000; margin-bottom: 6px;"></div>
                    Thank you for choosing ETRAV!<br>
                    Please present this coupon upon boarding.
                </div>
            `;

            // Trigger system output print view
            window.print();

            // Reset sandbox clean after execution
            printSandbox.innerHTML = '';
        }

        document.addEventListener('DOMContentLoaded', function () {
            // --- 1. FILTER TABS LOGIC ---
            const filterButtons = document.querySelectorAll('.filter-btn');
            const cardItems = document.querySelectorAll('.booking-card-item');
            const jsEmptyAlert = document.getElementById('jsEmptyAlert');
            const baseAlert = document.getElementById('noBookingsAlert');

            filterButtons.forEach(btn => {
                btn.addEventListener('click', function () {
                    filterButtons.forEach(b => {
                        b.classList.remove('btn-primary');
                        b.classList.add('btn-light', 'text-muted');
                    });
                    this.classList.remove('btn-light', 'text-muted');
                    this.classList.add('btn-primary');

                    const targetStatus = this.getAttribute('data-filter');
                    let visibleCount = 0;

                    cardItems.forEach(card => {
                        const cardStatus = card.getAttribute('data-status');
                        if (targetStatus === 'all' || cardStatus === targetStatus) {
                            card.classList.remove('d-none');
                            visibleCount++;
                        } else {
                            card.classList.add('d-none');
                        }
                    });

                    if (baseAlert) baseAlert.classList.add('d-none');
                    
                    if (visibleCount === 0) {
                        jsEmptyAlert.classList.remove('d-none');
                    } else {
                        jsEmptyAlert.classList.add('d-none');
                    }
                });
            });

            // --- 2. MODAL DYNAMIC DATA HYDRATION ---
            const editButtons = document.querySelectorAll('.edit-manifest-btn');
            const editForm = document.getElementById('editManifestForm');
            const modalPaxInput = document.getElementById('modalPaxInput');
            const modalPlacesContainer = document.getElementById('modalPlacesContainer');

            editButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const bookingId = this.getAttribute('data-id');
                    const paxValue = this.getAttribute('data-pax');
                    const placesData = JSON.parse(this.getAttribute('data-places') || '[]');

                    editForm.action = `/admin/bookings/${bookingId}/update-settings`;
                    modalPaxInput.value = paxValue;
                    modalPlacesContainer.innerHTML = '';
                    
                    if (placesData.length === 0) {
                        modalPlacesContainer.innerHTML = '<div class="text-center py-2 text-muted small bg-light rounded border border-dashed">No routes assigned.</div>';
                    } else {
                        placesData.forEach(place => {
                            const wrapper = document.createElement('div');
                            wrapper.className = "d-flex justify-content-between align-items-center p-2.5 bg-light rounded-3 border";
                            wrapper.innerHTML = `
                                <div class="d-flex align-items-center gap-2 text-truncate">
                                    <i class="bi bi-geo-alt-fill text-danger small"></i>
                                    <span class="small fw-semibold text-dark text-truncate">${place.name}</span>
                                    <input type="hidden" name="itinerary_places[]" value="${place.id}">
                                </div>
                                <button type="button" class="btn btn-link p-0 text-danger text-decoration-none small fw-medium" onclick="this.parentElement.remove()">
                                    <i class="bi bi-dash-circle me-1"></i>Remove
                                </button>
                            `;
                            modalPlacesContainer.appendChild(wrapper);
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>