<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ETRAV - My Bookings</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        .booking-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .booking-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 .5rem 1.5rem rgba(0, 0, 0, .08) !important;
        }

        .status-badge {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.5px;
            padding: 6px 12px;
            border-radius: 50px;
        }

        .nav-pills .nav-link {
            color: #6c757d;
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 50px;
            transition: all 0.2s ease;
        }

        .nav-pills .nav-link.active {
            background-color: #0d6efd;
            color: #fff;
        }

        .itinerary-timeline {
            position: relative;
            border-left: 2px dashed #dee2e6;
            margin-left: 10px;
            padding-left: 20px;
        }

        .timeline-item {
            position: relative;
            padding-bottom: 15px;
        }

        .timeline-item::before {
            content: "";
            position: absolute;
            left: -26px;
            top: 4px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: #0d6efd;
        }

        /* --- CUSTOM LARGER NOTIFICATION DOTS --- */
        .notify-dot-absolute {
            position: absolute;
            top: -2px;
            right: -6px;
            width: 14px;
            height: 14px;
            background-color: #dc3545;
            border: 2px solid #fff;
            border-radius: 50%;
            box-shadow: 0 0 0 2px rgba(220, 53, 69, 0.2);
        }
    </style>
</head>

<body class="bg-light">

    <!-- CSRF Token Meta Tag for AJAX -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $hasNotification = $bookings->contains('notify', true);
    @endphp

    <!-- 1. BREEZE AUTHENTICATION NAVBAR -->
    <nav class="navbar navbar-expand-md navbar-light bg-white border-bottom sticky-top py-3">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold text-dark d-flex align-items-center gap-2"
                href="{{ route('bookings.view') }}">
                <img src="{{ asset('storage/logotext.png') }}" alt="ETRAV Logo"
                    style="height: 38px; object-fit: contain;">
            </a>

            <div class="ms-auto">
                <div class="dropdown">
                    <button
                        class="btn border-0 d-flex align-items-center gap-1 text-muted fw-medium fs-6 bg-transparent p-0 position-relative"
                        type="button" id="breezeDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <span>{{ Auth::user()->name ?? 'Guest User' }}</span>
                        <span id="nav-red-dot"
                            class="notify-dot-absolute {{ $hasNotification ? '' : 'd-none' }}"></span>
                        <i class="bi bi-chevron-down small ms-1" style="font-size: 12px;"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border mt-2 py-1"
                        aria-labelledby="breezeDropdown" style="width: 220px; border-radius: 6px;">
                        @if(Auth::user()->is_admin)
                            <li>
                                <a class="dropdown-item py-2 text-primary fw-bold px-4 d-flex align-items-center" href="{{ route('admin.bookings') }}">
                                    <i class="bi bi-shield-lock-fill me-2 fs-6"></i> Admin Panel
                                </a>
                            </li>
                            <li><hr class="dropdown-divider my-1"></li>
                        @endif
                        <li><a class="dropdown-item py-2 text-muted px-4 d-flex align-items-center" href="{{ route('home') }}"><i class="bi bi-map me-2 fs-6"></i> Explore Tours</a></li>
                        <li><a class="dropdown-item py-2 text-muted px-4 d-flex align-items-center" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2 fs-6"></i> Profile</a></li>
                        <li>
                            <a class="dropdown-item py-2 text-primary px-4 fw-medium d-flex align-items-center justify-content-between"
                                href="{{ route('bookings.view') }}">
                                <span><i class="bi bi-journal-bookmark me-2"></i> Bookings</span>
                                <span id="dropdown-new-badge"
                                    class="badge rounded-pill bg-danger {{ $hasNotification ? '' : 'd-none' }}"
                                    style="font-size: 10px;">New</span>
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider my-1">
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item py-2 text-danger px-4 fw-medium"><i
                                        class="bi bi-box-arrow-right me-2"></i> Log Out</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- 2. MAIN CONTAINER -->
    <div class="container-xl py-5" style="max-width: 1100px;">
        <div class="row mb-4 align-items-center">
            <div class="col-md-6">
                <h4 class="fw-bold text-dark mb-1">My Tour Reservations</h4>
                <p class="text-muted small mb-0">Track your upcoming destinations, schedules, and custom itineraries.
                </p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <a href="{{ route('home') }}" class="btn btn-primary rounded-pill px-4 btn-sm fw-medium shadow-sm">
                    <i class="bi bi-plus-lg me-1"></i> Book New Adventure
                </a>
            </div>
        </div>

        <!-- STATUS TABS FILTER -->
        <ul class="nav nav-pills mb-4 gap-2" id="bookingTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active shadow-sm border btn-sm position-relative" id="all-tab"
                    data-bs-toggle="pill" data-bs-target="#tab-all" type="button" role="tab">
                    All Bookings ({{ $bookings->count() }})
                    <span class="notify-dot-absolute tab-badge-all {{ $hasNotification ? '' : 'd-none' }}"></span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link shadow-sm border btn-sm position-relative" id="pending-tab"
                    data-bs-toggle="pill" data-bs-target="#tab-pending" type="button" role="tab">
                    Pending ({{ $bookings->whereIn('status', ['pending', 'approved', 'pending_price', 'pending_downpayment'])->count() }})
                    @php $pendingUnread = $bookings->where('status', 'pending')->contains('notify', true); @endphp
                    <span class="notify-dot-absolute tab-badge-pending {{ $pendingUnread ? '' : 'd-none' }}"></span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link shadow-sm border btn-sm position-relative" id="confirmed-tab"
                    data-bs-toggle="pill" data-bs-target="#tab-confirmed" type="button" role="tab">
                    Confirmed ({{ $bookings->where('status', 'confirmed')->count() }})
                    @php $confirmedUnread = $bookings->where('status', 'confirmed')->contains('notify', true); @endphp
                    <span
                        class="notify-dot-absolute tab-badge-confirmed {{ $confirmedUnread ? '' : 'd-none' }}"></span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
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
            </li>
        </ul>

        <!-- BOOKINGS DISPLAY PANEL -->
        <div class="tab-content" id="bookingTabsContent">

            <!-- TAB: ALL -->
            <div class="tab-pane fade show active" id="tab-all" role="tabpanel">
                <div class="row g-4">
                    @forelse($bookings as $booking)
                        <div class="col-12">
                            <div
                                class="card mb-4 shadow-sm rounded-4 booking-card bg-white overflow-hidden booking-card-{{ $booking->id }} {{ $booking->notify ? 'border-2 border-danger' : 'border' }}">
                                <div class="row g-0">
                                    <div class="col-md-3 bg-secondary-subtle d-flex align-items-center justify-content-center text-muted border-end position-relative"
                                        style="min-height: 140px;">
                                        @if (isset($booking->package->image_path))
                                            <img src="{{ $booking->package->image_path }}"
                                                alt="{{ $booking->package->name }}"
                                                class="w-100 h-100 position-absolute" style="object-fit: cover;">
                                        @else
                                            <div class="text-center p-3">
                                                <i class="bi bi-image fs-1 d-block opacity-50 mb-1"></i>
                                                <span class="small">No Preview</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-md-9 p-4">
                                        <div
                                            class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                                            <div>
                                                <span
                                                    class="text-muted font-monospace small d-flex align-items-center gap-2 mb-1">
                                                    BOOKING-ID: #ETV-{{ $booking->id }}
                                                    <span
                                                        class="badge bg-danger rounded-pill px-2 notify-badge-{{ $booking->id }} {{ $booking->notify ? '' : 'd-none' }}"
                                                        style="font-size: 9px; letter-spacing: 0.3px;">NEW
                                                        UPDATE</span>
                                                </span>
                                                <h5 class="fw-bold text-dark mb-0">
                                                    {{ $booking->package->name ?? 'Custom Package' }}</h5>
                                            </div>
                                            <span
                                                class="badge status-badge {{ $booking->status === 'pending' ? 'bg-warning-subtle text-warning border border-warning-subtle' : ($booking->status === 'confirmed' ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-secondary-subtle text-secondary border border-secondary-subtle') }} text-uppercase">
                                                {{ $booking->status }}
                                            </span>
                                          </div>

                                          @if($booking->admin_message && in_array($booking->status, ['denied', 'cancelled']))
                                              <div class="mt-1 mb-3 bg-danger-subtle p-3 rounded-3 text-danger border border-danger-subtle shadow-sm">
                                                  <div class="d-flex align-items-start gap-2">
                                                      <i class="bi bi-x-circle-fill mt-1 fs-5"></i>
                                                      <div>
                                                          <strong class="d-block mb-1" style="font-size: 14px;">Reason for Rejection</strong>
                                                          <span style="font-size: 13.5px; opacity: 0.9;">{{ $booking->admin_message }}</span>
                                                      </div>
                                                  </div>
                                              </div>
                                          @endif
                                          <div class="row g-3 my-2 text-muted small">
                                            <div class="col-sm-4"><i class="bi bi-calendar3 text-primary me-1"></i>
                                                Date: <strong class="text-dark fs-6">{{ date('M d, Y', strtotime($booking->pickup_datetime)) }}</strong>
                                            </div>
                                            <div class="col-sm-4"><i class="bi bi-clock text-primary me-1"></i>
                                                Pickup: <strong class="text-dark fs-6">{{ date('h:i A', strtotime($booking->pickup_datetime)) }}</strong>
                                            </div>
                                            <div class="col-sm-4"><i class="bi bi-people text-primary me-1"></i>
                                                Travelers: <strong class="text-dark fs-6">{{ $booking->pax }} Passengers</strong></div>
                                        </div>
                                        <div
                                            class="d-flex justify-content-between align-items-center pt-3 mt-3 border-top flex-wrap gap-3">
                                            <div>
                                                <span class="text-muted small d-block mb-1">Total Price</span>
