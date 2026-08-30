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

        /* Custom Dynamic Category Map Marker Pins */
        .custom-category-pin {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            color: white;
            font-size: 16px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            border: 2px solid white;
            transition: transform 0.2s ease;
        }

        .custom-category-pin:hover {
            transform: scale(1.15);
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
                        <i class="bi bi-person-circle fs-5 text-secondary me-1"></i>
                        <span>Hello {{ $user->name }}</span>
                        <span id="nav-red-dot"
                            class="notify-dot-absolute {{ $hasNotification ? '' : 'd-none' }}"></span>
                        <i class="bi bi-chevron-down small style-muted ms-1" style="font-size: 12px;"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border mt-2 py-1"
                        aria-labelledby="breezeDropdown" style="width: 220px; border-radius: 6px;">
                        <li><a class="dropdown-item py-2 text-muted px-4 d-flex align-items-center" href="#"><i
                                    class="bi bi-person me-2 fs-6"></i> Profile</a></li>
                        <li>
                            <a class="dropdown-item py-2 text-muted px-4 d-flex align-items-center justify-content-between"
                                href="{{ route('bookings.view') }}">
                                <span class="d-flex align-items-center"><i class="bi bi-journal-bookmark me-2 fs-6"></i>
                                    Bookings</span>
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
                                <button type="submit"
                                    class="dropdown-item py-2 text-danger px-4 fw-medium d-flex align-items-center"><i
                                        class="bi bi-box-arrow-right me-2 fs-6"></i> Log Out</button>
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
            <div class="col-12 col-md-4 sidebar-scroll p-0 bg-white border-end d-flex flex-column" style="height: calc(100vh - 55px);">
                <div class="p-3 bg-primary text-white shadow-sm z-1">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h5 class="fw-bold mb-0 d-flex align-items-center gap-2">
                            <i class="bi bi-shield-check fs-5"></i> Secure Your Reservation
                        </h5>
                        <a href="{{ route('home') }}" class="btn btn-sm btn-light rounded-pill px-3 py-1 fw-medium text-primary">
                            <i class="bi bi-arrow-left"></i> Back
                        </a>
                    </div>
                    <p class="small mb-0 opacity-75">Complete the form below to finalize your booking.</p>
                </div>
                
                <form action="{{ route('booking.store') }}" method="POST" id="bookingForm" class="flex-grow-1 overflow-auto p-3 position-relative">
                    @csrf
                    <input type="hidden" name="package_id" id="modalPackageId" value="{{ $package->id ?? old('package_id') }}">
                    <input type="hidden" name="pickup_latitude" id="pickupLatitude" value="{{ old('pickup_latitude') }}">
                    <input type="hidden" name="pickup_longitude" id="pickupLongitude" value="{{ old('pickup_longitude') }}">
                    <input type="hidden" name="pickup_place_name" id="pickupPlaceName" value="{{ old('pickup_place_name') }}">

                    @if ($errors->has('package_id') || $errors->has('pickup_latitude') || $errors->has('pickup_longitude'))
                        <div class="alert alert-danger rounded-3 p-2.5 mb-3 small d-flex align-items-center gap-2" role="alert">
                            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                            <span>Please ensure a valid tour package and map pickup location are selected.</span>
                        </div>
                    @endif

                    <div class="p-3 bg-light border rounded-3 mb-3">
                        <h6 class="fw-bold text-dark mb-2 d-flex align-items-center gap-2" id="modalPackageName">
                            <i class="bi bi-box-seam text-primary"></i> {{ $package->name ?? 'Tour Package' }}
                        </h6>
                        <div class="d-flex flex-wrap align-items-center gap-3 text-muted small">
                            <div class="d-flex align-items-center gap-1">
                                <i class="bi bi-tag-fill text-primary"></i>
                                <span>Base:</span>
                                <span id="modalBasePriceLabel" class="fw-semibold text-dark">₱{{ number_format($package->package_price ?? 0, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3 p-3 bg-white border rounded-3 shadow-sm" id="standardSpotsInfo">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold text-dark small mb-0"><i class="bi bi-geo-alt-fill text-danger me-1"></i> Included Stops (Draggable):</h6>
                        </div>
                        <div id="modalItineraryContainer" class="d-flex flex-column gap-2">
                            <!-- Spots will be injected here via JS to allow dragging -->
                        </div>
                    </div>

                    <!-- Pickup Location -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted small d-block">
                            <i class="bi bi-geo-alt me-1 text-primary"></i> Pickup Location
                        </label>
                        <div class="p-2.5 border rounded-3 bg-white d-flex align-items-center justify-content-between @if ($errors->has('pickup_latitude') || $errors->has('pickup_longitude')) border-danger @endif">
                            <div class="d-flex align-items-center gap-2 overflow-hidden me-2" style="min-width: 0;">
                                <i class="bi bi-geo-alt-fill text-warning fs-5 flex-shrink-0"></i>
                                <span class="small text-muted text-truncate d-inline-block" id="pickupCoordinatesPlaceholder" style="max-width: 240px;" title="No pickup location selected on map">
                                    @if (old('pickup_latitude') && old('pickup_longitude'))
                                        Lat: {{ old('pickup_latitude') }}, Lng: {{ old('pickup_longitude') }}
                                    @else
                                        Choose on the map
                                    @endif
                                </span>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill flex-shrink-0 d-flex align-items-center gap-1" onclick="startPickupMapMapping()">
                                <i class="bi bi-pin-map"></i> Set
                            </button>
                        </div>
                        @error('pickup_latitude')
                            <div class="text-danger small mt-1 d-flex align-items-center gap-1" style="font-size: 11px;">
                                <i class="bi bi-exclamation-circle-fill"></i> Please select a pickup point on the map.
                            </div>
                        @enderror
                    </div>

                    <!-- Date & Time -->
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label for="pickupDate" class="form-label fw-semibold text-muted small"><i class="bi bi-calendar3 me-1 text-primary"></i> Date</label>
                            <input type="date" name="pickup_date" id="pickupDate" class="form-control form-control-sm text-muted @error('pickup_date') is-invalid @enderror" value="{{ old('pickup_date') }}" required min="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-6">
                            <label for="pickupTime" class="form-label fw-semibold text-muted small"><i class="bi bi-clock me-1 text-primary"></i> Time</label>
                            <input type="time" name="pickup_time" id="pickupTime" class="form-control form-control-sm text-muted @error('pickup_time') is-invalid @enderror" value="{{ old('pickup_time') }}" required>
                        </div>
                    </div>

                    <!-- Select Vehicle -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted small">
                            <i class="bi bi-car-front me-1 text-primary"></i> Transport Vehicle
                        </label>
                        <input type="hidden" name="vehicle_id" id="vehicleSelect" value="{{ old('vehicle_id') }}" required>
                        <div class="p-2 border rounded-3 bg-white d-flex align-items-center justify-content-between @error('vehicle_id') border-danger @enderror" id="selectedVehicleDisplayBox">
                            <div class="d-flex align-items-center gap-2 overflow-hidden me-2" style="min-width: 0;">
                                <div id="selectedVehicleImg" class="rounded bg-light d-flex align-items-center justify-content-center text-muted overflow-hidden" style="width: 36px; height: 36px; flex-shrink: 0;">
                                    <i class="bi bi-car-front fs-6"></i>
                                </div>
                                <div class="d-flex flex-column">
                                    <span class="small fw-bold text-dark text-truncate" id="selectedVehicleName">No vehicle selected</span>
                                    <span class="text-muted" style="font-size: 10px;" id="selectedVehicleDetails">Click choose to browse</span>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill flex-shrink-0 d-flex align-items-center gap-1" style="font-size: 11px;" onclick="openVehicleModal()">
                                <i class="bi bi-search"></i> Choose
                            </button>
                        </div>
                    </div>

                    <!-- Number of Heads -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label for="numberHeads" class="form-label fw-semibold text-muted small mb-0"><i class="bi bi-people me-1 text-primary"></i> Number of Heads</label>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-1" style="font-size: 10px;">
                                <i class="bi bi-people-fill me-1"></i> Max: <span id="modalPaxLimitLabel">0 pax</span>
                            </span>
                        </div>
                        <div class="input-group input-group-sm has-validation">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-person-plus text-primary"></i></span>
                            <input type="number" name="number_of_heads" id="numberHeads" class="form-control border-start-0 @error('number_of_heads') is-invalid @enderror" value="{{ old('number_of_heads', 1) }}" required min="1" oninput="calculateTotal()">
                        </div>
                    </div>

                    <!-- JOINER / OPEN GROUP OPTION -->
                    <div class="p-2 border rounded-3 bg-light-subtle mb-3">
                        <div class="form-check form-switch mb-1">
                            <input class="form-check-input" type="checkbox" name="allow_joiners" id="allowJoinersCheck" value="1" {{ old('allow_joiners') ? 'checked' : '' }} onchange="calculateTotal()">
                            <label class="form-check-label fw-semibold text-dark small" for="allowJoinersCheck">
                                <i class="bi bi-people-fill text-primary me-1"></i> Allow Joiners / Open Group
                            </label>
                        </div>
                    </div>

                    <div class="bg-light p-3 rounded-3 mb-4" style="font-size: 13px;">
                        <div class="d-flex justify-content-between mb-1 text-muted">
                            <span><i class="bi bi-info-circle me-1"></i> Type:</span>
                            <span id="breakdownBase">Private Tour</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1 text-muted">
                            <span><i class="bi bi-calculator me-1"></i> Breakdown:</span>
                            <span id="breakdownHeads">1 head</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 text-primary fw-semibold bg-primary-subtle p-2 rounded-2" style="font-size: 12px;">
                            <span><i class="bi bi-person-fill me-1"></i> Cost Per Person:</span>
                            <span id="breakdownPerPerson">₱0.00 / person</span>
                        </div>
                        <div class="d-flex justify-content-between border-top pt-2 fw-bold text-dark fs-6 mb-2">
                            <span>Total (Group):</span>
                            <span id="modalTotalPrice">₱0.00</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center border-top pt-2">
                            <div class="text-primary fw-bold" style="font-size: 12px;">
                                <span>25% Deposit:</span>
                            </div>
                            <span class="fs-5 fw-black text-primary fw-bold" id="modalDownpaymentPrice">₱0.00</span>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-medium d-flex align-items-center justify-content-center gap-2 shadow-sm sticky-bottom" style="bottom: 0;">
                        <i class="bi bi-credit-card"></i> Proceed to Payment
                    </button>
                </form>
            </div>

            <!-- RIGHT REGION: MAP VIEW -->
            <div class="col-12 col-md-8 p-3 bg-light position-relative d-none d-md-block">
                <div class="map-container">
                    <!-- Map Custom Control for adding pins -->
                    <div class="dropdown" style="position: absolute; top: 12px; right: 12px; z-index: 1000;">
                        <button type="button" class="btn btn-sm btn-light border shadow-sm dropdown-toggle fw-bold" style="border-radius: 8px;" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-pin-map-fill text-success me-1"></i> Add Stop
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="font-size: 13px; min-width: 160px; z-index: 1001;">
                            <li><h6 class="dropdown-header">Custom Itinerary</h6></li>
                            <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); addCustomStop('swimming')"><i class="bi bi-water text-info me-2"></i>Swimming</a></li>
                            <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); addCustomStop('mountain')"><i class="bi bi-tree-fill text-success me-2"></i>Mountain</a></li>
                            <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); addCustomStop('restaurant')"><i class="bi bi-cup-hot-fill text-warning me-2"></i>Restaurant</a></li>
                            <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); addCustomStop('terminal')"><i class="bi bi-bus-front-fill me-2" style="color: #6f42c1;"></i>Terminal</a></li>
                            <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); addCustomStop('water falls')"><i class="bi bi-tsunami text-primary me-2"></i>Water Falls</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); addCustomStop('custom')"><i class="bi bi-pin-map-fill text-danger me-2"></i>Other</a></li>
                        </ul>
                    </div>

                    <div id="mapPickerInstruction"
                        class="alert alert-warning py-2 px-3 align-items-center gap-2 d-none rounded-pill border-0"
                        role="alert">
                        <i class="bi bi-pin-map-fill text-danger fs-5"></i>
                        <span class="small fw-semibold text-dark">Click anywhere on the map or drag the gold pin to set
                            your Pickup Point!</span>
                    </div>

                    <div id="map" class="shadow-sm"></div>

                    <div id="spotsPanel" class="spots-overlay-panel card shadow border-0 bg-white d-none">
                        <div
                            class="card-header bg-dark text-white py-2 px-3 fw-bold small d-flex justify-content-between align-items-center">
                            <span class="d-flex align-items-center gap-1"><i
                                    class="bi bi-pin-angle-fill text-warning"></i> Tour Spots Itinerary</span>
                            <span class="badge bg-secondary-subtle text-dark border font-monospace"
                                id="spotCount">0</span>
                        </div>
                        <div class="list-group list-group-flush" id="spotsListGroup">
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>



    <!-- Vehicle Selection Modal -->
    <div class="modal fade" id="vehicleSelectionModal" tabindex="-1" aria-labelledby="vehicleSelectionModalLabel" aria-hidden="true" style="z-index: 1060; background: rgba(0,0,0,0.6);">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header bg-light border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2" id="vehicleSelectionModalLabel">
                        <i class="bi bi-car-front-fill text-primary fs-5"></i> Choose Vehicle
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="input-group mb-3 shadow-sm rounded-pill overflow-hidden border">
                        <span class="input-group-text bg-white border-0 ps-3"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" class="form-control border-0 shadow-none" id="vehicleSearchInput" placeholder="Search by model or capacity..." onkeyup="filterVehicles()">
                    </div>
                    
                    @php
                        $brands = $vehicles->pluck('brand')->unique()->filter();
                    @endphp
                    @if($brands->isNotEmpty())
                    <div class="d-flex gap-2 overflow-x-auto pb-2 mb-3 brand-filters" style="scroll-snap-type: x mandatory;">
                        <button type="button" class="btn btn-sm btn-primary rounded-pill flex-shrink-0 brand-filter-btn" onclick="filterByBrand(this, '')">All</button>
                        @foreach($brands as $brand)
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill flex-shrink-0 brand-filter-btn" onclick="filterByBrand(this, '{{ strtolower($brand) }}')">{{ $brand }}</button>
                        @endforeach
                    </div>
                    @endif
                    
                    <div id="vehicleListContainer" class="d-flex flex-column gap-3">
                        @foreach($vehicles as $vehicle)
                            <div class="card border rounded-4 vehicle-item-row overflow-hidden" style="cursor: pointer; transition: 0.2s;" onclick="selectVehicleFromModal({{ $vehicle->id }})">
                                <div class="row g-0">
                                    <div class="col-4 bg-light d-flex align-items-center justify-content-center">
                                        @if($vehicle->front_image_path || $vehicle->image_path)
                                            <img src="{{ asset('storage/' . ($vehicle->front_image_path ?? $vehicle->image_path)) }}" class="img-fluid w-100 h-100" style="object-fit: cover;" alt="{{ $vehicle->model }}">
                                        @else
                                            <i class="bi bi-car-front text-muted opacity-25" style="font-size: 3rem;"></i>
                                        @endif
                                    </div>
                                    <div class="col-8">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-start mb-1">
                                                <h6 class="fw-bold text-dark mb-0 vehicle-name-text">{{ $vehicle->brand }} {{ $vehicle->model }}</h6>
                                                <span class="badge bg-success-subtle text-success rounded-pill" style="font-size: 10px;">{{ $vehicle->status ?? 'Available' }}</span>
                                            </div>
                                            <p class="text-muted small mb-2 font-monospace" style="font-size: 11px;">Plate: {{ $vehicle->plate_number ?? 'N/A' }}</p>
                                            
                                            <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                                                <span class="text-secondary small fw-medium vehicle-capacity-text"><i class="bi bi-people-fill me-1"></i>{{ $vehicle->capacity }} Pax</span>
                                                <span class="text-primary fw-bold">₱{{ number_format($vehicle->base_price ?? 0, 0) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div id="noVehicleFound" class="text-center text-muted py-4 d-none">
                        <i class="bi bi-emoji-frown fs-2 d-block mb-2 opacity-50"></i>
                        <span class="small">No vehicles matched your search.</span>
                    </div>
                </div>
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

        const packageData = {
            "{{ $package->id ?? 0 }}": {
                id: {{ $package->id ?? 0 }},
                name: {!! json_encode($package->name ?? 'Tour Package') !!},
                package_price: {{ $package->package_price ?? 0 }},
                max_pax: {{ $package->max_pax ?? ($package->pax_limit ?? ($package->capacity ?? 10)) }},
                spots: [
                    @if(isset($package->places))
                        @foreach ($package->places as $place)
                            {
                                id: {{ $place->id }},
                                name: {!! json_encode($place->name) !!},
                                category: {!! json_encode($place->category ?? 'other') !!},
                                description: {!! json_encode($place->description ?? '') !!},
                                @php
                                    $dur = '1h 0m';
                                    if(isset($place->pivot) && $place->pivot->duration) {
                                        $parts = explode(':', $place->pivot->duration);
                                        if(count($parts) >= 2) {
                                            $dur = (int)$parts[0] . 'h ' . (int)$parts[1] . 'm';
                                        }
                                    }
                                @endphp
                                duration: {!! json_encode($dur) !!},
                                image: {!! json_encode($place->image_path ?? '') !!},
                                lat: {{ $place->latitude ?? ($place->lat ?? 0) }},
                                lng: {{ $place->longitude ?? ($place->lng ?? 0) }}
                            },
                        @endforeach
                    @endif
                ]
            }
        };

        const allPlacesData = {};
        @if(isset($places))
            @foreach ($places as $place)
                allPlacesData[{{ $place->id }}] = {
                    id: {{ $place->id }},
                    name: {!! json_encode($place->name) !!},
                    category: {!! json_encode($place->category ?? 'other') !!},
                    description: {!! json_encode($place->description ?? '') !!},
                    duration: "1h 0m",
                    image: {!! json_encode($place->image_path ?? '') !!},
                    lat: {{ $place->latitude ?? ($place->lat ?? 0) }},
                    lng: {{ $place->longitude ?? ($place->lng ?? 0) }}
                };
            @endforeach
        @endif

        const vehiclesData = {};
        @foreach ($vehicles as $vehicle)
            vehiclesData[{{ $vehicle->id }}] = {
                id: {{ $vehicle->id }},
                name: {!! json_encode($vehicle->brand . ' ' . $vehicle->model) !!},
                base_price: {{ $vehicle->base_price ?? 0 }},
                capacity: {{ $vehicle->capacity ?? 10 }}
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
                                <div class="mb-2"><span class="badge bg-warning text-dark"><i class="bi bi-clock-history me-1"></i> Est: ${spot.duration}</span></div>
                                <p class="text-muted small mb-1">${spot.description || 'No summary overview provided.'}</p>
                                <span class="badge bg-dark rounded-pill" style="font-size: 10px;"><i class="bi bi-box-seam me-1"></i> Part of: ${currentPackage.name}</span>
                            </div>
                        </div>
                    `;

                    const customIcon = createCategoryPinIcon(spot.category);
                    var marker = L.marker([spot.lat, spot.lng], {icon: customIcon}).addTo(map).bindPopup(popupContent);
                    marker.bindTooltip(spot.name, {
                        permanent: true,
                        direction: 'top',
                        className: 'custom-pin-label',
                        offset: [0, -36]
                    });

                    marker.on('click', function() {
                        // In single package view, clicking a global pin doesn't need to filter a sidebar
                        // because we are already viewing the package.
                        // We could optionally just focus the route.
                        if (!pickupMappingModeActive) {
                            focusOnPackageRoute(currentPackage.id);
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

        const categoryConfig = {
            'swimming': {
                icon: 'bi-water',
                bg: '#0dcaf0'
            },
            'mountain': {
                icon: 'bi-tree-fill',
                bg: '#198754'
            },
            'restaurant': {
                icon: 'bi-cup-hot-fill',
                bg: '#fd7e14'
            },
            'terminal': {
                icon: 'bi-bus-front-fill',
                bg: '#6f42c1'
            },
            'water falls': {
                icon: 'bi-tsunami',
                bg: '#0d6efd'
            },
            'other': {
                icon: 'bi-geo-alt-fill',
                bg: '#6c757d'
            },
            'custom': {
                icon: 'bi-pin-map-fill',
                bg: '#dc3545'
            }
        };

        function getCategoryDetails(categoryKey) {
            const key = (categoryKey || '').toLowerCase();
            return categoryConfig[key] || {
                icon: 'bi-geo-alt-fill',
                bg: '#0d6efd'
            };
        }

        function createCategoryPinIcon(categoryKey) {
            const config = getCategoryDetails(categoryKey);
            return L.divIcon({
                className: 'custom-pin-wrapper',
                html: `<div class="custom-category-pin" style="background-color: ${config.bg};"><i class="bi ${config.icon}"></i></div>`,
                iconSize: [36, 36],
                iconAnchor: [18, 36],
                popupAnchor: [0, -34]
            });
        }

        function createGrayCategoryPinIcon(categoryKey) {
            const config = getCategoryDetails(categoryKey);
            return L.divIcon({
                className: 'custom-pin-wrapper',
                html: `<div class="custom-category-pin" style="background-color: #adb5bd; opacity: 0.8; filter: grayscale(100%);"><i class="bi ${config.icon}"></i></div>`,
                iconSize: [36, 36],
                iconAnchor: [18, 36],
                popupAnchor: [0, -34]
            });
        }

        function clearMapLayers() {
            currentMarkers.forEach(marker => map.removeLayer(marker));
            currentMarkers = [];
            if (activeRouteLine) {
                map.removeLayer(activeRouteLine);
                activeRouteLine = null;
            }
        }
        
        function addPlaceToItinerary(placeId) {
            const activePackageId = "{{ $package->id ?? 0 }}";
            const place = allPlacesData[placeId];
            if (place && packageData[activePackageId]) {
                // Add to the package data
                packageData[activePackageId].spots.push(place);
                
                // Redraw map and form
                initializeForm();
                focusOnPackageRoute(activePackageId);
            }
        }
        
        function removePlaceFromItinerary(placeId) {
            const activePackageId = "{{ $package->id ?? 0 }}";
            if (packageData[activePackageId]) {
                // Remove from the package data
                packageData[activePackageId].spots = packageData[activePackageId].spots.filter(spot => spot.id != placeId);
                
                // Redraw map and form
                initializeForm();
                focusOnPackageRoute(activePackageId);
            }
        }
        
        function renameCustomStop(spotId) {
            const activePackageId = "{{ $package->id ?? 0 }}";
            if (packageData[activePackageId]) {
                const spot = packageData[activePackageId].spots.find(s => s.id == spotId);
                if (spot) {
                    const newName = prompt("Enter a name for this custom stop:", spot.name);
                    if (newName && newName.trim() !== '') {
                        spot.name = newName.trim();
                        spot.customNameSet = true;
                        initializeForm();
                        focusOnPackageRoute(activePackageId);
                    }
                }
            }
        }
        function addCustomStop(category = 'custom') {
            const activePackageId = "{{ $package->id ?? 0 }}";
            if (!packageData[activePackageId]) return;
            
            const center = map.getCenter();
            const customId = 'custom_' + Date.now();
            let catName = category.charAt(0).toUpperCase() + category.slice(1);
            if(category === 'water falls') catName = 'Water Falls';
            if(category === 'custom') catName = 'Custom';
            
            const newSpot = {
                id: customId,
                name: `${catName} Stop (Drag to adjust)`,
                category: category,
                description: 'Drag this pin to set your custom location.',
                duration: '1h 0m',
                image: '',
                lat: center.lat,
                lng: center.lng,
                isCustom: true
            };
            
            packageData[activePackageId].spots.push(newSpot);
            
            initializeForm();
            focusOnPackageRoute(activePackageId);
            
            // Show alert instruction once
            const toast = document.createElement('div');
            toast.className = 'alert alert-info position-absolute shadow-sm';
            toast.style.cssText = 'top: 20px; left: 50%; transform: translateX(-50%); z-index: 9999;';
            toast.innerHTML = '<i class="bi bi-info-circle-fill me-2"></i><strong>Custom Pin Added!</strong> Drag the red pin on the map to your desired location.';
            document.querySelector('.map-container').appendChild(toast);
            setTimeout(() => toast.remove(), 4000);
        }

        function focusOnPackageRoute(packageId) {
            clearMapLayers();
            const selectedPackage = packageData[packageId];
            if (!selectedPackage) return;

            const listGroup = document.getElementById('spotsListGroup');
            listGroup.innerHTML = '';

            let bounds = [];
            let routeCoordinates = [];

            if (livePickupMarker) {
                const p = livePickupMarker.getLatLng();
                routeCoordinates.push([p.lat, p.lng]);
                bounds.push([p.lat, p.lng]);
            }

            selectedPackage.spots.forEach((spot, index) => {
                const imgUrl = spot.image ? spot.image :
                    'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?q=80&w=400';

                const popupContent = `
                    <div class="card border-0">
                        <img src="${imgUrl}" class="popup-img" alt="${spot.name}">
                        <div class="p-3">
                            <h6 class="fw-bold mb-1 text-dark">${spot.name}</h6>
                            <div class="mb-1"><span class="badge bg-warning text-dark"><i class="bi bi-clock-history me-1"></i> Est: ${spot.duration}</span></div>
                            <p class="text-muted small mb-0">${spot.description || 'No summary overview provided.'}</p>
                            <span class="badge bg-primary-subtle text-primary rounded-pill mt-2 font-monospace">Stop #${index + 1}</span>
                        </div>
                    </div>
                `;

                const customIcon = createCategoryPinIcon(spot.category);
                var marker = L.marker([spot.lat, spot.lng], {
                    icon: customIcon,
                    draggable: spot.isCustom ? true : false
                }).addTo(map).bindPopup(popupContent);
                
                if (spot.isCustom) {
                    marker.on('dragend', function(event) {
                        const position = event.target.getLatLng();
                        spot.lat = position.lat;
                        spot.lng = position.lng;
                        
                        // Optionally update name based on reverse geocoding
                        fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${position.lat}&lon=${position.lng}`)
                            .then(res => res.json())
                            .then(data => {
                                let placeName = "";
                                if (data && data.address) {
                                    if (data.address.amenity) placeName += data.address.amenity + ", ";
                                    if (data.address.suburb) placeName += data.address.suburb + ", ";
                                    if (data.address.city) placeName += data.address.city;
                                    else if (data.address.town) placeName += data.address.town;
                                    placeName = placeName.replace(/, $/, '');
                                }
                                if (!spot.customNameSet) {
                                    if (placeName) {
                                        spot.name = placeName;
                                    } else {
                                        let catName = spot.category.charAt(0).toUpperCase() + spot.category.slice(1);
                                        if(spot.category === 'water falls') catName = 'Water Falls';
                                        if(spot.category === 'custom') catName = 'Custom';
                                        spot.name = `${catName} Stop (Drag to adjust)`;
                                    }
                                }
                                initializeForm();
                                focusOnPackageRoute("{{ $package->id ?? 0 }}");
                            }).catch(() => {
                                initializeForm();
                                focusOnPackageRoute("{{ $package->id ?? 0 }}");
                            });
                    });
                }
                marker.bindTooltip(`Stop ${index + 1}: ${spot.name}`, {
                    permanent: true,
                    direction: 'top',
                    className: 'custom-pin-label',
                    offset: [0, -36]
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
                    <span class="text-muted font-monospace" style="font-size:11px;"><i class="bi bi-clock me-1"></i>${spot.duration}</span>
                `;

                btn.onclick = function() {
                    map.setView([spot.lat, spot.lng], 16);
                    marker.openPopup();
                };
                listGroup.appendChild(btn);
            });
            
            // Plot all other places that are not in the current package spots
            const spotIds = selectedPackage.spots.map(s => s.id);
            Object.values(allPlacesData).forEach(place => {
                if (!spotIds.includes(place.id)) {
                    const imgUrl = place.image ? place.image : 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?q=80&w=400';
                    const popupContent = `
                        <div class="card border-0">
                            <img src="${imgUrl}" class="popup-img" alt="${place.name}" style="filter: grayscale(80%);">
                            <div class="p-3">
                                <h6 class="fw-bold mb-1 text-dark text-muted">${place.name}</h6>
                                <div class="mb-1"><span class="badge bg-secondary"><i class="bi bi-clock-history me-1"></i> Est: ${place.duration}</span></div>
                                <p class="text-muted small mb-2">${place.description || 'No summary overview provided.'}</p>
                                <button class="btn btn-sm btn-outline-primary rounded-pill w-100 fw-medium" onclick="addPlaceToItinerary(${place.id})">
                                    <i class="bi bi-plus-circle"></i> Add to Itinerary
                                </button>
                            </div>
                        </div>
                    `;

                    const grayIcon = createGrayCategoryPinIcon(place.category);

                    var altMarker = L.marker([place.lat, place.lng], {icon: grayIcon}).addTo(map).bindPopup(popupContent);
                    altMarker.bindTooltip(`${place.name}`, {
                        permanent: false,
                        direction: 'top',
                        className: 'custom-pin-label-alt text-muted',
                        offset: [0, -36]
                    });

                    currentMarkers.push(altMarker);
                    bounds.push([place.lat, place.lng]);
                }
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
            
            const resetBtn = document.getElementById('resetFilterBtn');
            if (resetBtn) resetBtn.classList.remove('d-none');

            if (bounds.length > 0) {
                map.fitBounds(bounds, {
                    padding: [50, 50]
                });
            }
        }

        // Package card filtering removed for single-package view

        function resetFilters() {
            // For single-package view, resetting filters just hides the spot panel and reloads pins
            const resetBtn = document.getElementById('resetFilterBtn');
            if (resetBtn) resetBtn.classList.add('d-none');
            
            const spotsPanel = document.getElementById('spotsPanel');
            if (spotsPanel) spotsPanel.classList.add('d-none');

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
                
            // Redraw the map to include this pickup point in the route polyline
            focusOnPackageRoute("{{ $package->id ?? 0 }}");
        }

        function initializeForm() {
            // Load base price from PHP
            activeBasePrice = parseFloat("{{ $package->package_price ?? 0 }}") || 0;
            
            // Build draggable spots
            const itineraryContainer = document.getElementById('modalItineraryContainer');
            itineraryContainer.innerHTML = '';
            const standardSpotsInfo = document.getElementById('standardSpotsInfo');

            // Find this package's data in the pre-loaded packageData object
            const targetedPackageData = packageData["{{ $package->id ?? 0 }}"];
            if (targetedPackageData && targetedPackageData.spots && targetedPackageData.spots.length > 0) {
                standardSpotsInfo.classList.remove('d-none');
                targetedPackageData.spots.forEach((spot, index) => {
                    const badge = document.createElement('div');
                    badge.className = "d-flex align-items-center bg-light border rounded-3 p-2 small fw-medium draggable-spot-item";
                    badge.style.cursor = "grab";
                    let editBtnHtml = '';
                    if (spot.isCustom) {
                        editBtnHtml = `
                        <button type="button" class="btn btn-sm btn-link text-primary p-0 ms-1 me-1" onclick="renameCustomStop('${spot.id}')" title="Rename stop">
                            <i class="bi bi-pencil-square"></i>
                        </button>`;
                    }

                    badge.innerHTML = `
                        <div class="d-flex align-items-center flex-grow-1 overflow-hidden">
                            <i class="bi bi-grip-vertical text-muted me-2"></i>
                            <span class="text-primary me-2">${index + 1}.</span> 
                            <span class="text-truncate">${spot.name}</span>
                        </div>
                        ${editBtnHtml}
                        <button type="button" class="btn btn-sm btn-link text-danger p-0 ms-1" onclick="removePlaceFromItinerary('${spot.id}')" title="Remove spot">
                            <i class="bi bi-x-circle-fill"></i>
                        </button>
                    `;
                    // Add hidden input so form submission retains the custom order
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'spots_order[]';
                    input.value = spot.id;
                    badge.appendChild(input);

                    itineraryContainer.appendChild(badge);
                });
                
                // Initialize SortableJS
                if (typeof Sortable !== 'undefined') {
                    new Sortable(itineraryContainer, {
                        animation: 150,
                        ghostClass: 'sortable-ghost',
                        onEnd: function (evt) {
                            // Re-number the spots after drag and drop
                            const items = itineraryContainer.querySelectorAll('.draggable-spot-item');
                            const newOrderIds = [];
                            
                            items.forEach((item, i) => {
                                const numberSpan = item.querySelector('.text-primary');
                                if (numberSpan) {
                                    numberSpan.innerText = (i + 1) + ".";
                                }
                                const hiddenInput = item.querySelector('input[name="spots_order[]"]');
                                if (hiddenInput) {
                                    newOrderIds.push(hiddenInput.value);
                                }
                            });
                            
                            // Reorder packageData spots so the map polyline updates!
                            const activePackageId = "{{ $package->id ?? 0 }}";
                            if (packageData[activePackageId]) {
                                const oldSpots = [...packageData[activePackageId].spots];
                                const newSpots = [];
                                newOrderIds.forEach(id => {
                                    const spot = oldSpots.find(s => s.id == id);
                                    if (spot) newSpots.push(spot);
                                });
                                packageData[activePackageId].spots = newSpots;
                                
                                // Redraw map lines without fully rebuilding the form HTML
                                focusOnPackageRoute(activePackageId);
                            }
                        }
                    });
                }
            } else {
                standardSpotsInfo.classList.add('d-none');
            }

            calculateTotal();
        }

        function openVehicleModal() {
            // Keep bookingModal open in the background, just show vehicleSelectionModal over it
            const vehicleModalEl = document.getElementById('vehicleSelectionModal');
            const modal = bootstrap.Modal.getOrCreateInstance(vehicleModalEl);
            modal.show();
        }

        let activeBrandFilter = '';

        function filterByBrand(btn, brand) {
            activeBrandFilter = brand;
            
            // Update button styles
            document.querySelectorAll('.brand-filter-btn').forEach(el => {
                el.classList.remove('btn-primary');
                el.classList.add('btn-outline-primary');
            });
            btn.classList.remove('btn-outline-primary');
            btn.classList.add('btn-primary');
            
            filterVehicles();
        }

        function filterVehicles() {
            const query = document.getElementById('vehicleSearchInput').value.toLowerCase();
            const rows = document.querySelectorAll('.vehicle-item-row');
            let hasVisible = false;
            
            rows.forEach(row => {
                const name = row.querySelector('.vehicle-name-text').innerText.toLowerCase();
                const capacity = row.querySelector('.vehicle-capacity-text').innerText.toLowerCase();
                
                const matchesSearch = name.includes(query) || capacity.includes(query);
                const matchesBrand = activeBrandFilter === '' || name.includes(activeBrandFilter);
                
                if (matchesSearch && matchesBrand) {
                    row.classList.remove('d-none');
                    hasVisible = true;
                } else {
                    row.classList.add('d-none');
                }
            });
            
            const noFoundMsg = document.getElementById('noVehicleFound');
            if(hasVisible) {
                noFoundMsg.classList.add('d-none');
            } else {
                noFoundMsg.classList.remove('d-none');
            }
        }

        function selectVehicleFromModal(id) {
            document.getElementById('vehicleSelect').value = id;
            
            const vehicle = vehiclesData[id];
            if (vehicle) {
                document.getElementById('selectedVehicleName').innerText = vehicle.name;
                document.getElementById('selectedVehicleDetails').innerText = `${vehicle.capacity} Pax | ₱${vehicle.base_price}`;
                
                // Try to find the image from the clicked row
                const row = Array.from(document.querySelectorAll('.vehicle-item-row')).find(r => r.getAttribute('onclick').includes(`(${id})`));
                const imgElement = row ? row.querySelector('img') : null;
                
                const imgContainer = document.getElementById('selectedVehicleImg');
                if (imgElement) {
                    imgContainer.innerHTML = `<img src="${imgElement.src}" class="w-100 h-100" style="object-fit: cover;">`;
                    imgContainer.classList.add('p-0');
                } else {
                    imgContainer.innerHTML = '<i class="bi bi-car-front fs-5"></i>';
                    imgContainer.classList.remove('p-0');
                }
            }
            
            calculateTotal();
            
            // Close the vehicle modal
            const vehicleModalEl = document.getElementById('vehicleSelectionModal');
            const modal = bootstrap.Modal.getInstance(vehicleModalEl);
            if (modal) {
                modal.hide();
                // When closing a nested modal, Bootstrap removes modal-open from body. 
                // We add it back so the main booking modal continues to scroll.
                setTimeout(() => {
                    document.body.classList.add('modal-open');
                }, 400);
            }
        }

        function calculateTotal() {
            const headsInput = document.getElementById('numberHeads');
            let headsCount = parseInt(headsInput.value) || 1;
            const isJoinerAllowed = document.getElementById('allowJoinersCheck').checked;
            const vehicleSelect = document.getElementById('vehicleSelect');
            
            let vehiclePrice = 0;
            let currentPaxLimit = activePaxLimit;

            if (vehicleSelect && vehicleSelect.value && vehiclesData[vehicleSelect.value]) {
                const selectedVehicle = vehiclesData[vehicleSelect.value];
                vehiclePrice = parseFloat(selectedVehicle.base_price) || 0;
                currentPaxLimit = parseInt(selectedVehicle.capacity) || 10;
            }
            
            document.getElementById('modalPaxLimitLabel').innerText = `${currentPaxLimit} pax`;
            headsInput.setAttribute('max', currentPaxLimit);

            if (headsCount < 1) {
                headsCount = 1;
                headsInput.value = 1;
            } else if (headsCount > currentPaxLimit) {
                headsCount = currentPaxLimit;
                headsInput.value = currentPaxLimit;
            }
            
            const totalBasePrice = activeBasePrice + vehiclePrice;
            const perHeadRate = totalBasePrice / (currentPaxLimit || 1);

            let totalToPay = 0;
            let costPerPerson = 0;

            if (isJoinerAllowed) {
                totalToPay = perHeadRate * headsCount;
                costPerPerson = perHeadRate;
            } else {
                totalToPay = totalBasePrice;
                costPerPerson = totalBasePrice / headsCount;
            }

            const downpaymentRequired = totalToPay * 0.25;
            const formatCurrency = (val) =>
                `₱${val.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;

            const perHeadLabel = document.getElementById('modalPerHeadLabel');
            if (perHeadLabel) {
                perHeadLabel.innerText = formatCurrency(perHeadRate);
            }
            document.getElementById('modalBasePriceLabel').innerText = formatCurrency(totalBasePrice);

            document.getElementById('breakdownBase').innerText = isJoinerAllowed ?
                `Joiner Mode (${headsCount}/${currentPaxLimit} slots)` :
                'Private Tour (Full Base)';

            document.getElementById('breakdownHeads').innerText = isJoinerAllowed ?
                `${headsCount} head(s) @ ${formatCurrency(perHeadRate)}/head` :
                `${headsCount} head(s) splitting ${formatCurrency(totalBasePrice)}`;

            document.getElementById('breakdownPerPerson').innerText = `${formatCurrency(costPerPerson)} / person`;
            document.getElementById('modalTotalPrice').innerText = formatCurrency(totalToPay);
            document.getElementById('modalDownpaymentPrice').innerText = formatCurrency(downpaymentRequired);
        }

        window.onload = function() {
            // Automatically map out the active package route
            const activePackageId = {{ $package->id ?? 0 }};
            if (activePackageId) {
                focusOnPackageRoute(activePackageId);
            } else {
                loadAllGlobalPins();
            }
            
            initializeForm();
            
            // Fix Bootstrap 5 nested modal scrolling issue
            const vehicleModalEl = document.getElementById('vehicleSelectionModal');
            if (vehicleModalEl) {
                vehicleModalEl.addEventListener('hidden.bs.modal', function () {
                    // Re-enable scrolling on the main page if needed
                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = 'auto';
                });
            }
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
