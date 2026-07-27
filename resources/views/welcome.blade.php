<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ETRAV - Cebu Tour Packages</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <style>
        .main-wrapper {
            height: calc(100vh - 65px);
            overflow: hidden;
        }

        .sidebar-scroll {
            height: 100%;
            overflow-y: auto;
            scroll-behavior: smooth;
        }

        .map-container {
            position: relative;
            height: 100%;
            width: 100%;
        }

        #map {
            height: 100%;
            width: 100%;
            border-radius: 12px;
        }

        .spots-overlay-panel {
            position: absolute;
            bottom: 20px;
            right: 20px;
            width: 320px;
            max-height: 250px;
            overflow-y: auto;
            z-index: 1000;
            border-radius: 12px;
        }

        .spot-item-btn {
            transition: all 0.2s ease;
            text-align: left;
        }

        .spot-item-btn:hover {
            background-color: #f8f9fa !important;
            padding-left: 1rem !important;
        }

        .type-badge {
            position: absolute;
            top: 12px;
            left: 12px;
            z-index: 10;
        }

        .leaflet-popup-content-wrapper {
            padding: 0;
            overflow: hidden;
            border-radius: 14px;
        }

        .leaflet-popup-content {
            margin: 0;
            width: 260px !important;
        }

        .popup-img {
            width: 100%;
            height: 140px;
            object-fit: cover;
        }

        .highlight-card {
            border: 2px solid #0d6efd !important;
            box-shadow: 0 .5rem 1rem rgba(13, 110, 253, .15) !important;
        }

        /* Fixed Map Pin Tooltip Wrap & Alignment */
        .leaflet-tooltip.custom-pin-label {
            background: rgba(33, 37, 41, 0.95) !important;
            color: #fff !important;
            border: none !important;
            border-radius: 8px !important;
            padding: 6px 10px !important;
            font-size: 11px !important;
            font-weight: 600 !important;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15) !important;
            text-align: center !important;
            white-space: nowrap !important;
            line-height: 1.3 !important;
        }

        .leaflet-tooltip-top.custom-pin-label::before {
            border-top-color: rgba(33, 37, 41, 0.95) !important;
        }

        .leaflet-tooltip.custom-pickup-label {
            background: rgba(255, 193, 7, 0.95) !important;
            color: #212529 !important;
            border: 1px solid #ffc107 !important;
        }

        .leaflet-tooltip-top.custom-pickup-label::before {
            border-top-color: rgba(255, 193, 7, 0.95) !important;
        }

        #mapPickerInstruction {
            position: absolute;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1050;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
            pointer-events: none;
        }

        input[type=number].no-spinners::-webkit-inner-spin-button,
        input[type=number].no-spinners::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

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

        .draggable-spot-item {
            cursor: grab;
            transition: background-color 0.2s ease;
        }

        .draggable-spot-item:active {
            cursor: grabbing;
        }

        .sortable-ghost {
            opacity: 0.4;
            background-color: #e9ecef !important;
        }

        .package-card-rect {
            display: flex;
            flex-direction: column;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #e0e0e0;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .package-card-rect:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08) !important;
        }
    </style>
</head>