<span class="fs-5 fw-bold text-dark">&#8369;{{ number_format($booking->total_price, 2) }}</span>
                                                @if($booking->status === 'approved')
    <div class="fw-bold text-danger" style="font-size: 15px;">25% Deposit Due: &#8369;{{ number_format($booking->deposit_amount, 2) }}</div>
@elseif($booking->status === 'confirmed')
    @if($booking->amount_paid >= $booking->deposit_amount)
        <div class="fw-bold text-success" style="font-size: 15px;"><i class="bi bi-check-circle-fill me-1"></i>25% Deposit Paid</div>
    @else
        <div class="fw-bold text-warning" style="font-size: 15px;"><i class="bi bi-clock-fill me-1"></i>Payment Pending</div>
    @endif
@endif
                                            </div>
                                            <div class="d-flex gap-2">
                                                <button
                                                    class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-medium"
                                                    onclick="viewItineraryDetails(this)"
                                                    data-id="{{ $booking->id }}"
                                                    data-notify="{{ $booking->notify ? '1' : '0' }}"
                                                    data-status="{{ $booking->status }}"
                                                    data-title="{{ $booking->package->name ?? 'Package Specification' }}"
                                                    data-pickup="{{ $booking->pickup_place_name }}"
                                                    data-base="&#8369;{{ number_format($booking->total_price, 2) }} ({{ number_format($booking->distance / 1000, 1) }} km)"
                                                    data-heads="&#8369;{{ number_format($booking->head_price, 2) }} / person"
                                                    data-total="&#8369;{{ number_format($booking->total_price, 2) }}"
                                                    data-places="{{ $booking->itinerary->map(fn($i) => ['name' => $i->place ? $i->place->name : ($i->custom_name ?? 'Custom Stop'), 'description' => $i->place ? ($i->place->description ?? 'Included destination.') : 'Custom destination pinned by you.'])->toJson() }}">
                                                    <i class="bi bi-eye"></i> View Details
                                                </button>
                                                @if(in_array($booking->status, ['pending', 'pending_price', 'pending_downpayment', 'approved', 'confirmed']))
                                                @if($booking->status === 'pending_downpayment' || $booking->status === 'approved')
                                                    <a href="{{ route('booking.payment', $booking->id) }}" class="btn btn-primary btn-sm rounded-pill px-4 fw-medium shadow-sm">Pay 25% Deposit</a>
                                                @endif
                                                @if($booking->is_custom && in_array($booking->status, ['pending_price', 'pending_downpayment']))
                                                    <a href="{{ route('booking.edit', $booking->id) }}" class="btn btn-outline-primary btn-sm rounded-pill px-4 fw-medium shadow-sm"><i class="bi bi-pencil-square me-1"></i> Edit Booking</a>
                                                @endif
                                                <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-4 fw-medium shadow-sm" onclick="confirmCancel({{ $booking->id }}, '{{ $booking->status }}')">Cancel</button>
                                            @endif
                                                @if(in_array($booking->status, ['denied', 'cancelled']))
                                                      <a href="{{ route('booking.edit', $booking->id) }}?rebook=1" class="btn btn-primary btn-sm rounded-pill px-3 fw-medium shadow-sm"><i class="bi bi-arrow-repeat me-1"></i>Rebook</a>
                                                  @endif
                                            </div>
                                          </div>
                                          
                                          
                                      </div>
                                  </div>
                              </div>
                          </div>
                      @empty
                        <div class="col-12">
                            <div class="alert alert-light text-center py-5 rounded-4 shadow-sm border"><i
                                    class="bi bi-journal-x text-muted fs-2 d-block mb-2"></i>
                                <h6>No Bookings Found</h6>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- TAB: PENDING -->
            <div class="tab-pane fade" id="tab-pending" role="tabpanel">
                <div class="row g-4">
                    @forelse($bookings->whereIn('status', ['pending', 'approved', 'pending_price', 'pending_downpayment']) as $booking)
                        <div class="col-12">
                            <div
                                class="card mb-4 shadow-sm rounded-4 booking-card bg-white overflow-hidden booking-card-{{ $booking->id }} {{ $booking->notify ? 'border-2 border-danger' : 'border' }}">
                                <div class="row g-0">
                                    <div class="col-md-3 bg-secondary-subtle position-relative">
                                        @if (isset($booking->package->image_path))
                                            <img src="{{ $booking->package->image_path }}"
                                                class="w-100 h-100 position-absolute" style="object-fit: cover;">
                                        @endif
                                    </div>
                                    <div class="col-md-9 p-4">
                                        <div class="d-flex justify-content-between mb-2">
                                            <h5 class="fw-bold text-dark mb-0">
                                                {{ $booking->package->name ?? 'Custom Package' }}
                                                <span
                                                    class="badge bg-danger rounded-pill px-2 ms-1 notify-badge-{{ $booking->id }} {{ $booking->notify ? '' : 'd-none' }}"
                                                    style="font-size: 9px;">NEW UPDATE</span>
                                            </h5>
                                            @if($booking->status === 'pending')
                                                @php
