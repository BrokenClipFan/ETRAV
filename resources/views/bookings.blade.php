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
                <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg"
                    style="height: 36px; width: 36px;" class="text-primary fill-current">
                    <path
                        d="M24 4C12.95 4 4 12.95 4 24s8.95 20 20 20 20-8.95 20-20S35.05 4 24 4zm2 32h-4v-4h4v4zm0-8h-4V12h4v16z"
                        fill="currentColor" />
                </svg>
                <span class="fs-5 tracking-wider">ETRAV</span>
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
                        <li><a class="dropdown-item py-2 text-muted px-4" href="{{ route('home') }}"><i
                                    class="bi bi-map me-2"></i> Explore Tours</a></li>
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
    <div class="container py-5">
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
                    Pending Deposit ({{ $bookings->where('status', 'pending')->count() }})
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
        </ul>

        <!-- BOOKINGS DISPLAY PANEL -->
        <div class="tab-content" id="bookingTabsContent">

            <!-- TAB: ALL -->
            <div class="tab-pane fade show active" id="tab-all" role="tabpanel">
                <div class="row g-4">
                    @forelse($bookings as $booking)
                        <div class="col-12">
                            <div
                                class="card shadow-sm rounded-4 booking-card bg-white overflow-hidden booking-card-{{ $booking->id }} {{ $booking->notify ? 'border-2 border-danger' : 'border' }}">
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
                                        <div class="row g-3 my-2 text-muted small">
                                            <div class="col-sm-4"><i class="bi bi-calendar3 text-primary me-1"></i>
                                                Date:
                                                <strong>{{ date('M d, Y', strtotime($booking->pickup_datetime)) }}</strong>
                                            </div>
                                            <div class="col-sm-4"><i class="bi bi-clock text-primary me-1"></i>
                                                Pickup:
                                                <strong>{{ date('h:i A', strtotime($booking->pickup_datetime)) }}</strong>
                                            </div>
                                            <div class="col-sm-4"><i class="bi bi-people text-primary me-1"></i>
                                                Travelers: <strong>{{ $booking->pax }} Heads</strong></div>
                                        </div>
                                        <div
                                            class="d-flex justify-content-between align-items-center pt-3 mt-3 border-top flex-wrap gap-3">
                                            <div>
                                                <span class="text-muted small d-block">Total Price:
                                                    ₱{{ number_format($booking->total_price, 2) }}</span>
                                                <div class="fw-bold {{ $booking->status === 'pending' ? 'text-danger' : 'text-success' }}"
                                                    style="font-size: 15px;">
                                                    {{ $booking->status === 'pending' ? 'Deposit Due: ₱' . number_format($booking->deposit_amount, 2) : 'Paid' }}
                                                </div>
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
                                                    data-base="₱{{ number_format($booking->package->package_price ?? 0, 2) }}"
                                                    data-heads="₱{{ number_format(($booking->package->perhead_price ?? 0) * $booking->pax, 2) }} ({{ $booking->pax }} x ₱{{ $booking->package->perhead_price ?? 0 }})"
                                                    data-total="₱{{ number_format($booking->total_price, 2) }}"
                                                    data-places="{{ $booking->places->toJson() }}">
                                                    <i class="bi bi-eye"></i> View Details
                                                </button>
                                                @if ($booking->status === 'pending')
                                                    <button
                                                        class="btn btn-primary btn-sm rounded-pill px-4 fw-medium shadow-sm">Pay
                                                        Deposit</button>
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
                    @forelse($bookings->where('status', 'pending') as $booking)
                        <div class="col-12">
                            <div
                                class="card shadow-sm rounded-4 booking-card bg-white overflow-hidden booking-card-{{ $booking->id }} {{ $booking->notify ? 'border-2 border-danger' : 'border' }}">
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
                                                {{ $booking->package->name }}
                                                <span
                                                    class="badge bg-danger rounded-pill px-2 ms-1 notify-badge-{{ $booking->id }} {{ $booking->notify ? '' : 'd-none' }}"
                                                    style="font-size: 9px;">NEW UPDATE</span>
                                            </h5>
                                            <span
                                                class="badge status-badge bg-warning-subtle text-warning border border-warning-subtle text-uppercase">Pending</span>
                                        </div>
                                        <div class="row my-2 text-muted small">
                                            <div class="col-sm-4">Date:
                                                <strong>{{ date('M d, Y', strtotime($booking->pickup_datetime)) }}</strong>
                                            </div>
                                            <div class="col-sm-4">Pickup:
                                                <strong>{{ date('h:i A', strtotime($booking->pickup_datetime)) }}</strong>
                                            </div>
                                            <div class="col-sm-4">Travelers: <strong>{{ $booking->pax }}
                                                    Heads</strong></div>
                                        </div>
                                        <div
                                            class="d-flex justify-content-between align-items-center pt-3 mt-3 border-top">
                                            <div><span class="text-muted small d-block">Total Price:
                                                    ₱{{ number_format($booking->total_price, 2) }}</span>
                                                <div class="fw-bold text-danger">Deposit Due:
                                                    ₱{{ number_format($booking->deposit_amount, 2) }}</div>
                                            </div>
                                            <button class="btn btn-outline-secondary btn-sm rounded-pill"
                                                onclick="viewItineraryDetails(this)" data-id="{{ $booking->id }}"
                                                data-notify="{{ $booking->notify ? '1' : '0' }}"
                                                data-status="{{ $booking->status }}"
                                                data-title="{{ $booking->package->name }}"
                                                data-pickup="Lat: {{ $booking->latitude }}, Lng: {{ $booking->longitude }}"
                                                data-base="₱{{ number_format($booking->package->package_price, 2) }}"
                                                data-heads="₱{{ number_format($booking->package->perhead_price * $booking->pax, 2) }}"
                                                data-total="₱{{ number_format($booking->total_price, 2) }}"
                                                data-places="{{ $booking->places->toJson() }}"><i
                                                    class="bi bi-eye"></i> View Details</button>
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
                                class="card shadow-sm rounded-4 booking-card bg-white overflow-hidden booking-card-{{ $booking->id }} {{ $booking->notify ? 'border-2 border-danger' : 'border' }}">
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
                                                {{ $booking->package->name }}
                                                <span
                                                    class="badge bg-danger rounded-pill px-2 ms-1 notify-badge-{{ $booking->id }} {{ $booking->notify ? '' : 'd-none' }}"
                                                    style="font-size: 9px;">NEW UPDATE</span>
                                            </h5>
                                            <span
                                                class="badge status-badge bg-success-subtle text-success border border-success-subtle text-uppercase">Confirmed</span>
                                        </div>
                                        <div class="row my-2 text-muted small">
                                            <div class="col-sm-4">Date:
                                                <strong>{{ date('M d, Y', strtotime($booking->pickup_datetime)) }}</strong>
                                            </div>
                                            <div class="col-sm-4">Pickup:
                                                <strong>{{ date('h:i A', strtotime($booking->pickup_datetime)) }}</strong>
                                            </div>
                                            <div class="col-sm-4">Travelers: <strong>{{ $booking->pax }}
                                                    Heads</strong></div>
                                        </div>
                                        <div
                                            class="d-flex justify-content-between align-items-center pt-3 mt-3 border-top">
                                            <div><span class="text-muted small d-block">Total Price:
                                                    ₱{{ number_format($booking->total_price, 2) }}</span>
                                                <div class="fw-bold text-success">Paid</div>
                                            </div>
                                            <button class="btn btn-outline-secondary btn-sm rounded-pill"
                                                onclick="viewItineraryDetails(this)" data-id="{{ $booking->id }}"
                                                data-notify="{{ $booking->notify ? '1' : '0' }}"
                                                data-status="{{ $booking->status }}"
                                                data-title="{{ $booking->package->name }}"
                                                data-pickup="Lat: {{ $booking->latitude }}, Lng: {{ $booking->longitude }}"
                                                data-base="₱{{ number_format($booking->package->package_price, 2) }}"
                                                data-heads="₱{{ number_format($booking->package->perhead_price * $booking->pax, 2) }}"
                                                data-total="₱{{ number_format($booking->total_price, 2) }}"
                                                data-places="{{ $booking->places->toJson() }}"><i
                                                    class="bi bi-eye"></i> View Details</button>
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
                                class="card shadow-sm rounded-4 booking-card bg-white overflow-hidden booking-card-{{ $booking->id }} {{ $booking->notify ? 'border-2 border-danger' : 'border' }}">
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
                                                {{ $booking->package->name }}
                                                <span
                                                    class="badge bg-danger rounded-pill px-2 ms-1 notify-badge-{{ $booking->id }} {{ $booking->notify ? '' : 'd-none' }}"
                                                    style="font-size: 9px;">NEW UPDATE</span>
                                            </h5>
                                            <span
                                                class="badge status-badge bg-secondary-subtle text-secondary border border-secondary-subtle text-uppercase">Completed</span>
                                        </div>
                                        <div class="row my-2 text-muted small">
                                            <div class="col-sm-4">Date:
                                                <strong>{{ date('M d, Y', strtotime($booking->pickup_datetime)) }}</strong>
                                            </div>
                                            <div class="col-sm-4">Pickup:
                                                <strong>{{ date('h:i A', strtotime($booking->pickup_datetime)) }}</strong>
                                            </div>
                                            <div class="col-sm-4">Travelers: <strong>{{ $booking->pax }}
                                                    Heads</strong></div>
                                        </div>
                                        <div
                                            class="d-flex justify-content-between align-items-center pt-3 mt-3 border-top">
                                            <div><span class="text-muted small d-block">Total Price:
                                                    ₱{{ number_format($booking->total_price, 2) }}</span>
                                                <div class="fw-bold text-success">Finished</div>
                                            </div>
                                            <button class="btn btn-outline-secondary btn-sm rounded-pill"
                                                onclick="viewItineraryDetails(this)" data-id="{{ $booking->id }}"
                                                data-notify="{{ $booking->notify ? '1' : '0' }}"
                                                data-status="{{ $booking->status }}"
                                                data-title="{{ $booking->package->name }}"
                                                data-pickup="Lat: {{ $booking->latitude }}, Lng: {{ $booking->longitude }}"
                                                data-base="₱{{ number_format($booking->package->package_price, 2) }}"
                                                data-heads="₱{{ number_format($booking->package->perhead_price * $booking->pax, 2) }}"
                                                data-total="₱{{ number_format($booking->total_price, 2) }}"
                                                data-places="{{ $booking->places->toJson() }}"><i
                                                    class="bi bi-eye"></i> View Details</button>
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
                        <div class="d-flex justify-content-between mb-1 text-muted"><span>Base Tier
                                Pricing:</span><span id="detailBasePrice">₱0.00</span></div>
                        <div class="d-flex justify-content-between mb-1 text-muted"><span>Per Head Scaling:</span><span
                                id="detailHeadsPrice">₱0.00</span></div>
                        <div class="d-flex justify-content-between fw-bold text-dark border-top pt-2 fs-6">
                            <span>Estimated Overall:</span><span id="detailTotalPrice">₱0.00</span>
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

            unreadButtons.forEach(btn => {
                const id = btn.getAttribute('data-id');
                const status = btn.getAttribute('data-status');

                unreadIds.add(id);

                if (status === 'pending') pendingUnread = true;
                if (status === 'confirmed') confirmedUnread = true;
                if (status === 'completed') completedUnread = true;
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
        }

        function toggleBadgeVisibility(selector, isVisible) {
            const el = document.querySelector(selector);
            if (el) {
                if (isVisible) el.classList.remove('d-none');
                else el.classList.add('d-none');
            }
        }
    </script>
</body>

</html>