<body class="bg-light">

    <!-- 1. AUTHENTICATION NAVBAR -->
    <nav class="navbar navbar-expand-md navbar-light bg-white border-bottom sticky-top py-2">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold text-dark d-flex align-items-center gap-2" href="#">
                <img src="{{ asset('storage/logotext.png') }}" alt="ETRAV Logo"
                    style="height: 38px; object-fit: contain;">
            </a>

            <div class="ms-auto">
                <div class="dropdown">
                    <button
                        class="btn border-0 d-flex align-items-center gap-1 text-muted fw-medium fs-6 bg-transparent p-0 position-relative"
                        type="button" id="breezeDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <span>Hello {{ $user->name }}</span>
                        <span id="nav-red-dot"
                            class="notify-dot-absolute {{ $hasNotification ? '' : 'd-none' }}"></span>
                        <i class="bi bi-chevron-down small style-muted ms-1" style="font-size: 12px;"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border mt-2 py-1"
                        aria-labelledby="breezeDropdown" style="width: 220px; border-radius: 6px;">
                        <li><a class="dropdown-item py-2 text-muted px-4" href="#"><i
                                    class="bi bi-person me-2"></i> Profile</a></li>
                        <li>
                            <a class="dropdown-item py-2 text-muted px-4 d-flex align-items-center justify-content-between"
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

    <!-- 2. MAIN SPLIT INTERFACE -->
    <div class="container-fluid main-wrapper">
        <div class="row h-100 g-0">

            <!-- LEFT SIDEBAR -->
            <div class="col-12 col-md-3 sidebar-scroll p-3 bg-white border-end" id="packageSidebar">
                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-0 text-dark fs-6">Cebu Tour Packages</h5>
                        <p class="text-muted small mb-0" style="font-size: 11px;">Select a package or custom route.</p>
                    </div>
                    <!-- Action Buttons -->
                    <div class="d-flex gap-1.5 align-items-center">
                        <button
                            class="btn btn-sm btn-primary rounded-pill px-3 py-1.5 fw-medium shadow-sm d-flex align-items-center"
                            onclick="openCustomBookingModal()" style="font-size: 11px;">
                            <i class="bi bi-plus-lg me-1"></i> Custom
                        </button>
                        <button
                            class="btn btn-sm btn-outline-secondary rounded-pill d-none px-3 py-1.5 fw-medium d-flex align-items-center"
                            id="resetFilterBtn" onclick="resetFilters()" style="font-size: 11px;">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                        </button>
                    </div>
                </div>

                <div id="packagesContainer" class="d-flex flex-column gap-3">
                    @forelse($packages as $package)
                        @php
                            $type = $package->type ?? 'Standard';

                            $badgeStyles = match (strtolower($type)) {
                                'popular' => 'bg-danger text-white',
                                'best combo' => 'bg-warning text-dark',
                                'trending' => 'bg-primary text-white',
                                'budget friendly' => 'bg-success text-white',
                                default => 'bg-secondary text-white',
                            };

                            $tagIcon = match (strtolower($type)) {
                                'popular' => 'bi-fire',
                                'best combo' => 'bi-star-fill',
                                'trending' => 'bi-lightning-charge-fill',
                                'budget friendly' => 'bi-wallet2',
                                default => 'bi-bookmark-fill',
                            };
                        @endphp

                        <div class="card package-card-rect package-card shadow-sm bg-white"
                            data-package-id="{{ $package->id }}" id="package-card-{{ $package->id }}">
                            <div class="position-relative text-center d-flex align-items-center justify-content-center text-muted"
                                style="height: 160px; width: 100%; background-color: #f1f3f5;">
                                <span
                                    class="badge type-badge shadow-sm {{ $badgeStyles }} text-uppercase tracking-wider px-2.5 py-1.5 rounded-pill"
                                    style="font-size: 10px;">
                                    <i class="bi {{ $tagIcon }} me-1"></i> {{ $type }}
                                </span>

                                @if (!empty($package->image_path))
                                    <img src="{{ $package->image_path }}" alt="{{ $package->name }}"
                                        class="w-100 h-100" style="object-fit: cover;">
                                @else
                                    <i class="bi bi-image fs-1 opacity-25"></i>
                                @endif

                                <span
                                    class="position-absolute bottom-0 end-0 m-2 badge bg-dark px-2.5 py-1.5 rounded-pill fs-7 opacity-90">
                                    Base: ₱{{ number_format($package->package_price ?? 0) }}
                                </span>
                            </div>

                            <div class="p-3 d-flex flex-column flex-grow-1 justify-content-between">
                                <div>
                                    <h6 class="fw-bold text-dark mb-1 fs-6 text-truncate">{{ $package->name }}</h6>
                                    <p class="text-primary fw-semibold small mb-2">
                                        ₱{{ number_format($package->perhead_price ?? 0) }} <span
                                            class="text-muted fw-normal">/ per head</span>
                                    </p>
                                    <p class="text-muted mb-3"
                                        style="font-size: 12px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                        {{ $package->description ?? 'No description available for this package.' }}
                                    </p>
                                </div>

                                <div class="d-flex justify-content-between align-items-center pt-2 border-top mt-auto">
                                    <span class="text-muted font-monospace small" style="font-size: 11px;">
                                        <i class="bi bi-geo-alt-fill text-danger"></i> {{ $package->places->count() }}
                                        Spots
                                    </span>
                                    <div class="d-flex gap-1">
                                        <button
                                            class="btn btn-outline-primary btn-sm rounded-pill px-2.5 py-1 fw-medium"
                                            onclick="focusOnPackageRoute({{ $package->id }})"
                                            style="font-size: 11px;">
                                            <i class="bi bi-map"></i> View
                                        </button>
                                        <button class="btn btn-primary btn-sm rounded-pill px-3 py-1 fw-medium"
                                            onclick='openBookingModal(@json($package))'
                                            style="font-size: 11px;">
                                            Book Now
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="alert alert-info text-center py-4 rounded-4 border-0 shadow-sm">
                            <i class="bi bi-inbox fs-2 text-muted mb-2 d-block"></i>
                            <h6 class="fw-bold mb-1">No Packages Available</h6>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- RIGHT REGION: MAP VIEW -->
            <div class="col-12 col-md-9 p-3 bg-light position-relative d-none d-md-block">
                <div class="map-container">
                    <div id="mapPickerInstruction"
                        class="alert alert-warning py-2 px-3 align-items-center gap-2 d-none rounded-pill border-0"
                        role="alert">
                        <i class="bi bi-pin-map-fill text-danger"></i>
                        <span class="small fw-semibold text-dark">Click anywhere on the map or drag the gold pin to set
                            your Pickup Point!</span>
                    </div>

                    <div id="map" class="shadow-sm"></div>

                    <div id="spotsPanel" class="spots-overlay-panel card shadow border-0 bg-white d-none">
                        <div
                            class="card-header bg-dark text-white py-2 px-3 fw-bold small d-flex justify-content-between align-items-center">
                            <span>📍 Tour Spots Itinerary</span>
                            <span class="badge bg-secondary-subtle text-dark border font-monospace"
                                id="spotCount">0</span>
                        </div>
                        <div class="list-group list-group-flush" id="spotsListGroup">
                            <!-- Dynamic items via JavaScript -->
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- 3. BOOKING MODAL -->
    <div class="modal fade" id="bookingModal" data-bs-backdrop="static" tabindex="-1"
        aria-labelledby="bookingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header bg-primary text-white py-3 rounded-top-4">
                    <h5 class="modal-title fw-bold" id="bookingModalLabel">Secure Your Reservation</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form action="{{ route('booking.store') }}" method="POST" id="bookingForm"
                    onsubmit="return validateTimeLimits()">
                    @csrf
                    <input type="hidden" name="package_id" id="modalPackageId" value="{{ old('package_id') }}">
                    <input type="hidden" name="pickup_latitude" id="pickupLatitude"
                        value="{{ old('pickup_latitude') }}">
                    <input type="hidden" name="pickup_longitude" id="pickupLongitude"
                        value="{{ old('pickup_longitude') }}">
                    <input type="hidden" name="pickup_place_name" id="pickupPlaceName"
                        value="{{ old('pickup_place_name') }}">

                    <div class="modal-body p-4" style="max-height: 75vh; overflow-y: auto;">

                        @if ($errors->has('package_id') || $errors->has('pickup_latitude') || $errors->has('pickup_longitude'))
                            <div class="alert alert-danger rounded-3 p-2.5 mb-3 small" role="alert">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                <span>Please ensure a valid tour package and map pickup location are selected.</span>
                            </div>
                        @endif

                        <div class="p-3 bg-light rounded-3 mb-3">
                            <h6 class="fw-bold text-dark mb-2" id="modalPackageName">Package Name</h6>
                            <div class="d-flex flex-wrap align-items-center gap-3 text-muted small">
                                <div class="d-flex align-items-center gap-1">
                                    <i class="bi bi-tag-fill text-primary"></i>
                                    <span>Base:</span>
                                    <span id="modalBasePriceLabel" class="fw-semibold text-dark">₱0.00</span>
                                </div>
                                <div class="d-flex align-items-center gap-1">
                                    <i class="bi bi-person-fill text-primary"></i>
                                    <span>Per Head:</span>
                                    <span id="modalPerHeadLabel" class="fw-semibold text-dark">₱0.00</span>
                                </div>
                            </div>
                        </div>

                        <!-- Custom Stay Duration & Draggable Order per Stop -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label fw-semibold text-muted small mb-0">
                                    <i class="bi bi-hourglass-split me-1"></i> Custom Stay Duration per Stop (Max: 24h)
                                </label>
                                <span class="text-muted" style="font-size: 11px;"><i class="bi bi-grip-vertical"></i>
                                    Drag to reorder</span>
                            </div>
                            <div class="border rounded-3 p-3 bg-light-subtle" id="modalItineraryContainer"
                                style="max-height: 220px; overflow-y: auto;">
                                <!-- Dynamic draggable rows injected via JS -->
                            </div>
                            <div id="durationErrorMessage" class="text-danger small mt-1 d-none"
                                style="font-size: 11px;">
                                Total custom duration across all stops cannot exceed 24 hours.
                            </div>
                            @error('duration_hrs.*')
                                <div class="text-danger small mt-1" style="font-size: 11px;">{{ $message }}</div>
                            @enderror
                            @error('duration_mins.*')
                                <div class="text-danger small mt-1" style="font-size: 11px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Pickup Location -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted small d-block">Pickup Location</label>
                            <div
                                class="p-2.5 border rounded-3 bg-white d-flex align-items-center justify-content-between @if ($errors->has('pickup_latitude') || $errors->has('pickup_longitude')) border-danger @endif">
                                <div class="d-flex align-items-center gap-2 overflow-hidden me-2"
                                    style="min-width: 0;">
                                    <i class="bi bi-geo-alt-fill text-warning fs-5 flex-shrink-0"></i>
                                    <span class="small text-muted text-truncate d-inline-block"
                                        id="pickupCoordinatesPlaceholder" style="max-width: 240px;"
                                        title="No pickup location selected on map">
                                        @if (old('pickup_latitude') && old('pickup_longitude'))
                                            Lat: {{ old('pickup_latitude') }}, Lng: {{ old('pickup_longitude') }}
                                        @else
                                            No pickup location selected on map
                                        @endif
                                    </span>
                                </div>
                                <button type="button"
                                    class="btn btn-sm btn-outline-primary rounded-pill flex-shrink-0"
                                    onclick="startPickupMapMapping()">
                                    <i class="bi bi-pin-map"></i> Choose on Map
                                </button>
                            </div>
                            @error('pickup_latitude')
                                <div class="text-danger small mt-1" style="font-size: 11px;">Please select a pickup point
                                    on the map.</div>
                            @enderror
                        </div>

                        <!-- Date & Time -->
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label for="pickupDate" class="form-label fw-semibold text-muted small">Pickup
                                    Date</label>
                                <input type="date" name="pickup_date" id="pickupDate"
                                    class="form-control text-muted @error('pickup_date') is-invalid @enderror"
                                    value="{{ old('pickup_date') }}" required min="{{ date('Y-m-d') }}">
                                @error('pickup_date')
                                    <div class="invalid-feedback" style="font-size: 11px;">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-6">
                                <label for="pickupTime" class="form-label fw-semibold text-muted small">Pickup
                                    Time</label>
                                <input type="time" name="pickup_time" id="pickupTime"
                                    class="form-control text-muted @error('pickup_time') is-invalid @enderror"
                                    value="{{ old('pickup_time') }}" required>
                                @error('pickup_time')
                                    <div class="invalid-feedback" style="font-size: 11px;">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Number of Heads -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label for="numberHeads" class="form-label fw-semibold text-muted small mb-0">Number
                                    of Heads</label>
                                <span
                                    class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1">
                                    <i class="bi bi-people-fill me-1"></i> Max: <span id="modalPaxLimitLabel">0
                                        pax</span>
                                </span>
                            </div>
                            <div class="input-group has-validation">
                                <span class="input-group-text bg-white border-end-0"><i
                                        class="bi bi-people text-primary"></i></span>
                                <input type="number" name="number_of_heads" id="numberHeads"
                                    class="form-control border-start-0 @error('number_of_heads') is-invalid @enderror"
                                    value="{{ old('number_of_heads', 1) }}" required min="1"
                                    oninput="calculateTotal()">
                                @error('number_of_heads')
                                    <div class="invalid-feedback" style="font-size: 11px;">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- JOINER / OPEN GROUP OPTION -->
                        <div class="p-3 border rounded-3 bg-light-subtle mb-3">
                            <div class="form-check form-switch mb-1">
                                <input class="form-check-input" type="checkbox" name="allow_joiners"
                                    id="allowJoinersCheck" value="1" {{ old('allow_joiners') ? 'checked' : '' }}
                                    onchange="calculateTotal()">
                                <label class="form-check-label fw-semibold text-dark small" for="allowJoinersCheck">
                                    <i class="bi bi-people-fill text-primary me-1"></i> Allow Joiners / Open Tour Group
                                </label>
                            </div>
                            <small class="d-block text-muted" style="font-size: 11px;">
                                <strong>On:</strong> Pay only for your group size ((Base Price / Max Pax) &times; Your
                                Heads). Other joiners can book remaining slots.<br>
                                <strong>Off:</strong> Private Tour. You pay the full package price regardless of your
                                group size.
                            </small>
                        </div>

                        <div class="bg-light p-3 rounded-3 mb-3" style="font-size: 14px;">
                            <div class="d-flex justify-content-between mb-1.5 text-muted">
                                <span>Booking Type:</span>
                                <span id="breakdownBase">Private Tour (Full Base)</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1.5 text-muted">
                                <span>Rate Breakdown:</span>
                                <span id="breakdownHeads">1 head(s)</span>
                            </div>
                            <div
                                class="d-flex justify-content-between mb-2 text-primary fw-semibold bg-primary-subtle p-2 rounded-2">
                                <span><i class="bi bi-person-fill me-1"></i> Cost Per Person to Pay:</span>
                                <span id="breakdownPerPerson">₱0.00 / person</span>
                            </div>
                            <div class="d-flex justify-content-between border-top pt-2 fw-bold text-dark fs-6 mb-3">
                                <span>Estimated Total (Group):</span>
                                <span id="modalTotalPrice">₱0.00</span>
                            </div>

                            <div
                                class="d-flex justify-content-between align-items-center border-top border-2 border-primary-subtle pt-2">
                                <div class="text-primary fw-bold">
                                    <span>25% Booking Deposit:</span>
                                    <small class="d-block text-muted fw-normal" style="font-size: 11px;">Required to
                                        confirm reservation</small>
                                </div>
                                <span class="fs-4 fw-black text-primary fw-bold"
                                    id="modalDownpaymentPrice">₱0.00</span>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4 py-2 text-muted fw-medium"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 fw-medium">Proceed to
                            Payment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Leaflet Map JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <!-- SortableJS for Drag-and-Drop functionality -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    <script>
        var map = L.map('map').setView([10.3157, 123.8854], 10);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        var currentMarkers = [];
        var activeRouteLine = null;

        var pickupMappingModeActive = false;
        var livePickupMarker = null;
        var bsModalInstance = null;
        var sortableItineraryInstance = null;

        let activeBasePrice = 0;
        let activePaxLimit = 10;

        const packageData = {};
        @foreach ($packages as $package)
            packageData[{{ $package->id }}] = {
                id: {{ $package->id }},
                name: {!! json_encode($package->name) !!},
                package_price: {{ $package->package_price ?? 0 }},
                max_pax: {{ $package->max_pax ?? ($package->pax_limit ?? ($package->capacity ?? 10)) }},
                spots: [
                    @foreach ($package->places as $place)
                        {
                            id: {{ $place->id }},
                            name: {!! json_encode($place->name) !!},
                            description: {!! json_encode($place->description ?? '') !!},
                            duration: "{{ str_contains(strtolower($place->name), 'oslob') ? '3-4 Hours' : '1-2 Hours' }}",
                            image: {!! json_encode($place->image_path ?? '') !!},
                            lat: {{ $place->latitude ?? ($place->lat ?? 0) }},
                            lng: {{ $place->longitude ?? ($place->lng ?? 0) }}
                        },
                    @endforeach
                ]
            };
        @endforeach

        function loadAllGlobalPins() {
            clearMapLayers();
            let bounds = [];

            Object.keys(packageData).forEach(packageId => {
                const currentPackage = packageData[packageId];

                currentPackage.spots.forEach((spot) => {
                    const imgUrl = spot.image ? spot.image :
                        'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?q=80&w=400';

                    const popupContent = `
                        <div class="card border-0">
                            <img src="${imgUrl}" class="popup-img" alt="${spot.name}">
                            <div class="p-3">
                                <h6 class="fw-bold mb-1 text-dark">${spot.name}</h6>
                                <div class="mb-2"><span class="badge bg-warning text-dark"><i class="bi bi-clock-history"></i> Est: ${spot.duration}</span></div>
                                <p class="text-muted small mb-1">${spot.description || 'No summary overview provided.'}</p>
                                <span class="badge bg-dark rounded-pill" style="font-size: 10px;">Part of: ${currentPackage.name}</span>
                            </div>
                        </div>
                    `;

                    var marker = L.marker([spot.lat, spot.lng]).addTo(map).bindPopup(popupContent);
                    marker.bindTooltip(spot.name, {
                        permanent: true,
                        direction: 'top',
                        className: 'custom-pin-label',
                        offset: [-15, -15]
                    });

                    marker.on('click', function() {
                        if (!pickupMappingModeActive) {
                            filterSidebarByPackage(currentPackage.id);
                        }
                    });

                    currentMarkers.push(marker);
                    bounds.push([spot.lat, spot.lng]);
                });
            });

            if (bounds.length > 0 && !livePickupMarker) {
                map.fitBounds(bounds, {
                    padding: [50, 50]
                });
            }
        }

        function clearMapLayers() {
            currentMarkers.forEach(marker => map.removeLayer(marker));
            currentMarkers = [];
            if (activeRouteLine) {
                map.removeLayer(activeRouteLine);
                activeRouteLine = null;
            }
        }

        function focusOnPackageRoute(packageId) {
            clearMapLayers();
            const selectedPackage = packageData[packageId];
            if (!selectedPackage) return;

            const listGroup = document.getElementById('spotsListGroup');
            listGroup.innerHTML = '';

            let bounds = [];
            let routeCoordinates = [];

            selectedPackage.spots.forEach((spot, index) => {
                const imgUrl = spot.image ? spot.image :
                    'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?q=80&w=400';

                const popupContent = `
                    <div class="card border-0">
                        <img src="${imgUrl}" class="popup-img" alt="${spot.name}">
                        <div class="p-3">
                            <h6 class="fw-bold mb-1 text-dark">${spot.name}</h6>
                            <div class="mb-1"><span class="badge bg-warning text-dark"><i class="bi bi-clock-history"></i> Est: ${spot.duration}</span></div>
                            <p class="text-muted small mb-0">${spot.description || 'No summary overview provided.'}</p>
                            <span class="badge bg-primary-subtle text-primary rounded-pill mt-2 font-monospace">Stop #${index + 1}</span>
                        </div>
                    </div>
                `;

                var marker = L.marker([spot.lat, spot.lng]).addTo(map).bindPopup(popupContent);
                marker.bindTooltip(`Stop ${index + 1}: ${spot.name}`, {
                    permanent: true,
                    direction: 'top',
                    className: 'custom-pin-label',
                    offset: [0, -5]
                });

                currentMarkers.push(marker);
                bounds.push([spot.lat, spot.lng]);
                routeCoordinates.push([spot.lat, spot.lng]);

                const btn = document.createElement('button');
                btn.className =
                    "list-group-item list-group-item-action spot-item-btn border-0 py-2 px-3 small bg-white text-muted fw-medium d-flex justify-content-between align-items-center";
                btn.innerHTML = `
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary-subtle text-primary rounded-circle font-monospace">${index + 1}</span> 
                        <span>${spot.name}</span>
                    </div>
                    <span class="text-muted font-monospace" style="font-size:11px;"><i class="bi bi-clock"></i> ${spot.duration}</span>
                `;

                btn.onclick = function() {
                    map.setView([spot.lat, spot.lng], 16);
                    marker.openPopup();
                };
                listGroup.appendChild(btn);
            });

            if (routeCoordinates.length > 1) {
                activeRouteLine = L.polyline(routeCoordinates, {
                    color: '#0d6efd',
                    weight: 4,
                    opacity: 0.75,
                    dashArray: '8, 8',
                    lineJoin: 'round'
                }).addTo(map);
            }

            document.getElementById('spotCount').innerText = selectedPackage.spots.length;
            document.getElementById('spotsPanel').classList.remove('d-none');
            document.getElementById('resetFilterBtn').classList.remove('d-none');

            if (bounds.length > 0) {
                map.fitBounds(bounds, {
                    padding: [50, 50]
                });
            }
        }

        function filterSidebarByPackage(packageId) {
            const cardsContainer = document.getElementById('packagesContainer');
            const cards = Array.from(document.querySelectorAll('.package-card'));

            cards.forEach(card => {
                const cardId = parseInt(card.getAttribute('data-package-id'));
                if (cardId === packageId) {
                    card.classList.add('highlight-card');
                    cardsContainer.prepend(card);
                    document.getElementById('packageSidebar').scrollTop = 0;
                } else {
                    card.classList.remove('highlight-card');
                }
            });

            document.getElementById('resetFilterBtn').classList.remove('d-none');
            focusOnPackageRoute(packageId);
        }

        function resetFilters() {
            document.getElementById('resetFilterBtn').classList.add('d-none');
            document.getElementById('spotsPanel').classList.add('d-none');

            document.querySelectorAll('.package-card').forEach(card => {
                card.classList.remove('highlight-card');
            });

            const cardsContainer = document.getElementById('packagesContainer');
            const cards = Array.from(document.querySelectorAll('.package-card'));
            cards.sort((a, b) => parseInt(a.getAttribute('data-package-id')) - parseInt(b.getAttribute('data-package-id')));
            cards.forEach(card => cardsContainer.appendChild(card));

            loadAllGlobalPins();
        }

        function startPickupMapMapping() {
            if (bsModalInstance) {
                bsModalInstance.hide();
            }
            pickupMappingModeActive = true;
            document.getElementById('mapPickerInstruction').classList.remove('d-none');
            document.getElementById('mapPickerInstruction').classList.add('d-flex');
        }

        map.on('click', function(e) {
            if (!pickupMappingModeActive) return;
            updatePickupPointerPosition(e.latlng.lat, e.latlng.lng);
        });

        function updatePickupPointerPosition(lat, lng) {
            var pickupIcon = L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-gold.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });

            if (livePickupMarker) {
                livePickupMarker.setLatLng([lat, lng]);
            } else {
                livePickupMarker = L.marker([lat, lng], {
                    icon: pickupIcon,
                    draggable: true
                }).addTo(map);
                livePickupMarker.bindTooltip("Your Selected Pickup Point", {
                    permanent: true,
                    direction: 'top',
                    className: 'custom-pin-label custom-pickup-label',
                    offset: [0, -50]
                }).openTooltip();

                livePickupMarker.on('dragend', function(event) {
                    var marker = event.target;
                    var position = marker.getLatLng();
                    saveSelectedPickupCoordinates(position.lat, position.lng);
                });
            }

            saveSelectedPickupCoordinates(lat, lng);

            setTimeout(() => {
                pickupMappingModeActive = false;
                document.getElementById('mapPickerInstruction').classList.add('d-none');
                document.getElementById('mapPickerInstruction').classList.remove('d-flex');
                if (bsModalInstance) {
                    bsModalInstance.show();
                }
            }, 600);
        }

        function saveSelectedPickupCoordinates(lat, lng) {
            document.getElementById('pickupLatitude').value = lat.toFixed(6);
            document.getElementById('pickupLongitude').value = lng.toFixed(6);

            fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`)
                .then(response => response.json())
                .then(data => {
                    let placeName = "";
                    if (data.address.amenity) placeName += data.address.amenity + ", ";
                    if (data.address.suburb) placeName += data.address.suburb + ", ";
                    if (data.address.city) placeName += data.address.city + ", ";
                    if (data.address.state) placeName += data.address.state + ", ";

                    const labelEl = document.getElementById('pickupCoordinatesPlaceholder');
                    const formattedText = placeName ? placeName.replace(/, $/, '') : "Location Selected";
                    labelEl.innerText = formattedText;
                    labelEl.title = formattedText;
                    document.getElementById('pickupPlaceName').value = formattedText;
                })
                .catch(error => {
                    console.error('Error fetching place name:', error);
                });
        }

        function openCustomBookingModal() {
            const customPackage = {
                id: 0,
                name: "Custom Tour Package",
                package_price: 3500,
                max_pax: 10,
                spots: []
            };
            openBookingModal(customPackage);
            document.getElementById('bookingModalLabel').innerText = "Create Your Custom Booking";
        }

        function updateItineraryNumbers() {
            const items = document.querySelectorAll('#modalItineraryContainer .draggable-spot-item');
            items.forEach((item, idx) => {
                const numBadge = item.querySelector('.spot-number');
                if (numBadge) numBadge.innerText = `${idx + 1}.`;

                const posInput = item.querySelector('.spot-position-input');
                if (posInput) posInput.value = idx + 1;
            });
        }

        function validateTimeLimits() {
            let totalMinutes = 0;

            const hrInputs = document.querySelectorAll('input[name^="duration_hrs"]');
            const minInputs = document.querySelectorAll('input[name^="duration_mins"]');

            hrInputs.forEach(input => {
                totalMinutes += (parseInt(input.value) || 0) * 60;
            });

            minInputs.forEach(input => {
                totalMinutes += (parseInt(input.value) || 0);
            });

            const errorEl = document.getElementById('durationErrorMessage');
            if (totalMinutes > 1440) {
                errorEl.classList.remove('d-none');
                return false;
            } else {
                errorEl.classList.add('d-none');
                return true;
            }
        }

        function openBookingModal(packageObj) {
            document.getElementById('bookingModalLabel').innerText = "Secure Your Reservation";
            document.getElementById('modalPackageId').value = packageObj.id || 0;
            document.getElementById('modalPackageName').innerText = packageObj.name || 'Tour Package';

            activeBasePrice = parseFloat(packageObj.package_price) || 0;

            const rawPax = packageObj.max_pax || packageObj.pax_limit || packageObj.pax || packageObj.capacity;
            activePaxLimit = parseInt(rawPax) > 0 ? parseInt(rawPax) : 10;

            document.getElementById('modalPaxLimitLabel').innerText = `${activePaxLimit} pax`;

            document.getElementById('allowJoinersCheck').checked = false;
            const headsInput = document.getElementById('numberHeads');
            headsInput.value = 1;
            headsInput.setAttribute('max', activePaxLimit);
            headsInput.setAttribute('min', 1);

            const itineraryContainer = document.getElementById('modalItineraryContainer');
            itineraryContainer.innerHTML = '';

            const targetedPackageData = packageData[packageObj.id];
            if (targetedPackageData && targetedPackageData.spots && targetedPackageData.spots.length > 0) {
                targetedPackageData.spots.forEach((spot, index) => {
                    const row = document.createElement('div');
                    row.className =
                        "row g-2 align-items-center mb-3 pb-2 border-bottom draggable-spot-item bg-white p-2 rounded-2 shadow-sm";
                    row.innerHTML = `
                        <input type="hidden" name="position[${spot.id}]" class="spot-position-input" value="${index + 1}">
                        <div class="col-6 d-flex align-items-center gap-1 overflow-hidden">
                            <i class="bi bi-grip-vertical text-muted fs-5 flex-shrink-0 drag-handle" style="cursor: grab;"></i>
                            <span class="fw-bold text-primary spot-number flex-shrink-0">${index + 1}.</span> 
                            <div class="text-truncate">
                                <span class="text-dark fw-semibold small d-block text-truncate">${spot.name}</span>
                                <small class="text-muted d-block" style="font-size: 10px;"><i class="bi bi-info-circle"></i> ${spot.duration}</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="input-group input-group-sm">
                                <input type="number" name="duration_hrs[${spot.id}]" class="form-control text-center px-1 no-spinners" placeholder="0" min="0" max="24" required oninput="validateTimeLimits()">
                                <span class="input-group-text bg-white text-muted px-2" style="font-size: 11px;">hrs</span>
                                <input type="number" name="duration_mins[${spot.id}]" class="form-control text-center px-1 no-spinners" placeholder="0" min="0" max="59" required oninput="validateTimeLimits()">
                                <span class="input-group-text bg-white text-muted px-2" style="font-size: 11px;">mins</span>
                            </div>
                        </div>
                    `;
                    itineraryContainer.appendChild(row);
                });

                if (sortableItineraryInstance) {
                    sortableItineraryInstance.destroy();
                }
                sortableItineraryInstance = new Sortable(itineraryContainer, {
                    animation: 150,
                    handle: '.drag-handle',
                    ghostClass: 'sortable-ghost',
                    onEnd: function() {
                        updateItineraryNumbers();
                    }
                });

            } else {
                itineraryContainer.innerHTML =
                    `<div class="text-center text-muted py-2 small">Custom itinerary or standard route stops.</div>`;
            }

            calculateTotal();

            if (!bsModalInstance) {
                bsModalInstance = new bootstrap.Modal(document.getElementById('bookingModal'));
            }
            bsModalInstance.show();
        }

        function calculateTotal() {
            const headsInput = document.getElementById('numberHeads');
            let headsCount = parseInt(headsInput.value) || 1;
            const isJoinerAllowed = document.getElementById('allowJoinersCheck').checked;

            if (headsCount < 1) {
                headsCount = 1;
                headsInput.value = 1;
            } else if (headsCount > activePaxLimit) {
                headsCount = activePaxLimit;
                headsInput.value = activePaxLimit;
            }

            const perHeadRate = activeBasePrice / (activePaxLimit || 1);

            let totalToPay = 0;
            let costPerPerson = 0;

            if (isJoinerAllowed) {
                totalToPay = perHeadRate * headsCount;
                costPerPerson = perHeadRate;
            } else {
                totalToPay = activeBasePrice;
                costPerPerson = activeBasePrice / headsCount;
            }

            const downpaymentRequired = totalToPay * 0.25;
            const formatCurrency = (val) =>
                `₱${val.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;

            document.getElementById('modalPerHeadLabel').innerText = formatCurrency(perHeadRate);
            document.getElementById('modalBasePriceLabel').innerText = formatCurrency(activeBasePrice);

            document.getElementById('breakdownBase').innerText = isJoinerAllowed ?
                `Joiner Mode (${headsCount}/${activePaxLimit} slots)` :
                'Private Tour (Full Base)';

            document.getElementById('breakdownHeads').innerText = isJoinerAllowed ?
                `${headsCount} head(s) @ ${formatCurrency(perHeadRate)}/head` :
                `${headsCount} head(s) splitting ${formatCurrency(activeBasePrice)}`;

            document.getElementById('breakdownPerPerson').innerText = `${formatCurrency(costPerPerson)} / person`;
            document.getElementById('modalTotalPrice').innerText = formatCurrency(totalToPay);
            document.getElementById('modalDownpaymentPrice').innerText = formatCurrency(downpaymentRequired);
        }

        window.onload = function() {
            loadAllGlobalPins();
        };

        @if ($errors->any())
            document.addEventListener("DOMContentLoaded", function() {
                const failedPackageId = "{{ old('package_id', 0) }}";
                const targetPackage = packageData[failedPackageId] || {
                    id: 0,
                    name: "Custom Tour Package",
                    package_price: 3500,
                    max_pax: 10,
                    spots: []
                };

                openBookingModal(targetPackage);
            });
        @endif
    </script>
</body>

</html>