$badgeClass = match($booking->status) {
    'pending' => 'bg-primary-subtle text-primary border border-primary-subtle',
      'pending_price' => 'bg-warning-subtle text-warning border border-warning-subtle',
      'pending_downpayment' => 'bg-info-subtle text-info border border-info-subtle',
    'approved' => 'bg-info-subtle text-info border border-info-subtle',
    'confirmed' => 'bg-success-subtle text-success border border-success-subtle',
    'completed' => 'bg-dark-subtle text-dark border border-dark-subtle',
    'denied', 'cancelled' => 'bg-danger-subtle text-danger border border-danger-subtle',
    default => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
};
@endphp
<span class="badge status-badge {{ $badgeClass }} text-uppercase">{{ $booking->status }}</span>
                                            @else
                                                @php
$badgeClass = match($booking->status) {
    'pending' => 'bg-primary-subtle text-primary border border-primary-subtle',
    'approved' => 'bg-info-subtle text-info border border-info-subtle',
    'confirmed' => 'bg-success-subtle text-success border border-success-subtle',
    'completed' => 'bg-dark-subtle text-dark border border-dark-subtle',
    'denied', 'cancelled' => 'bg-danger-subtle text-danger border border-danger-subtle',
    default => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
};
@endphp
<span class="badge status-badge {{ $badgeClass }} text-uppercase">{{ $booking->status }}</span>
                                            @endif
                                        </div>
                                        <div class="row my-2 text-muted small">
                                            <div class="col-sm-4">Date: <strong class="text-dark fs-6">{{ date('M d, Y', strtotime($booking->pickup_datetime)) }}</strong>
                                            </div>
                                            <div class="col-sm-4">Pickup: <strong class="text-dark fs-6">{{ date('h:i A', strtotime($booking->pickup_datetime)) }}</strong>
                                            </div>
                                            <div class="col-sm-4">Travelers: <strong class="text-dark fs-6">{{ $booking->pax }}
                                                    Passengers</strong></div>
                                        </div>
                                        <div
                                            class="d-flex justify-content-between align-items-center pt-3 mt-3 border-top flex-wrap gap-3">
                                            <div><span class="text-muted small d-block mb-1">Total Price</span>
