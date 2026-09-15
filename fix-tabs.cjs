const fs = require('fs');
let text = fs.readFileSync('resources/views/bookings.blade.php', 'utf8');

// 1. Re-add Important Updates Tab in the tabs list (and make it hidden if 0)
let tabsList = `<ul class="nav nav-pills mb-4 gap-2" id="bookingTabs" role="tablist">
            @php $importantCount = $bookings->where('notify', true)->count(); @endphp
            @if($importantCount > 0)
            <li class="nav-item" role="presentation">
                <button class="nav-link active shadow-sm border btn-sm position-relative" id="important-tab"
                    data-bs-toggle="pill" data-bs-target="#tab-important" type="button" role="tab">
                    <i class="bi bi-exclamation-circle me-1"></i> Important Updates ({{ $importantCount }})
                </button>
            </li>
            @endif
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $importantCount > 0 ? '' : 'active' }} shadow-sm border btn-sm position-relative" id="all-tab"
                    data-bs-toggle="pill" data-bs-target="#tab-all" type="button" role="tab">
                    All Bookings ({{ $bookings->count() }})
                    <span class="notify-dot-absolute tab-badge-all {{ $hasNotification ? '' : 'd-none' }}"></span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link shadow-sm border btn-sm position-relative" id="pending-tab"
                    data-bs-toggle="pill" data-bs-target="#tab-pending" type="button" role="tab">
                    Pending Deposit ({{ $bookings->whereIn('status', ['pending', 'approved'])->count() }})
                    @php $pendingUnread = $bookings->whereIn('status', ['pending', 'approved'])->contains('notify', true); @endphp
                    <span class="notify-dot-absolute tab-badge-pending {{ $pendingUnread ? '' : 'd-none' }}"></span>
                </button>
            </li>`;

text = text.replace(/<ul class="nav nav-pills mb-4 gap-2" id="bookingTabs" role="tablist">[\s\S]*?<button class="nav-link shadow-sm border btn-sm position-relative" id="confirmed-tab"/, tabsList + '\n            <li class="nav-item" role="presentation">\n                <button class="nav-link shadow-sm border btn-sm position-relative" id="confirmed-tab"');

