const fs = require('fs');
let viewPath = 'resources/views/bookings.blade.php';
let text = fs.readFileSync(viewPath, 'utf8');

// Fix margin
text = text.replace(/class="card shadow-sm/g, 'class="card mb-4 shadow-sm');

// Add Cancel Modal
let cancelModal = `
    <!-- Cancel Booking Modal -->
    <div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <form id="cancelBookingForm" method="POST" action="">
                    <input type="hidden" name="_token" id="cancelCsrfToken" value="">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Cancel Booking</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4 pt-3">
                        <p class="text-muted small mb-0" id="cancelModalMessage">Are you sure you want to cancel this booking?</p>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Keep Booking</button>
                        <button type="button" class="btn btn-danger rounded-pill px-4 fw-bold" onclick="submitCancelForm()">Yes, Cancel It</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
`;
text = text.replace(/<\/body>/, cancelModal + '\n</body>');

// Add cancel JS
let cancelJs = `
        let currentCancelBookingId = null;

        function confirmCancel(bookingId, status) {
            currentCancelBookingId = bookingId;
            let messageBox = document.getElementById('cancelModalMessage');
            
            if(status === 'approved' || status === 'confirmed') {
                messageBox.innerHTML = "You have already paid a deposit for this booking. <strong class='text-danger'>Deposits are strictly non-refundable.</strong> Are you absolutely sure you want to cancel?";
            } else {
                messageBox.innerHTML = "Are you sure you want to cancel this booking request?";
            }
            
            document.getElementById('cancelBookingForm').action = "/bookings/" + bookingId + "/cancel";
            document.getElementById('cancelCsrfToken').value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            new bootstrap.Modal(document.getElementById('cancelModal')).show();
        }

        function submitCancelForm() {
            if (currentCancelBookingId) {
                document.getElementById('cancelBookingForm').submit();
            }
        }
`;
text = text.replace(/<script>\n\s*function viewItineraryDetails/, `<script>\n${cancelJs}\n        function viewItineraryDetails`);

// Cancel button in cards
text = text.replace(/<button class="btn btn-outline-secondary btn-sm rounded-pill"[^>]*>[\s\S]*?View Details<\/button>/g, function(match) {
    return match + `\n                                            @if(in_array($booking->status, ['pending', 'approved', 'confirmed']))
                                                <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-4 fw-medium shadow-sm" onclick="confirmCancel({{ $booking->id }}, '{{ $booking->status }}')">Cancel</button>
                                            @endif`;
});

// Add tabs
let oldTabs = `<li class="nav-item" role="presentation">
                <button class="nav-link shadow-sm border btn-sm position-relative" id="completed-tab"
                    data-bs-toggle="pill" data-bs-target="#tab-completed" type="button" role="tab">
                    Completed ({{ $bookings->where('status', 'completed')->count() }})
                    @php $completedUnread = $bookings->where('status', 'completed')->contains('notify', true); @endphp
                    <span
                        class="notify-dot-absolute tab-badge-completed {{ $completedUnread ? '' : 'd-none' }}"></span>
                </button>
            </li>`;

let newTabs = `<li class="nav-item" role="presentation">
                <button class="nav-link shadow-sm border btn-sm position-relative" id="completed-tab"
                    data-bs-toggle="pill" data-bs-target="#tab-completed" type="button" role="tab">
                    Completed ({{ $bookings->where('status', 'completed')->count() }})
                    @php $completedUnread = $bookings->where('status', 'completed')->contains('notify', true); @endphp
                    <span
                        class="notify-dot-absolute tab-badge-completed {{ $completedUnread ? '' : 'd-none' }}"></span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link shadow-sm border btn-sm position-relative" id="denied-tab"
                    data-bs-toggle="pill" data-bs-target="#tab-denied" type="button" role="tab">
                    Denied ({{ $bookings->where('status', 'denied')->count() }})
                    @php $deniedUnread = $bookings->where('status', 'denied')->contains('notify', true); @endphp
                    <span
                        class="notify-dot-absolute tab-badge-denied {{ $deniedUnread ? '' : 'd-none' }}"></span>
                </button>
            </li>`;
text = text.replace(oldTabs, newTabs);

// Add Denied Pane
let newTabPane = `
            <!-- TAB: DENIED -->
            <div class="tab-pane fade" id="tab-denied" role="tabpanel">
                <div class="row g-4">
                    @forelse($bookings->where('status', 'denied') as $booking)
                        <div class="col-12">
                            <div class="card mb-4 shadow-sm rounded-4 booking-card bg-white overflow-hidden booking-card-{{ $booking->id }} {{ $booking->notify ? 'border-2 border-danger' : 'border' }}">
                                <div class="row g-0">
                                    <div class="col-md-3 bg-secondary-subtle position-relative">
                                        @if (isset($booking->package->image_path))
                                            <img src="{{ $booking->package->image_path }}" class="w-100 h-100 position-absolute" style="object-fit: cover;">
                                        @endif
                                    </div>
                                    <div class="col-md-9 p-4">
                                        <div class="d-flex justify-content-between mb-2">
                                            <h5 class="fw-bold text-dark mb-0">
                                                {{ $booking->package->name ?? 'Custom Package' }}
                                                <span class="badge bg-danger rounded-pill px-2 ms-1 notify-badge-{{ $booking->id }} {{ $booking->notify ? '' : 'd-none' }}" style="font-size: 9px;">NEW UPDATE</span>
                                            </h5>
                                            <span class="badge status-badge bg-danger-subtle text-danger border border-danger-subtle text-uppercase">Denied</span>
                                        </div>
                                        <div class="row my-2 text-muted small">
                                            <div class="col-sm-4">Date: <strong>{{ date('M d, Y', strtotime($booking->pickup_datetime)) }}</strong></div>
                                            <div class="col-sm-4">Pickup: <strong>{{ date('h:i A', strtotime($booking->pickup_datetime)) }}</strong></div>
                                            <div class="col-sm-4">Travelers: <strong>{{ $booking->pax }} Heads</strong></div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center pt-3 mt-3 border-top">
                                            <div>
                                                <span class="text-muted small d-block">Total Price: &#8369;{{ number_format($booking->total_price, 2) }}</span>
                                                <div class="fw-bold text-danger">Denied</div>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <button class="btn btn-outline-secondary btn-sm rounded-pill" onclick="viewItineraryDetails(this)" data-id="{{ $booking->id }}" data-notify="{{ $booking->notify ? '1' : '0' }}" data-status="{{ $booking->status }}" data-title="{{ $booking->package->name ?? 'Custom Package' }}" data-pickup="{{ $booking->pickup_place_name }}" data-base="&#8369;{{ number_format($booking->vehicle->base_price ?? 0, 2) }}" data-heads="&#8369;{{ number_format($booking->head_price, 2) }} / person" data-total="&#8369;{{ number_format($booking->total_price, 2) }}" data-places="{{ $booking->itinerary->map(fn($i) => ['name' => $i->place ? $i->place->name : ($i->custom_name ?? 'Custom Stop'), 'description' => $i->place ? ($i->place->description ?? 'Included destination.') : 'Custom destination pinned by you.'])->toJson() }}"><i class="bi bi-eye"></i> View Details</button>
                                            </div>
                                        </div>
                                        
                                        @if($booking->admin_message)
                                            <div class="mt-3 bg-danger-subtle p-2 rounded text-danger" style="font-size: 13px;">
                                                <i class="bi bi-x-circle-fill me-1"></i><strong>Admin Message:</strong> {{ $booking->admin_message }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-5">
                            <i class="bi bi-x-circle text-muted mb-3" style="font-size: 3rem;"></i>
                            <h5 class="fw-bold text-dark">No Denied Bookings</h5>
                            <p class="text-muted mb-0">None of your bookings have been rejected.</p>
                        </div>
                    @endforelse
                </div>
            </div>
`;
text = text.replace('<!-- 3. DETAILED SPECIFICATION MODAL OVERLAY -->', newTabPane + '\n    <!-- 3. DETAILED SPECIFICATION MODAL OVERLAY -->');

// Admin Message in other tabs
let adminMessageHtml = `
                                        @if($booking->admin_message && $booking->status === 'denied')
                                            <div class="mt-3 bg-danger-subtle p-2 rounded text-danger" style="font-size: 13px;">
                                                <i class="bi bi-x-circle-fill me-1"></i><strong>Admin Message:</strong> {{ $booking->admin_message }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
`;
text = text.replace(/<\/div>\s*<\/div>\s*<\/div>\s*<\/div>\s*<\/div>\s*<\/div>\s*@empty/g, adminMessageHtml + `                            </div>\n                        </div>\n                    @empty`);

// JS badges
text = text.replace(/let completedUnread = false;/, "let completedUnread = false;\n            let deniedUnread = false;");
text = text.replace(/if \(status === 'completed'\) completedUnread = true;/g, "if (status === 'completed') completedUnread = true;\n                if (status === 'denied') deniedUnread = true;");
text = text.replace(/toggleBadgeVisibility\('.tab-badge-completed', completedUnread\);/, "toggleBadgeVisibility('.tab-badge-completed', completedUnread);\n            toggleBadgeVisibility('.tab-badge-denied', typeof deniedUnread !== 'undefined' ? deniedUnread : false);");

// Safely replace non-ASCII characters directly preceding {{ (for all the garbled PHP blocks)
text = text.replace(/[^\x00-\x7F]+(?=\{\{)/g, '&#8369;');

// Safely fix openGcashModal which might have [garbled]{{
text = text.replace(/openGcashModal\(\{\{ \$booking->id \}\}, '[^']*'/g, "openGcashModal({{ $booking->id }}, '&#8369;{{ number_format($booking->deposit_amount, 2) }}')");

fs.writeFileSync(viewPath, text);
console.log('done rebuilding final');