<span class="fs-5 fw-bold text-dark">&#8369;{{ number_format($booking->total_price, 2) }}</span>
                                                @if($booking->status === 'approved')
    <div class="fw-bold text-danger" style="font-size: 15px;">25% Deposit Due: &#8369;{{ number_format($booking->deposit_amount, 2) }}</div>
@elseif($booking->status === 'confirmed')
    @if($booking->amount_paid >= $booking->deposit_amount)
        <div class="fw-bold text-success" style="font-size: 15px;"><i class="bi bi-check-circle-fill me-1"></i>25% Deposit Paid</div>
    @else
        <div class="fw-bold text-warning" style="font-size: 15px;"><i class="bi bi-clock-fill me-1"></i>Payment Pending</div>
    @endif
@endif
                                            </div>
                                            <div class="d-flex gap-2">
                                                <button class="btn btn-outline-secondary btn-sm rounded-pill px-4 fw-medium"
                                                    onclick="viewItineraryDetails(this)" data-id="{{ $booking->id }}"
                                                    data-notify="{{ $booking->notify ? '1' : '0' }}"
                                                    data-status="{{ $booking->status }}"
                                                    data-title="{{ $booking->package->name ?? 'Custom Package' }}"
                                                    data-pickup="{{ $booking->pickup_place_name }}"
                                                    data-base="&#8369;{{ number_format($booking->total_price, 2) }} ({{ number_format($booking->distance / 1000, 1) }} km)"
                                                    data-heads="&#8369;{{ number_format($booking->head_price, 2) }} / person"
                                                    data-total="&#8369;{{ number_format($booking->total_price, 2) }}"
                                                    data-places="{{ $booking->itinerary->map(fn($i) => ['name' => $i->place ? $i->place->name : ($i->custom_name ?? 'Custom Stop'), 'description' => $i->place ? ($i->place->description ?? 'Included destination.') : 'Custom destination pinned by you.'])->toJson() }}"><i
                                                        class="bi bi-eye"></i> View Details</button>
                                                @if(in_array($booking->status, ['pending', 'pending_price', 'pending_downpayment', 'approved', 'confirmed']))
                                                @if($booking->status === 'pending_downpayment' || $booking->status === 'approved')
                                                    <a href="{{ route('booking.payment', $booking->id) }}" class="btn btn-primary btn-sm rounded-pill px-4 fw-medium shadow-sm">Pay 25% Deposit</a>
                                                @endif
                                                @if($booking->is_custom && in_array($booking->status, ['pending_price', 'pending_downpayment']))
                                                    <a href="{{ route('booking.edit', $booking->id) }}" class="btn btn-outline-primary btn-sm rounded-pill px-4 fw-medium shadow-sm"><i class="bi bi-pencil-square me-1"></i> Edit Booking</a>
                                                @endif
                                                <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-4 fw-medium shadow-sm" onclick="confirmCancel({{ $booking->id }}, '{{ $booking->status }}')">Cancel</button>
                                            @endif
                                                
                                            </div>
                                          </div>
                                          
                                          
                                      </div>
                                  </div>
                              </div>
                          </div>
                      @empty
                        <div class="col-12">
                            <p class="text-muted small text-center py-5 bg-white rounded-4 shadow-sm border">No pending
                                deposits found.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- TAB: CONFIRMED -->
            <div class="tab-pane fade" id="tab-confirmed" role="tabpanel">
                <div class="row g-4">
                    @forelse($bookings->where('status', 'confirmed') as $booking)
                        <div class="col-12">
                            <div
                                class="card mb-4 shadow-sm rounded-4 booking-card bg-white overflow-hidden booking-card-{{ $booking->id }} {{ $booking->notify ? 'border-2 border-danger' : 'border' }}">
                                <div class="row g-0">
                                    <div class="col-md-3 bg-secondary-subtle position-relative">
                                        @if (isset($booking->package->image_path))
                                            <img src="{{ $booking->package->image_path }}"
                                                class="w-100 h-100 position-absolute" style="object-fit: cover;">
                                        @endif
                                    </div>
                                    <div class="col-md-9 p-4">
                                        <div class="d-flex justify-content-between mb-2">
                                            <h5 class="fw-bold text-dark mb-0">
                                                {{ $booking->package->name ?? 'Custom Package' }}
                                                <span
                                                    class="badge bg-danger rounded-pill px-2 ms-1 notify-badge-{{ $booking->id }} {{ $booking->notify ? '' : 'd-none' }}"
                                                    style="font-size: 9px;">NEW UPDATE</span>
                                            </h5>
                                            <span
                                                class="badge status-badge bg-success-subtle text-success border border-success-subtle text-uppercase">Confirmed</span>
                                          </div>

                                          @if($booking->admin_message && in_array($booking->status, ['denied', 'cancelled']))
                                              <div class="mt-1 mb-3 bg-danger-subtle p-3 rounded-3 text-danger border border-danger-subtle shadow-sm">
                                                  <div class="d-flex align-items-start gap-2">
                                                      <i class="bi bi-x-circle-fill mt-1 fs-5"></i>
                                                      <div>
                                                          <strong class="d-block mb-1" style="font-size: 14px;">Reason for Rejection</strong>
                                                          <span style="font-size: 13.5px; opacity: 0.9;">{{ $booking->admin_message }}</span>
                                                      </div>
                                                  </div>
                                              </div>
                                          @endif
                                          <div class="row my-2 text-muted small">
                                            <div class="col-sm-4">Date: <strong class="text-dark fs-6">{{ date('M d, Y', strtotime($booking->pickup_datetime)) }}</strong>
                                            </div>
                                            <div class="col-sm-4">Pickup: <strong class="text-dark fs-6">{{ date('h:i A', strtotime($booking->pickup_datetime)) }}</strong>
                                            </div>
                                            <div class="col-sm-4">Travelers: <strong class="text-dark fs-6">{{ $booking->pax }}
                                                    Passengers</strong></div>
                                        </div>
                                        <div
                                            class="d-flex justify-content-between align-items-center pt-3 mt-3 border-top">
                                            <div><span class="text-muted small d-block mb-1">Total Price</span>