// 2. Re-add Important Updates Tab Pane
let importantPane = `        <div class="tab-content" id="bookingTabsContent">
            @if($importantCount > 0)
            <!-- TAB: IMPORTANT UPDATES -->
            <div class="tab-pane fade show active" id="tab-important" role="tabpanel">
                <div class="row g-4">
                    @forelse($bookings->where('notify', true) as $booking)
                        <div class="col-12">
                            <div
                                class="card shadow-sm rounded-4 booking-card bg-white overflow-hidden booking-card-{{ $booking->id }} border-2 border-danger">
                                <div class="row g-0">
                                    <div class="col-md-3 bg-secondary-subtle position-relative">
                                        @if (isset($booking->package->image_path))
                                            <img src="{{ $booking->package->image_path }}"
                                                class="w-100 h-100 position-absolute" style="object-fit: cover;">
                                        @else
                                            <div
                                                class="w-100 h-100 position-absolute d-flex align-items-center justify-content-center">
                                                <i class="bi bi-image text-muted fs-1 opacity-25"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-md-9">
                                        <div class="card-body p-4">
                                            <div class="d-flex justify-content-between mb-2">
                                                <h5 class="fw-bold text-dark mb-0">
                                                    {{ $booking->package->name ?? 'Custom Package' }}
                                                </h5>
                                                <div>
                                                    <span
                                                        class="badge bg-danger rounded-pill px-3 notify-badge-{{ $booking->id }}">NEW UPDATE</span>
                                                </div>
                                            </div>

                                            <div class="row mt-3 text-muted small">
                                                <div class="col-sm-6 mb-2">
                                                    <i class="bi bi-geo-alt-fill text-primary me-2"></i> Pickup:
                                                    <span class="text-dark fw-medium">{{ $booking->pickup_place_name }}</span>
                                                </div>
                                                <div class="col-sm-6 mb-2">
                                                    <i class="bi bi-calendar-event-fill text-primary me-2"></i> Date:
                                                    <span class="text-dark fw-medium">{{ \Carbon\Carbon::parse($booking->pickup_date)->format('F d, Y') }}</span>
                                                </div>
                                                <div class="col-sm-6 mb-2">
                                                    <i class="bi bi-clock-fill text-primary me-2"></i> Time:
                                                    <span class="text-dark fw-medium">{{ \Carbon\Carbon::parse($booking->pickup_time)->format('h:i A') }}</span>
                                                </div>
                                                <div class="col-sm-6 mb-2">
                                                    <i class="bi bi-people-fill text-primary me-2"></i> Passengers:
                                                    <span class="text-dark fw-medium">{{ $booking->number_of_heads }} pax</span>
                                                </div>
                                            </div>

                                            <div
                                                class="d-flex justify-content-between align-items-center pt-3 mt-3 border-top flex-wrap gap-3">
                                                <div><span class="text-muted small d-block">Total Price:
                                                        ?{{ number_format($booking->total_price, 2) }}</span>
                                                    <div class="fw-bold text-danger">
                                                        {{ $booking->status === 'pending' ? 'Awaiting Approval' : ($booking->status === 'approved' ? '25% Deposit Due: ?' . number_format($booking->deposit_amount, 2) : '25% Deposit Paid') }}
                                                    </div>
                                                </div>
                                                <div class="d-flex gap-2 justify-content-end w-100">
                                                    <button class="btn btn-outline-secondary btn-sm rounded-pill px-4 fw-medium"
                                                        onclick="viewItineraryDetails(this)" data-id="{{ $booking->id }}"
                                                        data-notify="{{ $booking->notify ? '1' : '0' }}"
                                                        data-status="{{ $booking->status }}"
                                                        data-title="{{ $booking->package->name ?? 'Custom Package' }}"
                                                        data-pickup="{{ $booking->pickup_place_name }}"
                                                        data-base="?{{ number_format($booking->total_price, 2) }} ({{ number_format($booking->distance / 1000, 1) }} km)"
                                                        data-heads="?{{ number_format($booking->head_price, 2) }} / person"
                                                        data-total="?{{ number_format($booking->total_price, 2) }}"
                                                        data-places="{{ $booking->itinerary->map(fn($i) => ['name' => $i->place ? $i->place->name : ($i->custom_name ?? 'Custom Stop'), 'description' => $i->place ? ($i->place->description ?? 'Included destination.') : 'Custom destination pinned by you.'])->toJson() }}"><i
                                                            class="bi bi-eye"></i> View Details</button>
                                                    @if($booking->status === 'approved')
                                                        <button type="button" class="btn btn-primary btn-sm rounded-pill px-4 fw-medium shadow-sm" onclick="openGcashModal({{ $booking->id }}, '?{{ number_format($booking->deposit_amount, 2) }}')">Pay 25% Deposit</button>
                                                    @endif
                                                    @if(in_array($booking->status, ['pending', 'approved', 'confirmed']))
                                                        <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-4 fw-medium shadow-sm" onclick="confirmCancel({{ $booking->id }}, '{{ $booking->status }}')">Cancel</button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                    @endforelse
                </div>
            </div>
            @endif`;

text = text.replace(/<div class="tab-content" id="bookingTabsContent">/, importantPane);

// Fix the "tab-all" pane to not be "show active" if Important Updates is active!
text = text.replace(/<div class="tab-pane fade show active" id="tab-all" role="tabpanel">/, '<div class="tab-pane fade {{ $importantCount > 0 ? \'\' : \'show active\' }}" id="tab-all" role="tabpanel">');

// Fix the "tab-pending" pending deposit loop status! (to include approved)
text = text.replace(/@forelse\(\$bookings->where\('status', 'pending'\) as \$booking\)/, "@forelse($bookings->whereIn('status', ['pending', 'approved']) as $booking)");

fs.writeFileSync('resources/views/bookings.blade.php', text);
console.log("Fixed!");
