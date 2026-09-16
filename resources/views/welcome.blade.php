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
            <a class="navbar-brand fw-bold text-dark d-flex align-items-center gap-2"
                href="{{ url()->current() }}">
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
                        @if($user->is_admin)
                            <li>
                                <a class="dropdown-item py-2 text-primary fw-bold px-4 d-flex align-items-center" href="{{ route('admin.bookings') }}">
                                    <i class="bi bi-shield-lock-fill me-2 fs-6"></i> Admin Panel
                                </a>
                            </li>
                            <li><hr class="dropdown-divider my-1"></li>
                        @endif
                        <li><a class="dropdown-item py-2 text-muted px-4 d-flex align-items-center" href="{{ route('profile.edit') }}"><i
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
            <div class="col-12 col-md-3 sidebar-scroll p-3 bg-white border-end" id="packageSidebar">
                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-0 text-dark fs-6 d-flex align-items-center gap-1">
                            <i class="bi bi-compass text-primary"></i> Cebu Tour Packages
                        </h5>
                        <p class="text-muted small mb-0" style="font-size: 11px;">Select a package or custom route.</p>
                    </div>
                    <!-- Action Buttons -->
                    <div class="d-flex gap-1.5 align-items-center">
                        <a href="/custom-package"
                            class="btn btn-sm btn-primary rounded-pill px-3 py-1.5 fw-medium shadow-sm d-flex align-items-center"
                            style="font-size: 11px;">
                            <i class="bi bi-plus-lg me-1"></i> Custom
                        </a>
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
                                'best_combo' => 'bg-warning text-dark',
                                'trending' => 'bg-primary text-white',
                                'budget' => 'bg-success text-white',
                                default => 'bg-secondary text-white',
                            };

                            $tagIcon = match (strtolower($type)) {
                                'popular' => 'bi-fire',
                                'best_combo' => 'bi-star-fill',
                                'trending' => 'bi-lightning-charge-fill',
                                'budget' => 'bi-wallet2',
                                default => 'bi-bookmark-fill',
                            };
                        @endphp

                        <div class="card package-card-rect package-card border-0 shadow bg-white" 
                            data-package-id="{{ $package->id }}" id="package-card-{{ $package->id }}" 
                            onclick="filterSidebarByPackage({{ $package->id }})"
                            style="border-radius: 1rem !important; overflow: hidden; transition: transform 0.3s ease; cursor: pointer;">
                            
                            <!-- Top Image Container with Purple Gradient fallback and Heart -->
                            <div class="position-relative d-flex align-items-center justify-content-center text-muted"
                                style="height: 250px; width: 100%; background: linear-gradient(135deg, #084298, #0d6efd, #6ea8fe);">
                                
                                <!-- Floating Heart Icon -->
                                <button class="btn btn-sm position-absolute top-0 end-0 m-3 rounded-circle d-flex align-items-center justify-content-center border-0" 
                                    style="width: 36px; height: 36px; background: rgba(255,255,255,0.2); backdrop-filter: blur(5px);">
                                    <i class="bi bi-heart text-white fs-6"></i>
                                </button>

                                @if (!empty($package->image_path))
                                    <img src="{{ $package->image_path }}" onerror="this.onerror=null;this.src='data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22400%22%20height%3D%22300%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20400%20300%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_1%20text%20%7B%20fill%3A%23999%3Bfont-weight%3Anormal%3Bfont-family%3Avar(--bs-font-sans-serif)%2C%20sans-serif%3Bfont-size%3A20pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_1%22%3E%3Crect%20width%3D%22400%22%20height%3D%22300%22%20fill%3D%22%23e2e3e5%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%22144%22%20y%3D%22160%22%3ENo%20Image%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E';" alt="{{ $package->name }}" style="object-fit: cover; width: 100%; height: 100%;">
                                @else
                                    <i class="bi bi-image fs-1 opacity-25"></i>
                                @endif
                            </div>

                            <!-- Bottom Content Container -->
                            <div class="p-3 d-flex flex-column flex-grow-1 bg-white">
                                <!-- Title -->
                                <h5 class="fw-bold text-dark mb-2 mt-1 fs-5 text-truncate" style="letter-spacing: -0.5px;" >{{ $package->name }}</h5>
                                
                                <!-- Badges / Tags -->
                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    <span class="rounded px-2 py-1 {{ $badgeStyles }} d-flex align-items-center gap-1 shadow-sm" style="font-size: 11px; font-weight: 600; letter-spacing: 0.5px; border: none !important;">
                                        <i class="bi {{ $tagIcon }}"></i> {{ strtoupper(str_replace('_', ' ', $type)) }}
                                    </span>
                                    <span class="border rounded px-2 py-1 text-dark" style="font-size: 11px; font-weight: 600; letter-spacing: 0.5px; border-color: #6c757d !important;">
                                        {{ $package->places->count() }} STOPS
                                    </span>
                                    
                                </div>
                                
                                <!-- Description -->
                                <p class="text-muted mb-4" style="font-size: 14px; line-height: 1.6; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; color: #4b5563 !important;">
                                    {{ $package->description ?? 'Experience the best of what this package has to offer. A curated journey designed for comfort and flair.' }}
                                </p>

                                <!-- Footer: Price and Button -->
                                <div class="d-flex justify-content-between align-items-end mt-auto pt-2">
                                    <div class="d-flex flex-column">
                                        <span style="font-size: 10px; font-weight: 800; letter-spacing: 1px; color: #374151;">PRICE</span>
                                        <span class="fs-4 fw-bold text-dark est-price-display" style="letter-spacing: -1px; line-height: 1;">&#8369; --</span>
                                    </div>
                                    
                                    <a href="/package/{{ $package->id }}" class="btn btn-primary rounded-3 px-4 py-2 shadow-sm" style="font-weight: 500; font-size: 14px; letter-spacing: 0.3px;">
                                        Book Now
                                    </a>
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
                                            <img src="{{ asset('storage/' . ($vehicle->front_image_path ?? $vehicle->image_path)) }}" onerror="this.onerror=null;this.src='data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22400%22%20height%3D%22300%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20400%20300%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_1%20text%20%7B%20fill%3A%23999%3Bfont-weight%3Anormal%3Bfont-family%3Avar(--bs-font-sans-serif)%2C%20sans-serif%3Bfont-size%3A20pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_1%22%3E%3Crect%20width%3D%22400%22%20height%3D%22300%22%20fill%3D%22%23e2e3e5%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%22144%22%20y%3D%22160%22%3ENo%20Image%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E';" class="img-fluid w-100 h-100" style="object-fit: cover;" alt="{{ $vehicle->model }}">
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
                ]
            };
        @endforeach
        const categoryConfig = {
            'swimming': { icon: 'bi-water', bg: '#0dcaf0' },
            'mountain': { icon: 'bi-tree-fill', bg: '#198754' },
            'restaurant': { icon: 'bi-cup-hot-fill', bg: '#fd7e14' },
            'terminal': { icon: 'bi-bus-front-fill', bg: '#6f42c1' },
            'water falls': { icon: 'bi-tsunami', bg: '#0d6efd' },
            'other': { icon: 'bi-geo-alt-fill', bg: '#6c757d' },
            'custom': { icon: 'bi-pin-map-fill', bg: '#dc3545' }
        };

        function getCategoryDetails(categoryKey) {
            const key = (categoryKey || '').toLowerCase();
            return categoryConfig[key] || { icon: 'bi-geo-alt-fill', bg: '#0d6efd' };
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
                        'data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22400%22%20height%3D%22300%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20400%20300%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_1%20text%20%7B%20fill%3A%23999%3Bfont-weight%3Anormal%3Bfont-family%3Avar(--bs-font-sans-serif)%2C%20sans-serif%3Bfont-size%3A20pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_1%22%3E%3Crect%20width%3D%22400%22%20height%3D%22300%22%20fill%3D%22%23e2e3e5%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%22144%22%20y%3D%22160%22%3ENo%20Image%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E';

                    const popupContent = `
                        <div class="card border-0">
                            <img src="${imgUrl}" onerror="this.onerror=null;this.src='data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22400%22%20height%3D%22300%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20400%20300%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_1%20text%20%7B%20fill%3A%23999%3Bfont-weight%3Anormal%3Bfont-family%3Avar(--bs-font-sans-serif)%2C%20sans-serif%3Bfont-size%3A20pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_1%22%3E%3Crect%20width%3D%22400%22%20height%3D%22300%22%20fill%3D%22%23e2e3e5%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%22144%22%20y%3D%22160%22%3ENo%20Image%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E';" class="popup-img" alt="${spot.name}">
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
                    'data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22400%22%20height%3D%22300%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20400%20300%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_1%20text%20%7B%20fill%3A%23999%3Bfont-weight%3Anormal%3Bfont-family%3Avar(--bs-font-sans-serif)%2C%20sans-serif%3Bfont-size%3A20pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_1%22%3E%3Crect%20width%3D%22400%22%20height%3D%22300%22%20fill%3D%22%23e2e3e5%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%22144%22%20y%3D%22160%22%3ENo%20Image%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E';

                const popupContent = `
                    <div class="card border-0">
                        <img src="${imgUrl}" onerror="this.onerror=null;this.src='data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22400%22%20height%3D%22300%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20400%20300%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_1%20text%20%7B%20fill%3A%23999%3Bfont-weight%3Anormal%3Bfont-family%3Avar(--bs-font-sans-serif)%2C%20sans-serif%3Bfont-size%3A20pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_1%22%3E%3Crect%20width%3D%22400%22%20height%3D%22300%22%20fill%3D%22%23e2e3e5%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%22144%22%20y%3D%22160%22%3ENo%20Image%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E';" class="popup-img" alt="${spot.name}">
                        <div class="p-3">
                            <h6 class="fw-bold mb-1 text-dark">${spot.name}</h6>
                            <div class="mb-1"><span class="badge bg-warning text-dark"><i class="bi bi-clock-history me-1"></i> Est: ${spot.duration}</span></div>
                            <p class="text-muted small mb-0">${spot.description || 'No summary overview provided.'}</p>
                            <span class="badge bg-primary-subtle text-primary rounded-pill mt-2 font-monospace">Stop #${index + 1}</span>
                        </div>
                    </div>
                `;

                const customIcon = createCategoryPinIcon(spot.category);
                var marker = L.marker([spot.lat, spot.lng], {icon: customIcon}).addTo(map).bindPopup(popupContent);
                marker.bindTooltip((index + 1) + '. ' + spot.name, {
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

        function openBookingModal(packageObj) {
            document.getElementById('bookingModalLabel').innerHTML =
                '<i class="bi bi-shield-check fs-5 me-1"></i> Secure Your Reservation';
            document.getElementById('modalPackageId').value = packageObj.id || 0;
            document.getElementById('modalPackageName').innerHTML =
                `<i class="bi bi-box-seam text-primary me-1"></i> ${packageObj.name || 'Tour Package'}`;

            activeBasePrice = parseFloat(packageObj.package_price) || 0;

            const rawPax = packageObj.max_pax || packageObj.pax_limit || packageObj.pax || packageObj.capacity;
            activePaxLimit = parseInt(rawPax) > 0 ? parseInt(rawPax) : 10;

            document.getElementById('modalPaxLimitLabel').innerText = `${activePaxLimit} pax`;

            document.getElementById('allowJoinersCheck').checked = false;
            const headsInput = document.getElementById('numberHeads');
            headsInput.value = 1;
            headsInput.setAttribute('max', activePaxLimit);
            headsInput.setAttribute('min', 1);

            const vehicleSelect = document.getElementById('vehicleSelect');
            if (vehicleSelect) {
                vehicleSelect.value = '';
                document.getElementById('selectedVehicleName').innerText = 'No vehicle selected';
                document.getElementById('selectedVehicleDetails').innerText = 'Click choose to browse';
                document.getElementById('selectedVehicleImg').innerHTML = '<i class="bi bi-car-front fs-5"></i>';
                document.getElementById('selectedVehicleImg').classList.remove('p-0');
            }

            const itineraryContainer = document.getElementById('modalItineraryContainer');
            itineraryContainer.innerHTML = '';
            
            const standardSpotsInfo = document.getElementById('standardSpotsInfo');

            const targetedPackageData = packageData[packageObj.id];
            if (targetedPackageData && targetedPackageData.spots && targetedPackageData.spots.length > 0) {
                standardSpotsInfo.classList.remove('d-none');
                targetedPackageData.spots.forEach((spot, index) => {
                    const badge = document.createElement('span');
                    badge.className = "badge bg-light text-dark border px-2 py-1 small fw-medium";
                    badge.innerHTML = `<span class="text-primary me-1">${index + 1}.</span> ${spot.name}`;
                    itineraryContainer.appendChild(badge);
                });
            } else {
                standardSpotsInfo.classList.add('d-none');
            }

            calculateTotal();

            if (!bsModalInstance) {
                bsModalInstance = new bootstrap.Modal(document.getElementById('bookingModal'));
            }
            bsModalInstance.show();
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

            document.getElementById('modalPerHeadLabel').innerText = formatCurrency(perHeadRate);
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
            loadAllGlobalPins();
            
            // Fix Bootstrap 5 nested modal scrolling issue
            const vehicleModalEl = document.getElementById('vehicleSelectionModal');
            vehicleModalEl.addEventListener('hidden.bs.modal', function () {
                if (document.getElementById('bookingModal').classList.contains('show')) {
                    document.body.classList.add('modal-open');
                }
            });
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
    
        const vehiclesList = @json($vehicles);

        document.addEventListener("DOMContentLoaded", function() {
            if (vehiclesList.length > 0) {
                let sortedVehicles = [...vehiclesList].sort((a, b) => parseFloat(a.base_price) - parseFloat(b.base_price));
                let minVehicle = sortedVehicles[0];
                let maxVehicle = sortedVehicles[sortedVehicles.length - 1];
                
                document.querySelectorAll('.package-card').forEach(card => {
                    const pkgId = card.getAttribute('data-package-id');
                    const pkgPrice = (packageData[pkgId] && packageData[pkgId].package_price) ? parseFloat(packageData[pkgId].package_price) : 0;
                    
                    let pDisplay = pkgPrice.toLocaleString('en-US', {minimumFractionDigits: 2});
                    let priceText = `&#8369;${pDisplay}`;
                    const estDisplay = card.querySelector('.est-price-display');
                    const distDisplay = card.querySelector('.pkg-distance-display');
                    
                    if(estDisplay) {
                        estDisplay.innerHTML = priceText;
                    }
                });
            }
        });
    </script>
</body>

</html>