<span class="fs-5 fw-bold text-dark">&#8369;{{ number_format($booking->total_price, 2) }}</span>
                                                
                                            </div>
                                            <button class="btn btn-outline-secondary btn-sm rounded-pill"
                                                onclick="viewItineraryDetails(this)" data-id="{{ $booking->id }}"
                                                data-notify="{{ $booking->notify ? '1' : '0' }}"
                                                data-status="{{ $booking->status }}"
                                                data-title="{{ $booking->package->name ?? 'Custom Package' }}"
                                                data-pickup="{{ $booking->pickup_place_name }}"
                                                data-base="&#8369;{{ number_format($booking->total_price, 2) }} ({{ number_format($booking->distance / 1000, 1) }} km)"
                                                data-heads="&#8369;{{ number_format($booking->head_price, 2) }} / person"
                                                data-total="&#8369;{{ number_format($booking->total_price, 2) }}"
                                                data-places="{{ $booking->itinerary->map(fn($i) => ['name' => $i->place ? $i->place->name : ($i->custom_name ?? 'Custom Stop'), 'description' => $i->place ? ($i->place->description ?? 'Included destination.') : 'Custom destination pinned by you.'])->toJson() }}"><i
                                                    class="bi bi-eye"></i> View Details</button>
                                            @if(in_array($booking->status, ['pending', 'pending_price', 'pending_downpayment', 'approved', 'confirmed']))
                                                @if($booking->status === 'pending_downpayment' || $booking->status === 'approved')
                                                    <a href="{{ route('booking.payment', $booking->id) }}" class="btn btn-primary btn-sm rounded-pill px-4 fw-medium shadow-sm">Pay 25% Deposit</a>
                                                @endif
                                                @if($booking->is_custom && in_array($booking->status, ['pending_price', 'pending_downpayment']))
                                                    <a href="{{ route('booking.edit', $booking->id) }}" class="btn btn-outline-primary btn-sm rounded-pill px-4 fw-medium shadow-sm"><i class="bi bi-pencil-square me-1"></i> Edit Booking</a>
                                                @endif
                                                <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-4 fw-medium shadow-sm" onclick="confirmCancel({{ $booking->id }}, '{{ $booking->status }}')">Cancel</button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <p class="text-muted small text-center py-5 bg-white rounded-4 shadow-sm border">No active
                                confirmed itineraries.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- TAB: COMPLETED -->
            <div class="tab-pane fade" id="tab-completed" role="tabpanel">
                <div class="row g-4">
                    @forelse($bookings->where('status', 'completed') as $booking)
                        <div class="col-12">
                            <div
                                class="card mb-4 shadow-sm rounded-4 booking-card bg-white overflow-hidden booking-card-{{ $booking->id }} {{ $booking->notify ? 'border-2 border-danger' : 'border' }}">
                                <div class="row g-0">
                                    <div class="col-md-3 bg-secondary-subtle position-relative">
                                        @if (isset($booking->package->image_path))
                                            <img src="{{ $booking->package->image_path }}"
                                                class="w-100 h-100 position-absolute" style="object-fit: cover;">
                                        @endif
                                    </div>
                                    <div class="col-md-9 p-4">
                                        <div class="d-flex justify-content-between mb-2">
                                            <h5 class="fw-bold text-dark mb-0">
                                                {{ $booking->package->name ?? 'Custom Package' }}
                                                <span
                                                    class="badge bg-danger rounded-pill px-2 ms-1 notify-badge-{{ $booking->id }} {{ $booking->notify ? '' : 'd-none' }}"
                                                    style="font-size: 9px;">NEW UPDATE</span>
                                            </h5>
                                            <span
                                                class="badge status-badge bg-secondary-subtle text-secondary border border-secondary-subtle text-uppercase">Completed</span>
                                          </div>

                                          @if($booking->admin_message && in_array($booking->status, ['denied', 'cancelled']))
                                              <div class="mt-1 mb-3 bg-danger-subtle p-3 rounded-3 text-danger border border-danger-subtle shadow-sm">
                                                  <div class="d-flex align-items-start gap-2">
                                                      <i class="bi bi-x-circle-fill mt-1 fs-5"></i>
                                                      <div>
                                                          <strong class="d-block mb-1" style="font-size: 14px;">Reason for Rejection</strong>
                                                          <span style="font-size: 13.5px; opacity: 0.9;">{{ $booking->admin_message }}</span>
                                                      </div>
                                                  </div>
                                              </div>
                                          @endif
                                          <div class="row my-2 text-muted small">
                                            <div class="col-sm-4">Date: <strong class="text-dark fs-6">{{ date('M d, Y', strtotime($booking->pickup_datetime)) }}</strong>
                                            </div>
                                            <div class="col-sm-4">Pickup: <strong class="text-dark fs-6">{{ date('h:i A', strtotime($booking->pickup_datetime)) }}</strong>
                                            </div>
                                            <div class="col-sm-4">Travelers: <strong class="text-dark fs-6">{{ $booking->pax }}
                                                    Passengers</strong></div>
                                        </div>
                                        <div
                                            class="d-flex justify-content-between align-items-center pt-3 mt-3 border-top">
                                            <div><span class="text-muted small d-block mb-1">Total Price</span>
