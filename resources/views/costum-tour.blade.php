<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ETRAV - Custom Tour Builder</title>
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

        .notify-dot-absolute {
            position: absolute;
            top: -2px;
            right: -6px;
            width: 14px;
            height: 14px;
            background-color: #dc3545;
            border: 2px solid #fff;
            border-radius: 50%;
        }
    </style>
</head>

<body class="bg-light">

    <!-- AUTHENTICATION NAVBAR -->
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
                        <span>Hello {{ $user->name ?? 'Guest' }}</span>
                        <i class="bi bi-chevron-down small text-muted ms-1" style="font-size: 12px;"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border mt-2 py-1"
                        aria-labelledby="breezeDropdown">
                        <li><a class="dropdown-item py-2 text-muted px-4" href="#"><i
                                    class="bi bi-person me-2"></i> Profile</a></li>
                        <li><a class="dropdown-item py-2 text-muted px-4" href="#"><i
                                    class="bi bi-journal-bookmark me-2"></i> Bookings</a></li>
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

    <!-- MAIN INTERFACE -->
    <div class="container-fluid main-wrapper">
        <div class="row h-100 g-0">

            <!-- LEFT SIDEBAR: CUSTOM BUILDER -->
            <div class="col-12 col-md-4 sidebar-scroll p-4 bg-white border-end d-flex flex-column" id="builderSidebar">
                <div class="mb-4">
                    <h5 class="fw-bold mb-1"><i class="bi bi-geo-alt-fill text-primary me-2"></i>Build Your Custom Route
                    </h5>
                    <p class="text-muted small">Search a place or click on the map to add stops.</p>
                </div>

                <!-- Search Bar -->
                <div class="input-group mb-4 shadow-sm rounded-3 overflow-hidden border">
                    <span class="input-group-text bg-white border-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" id="placeSearch" class="form-control border-0 shadow-none"
                        placeholder="Search for a location...">
                    <button class="btn btn-primary px-3" onclick="executeSearch()">Find</button>
                </div>

                <!-- Itinerary List -->
                <div id="customItineraryList" class="flex-grow-1 mb-3">
                    <!-- Pickup Spot -->
                    <div class="border rounded-3 p-3 bg-light mb-3 border-start border-4 border-warning">
                        <label class="form-label fw-bold text-dark small mb-1"><i
                                class="bi bi-car-front-fill text-warning me-1"></i> Pickup Location</label>
                        <input type="text" id="pickupInput" class="form-control form-control-sm bg-white"
                            placeholder="Waiting for selection..." readonly>
                        <input type="hidden" id="pickupLat"><input type="hidden" id="pickupLng">
                    </div>

                    <h6 class="fw-bold text-muted small mb-2">Tour Stops</h6>
                    <!-- Dynamic Stops Container -->
                    <div id="stopsContainer"></div>
                </div>

                <!-- Logistics (Date, Time, Pax) -->
                <div class="border-top pt-3 mb-3">
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-muted">Date</label>
                            <input type="date" class="form-control form-control-sm" id="tourDate">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-muted">Time</label>
                            <input type="time" class="form-control form-control-sm" id="tourTime">
                        </div>
                    </div>
                    <div>
                        <label class="form-label small fw-semibold text-muted d-flex justify-content-between">
                            <span>Number of Pax</span>
                            <span class="badge bg-primary-subtle text-primary">Max 12</span>
                        </label>
                        <input type="number" class="form-control form-control-sm" id="tourPax" min="1"
                            max="12" value="1" oninput="calculateEstimate()">
                    </div>
                </div>

                <!-- Price & Checkout -->
                <div class="bg-primary-subtle rounded-3 p-3 text-center border border-primary-subtle mt-auto">
                    <p class="text-muted small mb-1 fw-medium">Estimated Total Price</p>
                    <h3 class="text-primary fw-black mb-3" id="estimatedPrice">₱0.00</h3>
                    <button
                        class="btn btn-primary w-100 rounded-pill fw-medium py-2 shadow-sm d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-check-circle"></i> Confirm Booking
                    </button>
                </div>
            </div>

            <!-- RIGHT REGION: MAP -->
            <div class="col-12 col-md-8 p-3 bg-light">
                <div class="map-container shadow-sm">
                    <div id="map"></div>
                </div>
            </div>

        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

    <script>
        // 1. Initialize Map
        var map = L.map('map').setView([10.3157, 123.8854], 11);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        let customMarkers = [];
        let stopCounter = 0;

        // Pricing Logic Variables
        const BASE_RATE = 2500;
        const RATE_PER_STOP = 500;

        // 2. Search Functionality
        function executeSearch() {
            const query = document.getElementById('placeSearch').value;
            if (!query) return;

            fetch(
                    `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query + ', Cebu, Philippines')}`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.length > 0) {
                        const lat = parseFloat(data[0].lat);
                        const lon = parseFloat(data[0].lon);
                        let placeName = data[0].display_name.split(',').slice(0, 2).join(',');

                        map.setView([lat, lon], 15);
                        addCustomStop(lat, lon, placeName);
                        document.getElementById('placeSearch').value = ''; // clear search
                    } else {
                        alert("Location not found. Try being more specific.");
                    }
                })
                .catch(err => console.error("Search error:", err));
        }

        // 3. Map Click Functionality
        map.on('click', function(e) {
            fetch(
                    `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${e.latlng.lat}&lon=${e.latlng.lng}`)
                .then(response => response.json())
                .then(data => {
                    let placeName = data.address.amenity || data.address.road || data.address.suburb ||
                        "Selected Map Point";
                    addCustomStop(e.latlng.lat, e.latlng.lng, placeName);
                })
                .catch(err => {
                    addCustomStop(e.latlng.lat, e.latlng.lng, "Pinned Location");
                });
        });

        // 4. Add Stop to UI and Map
        function addCustomStop(lat, lng, name) {
            let markerIcon;
            let isPickup = (stopCounter === 0);
            let currentStopId = stopCounter;

            if (isPickup) {
                // Pickup Pin (Gold)
                markerIcon = L.icon({
                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-gold.png',
                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    tooltipAnchor: [1, -34],
                    shadowSize: [41, 41]
                });
                document.getElementById('pickupInput').value = name;
                document.getElementById('pickupLat').value = lat;
                document.getElementById('pickupLng').value = lng;
            } else {
                // Regular Stop Pin (Blue)
                markerIcon = L.icon({
                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    tooltipAnchor: [1, -34],
                    shadowSize: [41, 41]
                });

                const stopsContainer = document.getElementById('stopsContainer');
                const stopHtml = `
                    <div class="border rounded-3 p-2 bg-white mb-2 d-flex align-items-center justify-content-between shadow-sm stop-item" data-id="${currentStopId}">
                        <div class="overflow-hidden w-100 me-2">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge bg-primary rounded-circle px-2">${currentStopId}</span>
                                <input type="text" id="stopInput_${currentStopId}" name="stops[${currentStopId}][name]" class="form-control form-control-sm border-0 bg-transparent fw-medium p-0 text-truncate" value="${name}">
                                <input type="hidden" id="stopLat_${currentStopId}" name="stops[${currentStopId}][lat]" value="${lat}">
                                <input type="hidden" id="stopLng_${currentStopId}" name="stops[${currentStopId}][lng]" value="${lng}">
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-light text-danger rounded-circle p-1" onclick="removeStop(${currentStopId})">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                `;
                stopsContainer.insertAdjacentHTML('beforeend', stopHtml);
            }

            var marker = L.marker([lat, lng], {
                icon: markerIcon,
                draggable: true // Make all markers draggable
            }).addTo(map);
            
            marker.bindTooltip(isPickup ? `Pickup Location: ${name}` : `Stop ${currentStopId}: ${name}`, {
                permanent: true,
                direction: 'top',
                className: 'small fw-bold'
            });

            marker.on('dragend', function(e) {
                var newLatLng = marker.getLatLng();
                map.panTo(newLatLng); // Center the map on the new location
                fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${newLatLng.lat}&lon=${newLatLng.lng}`)
                    .then(response => response.json())
                    .then(data => {
                        let placeName = data.address.amenity || data.address.road || data.address.suburb || "Selected Map Point";
                        if (isPickup) {
                            document.getElementById('pickupInput').value = placeName;
                            document.getElementById('pickupLat').value = newLatLng.lat;
                            document.getElementById('pickupLng').value = newLatLng.lng;
                            marker.setTooltipContent(`Pickup Location: ${placeName}`);
                        } else {
                            document.getElementById(`stopInput_${currentStopId}`).value = placeName;
                            document.getElementById(`stopLat_${currentStopId}`).value = newLatLng.lat;
                            document.getElementById(`stopLng_${currentStopId}`).value = newLatLng.lng;
                            marker.setTooltipContent(`Stop ${currentStopId}: ${placeName}`);
                        }
                    })
                    .catch(err => {
                        if (isPickup) {
                            document.getElementById('pickupInput').value = "Pinned Location";
                            document.getElementById('pickupLat').value = newLatLng.lat;
                            document.getElementById('pickupLng').value = newLatLng.lng;
                            marker.setTooltipContent("Pickup Location: Pinned Location");
                        } else {
                            document.getElementById(`stopInput_${currentStopId}`).value = "Pinned Location";
                            document.getElementById(`stopLat_${currentStopId}`).value = newLatLng.lat;
                            document.getElementById(`stopLng_${currentStopId}`).value = newLatLng.lng;
                            marker.setTooltipContent(`Stop ${currentStopId}: Pinned Location`);
                        }
                    });
            });

            customMarkers.push({
                id: currentStopId,
                marker: marker
            });
            stopCounter++;
            calculateEstimate();
        }

        // 5. Remove Stop
        function removeStop(id) {
            // Remove from UI
            const stopEl = document.querySelector(`.stop-item[data-id="${id}"]`);
            if (stopEl) stopEl.remove();

            // Remove from Map
            const markerObj = customMarkers.find(m => m.id === id);
            if (markerObj) {
                map.removeLayer(markerObj.marker);
                customMarkers = customMarkers.filter(m => m.id !== id);
            }
            calculateEstimate();
            updateStopNumbers();
        }

        // Initialize SortableJS for Drag and Drop Reordering
        document.addEventListener("DOMContentLoaded", function() {
            var el = document.getElementById('stopsContainer');
            Sortable.create(el, {
                animation: 150,
                ghostClass: 'bg-light',
                onEnd: function (evt) {
                    updateStopNumbers();
                }
            });
        });

        function updateStopNumbers() {
            const stops = document.querySelectorAll('.stop-item');
            stops.forEach((stopEl, index) => {
                const newStopNumber = index + 1;
                // Update badge
                const badge = stopEl.querySelector('.badge');
                if (badge) {
                    badge.innerText = newStopNumber;
                }
                
                // Get the stop id
                const stopId = parseInt(stopEl.getAttribute('data-id'));
                
                // Update the marker's tooltip
                const markerObj = customMarkers.find(m => m.id === stopId);
                if (markerObj) {
                    const placeName = stopEl.querySelector(`input[id="stopInput_${stopId}"]`).value;
                    markerObj.marker.setTooltipContent(`Stop ${newStopNumber}: ${placeName}`);
                }
            });
        }

        // 6. Calculate Price
        function calculateEstimate() {
            const pax = parseInt(document.getElementById('tourPax').value) || 1;

            // Only count items in the DOM (excluding pickup)
            const activeStops = document.querySelectorAll('.stop-item').length;

            let total = 0;

            if (stopCounter > 0) { // If at least pickup is selected
                total = BASE_RATE + (activeStops * RATE_PER_STOP);

                // Add pax surcharge for larger vehicles
                if (pax > 4 && pax <= 8) total += 1000; // Upgrade to SUV/Van
                if (pax > 8) total += 2000; // Upgrade to Large Van
            }

            document.getElementById('estimatedPrice').innerText =
                `₱${total.toLocaleString('en-US', { minimumFractionDigits: 2 })}`;
        }
    </script>
</body>

</html>
