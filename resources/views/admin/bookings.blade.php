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
                <button class="btn btn-sm btn-primary rounded-2 px-3 filter-btn position-relative" data-filter="pending">
                    Pending
                    @if($bookings->whereIn('status', ['pending', 'pending_price'])->count() > 0)
                        <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"><span class="visually-hidden">New alerts</span></span>
                    @endif
                </button>
                <button class="btn btn-sm btn-light text-muted rounded-2 px-3 filter-btn position-relative" data-filter="approved">
                    Approved
                    @if($bookings->whereIn('status', ['approved', 'pending_downpayment'])->where('admin_notify', true)->count() > 0)
                        <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"><span class="visually-hidden">New alerts</span></span>
                    @endif
                </button>
                <button class="btn btn-sm btn-light text-muted rounded-2 px-3 filter-btn position-relative" data-filter="confirmed">
                    Paid
                    @if($bookings->where('status', 'confirmed')->where('admin_notify', true)->count() > 0)
                        <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"><span class="visually-hidden">New alerts</span></span>
                    @endif
                </button>
                <button class="btn btn-sm btn-light text-muted rounded-2 px-3 filter-btn" data-filter="completed">Completed</button>
                <button class="btn btn-sm btn-light text-muted rounded-2 px-3 filter-btn" data-filter="denied">Denied</button>
                <button class="btn btn-sm btn-light text-muted rounded-2 px-3 filter-btn" data-filter="all">All Bookings</button>
            </div>
        </div>

        
        @php
            $customBookings = $bookings->where('is_custom', true);
            $normalBookings = $bookings->where('is_custom', false);
        @endphp

        <h5 class="fw-bold mb-3 mt-4 text-warning-emphasis"><i class="bi bi-tools me-2"></i> Custom Route Bookings</h5>
        <div class="table-responsive custom-table-wrapper" style="max-height: 400px; overflow-y: auto;">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4 py-3 text-muted small fw-bold border-bottom-0">ID</th>
                        <th class="px-4 py-3 text-muted small fw-bold border-bottom-0">Client Name</th>
                        <th class="px-4 py-3 text-muted small fw-bold border-bottom-0">Trip Package</th>
                        <th class="px-4 py-3 text-muted small fw-bold border-bottom-0">Target Date</th>
                        <th class="px-4 py-3 text-muted small fw-bold border-bottom-0">Passengers</th>
                        <th class="px-4 py-3 text-muted small fw-bold border-bottom-0">Status</th>
                        <th class="px-4 py-3 text-muted small fw-bold border-bottom-0 text-end">Action</th>
                    </tr>
                </thead>
                <tbody class="manifestTableBody">
                    @forelse($customBookings as $booking)
                        <tr class="booking-table-row" data-status="{{ $booking->status }}">
                            <td class="px-4 py-3 fw-bold text-dark">#BKG-{{ $booking->id }}</td>
                            <td class="px-4 py-3">
                                <div class="fw-bold text-dark">{{ $booking->user->name ?? 'Unknown Client' }}</div>
                                <div class="small text-muted">{{ $booking->user->email ?? 'N/A' }}</div>
                            </td>
                            <td class="px-4 py-3 fw-medium text-dark">{{ $booking->package->name ?? 'Custom Package Bundle' }}</td>
                            <td class="px-4 py-3 text-muted">{{ date('F d, Y', strtotime($booking->pickup_datetime)) }}</td>
                            <td class="px-4 py-3 text-muted">{{ $booking->pax }} Pax</td>
                            <td>
                                @if($booking->status === 'pending')
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2">🟡 Pending Approval</span>
                                @elseif($booking->status === 'approved')
                                    <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2">🔵 Awaiting Payment</span>
                                @elseif($booking->status === 'confirmed')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2">🟢 Paid / Confirmed</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2">⚫ {{ ucfirst($booking->status) }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-end">
                                <a href="{{ route('admin.booking.show', $booking->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-medium position-relative">
                                    <i class="bi bi-eye me-1"></i> View Info
                                    @if($booking->admin_notify)
                                        <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
                                            <span class="visually-hidden">New alerts</span>
                                        </span>
                                    @endif
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr id="noBookingsRow">
                            <td colspan="7" class="text-center py-5">
                                <i class="bi bi-folder-x fs-2 text-muted opacity-50 d-block mb-2"></i>
                                <span class="text-muted">No processing workflows match your collection parameters right now.</span>
                            </td>
                        </tr>
                    @endforelse
                    
                    <tr class="d-none no-print jsEmptyTableRow" class="d-none no-print">
                        <td colspan="7" class="text-center py-5">
                            <i class="bi bi-folder-x fs-2 text-muted opacity-50 d-block mb-2"></i>
                            <span class="text-muted">No entries match this status filter.</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <h5 class="fw-bold mb-3 mt-5 text-dark"><i class="bi bi-card-checklist me-2"></i> Standard Bookings</h5>
        <div class="table-responsive custom-table-wrapper" style="max-height: 400px; overflow-y: auto;">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4 py-3 text-muted small fw-bold border-bottom-0">ID</th>
                        <th class="px-4 py-3 text-muted small fw-bold border-bottom-0">Client Name</th>
                        <th class="px-4 py-3 text-muted small fw-bold border-bottom-0">Trip Package</th>
                        <th class="px-4 py-3 text-muted small fw-bold border-bottom-0">Target Date</th>
                        <th class="px-4 py-3 text-muted small fw-bold border-bottom-0">Passengers</th>
                        <th class="px-4 py-3 text-muted small fw-bold border-bottom-0">Status</th>
                        <th class="px-4 py-3 text-muted small fw-bold border-bottom-0 text-end">Action</th>
                    </tr>
                </thead>
                <tbody class="manifestTableBody">
                    @forelse($normalBookings as $booking)
                        <tr class="booking-table-row" data-status="{{ $booking->status }}">
                            <td class="px-4 py-3 fw-bold text-dark">#BKG-{{ $booking->id }}</td>
                            <td class="px-4 py-3">
                                <div class="fw-bold text-dark">{{ $booking->user->name ?? 'Unknown Client' }}</div>
                                <div class="small text-muted">{{ $booking->user->email ?? 'N/A' }}</div>
                            </td>
                            <td class="px-4 py-3 fw-medium text-dark">{{ $booking->package->name ?? 'Custom Package Bundle' }}</td>
                            <td class="px-4 py-3 text-muted">{{ date('F d, Y', strtotime($booking->pickup_datetime)) }}</td>
                            <td class="px-4 py-3 text-muted">{{ $booking->pax }} Pax</td>
                            <td>
                                @if($booking->status === 'pending')
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2">🟡 Pending Approval</span>
                                @elseif($booking->status === 'approved')
                                    <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2">🔵 Awaiting Payment</span>
                                @elseif($booking->status === 'confirmed')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2">🟢 Paid / Confirmed</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2">⚫ {{ ucfirst($booking->status) }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-end">
                                <a href="{{ route('admin.booking.show', $booking->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-medium position-relative">
                                    <i class="bi bi-eye me-1"></i> View Info
                                    @if($booking->admin_notify)
                                        <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
                                            <span class="visually-hidden">New alerts</span>
                                        </span>
                                    @endif
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr id="noBookingsRow">
                            <td colspan="7" class="text-center py-5">
                                <i class="bi bi-folder-x fs-2 text-muted opacity-50 d-block mb-2"></i>
                                <span class="text-muted">No processing workflows match your collection parameters right now.</span>
                            </td>
                        </tr>
                    @endforelse
                    
                    <tr class="d-none no-print jsEmptyTableRow" class="d-none no-print">
                        <td colspan="7" class="text-center py-5">
                            <i class="bi bi-folder-x fs-2 text-muted opacity-50 d-block mb-2"></i>
                            <span class="text-muted">No entries match this status filter.</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>


    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @include('layouts.notification')

    <script>

        document.addEventListener('DOMContentLoaded', function () {
            // --- 1. FILTER TABS LOGIC ---
            const filterButtons = document.querySelectorAll('.filter-btn');
            const tbodies = document.querySelectorAll('.manifestTableBody');

            filterButtons.forEach(btn => {
                btn.addEventListener('click', function () {
                    filterButtons.forEach(b => {
                        b.classList.remove('btn-primary');
                        b.classList.add('btn-light', 'text-muted');
                    });
                    this.classList.remove('btn-light', 'text-muted');
                    this.classList.add('btn-primary');

                    const targetStatus = this.getAttribute('data-filter');

                    tbodies.forEach(tbody => {
                        const rows = tbody.querySelectorAll('.booking-table-row');
                        const emptyRow = tbody.querySelector('.jsEmptyTableRow');
                        let visibleCount = 0;

                        rows.forEach(card => {
                            const cardStatus = card.getAttribute('data-status');
                            if (targetStatus === 'all' || cardStatus === targetStatus || (targetStatus === 'pending' && ['pending', 'pending_price', 'pending_downpayment'].includes(cardStatus))) {
                                card.classList.remove('d-none');
                                visibleCount++;
                            } else {
                                card.classList.add('d-none');
                            }
                        });

                        if (emptyRow) {
                            if (visibleCount === 0) {
                                emptyRow.classList.remove('d-none');
                            } else {
                                emptyRow.classList.add('d-none');
                            }
                        }
                    });
                });
            });

            const defaultFilter = document.querySelector('.filter-btn[data-filter="pending"]');
            if (defaultFilter) {
                defaultFilter.click();
            }

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