<span class="fs-5 fw-bold text-dark">&#8369;{{ number_format($booking->total_price, 2) }}</span>
                                                
                                            </div>
                                            <button class="btn btn-outline-secondary btn-sm rounded-pill"
                                                onclick="viewItineraryDetails(this)" data-id="{{ $booking->id }}"
                                                data-notify="{{ $booking->notify ? '1' : '0' }}"
                                                data-status="{{ $booking->status }}"
                                                data-title="{{ $booking->package->name ?? 'Custom Package' }}"
                                                data-pickup="{{ $booking->pickup_place_name }}"
                                                data-base="&#8369;{{ number_format($booking->total_price, 2) }} ({{ number_format($booking->distance / 1000, 1) }} km)"
                                                data-heads="&#8369;{{ number_format($booking->head_price, 2) }} / person"
                                                data-total="&#8369;{{ number_format($booking->total_price, 2) }}"
                                                data-places="{{ $booking->itinerary->map(fn($i) => ['name' => $i->place ? $i->place->name : ($i->custom_name ?? 'Custom Stop'), 'description' => $i->place ? ($i->place->description ?? 'Included destination.') : 'Custom destination pinned by you.'])->toJson() }}"><i
                                                    class="bi bi-eye"></i> View Details</button>
                                            @if(in_array($booking->status, ['pending', 'pending_price', 'pending_downpayment', 'approved', 'confirmed']))
                                                @if($booking->status === 'pending_downpayment' || $booking->status === 'approved')
                                                    <a href="{{ route('booking.payment', $booking->id) }}" class="btn btn-primary btn-sm rounded-pill px-4 fw-medium shadow-sm">Pay 25% Deposit</a>
                                                @endif
                                                @if($booking->is_custom && in_array($booking->status, ['pending_price', 'pending_downpayment']))
                                                    <a href="{{ route('booking.edit', $booking->id) }}" class="btn btn-outline-primary btn-sm rounded-pill px-4 fw-medium shadow-sm"><i class="bi bi-pencil-square me-1"></i> Edit Booking</a>
                                                @endif
                                                <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-4 fw-medium shadow-sm" onclick="confirmCancel({{ $booking->id }}, '{{ $booking->status }}')">Cancel</button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <p class="text-muted small text-center py-5 bg-white rounded-4 shadow-sm border">No past
                                trips recorded.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    
            <!-- TAB: DENIED -->
              <div class="tab-pane fade" id="tab-denied" role="tabpanel">
                  <div class="row g-4">
                      @forelse($bookings->where('status', 'denied') as $booking)
                          <div class="col-12">
                              <div
                                  class="card mb-4 shadow-sm rounded-4 booking-card bg-white overflow-hidden booking-card-{{ $booking->id }} {{ $booking->notify ? 'border-2 border-danger' : 'border' }}">
                                  <div class="row g-0">
                                      <div class="col-md-3 bg-secondary-subtle d-flex align-items-center justify-content-center text-muted border-end position-relative"
                                          style="min-height: 140px;">
                                          @if (isset($booking->package->image_path))
                                              <img src="{{ $booking->package->image_path }}"
                                                  alt="{{ $booking->package->name }}"
                                                  class="w-100 h-100 position-absolute" style="object-fit: cover;">
                                          @else
                                              <div class="text-center p-3">
                                                  <i class="bi bi-image fs-1 d-block opacity-50 mb-1"></i>
                                                  <span class="small">No Preview</span>
                                              </div>
                                          @endif
                                      </div>
                                      <div class="col-md-9 p-4">
                                          <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                                              <div>
                                                  <span class="text-muted font-monospace small d-flex align-items-center gap-2 mb-1">
                                                      BOOKING-ID: #ETV-{{ $booking->id }}
                                                      <span class="badge bg-danger rounded-pill px-2 notify-badge-{{ $booking->id }} {{ $booking->notify ? '' : 'd-none' }}" style="font-size: 9px; letter-spacing: 0.3px;">NEW UPDATE</span>
                                                  </span>
                                                  <h5 class="fw-bold text-dark mb-0">{{ $booking->package->name ?? 'Custom Package' }}</h5>
                                              </div>
                                              @php
$badgeClass = match($booking->status) {
    'pending' => 'bg-primary-subtle text-primary border border-primary-subtle',
    'approved' => 'bg-info-subtle text-info border border-info-subtle',
    'confirmed' => 'bg-success-subtle text-success border border-success-subtle',
    'completed' => 'bg-dark-subtle text-dark border border-dark-subtle',
    'denied', 'cancelled' => 'bg-danger-subtle text-danger border border-danger-subtle',
    default => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
};
@endphp
<span class="badge status-badge {{ $badgeClass }} text-uppercase">{{ $booking->status }}</span>
                                          </div>

                                          @if($booking->admin_message && in_array($booking->status, ['denied', 'cancelled']))
                                              <div class="mt-1 mb-3 bg-danger-subtle p-3 rounded-3 text-danger border border-danger-subtle shadow-sm">
                                                  <div class="d-flex align-items-start gap-2">
                                                      <i class="bi bi-x-circle-fill mt-1 fs-5"></i>
                                                      <div>
                                                          <strong class="d-block mb-1" style="font-size: 14px;">Reason for Rejection</strong>
                                                          <span style="font-size: 13.5px; opacity: 0.9;">{{ $booking->admin_message }}</span>
                                                      </div>
                                                  </div>
                                              </div>
                                          @endif
                                          <div class="row g-3 my-2 text-muted small">
                                              <div class="col-sm-4"><i class="bi bi-calendar3 text-primary me-1"></i>
                                                  Date: <strong class="text-dark fs-6">{{ date('M d, Y', strtotime($booking->pickup_datetime)) }}</strong>
                                              </div>
                                              <div class="col-sm-4"><i class="bi bi-clock text-primary me-1"></i>
                                                  Pickup: <strong class="text-dark fs-6">{{ date('h:i A', strtotime($booking->pickup_datetime)) }}</strong>
                                              </div>
                                              <div class="col-sm-4"><i class="bi bi-people text-primary me-1"></i>
                                                  Travelers: <strong class="text-dark fs-6">{{ $booking->pax }} Passengers</strong></div>
                                          </div>
                                          <div class="d-flex justify-content-between align-items-center pt-3 mt-3 border-top flex-wrap gap-3">
                                              <div>
                                                  <span class="text-muted small d-block mb-1">Total Price</span>
