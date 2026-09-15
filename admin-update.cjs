const fs = require('fs');
let viewPath = 'resources/views/admin/booking-details.blade.php';
let text = fs.readFileSync(viewPath, 'utf8');

// 1. Update the status badge in the details panel
let oldBadge = `@else
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill">{{ ucfirst($booking->status) }}</span>
                            @endif`;

let newBadge = `@elseif($booking->status === 'cancelled')
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill">Cancelled / Denied</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill">{{ ucfirst($booking->status) }}</span>
                            @endif`;
text = text.replace(oldBadge, newBadge);

// 2. Add an alert displaying the admin_message if it exists, right at the top of the Left Panel (inside the card-body before the row)
let oldPanel = `<div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-dark mb-3"><i class="bi bi-info-circle me-2 text-primary"></i>Booking Information</h6>
                        <div class="row g-3">`;

let newPanel = `<div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        @if($booking->admin_message)
                            <div class="alert alert-danger rounded-3 py-2 px-3 mb-3 d-flex align-items-center gap-2">
                                <i class="bi bi-x-circle-fill"></i>
                                <div>
                                    <strong class="d-block" style="font-size: 13px;">Booking Denied</strong>
                                    <span style="font-size: 12px;">{{ $booking->admin_message }}</span>
                                </div>
                            </div>
                        @endif
                        <h6 class="fw-bold text-dark mb-3"><i class="bi bi-info-circle me-2 text-primary"></i>Booking Information</h6>
                        <div class="row g-3">`;
text = text.replace(oldPanel, newPanel);

fs.writeFileSync(viewPath, text);
console.log('done booking details');
