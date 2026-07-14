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
            border-radius: 6px !important;
            padding: 4px 10px !important;
            font-size: 11px !important;
            font-weight: 600 !important;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15) !important;
            text-align: center !important;
            white-space: nowrap !important;
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
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
            pointer-events: none;
        }
        
        /* Remove arrows from number inputs for a cleaner look */
        input[type=number].no-spinners::-webkit-inner-spin-button, 
        input[type=number].no-spinners::-webkit-outer-spin-button { 
            -webkit-appearance: none; 
            margin: 0; 
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

    <!-- 1. AUTHENTICATION NAVBAR -->
    <nav class="navbar navbar-expand-md navbar-light bg-white border-bottom sticky-top py-3">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold text-dark d-flex align-items-center gap-2" href="#">
                <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" style="height: 36px; width: 36px;" class="text-primary fill-current">
                    <path d="M24 4C12.95 4 4 12.95 4 24s8.95 20 20 20 20-8.95 20-20S35.05 4 24 4zm2 32h-4v-4h4v4zm0-8h-4V12h4v16z" fill="currentColor"/>
                </svg>
                <span class="fs-5 tracking-wider">ETRAV</span>
            </a>

            <div class="ms-auto">
                <div class="dropdown">
                    <button class="btn border-0 d-flex align-items-center gap-1 text-muted fw-medium fs-6 bg-transparent p-0 position-relative" type="button" id="breezeDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <span>Hello {{ $user->name }}</span>
                        <span id="nav-red-dot" class="notify-dot-absolute {{ $hasNotification ? '' : 'd-none' }}"></span>
                        <i class="bi bi-chevron-down small style-muted ms-1" style="font-size: 12px;"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border mt-2 py-1" aria-labelledby="breezeDropdown" style="width: 220px; border-radius: 6px;">
                        <li><a class="dropdown-item py-2 text-muted px-4" href="#"><i class="bi bi-person me-2"></i> Profile</a></li>
                        <li>
                            <a class="dropdown-item py-2 text-muted px-4 d-flex align-items-center justify-content-between" href="{{ route('bookings.view') }}">
                                <span><i class="bi bi-journal-bookmark me-2"></i> Bookings</span>
                                <span id="dropdown-new-badge" class="badge rounded-pill bg-danger {{ $hasNotification ? '' : 'd-none' }}" style="font-size: 10px;">New</span>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item py-2 text-danger px-4 fw-medium"><i class="bi bi-box-arrow-right me-2"></i> Log Out</button>
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
            <div class="col-12 col-md-4 sidebar-scroll p-4 bg-white border-end" id="packageSidebar">
                <div class="mb-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-1 text-dark">Cebu Tour Packages</h5>
                        <p class="text-muted small mb-0">Select a package to view its route or click map pins to filter.</p>
                    </div>
                    <button class="btn btn-sm btn-outline-secondary rounded-pill d-none" id="resetFilterBtn" onclick="resetFilters()">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </button>
                </div>

                <div id="packagesContainer">
                    @forelse($packages as $package)
                        @php
                            $type = $package->type ?? 'Standard'; 
                            
                            $bgStyles = match(strtolower($type)) {
                                'popular'         => 'bg-danger-subtle border-danger-subtle',
                                'best combo'      => 'bg-warning-subtle border-warning-subtle',
                                'trending'        => 'bg-primary-subtle border-primary-subtle',
                                'budget friendly' => 'bg-success-subtle border-success-subtle',
                                default           => 'bg-light border-light-subtle'
                            };

                            $badgeStyles = match(strtolower($type)) {
                                'popular'         => 'bg-danger text-white',
                                'best combo'      => 'bg-warning text-dark',
                                'trending'        => 'bg-primary text-white',
                                'budget friendly' => 'bg-success text-white',
                                default           => 'bg-secondary text-white'
                            };

                            $tagIcon = match(strtolower($type)) {
                                'popular'         => 'bi-fire',
                                'best combo'      => 'bi-star-fill',
                                'trending'        => 'bi-lightning-charge-fill',
                                'budget friendly' => 'bi-wallet2',
                                default           => 'bi-bookmark-fill'
                            };
                        @endphp

                        <div class="card border shadow-sm rounded-4 mb-4 overflow-hidden {{ $bgStyles }} package-card" 
                             data-package-id="{{ $package->id }}" 
                             id="package-card-{{ $package->id }}">
                            <div class="position-relative bg-secondary-subtle text-center d-flex align-items-center justify-content-center text-muted" style="height: 160px;">
                                <span class="badge type-badge shadow-sm {{ $badgeStyles }} text-uppercase tracking-wider px-2.5 py-1.5 rounded-pill" style="font-size: 10px;">
                                    <i class="bi {{ $tagIcon }} me-1"></i> {{ $type }}
                                </span>

                                @if(!empty($package->image_path))
                                    <img src="{{ $package->image_path }}" alt="{{ $package->name }}" class="w-100 h-100" style="object-fit: cover;">
                                @else
                                    <i class="bi bi-image fs-1 opacity-25"></i>
                                @endif

                                <span class="position-absolute top-0 end-0 m-2 badge bg-dark px-2.5 py-1.5 rounded-pill fs-7">
                                    Base: ₱{{ number_format($package->package_price ?? 0) }}
                                </span>
                            </div>
                            <div class="card-body p-3">
                                <h6 class="fw-bold text-dark mb-1">{{ $package->name }}</h6>
                                <p class="text-muted small mb-2">
                                    <i class="bi bi-person-fill text-muted"></i> ₱{{ number_format($package->perhead_price ?? 0) }} per head
                                </p>
                                <p class="text-muted mb-3" style="font-size: 13px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    {{ $package->description ?? 'No description available for this package.' }}
                                </p>
                                
                                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                    <span class="text-muted font-monospace small" style="font-size: 11px;">
                                        <i class="bi bi-geo-alt-fill text-danger"></i> {{ $package->places->count() }} Spots
                                    </span>
                                    <div class="d-flex gap-1.5">
                                        <button class="btn btn-outline-primary btn-sm rounded-pill px-2.5 py-1 fw-medium me-1" onclick="focusOnPackageRoute({{ $package->id }})" style="font-size: 11px;">
                                            <i class="bi bi-map"></i> View Route
                                        </button>
                                        <button class="btn btn-primary btn-sm rounded-pill px-3 py-1 fw-medium" 
                                                onclick="openBookingModal({{ json_encode($package) }})" 
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

            <!-- RIGHT REGION: Map View & Overlays -->
            <div class="col-12 col-md-8 p-3 bg-light position-relative d-none d-md-block">
                <div class="map-container">
                    <div id="mapPickerInstruction" class="alert alert-warning py-2 px-3 align-items-center gap-2 d-none rounded-pill border-0" role="alert">
                        <i class="bi bi-pin-map-fill text-danger animate-bounce"></i>
                        <span class="small fw-semibold text-dark">Click anywhere on the map or drag the gold pin to set your Pickup Point!</span>
                    </div>

                    <div id="map" class="shadow-sm"></div>

                    <div id="spotsPanel" class="spots-overlay-panel card shadow border-0 bg-white d-none">
                        <div class="card-header bg-dark text-white py-2 px-3 fw-bold small d-flex justify-content-between align-items-center">
                            <span>📍 Tour Spots Itinerary</span>
                            <span class="badge bg-secondary-subtle text-dark border font-monospace" id="spotCount">0</span>
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
    <div class="modal fade" id="bookingModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header bg-primary text-white py-3 rounded-top-4">
                    <h5 class="modal-title fw-bold" id="bookingModalLabel">Secure Your Reservation</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('booking.store') }}" method="POST" id="bookingForm">
                    @csrf
                    <input type="hidden" name="package_id" id="modalPackageId">
                    <input type="hidden" name="pickup_latitude" id="pickupLatitude">
                    <input type="hidden" name="pickup_longitude" id="pickupLongitude">

                    <div class="modal-body p-4" style="max-height: 75vh; overflow-y: auto;">
                        <div class="p-3 bg-light rounded-3 mb-3">
                            <h6 class="fw-bold text-dark mb-2" id="modalPackageName">Package Name</h6>
                            <div class="row g-2 text-muted small">
                                <div class="col-6"><i class="bi bi-tag-fill me-1 text-primary"></i> Base: <span id="modalBasePriceLabel">₱0.00</span></div>
                                <div class="col-6"><i class="bi bi-person-fill me-1 text-primary"></i> Per Head: <span id="modalPerHeadLabel">₱0.00</span></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted small">
                                <i class="bi bi-hourglass-split me-1"></i> Custom Stay Duration per Stop
                            </label>
                            <div class="border rounded-3 p-3 bg-light-subtle" id="modalItineraryContainer" style="max-height: 200px; overflow-y: auto;">
                                <!-- Dynamic rows with duration inputs injected via JS -->
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted small d-block">Pickup Location</label>
                            <div class="p-2.5 border rounded-3 bg-white d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-2 overflow-hidden me-2">
                                    <i class="bi bi-geo-alt-fill text-warning fs-5"></i>
                                    <span class="small text-muted text-truncate" id="pickupCoordinatesPlaceholder">No pickup location selected on map</span>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill flex-shrink-0" onclick="startPickupMapMapping()">
                                    <i class="bi bi-pin-map"></i> Choose on Map
                                </button>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label for="pickupDate" class="form-label fw-semibold text-muted small">Pickup Date</label>
                                <input type="date" name="pickup_date" id="pickupDate" class="form-control text-muted" required min="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-6">
                                <label for="pickupTime" class="form-label fw-semibold text-muted small">Pickup Time</label>
                                <input type="time" name="pickup_time" id="pickupTime" class="form-control text-muted" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="numberHeads" class="form-label fw-semibold text-muted small">Number of Heads</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-people text-primary"></i></span>
                                <input type="number" name="number_of_heads" id="numberHeads" class="form-control border-start-0" required min="1" value="1" oninput="calculateTotal()">
                            </div>
                        </div>

                        <div class="bg-light p-3 rounded-3 mb-3" style="font-size: 14px;">
                            <div class="d-flex justify-content-between mb-1.5 text-muted">
                                <span>Package Base Price:</span>
                                <span id="breakdownBase">₱0.00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 text-muted">
                                <span>Heads Accumulation Subtotal:</span>
                                <span id="breakdownHeads">₱0.00</span>
                            </div>
                            <div class="d-flex justify-content-between border-top pt-2 fw-bold text-dark fs-6 mb-3">
                                <span>Estimated Total:</span>
                                <span id="modalTotalPrice">₱0.00</span>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center border-top border-2 border-primary-subtle pt-2">
                                <div class="text-primary fw-bold">
                                    <span>25% Booking Deposit:</span>
                                    <small class="d-block text-muted fw-normal" style="font-size: 11px;">Required to confirm reservation</small>
                                </div>
                                <span class="fs-4 fw-black text-primary fw-bold" id="modalDownpaymentPrice">₱0.00</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4 py-2 text-muted fw-medium" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 fw-medium">Proceed to Payment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Leaflet Map JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
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
        var currentCachedPackageObject = null;

        let activeBasePrice = 0;
        let activePerHeadPrice = 0;

        const packageData = {};
        @foreach($packages as $package)
            packageData[{{ $package->id }}] = {
                id: {{ $package->id }},
                name: {!! json_encode($package->name) !!},
                package_price: {{ $package->package_price ?? 0 }},
                perhead_price: {{ $package->perhead_price ?? 0 }},
                spots: [
                    @foreach($package->places as $place)
                    {
                        id: {{ $place->id }},
                        name: {!! json_encode($place->name) !!},
                        description: {!! json_encode($place->description ?? '') !!},
                        duration: "{{ str_contains(strtolower($place->name), 'oslob') ? '3-4 Hours' : '1-2 Hours' }}",
                        image: {!! json_encode($place->image_path ?? '') !!},
                        lat: {{ $place->latitude ?? $place->lat ?? 0 }},
                        lng: {{ $place->longitude ?? $place->lng ?? 0 }}
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
                    const imgUrl = spot.image ? spot.image : 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?q=80&w=400';

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
                        offset: [0, -15]
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

            if(bounds.length > 0 && !livePickupMarker) {
                map.fitBounds(bounds, { padding: [50, 50] });
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
                const imgUrl = spot.image ? spot.image : 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?q=80&w=400';

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
                    offset: [0, -15]
                });

                currentMarkers.push(marker);
                bounds.push([spot.lat, spot.lng]);
                routeCoordinates.push([spot.lat, spot.lng]);

                const btn = document.createElement('button');
                btn.className = "list-group-item list-group-item-action spot-item-btn border-0 py-2 px-3 small bg-white text-muted fw-medium d-flex justify-content-between align-items-center";
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
                map.fitBounds(bounds, { padding: [50, 50] });
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
            bsModalInstance.hide();
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
                livePickupMarker = L.marker([lat, lng], {icon: pickupIcon, draggable: true}).addTo(map);
                livePickupMarker.bindTooltip("Your Selected Pickup Point", {
                    permanent: true,
                    direction: 'top',
                    className: 'custom-pin-label custom-pickup-label',
                    offset: [0, -15]
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
                bsModalInstance.show();
            }, 600);
        }

        function saveSelectedPickupCoordinates(lat, lng) {
            document.getElementById('pickupLatitude').value = lat.toFixed(6);
            document.getElementById('pickupLongitude').value = lng.toFixed(6);
            document.getElementById('pickupCoordinatesPlaceholder').innerText = `Lat: ${lat.toFixed(4)}, Lng: ${lng.toFixed(4)}`;
        }

        function openBookingModal(packageObj) {
            currentCachedPackageObject = packageObj;
            
            document.getElementById('modalPackageId').value = packageObj.id;
            document.getElementById('modalPackageName').innerText = packageObj.name;
            
            activeBasePrice = parseFloat(packageObj.package_price) || 0;
            activePerHeadPrice = parseFloat(packageObj.perhead_price) || 0;

            const formatCurrency = (val) => `₱${val.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
            document.getElementById('modalBasePriceLabel').innerText = formatCurrency(activeBasePrice);
            document.getElementById('modalPerHeadLabel').innerText = formatCurrency(activePerHeadPrice);
            
            const itineraryContainer = document.getElementById('modalItineraryContainer');
            itineraryContainer.innerHTML = '';
            
            const targetedPackageData = packageData[packageObj.id];
            if (targetedPackageData && targetedPackageData.spots.length > 0) {
                targetedPackageData.spots.forEach((spot, index) => {
                    const row = document.createElement('div');
                    row.className = "row g-2 align-items-center mb-3 pb-2 border-bottom last-border-0";
                    row.innerHTML = `
                        <div class="col-6">
                            <span class="fw-bold text-primary me-1">${index + 1}.</span> 
                            <span class="text-dark fw-semibold small d-inline-block text-truncate" style="max-width: 80%; vertical-align: middle;">${spot.name}</span>
                            <small class="d-block text-muted" style="font-size: 10px;"><i class="bi bi-info-circle"></i> Recommended: ${spot.duration}</small>
                        </div>
                        <div class="col-6">
                            <div class="input-group input-group-sm">
                                <input type="number" name="duration_hrs[${spot.id}]" class="form-control text-center px-1 no-spinners" placeholder="0" min="0" max="24" required>
                                <span class="input-group-text bg-white text-muted px-2" style="font-size: 11px;">hrs</span>
                                <input type="number" name="duration_mins[${spot.id}]" class="form-control text-center px-1 no-spinners" placeholder="0" min="0" max="59" step="5" required>
                                <span class="input-group-text bg-white text-muted px-2" style="font-size: 11px;">mins</span>
                            </div>
                        </div>
                    `;
                    itineraryContainer.appendChild(row);
                });
            } else {
                itineraryContainer.innerHTML = `<div class="text-center text-muted py-2 small">No structured itinerary stops mapped.</div>`;
            }

            document.getElementById('numberHeads').value = 1;
            calculateTotal();

            bsModalInstance = new bootstrap.Modal(document.getElementById('bookingModal'));
            bsModalInstance.show();
        }

        function calculateTotal() {
            const headsInput = document.getElementById('numberHeads');
            let headsCount = parseInt(headsInput.value) || 0;

            if (headsCount < 1) {
                headsInput.value = 1;
                headsCount = 1;
            }

            const headsSubtotal = headsCount * activePerHeadPrice;
            const estimatedTotal = activeBasePrice + headsSubtotal;
            const downpaymentRequired = estimatedTotal * 0.25;

            const formatCurrency = (val) => `₱${val.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
            
            document.getElementById('breakdownBase').innerText = formatCurrency(activeBasePrice);
            document.getElementById('breakdownHeads').innerText = formatCurrency(headsSubtotal);
            document.getElementById('modalTotalPrice').innerText = formatCurrency(estimatedTotal);
            document.getElementById('modalDownpaymentPrice').innerText = formatCurrency(downpaymentRequired);
        }

        window.onload = function() {
            loadAllGlobalPins();
        };
    </script>
</body>
</html>