<span class="fs-5 fw-bold text-dark">&#8369;{{ number_format($booking->total_price, 2) }}</span>
                                                  
                                              </div>
                                              <div class="d-flex gap-2">
                                                <button class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-medium"
                                                      onclick="viewItineraryDetails(this)"
                                                      data-id="{{ $booking->id }}"
                                                      data-notify="{{ $booking->notify ? '1' : '0' }}"
                                                      data-status="{{ $booking->status }}"
                                                      data-title="{{ $booking->package->name ?? 'Package Specification' }}"
                                                      data-pickup="{{ $booking->pickup_place_name }}"
                                                      data-base="&#8369;{{ number_format($booking->total_price, 2) }} ({{ number_format($booking->distance / 1000, 1) }} km)"
                                                      data-heads="&#8369;{{ number_format($booking->head_price, 2) }} / person"
                                                      data-total="&#8369;{{ number_format($booking->total_price, 2) }}"
                                                      data-places="{{ $booking->itinerary->map(fn($i) => ['name' => $i->place ? $i->place->name : ($i->custom_name ?? 'Custom Stop'), 'description' => $i->place ? ($i->place->description ?? 'Included destination.') : 'Custom destination pinned by you.'])->toJson() }}">
                                                      <i class="bi bi-eye"></i> View Details
                                                  </button>
                                                @if(in_array($booking->status, ['pending', 'pending_price', 'pending_downpayment', 'approved', 'confirmed']))
                                                @if($booking->status === 'pending_downpayment' || $booking->status === 'approved')
                                                    <a href="{{ route('booking.payment', $booking->id) }}" class="btn btn-primary btn-sm rounded-pill px-4 fw-medium shadow-sm">Pay 25% Deposit</a>
                                                @endif
                                                @if($booking->is_custom && in_array($booking->status, ['pending_price', 'pending_downpayment']))
                                                    <a href="{{ route('booking.edit', $booking->id) }}" class="btn btn-outline-primary btn-sm rounded-pill px-4 fw-medium shadow-sm"><i class="bi bi-pencil-square me-1"></i> Edit Booking</a>
                                                @endif
                                                <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-4 fw-medium shadow-sm" onclick="confirmCancel({{ $booking->id }}, '{{ $booking->status }}')">Cancel</button>
                                            @endif
                                                @if(in_array($booking->status, ['denied', 'cancelled']))
                                                      <a href="{{ route('booking.edit', $booking->id) }}?rebook=1" class="btn btn-primary btn-sm rounded-pill px-3 fw-medium shadow-sm"><i class="bi bi-arrow-repeat me-1"></i>Rebook</a>
                                                  @endif
                                            </div>
                                          </div>
                                          
                                          
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

    <!-- 3. DETAILED SPECIFICATION MODAL OVERLAY -->
    <div class="modal fade" id="itineraryDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header bg-dark text-white py-3 rounded-top-4">
                    <h5 class="modal-title fw-bold" id="itineraryDetailModalLabel">Reservation Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-4">
                        <label class="form-label fw-bold text-muted small text-uppercase mb-2">📍 Target Route
                            Sequence</label>
                        <div class="itinerary-timeline mt-2" id="modalTimelineContainer"></div>
                    </div>
                    <div class="border-top pt-3 mt-3">
                        <label class="form-label fw-bold text-muted small text-uppercase mb-1">🗺️ Pick-up
                            Configuration</label>
                        <div class="p-3 bg-light rounded-3 d-flex align-items-center gap-2">
                            <i class="bi bi-pin-map-fill text-warning fs-4"></i>
                            <div>
                                <span class="d-block fw-semibold text-dark small" id="detailPickupCoords">Lat: 0.00,
                                    Lng: 0.00</span>
                                <small class="text-muted" style="font-size: 11px;">Mapped pinpoint custom
                                    coordinates</small>
                            </div>
                        </div>
                    </div>
                    <div class="border-top pt-3 mt-4 bg-light p-3 rounded-3" style="font-size: 13px;">
                        <h6 class="fw-bold mb-2 text-dark">Financial Document Summary</h6>
                        <div class="d-flex justify-content-between mb-1 text-muted"><span>Distance-Based Fare:</span><span id="detailBasePrice">₱0.00</span></div>
                        <div class="d-flex justify-content-between mb-1 text-muted"><span>Cost per Person:</span><span id="detailHeadsPrice">₱0.00</span></div>
                        <div class="d-flex justify-content-between fw-bold text-dark border-top pt-2 fs-6">
                            <span>Total Booking Cost:</span><span id="detailTotalPrice">₱0.00</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-4 btn-sm"
                        data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>

        let currentCancelBookingId = null;

        function confirmCancel(bookingId, status) {
            currentCancelBookingId = bookingId;
            let messageBox = document.getElementById('cancelModalMessage');
            
            if(status === 'approved' || status === 'confirmed') {
                messageBox.innerHTML = "You have already paid a deposit for this booking. <strong class='text-danger'>Deposits are strictly non-refundable.</strong> Are you absolutely sure you want to cancel?";
            } else {
                messageBox.innerHTML = "Are you sure you want to cancel this booking request?";
            }
            
            document.getElementById('cancelBookingForm').action = "/booking/" + bookingId + "/cancel";
            document.getElementById('cancelCsrfToken').value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            new bootstrap.Modal(document.getElementById('cancelModal')).show();
        }

        function submitCancelForm() {
            if (currentCancelBookingId) {
                document.getElementById('cancelBookingForm').submit();
            }
        }

        function viewItineraryDetails(button) {
            const bookingId = button.getAttribute('data-id');
            const hasNotify = button.getAttribute('data-notify') === '1';

            // Populate Modal Content
            document.getElementById('itineraryDetailModalLabel').innerText = button.getAttribute('data-title');
            document.getElementById('detailPickupCoords').innerText = button.getAttribute('data-pickup');
            document.getElementById('detailBasePrice').innerText = button.getAttribute('data-base');
            document.getElementById('detailHeadsPrice').innerText = button.getAttribute('data-heads');
            document.getElementById('detailTotalPrice').innerText = button.getAttribute('data-total');

            const timelineContainer = document.getElementById('modalTimelineContainer');
            timelineContainer.innerHTML = '';

            const places = JSON.parse(button.getAttribute('data-places') || '[]');
            if (places.length === 0) {
                timelineContainer.innerHTML = '<div class="text-muted small italic">No routes registered.</div>';
            } else {
                places.forEach((place) => {
                    const item = document.createElement('div');
                    item.className = "timeline-item";
                    item.innerHTML = `
                        <div class="fw-semibold text-dark small">${place.name}</div>
                        <small class="text-muted d-block" style="font-size: 11px;">${place.description || 'Included destination.'}</small>
                    `;
                    timelineContainer.appendChild(item);
                });
            }

            // Show the Modal Overlay
            new bootstrap.Modal(document.getElementById('itineraryDetailModal')).show();

            // Trigger AJAX call if this booking has an active unread status
            if (hasNotify && bookingId) {
                markBookingAsRead(bookingId);
            }
        }

        function markBookingAsRead(bookingId) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // --- OPTIMISTIC UI SYNCHRONIZATION ---

            // 1. Sync all duplicated button attributes on the page for this specific booking
            document.querySelectorAll(`button[data-id="${bookingId}"]`).forEach(btn => {
                btn.setAttribute('data-notify', '0');
            });

            // 2. Remove the active red borders from all duplicated cards for this booking ID
            document.querySelectorAll(`.booking-card-${bookingId}`).forEach(card => {
                card.classList.remove('border-2', 'border-danger');
                card.classList.add('border');
            });

            // 3. Hide all dynamic "NEW UPDATE" text badges across tabs
            document.querySelectorAll(`.notify-badge-${bookingId}`).forEach(badge => {
                badge.classList.add('d-none');
            });

            // 4. Immediately recalculate the status filter dot indicators
            recalculateUnreadBadges();

            // Send notification clearance request to back-end
            fetch(`/bookings/${bookingId}/read`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network update failed.');
                    }
                    return response.json();
                })
                .catch(error => {
                    console.error('AJAX sync failed:', error);
                });
        }

        function recalculateUnreadBadges() {
            // Find all "View Details" buttons with active notify status
            const unreadButtons = document.querySelectorAll('button[data-notify="1"]');

            // Collect unique unread booking IDs to prevent duplicate counts from multiple tabs
            const unreadIds = new Set();
            let pendingUnread = false;
            let confirmedUnread = false;
            let completedUnread = false;
            let deniedUnread = false;

            unreadButtons.forEach(btn => {
                const id = btn.getAttribute('data-id');
                const status = btn.getAttribute('data-status');

                unreadIds.add(id);

                if (status === 'pending' || status === 'pending_price' || status === 'pending_downpayment') pendingUnread = true;
                if (status === 'confirmed') confirmedUnread = true;
                if (status === 'completed') completedUnread = true;
                if (status === 'denied') deniedUnread = true;
            });

            const totalUnreadCount = unreadIds.size;

            // Global Navigation Header dots toggles
            const globalDot = document.getElementById('nav-red-dot');
            const dropBadge = document.getElementById('dropdown-new-badge');

            if (totalUnreadCount > 0) {
                globalDot?.classList.remove('d-none');
                dropBadge?.classList.remove('d-none');
                document.querySelector('.tab-badge-all')?.classList.remove('d-none');
            } else {
                globalDot?.classList.add('d-none');
                dropBadge?.classList.add('d-none');
                document.querySelector('.tab-badge-all')?.classList.add('d-none');
            }

            // Tabs dynamic dot triggers
            toggleBadgeVisibility('.tab-badge-pending', pendingUnread);
            toggleBadgeVisibility('.tab-badge-confirmed', confirmedUnread);
            toggleBadgeVisibility('.tab-badge-completed', completedUnread);
            toggleBadgeVisibility('.tab-badge-denied', typeof deniedUnread !== 'undefined' ? deniedUnread : false);
        }

        function toggleBadgeVisibility(selector, isVisible) {
            const el = document.querySelector(selector);
            if (el) {
                if (isVisible) el.classList.remove('d-none');
                else el.classList.add('d-none');
            }
        }

        let currentPaymentBookingId = null;


    </script>

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

</body>

</html>
