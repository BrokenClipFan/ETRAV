<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cebu Tour Packages</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <style>
        .main-wrapper {
            height: calc(100vh - 65px); /* Matches Breeze default navbar height */
            overflow: hidden;
        }
        .sidebar-scroll {
            height: 100%;
            overflow-y: auto;
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
        /* Floating Spot List in Bottom Right of Map */
        .spots-overlay-panel {
            position: absolute;
            bottom: 20px;
            right: 20px;
            width: 320px;
            max-height: 250px;
            overflow-y: auto;
            z-index: 1000; /* Stays on top of Leaflet layers */
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
    </style>
</head>
<body class="bg-light">

    <!-- 1. AUTHENTICATION NAVBAR (Strict Laravel Breeze Style Conversion) -->
    <nav class="navbar navbar-expand-md navbar-light bg-white border-bottom sticky-top py-3">
        <div class="container-fluid px-4">
            <!-- Application Logo Placeholder -->
            <a class="navbar-brand fw-bold text-dark d-flex align-items-center gap-2" href="#">
                <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" style="height: 36px; width: 36px;" class="text-primary fill-current">
                    <path d="M24 4C12.95 4 4 12.95 4 24s8.95 20 20 20 20-8.95 20-20S35.05 4 24 4zm2 32h-4v-4h4v4zm0-8h-4V12h4v16z" fill="currentColor"/>
                </svg>
                <span class="fs-5 tracking-wider">Laravel</span>
            </a>

            <!-- Right Side Settings Dropdown -->
            <div class="ms-auto">
                <div class="dropdown">
                    <button class="btn border-0 d-flex align-items-center gap-1 text-muted fw-medium fs-6 bg-transparent p-0" type="button" id="breezeDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <span>John Ninyo Sismar</span>
                        <i class="bi bi-chevron-down small style-muted ms-1" style="font-size: 12px;"></i>
                    </button>
                    <!-- Dropdown Content (Matches Breeze Menu Options) -->
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border mt-2 py-1" aria-labelledby="breezeDropdown" style="width: 200px; border-radius: 6px;">
                        <li><a class="dropdown-item py-2 text-muted px-4" href="#"><i class="bi bi-person me-2"></i> Profile</a></li>
                        <li><a class="dropdown-item py-2 text-muted px-4" href="#"><i class="bi bi-journal-bookmark me-2"></i> Bookings</a></li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <form method="POST" action="#">
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
            
            <!-- LEFT SIDEBAR: Slimmed down list with Vertical Cards -->
            <div class="col-12 col-md-4 sidebar-scroll p-4 bg-white border-end">
                <div class="mb-4">
                    <h5 class="fw-bold mb-1 text-dark">Cebu Tour Packages</h5>
                    <p class="text-muted small">Select a package to view its specific route map.</p>
                </div>

                <!-- Package 1: Vertical Layout Grid -->
                <div class="card border border-light-subtle shadow-sm rounded-4 mb-4 overflow-hidden">
                    <div class="position-relative bg-secondary-subtle text-center d-flex align-items-center justify-content-center text-muted" style="height: 160px;">
                        <i class="bi bi-image fs-1 opacity-25"></i>
                        <span class="position-absolute top-0 end-0 m-2 badge bg-dark px-2.5 py-1.5 rounded-pill fs-7">₱200/person</span>
                    </div>
                    <div class="card-body p-3">
                        <h6 class="fw-bold text-dark mb-1">Cebu Historical Tour</h6>
                        <p class="text-muted mb-3" style="font-size: 13px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            Enjoy the wonders of cebu. Explore deep historical monuments and heritage sites right in the heart of the city.
                        </p>
                        
                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                            <span class="text-muted font-monospace" style="font-size: 12px;"><i class="bi bi-geo-alt-fill text-danger"></i> 3 Spots</span>
                            <button class="btn btn-primary btn-sm rounded-pill px-3 py-1 fw-medium" onclick="loadPackageSpots(1)" style="font-size: 12px;">View Spots</button>
                        </div>
                    </div>
                </div>

                <!-- Package 2: Vertical Layout Grid -->
                <div class="card border border-light-subtle shadow-sm rounded-4 mb-4 overflow-hidden">
                    <div class="position-relative bg-secondary-subtle text-center d-flex align-items-center justify-content-center text-muted" style="height: 160px;">
                        <i class="bi bi-image fs-1 opacity-25"></i>
                        <span class="position-absolute top-0 end-0 m-2 badge bg-dark px-2.5 py-1.5 rounded-pill fs-7">₱350/person</span>
                    </div>
                    <div class="card-body p-3">
                        <h6 class="fw-bold text-dark mb-1">Highland & Nature Escape</h6>
                        <p class="text-muted mb-3" style="font-size: 13px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            Escape to the scenic mountain views and dynamic floral gardens overlooking the city landscape.
                        </p>
                        
                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                            <span class="text-muted font-monospace" style="font-size: 12px;"><i class="bi bi-geo-alt-fill text-danger"></i> 2 Spots</span>
                            <button class="btn btn-primary btn-sm rounded-pill px-3 py-1 fw-medium" onclick="loadPackageSpots(2)" style="font-size: 12px;">View Spots</button>
                        </div>
                    </div>
                </div>

            </div>

            <!-- RIGHT REGION: Map View & Floating Spots Overlay List -->
            <div class="col-12 col-md-8 p-3 bg-light position-relative d-none d-md-block">
                <div class="map-container">
                    <!-- Map Canvas -->
                    <div id="map" class="shadow-sm"></div>

                    <!-- Floating Spot List Overlay Box (Bottom Right) -->
                    <div id="spotsPanel" class="spots-overlay-panel card shadow border-0 bg-white d-none">
                        <div class="card-header bg-dark text-white py-2 px-3 fw-bold small d-flex justify-content-between align-items-center">
                            <span>📍 Tour Spots Itinerary</span>
                            <span class="badge bg-secondary-subtle text-dark border font-monospace" id="spotCount">0</span>
                        </div>
                        <div class="list-group list-group-flush" id="spotsListGroup">
                            <!-- Dynamic links appended via JavaScript -->
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Leaflet Map JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <!-- 3. INTERACTIVE MAP & PIN ENGINE SCRIPT -->
    <script>
        // Init Map Focus Point (Cebu)
        var map = L.map('map').setView([10.3157, 123.8854], 12);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        // Track active markers on screen to clear them out later
        var currentMarkers = [];

        // Sample Package Datasets (In production, replace with blade loops or AJAX fetch data)
        const packageData = {
            1: {
                name: "Cebu Historical Tour",
                spots: [
                    { name: "Magellan's Cross", lat: 10.2939, lng: 123.9019 },
                    { name: "Fort San Pedro", lat: 10.2927, lng: 123.9061 },
                    { name: "Basilica del Sto. Niño", lat: 10.2942, lng: 123.9021 }
                ]
            },
            2: {
                name: "Highland & Nature Escape",
                spots: [
                    { name: "Sirao Flower Garden", lat: 10.3995, lng: 123.8706 },
                    { name: "Temple of Leah", lat: 10.3694, lng: 123.9123 }
                ]
            }
        };

        function loadPackageSpots(packageId) {
            // 1. Clear existing pins
            currentMarkers.forEach(marker => map.removeLayer(marker));
            currentMarkers = [];

            const selectedPackage = packageData[packageId];
            if (!selectedPackage) return;

            const listGroup = document.getElementById('spotsListGroup');
            listGroup.innerHTML = ''; // Reset DOM text

            let bounds = [];

            // 2. Loop spots to create pins and list view elements
            selectedPackage.spots.forEach((spot, index) => {
                // Generate Leaflet Map Pin
                var marker = L.marker([spot.lat, spot.lng]).addTo(map)
                    .bindPopup(`<b>${spot.name}</b><br>Stop #${index + 1}`);
                
                currentMarkers.push(marker);
                bounds.push([spot.lat, spot.lng]);

                // Generate Floating Panel Row Item Button
                const btn = document.createElement('button');
                btn.className = "list-group-item list-group-item-action spot-item-btn border-0 py-2.5 px-3 small bg-white text-muted fw-medium d-flex align-items-center gap-2";
                btn.innerHTML = `<span class="badge bg-primary-subtle text-primary rounded-circle font-monospace">${index + 1}</span> ${spot.name}`;
                
                // Click interactive handler: Zooms right down to the marker coordinates!
                btn.onclick = function() {
                    map.setView([spot.lat, spot.lng], 16);
                    marker.openPopup();
                };
                listGroup.appendChild(btn);
            });

            // 3. Update overlay indicators and auto-scale view bounding bounds
            document.getElementById('spotCount').innerText = selectedPackage.spots.length;
            document.getElementById('spotsPanel').classList.remove('d-none');
            
            if(bounds.length > 0) {
                map.fitBounds(bounds, { padding: [50, 50] });
            }
        }
    </script>
</body>
</html>