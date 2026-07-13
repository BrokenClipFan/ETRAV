<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Packages</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <style>
        /* Force screen containment for the desktop 3-column layout */
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }
        .main-admin-wrapper {
            height: calc(100vh - 65px);
        }
        .scrollable-panel {
            height: 100%;
            overflow-y: auto;
        }
        #adminFullMap {
            height: 100%;
            width: 100%;
        }
        .spot-badge-item:hover {
            background-color: #fff5f5 !important;
            border-color: #f5c2c2 !important;
        }
        .leaflet-popup-content {
            width: 220px !important;
            margin: 13px;
        }
        /* Mobile fallback alignment handling */
        @media (max-width: 767.98px) {
            html, body { overflow: auto; height: auto; }
            .main-admin-wrapper { height: auto; }
            .scrollable-panel { height: auto; overflow-y: visible; }
            #adminFullMap { height: 400px; }
        }
    </style>
</head>
<body class="bg-light">

    <!-- NAVIGATION BAR (Breeze Layout Style Layout) -->
    <nav class="navbar navbar-expand-md navbar-light bg-white border-bottom py-3" style="height: 65px;">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold text-dark d-flex align-items-center gap-2" href="#">
                <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" style="height: 36px; width: 36px;" class="text-primary"><path d="M24 4C12.95 4 4 12.95 4 24s8.95 20 20 20 20-8.95 20-20S35.05 4 24 4zm2 32h-4v-4h4v4zm0-8h-4V12h4v16z" fill="currentColor"/></svg>
                <span class="fs-5 fw-semibold tracking-wider">Admin Dashboard</span>
            </a>
            <div class="ms-auto">
                <span class="text-muted small fw-medium me-3">Admin Mode</span>
                <a href="#" class="btn btn-sm btn-outline-secondary rounded-pill px-3">Go to User View</a>
            </div>
        </div>
    </nav>

    <!-- MAIN GRID CONTAINER -->
    <div class="container-fluid main-admin-wrapper">
        <div class="row h-100 g-0">
            
            <!-- COLUMN 1: Active Packages Directory -->
            <div class="col-12 col-md-3 bg-white border-end h-100 scrollable-panel p-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold text-dark mb-0">Active Packages</h6>
                    <span class="badge bg-dark-subtle text-dark border">2 Total</span>
                </div>
                
                <!-- Package Item 1 -->
                <div class="p-3 bg-light rounded-3 mb-2 border border-light-subtle d-flex justify-content-between align-items-start">
                    <div style="max-width: 70%;">
                        <div class="d-flex align-items-center gap-1 mb-1">
                            <h6 class="fw-bold mb-0 text-dark small text-truncate">Cebu Historical</h6>
                            <span class="badge bg-primary-subtle text-primary rounded-pill px-2 py-0.5" style="font-size: 9px;">Popular</span>
                        </div>
                        <span class="text-muted font-monospace d-block" style="font-size: 11px;">Base: ₱1,500</span>
                        <span class="text-muted font-monospace d-block" style="font-size: 11px;">+₱200/head (Max 15)</span>
                    </div>
                    <div class="btn-group btn-group-sm shadow-sm">
                        <button class="btn btn-white border text-secondary bg-white" title="Edit"><i class="bi bi-pencil-square"></i></button>
                        <button class="btn btn-white border text-danger bg-white" title="Delete"><i class="bi bi-trash3"></i></button>
                    </div>
                </div>

                <!-- Package Item 2 -->
                <div class="p-3 bg-light rounded-3 mb-2 border border-light-subtle d-flex justify-content-between align-items-start">
                    <div style="max-width: 70%;">
                        <div class="d-flex align-items-center gap-1 mb-1">
                            <h6 class="fw-bold mb-0 text-dark small text-truncate">Highland Escape</h6>
                            <span class="badge bg-success-subtle text-success rounded-pill px-2 py-0.5" style="font-size: 9px;">Best Combo</span>
                        </div>
                        <span class="text-muted font-monospace d-block" style="font-size: 11px;">Base: ₱2,200</span>
                        <span class="text-muted font-monospace d-block" style="font-size: 11px;">+₱350/head (Max 10)</span>
                    </div>
                    <div class="btn-group btn-group-sm shadow-sm">
                        <button class="btn btn-white border text-secondary bg-white" title="Edit"><i class="bi bi-pencil-square"></i></button>
                        <button class="btn btn-white border text-danger bg-white" title="Delete"><i class="bi bi-trash3"></i></button>
                    </div>
                </div>
            </div>
            
            <!-- COLUMN 2: Form Creator Panel -->
            <div class="col-12 col-md-4 bg-white border-end h-100 scrollable-panel p-4">
                <div class="mb-3">
                    <h5 class="fw-bold text-dark mb-1">✨ Create Package</h5>
                    <p class="text-muted small">Configure logic settings, package categorization badges, and coordinates.</p>
                </div>
                
                <!-- Action target should route to your Laravel backend resource stack store method -->
                <form id="packageForm" action="#" method="POST" enctype="multipart/form-data">
                    <!-- Laravel CSRF Protection Field Token Container placeholder -->
                    <!-- <input type="hidden" name="_token" value="{{ csrf_token() }}"> -->
                    
                    <!-- HIDDEN FLEXIBLE PAYLOAD INPUT FOR TOURIST SPOTS -->
                    <input type="hidden" name="tourist_spots" id="touristSpotsPayload">

                    <div class="row">
                        <div class="col-7 mb-2">
                            <label class="form-label text-muted small fw-bold mb-1">Package Name</label>
                            <input type="text" name="name" class="form-control rounded-3 form-control-sm" placeholder="e.g., South Cebu Adventure" required>
                        </div>
                        <div class="col-5 mb-2">
                            <label class="form-label text-muted small fw-bold mb-1">Package Type</label>
                            <select name="type" class="form-select rounded-3 form-control-sm" style="font-size: 13px;" required>
                                <option value="" disabled selected>Select tag...</option>
                                <option value="popular">🔥 Popular</option>
                                <option value="best_combo">⭐ Best Combo</option>
                                <option value="trending">⚡ Trending</option>
                                <option value="budget">💰 Budget Friendly</option>
                            </select>
                        </div>
                    </div>

                    <!-- Split pricing model metrics input structure -->
                    <div class="row">
                        <div class="col-4 mb-2">
                            <label class="form-label text-muted small fw-bold mb-1">Base Price</label>
                            <input type="number" name="base_price" class="form-control rounded-3 form-control-sm" placeholder="₱1500" min="0" required>
                        </div>
                        <div class="col-4 mb-2">
                            <label class="form-label text-muted small fw-bold mb-1">Price / Head</label>
                            <input type="number" name="price_per_head" class="form-control rounded-3 form-control-sm" placeholder="₱200" min="0" required>
                        </div>
                        <div class="col-4 mb-2">
                            <label class="form-label text-muted small fw-bold mb-1">Pax Limit</label>
                            <input type="number" name="pax_limit" class="form-control rounded-3 form-control-sm" placeholder="Max" min="1" required>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label text-muted small fw-bold mb-1">Feature Banner Image</label>
                        <input type="file" name="banner_image" class="form-control rounded-3 form-control-sm">
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold mb-1">Description</label>
                        <textarea name="description" class="form-control rounded-3 form-control-sm" rows="2" placeholder="Brief tour overview..."></textarea>
                    </div>

                    <!-- Selected Spots Itinerary Dynamic Queue Tracker Layout element -->
                    <div class="mb-4">
                        <label class="form-label text-muted small fw-bold d-flex justify-content-between align-items-center mb-2">
                            <span>📍 Selected Spots Itinerary</span>
                            <span class="badge bg-primary rounded-pill font-monospace" id="spotCountBadge">0</span>
                        </label>
                        <div id="selectedSpotsContainer" class="d-flex flex-column gap-2 p-2 bg-light rounded-3 border" style="min-height: 120px;">
                            <span class="text-muted small text-center my-auto mx-auto id-empty-msg">Click anywhere on the map grid to add tourist locations via popup form.</span>
                        </div>
                    </div>

                    <div class="d-flex gap-2 pt-2 border-top">
                        <button type="reset" class="btn btn-light btn-sm rounded-pill flex-fill fw-medium text-muted" onclick="clearAllSpots()">Clear</button>
                        <button type="submit" class="btn btn-primary btn-sm rounded-pill flex-fill fw-medium">Save Package</button>
                    </div>
                </form>
            </div>

            <!-- COLUMN 3: Large Interactive Leaflet Map Grid Container -->
            <div class="col-12 col-md-5 col-lg-5 h-100 position-relative">
                <div id="adminFullMap"></div>
            </div>

        </div>
    </div>

    <!-- Core Script Dependancy Assets loaded dynamically -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        // Set standard maps center configurations focused around Cebu City geographic baseline coordinates
        var adminMap = L.map('adminFullMap').setView([10.3157, 123.8854], 11);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap'
        }).addTo(adminMap);

        let activeSpots = [];
        let temporaryMarker = null;

        // Catch canvas selections to render working interactive input panels directly over nodes
        adminMap.on('click', function(e) {
            const lat = e.latlng.lat;
            const lng = e.latlng.lng;

            if (temporaryMarker) {
                adminMap.removeLayer(temporaryMarker);
            }

            temporaryMarker = L.marker([lat, lng]).addTo(adminMap);

            const popupContent = `
                <div class="p-1">
                    <h6 class="fw-bold text-dark mb-2 border-bottom pb-1" style="font-size: 13px;"><i class="bi bi-geo-alt-fill text-danger"></i> Add Spot</h6>
                    <div class="mb-2">
                        <label class="form-label text-muted mb-0" style="font-size: 10px;">Spot Place Name</label>
                        <input type="text" id="popSpotName" class="form-control form-control-sm rounded-2 py-0.5" placeholder="e.g., Oslob Whale Sharks" style="font-size: 11px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted mb-0" style="font-size: 10px;">Spot Image URL</label>
                        <input type="text" id="popSpotImg" class="form-control form-control-sm rounded-2 py-0.5" placeholder="https://example.com/pic.jpg" style="font-size: 11px;">
                    </div>
                    <div class="d-flex gap-1">
                        <button class="btn btn-secondary btn-xs text-white rounded-2 w-50 py-0.5" style="font-size: 11px;" onclick="cancelPopupMarker()">Cancel</button>
                        <button class="btn btn-primary btn-xs rounded-2 w-50 py-0.5" style="font-size: 11px;" onclick="confirmPopupMarker(${lat}, ${lng})">Add Spot</button>
                    </div>
                </div>
            `;

            temporaryMarker.bindPopup(popupContent, { closeButton: false }).openPopup();
        });

        function cancelPopupMarker() {
            if (temporaryMarker) {
                adminMap.removeLayer(temporaryMarker);
                temporaryMarker = null;
            }
        }

        function confirmPopupMarker(lat, lng) {
            const spotName = document.getElementById('popSpotName').value;
            const spotImg = document.getElementById('popSpotImg').value || 'https://via.placeholder.com/150';

            if (!spotName || spotName.trim() === "") {
                alert("Please type a location name.");
                return;
            }

            temporaryMarker.closePopup();
            temporaryMarker.unbindPopup();
            
            temporaryMarker.bindPopup(`
                <div class="text-center">
                    <img src="${spotImg}" class="rounded border mb-1 img-fluid" style="max-height: 80px; object-fit: cover; width: 100%;">
                    <b class="d-block text-dark" style="font-size: 12px;">${spotName}</b>
                </div>
            `);

            const spotObject = { name: spotName, img: spotImg, lat: lat, lng: lng, markerRef: temporaryMarker };
            activeSpots.push(spotObject);

            temporaryMarker = null;
            renderSpotsBadges();
        }

        function renderSpotsBadges() {
            const container = document.getElementById('selectedSpotsContainer');
            container.innerHTML = '';

            document.getElementById('spotCountBadge').innerText = activeSpots.length;

            if (activeSpots.length === 0) {
                container.innerHTML = '<span class="text-muted small text-center my-auto mx-auto id-empty-msg">Click anywhere on the map grid to add tourist locations via popup form.</span>';
                return;
            }

            activeSpots.forEach((spot, idx) => {
                const itemRow = document.createElement('div');
                itemRow.className = "d-flex justify-content-between align-items-center bg-white p-2 border rounded-3 spot-badge-item shadow-sm";
                
                itemRow.innerHTML = `
                    <div class="d-flex align-items-center gap-2" style="max-width: 85%;">
                        <img src="${spot.img}" class="rounded" style="width: 28px; height: 28px; object-fit: cover;">
                        <span class="small fw-semibold text-dark text-truncate" style="font-size: 12px;">${spot.name}</span>
                    </div>
                    <button type="button" class="btn p-0 border-0 text-danger" onclick="deleteSpot(${idx})"><i class="bi bi-trash"></i></button>
                `;
                
                container.appendChild(itemRow);
            });
        }

        function deleteSpot(index) {
            adminMap.removeLayer(activeSpots[index].markerRef);
            activeSpots.splice(index, 1);
            renderSpotsBadges();
        }

        function clearAllSpots() {
            activeSpots.forEach(spot => adminMap.removeLayer(spot.markerRef));
            activeSpots = [];
            renderSpotsBadges();
        }

        // FORM SUBMISSION: Automatically stringifies dynamic coordinate sets into single input parameters 
        document.getElementById('packageForm').addEventListener('submit', function(e) {
            // Filter dynamic object contexts down into standard raw data variables
            const payloadData = activeSpots.map(spot => ({
                name: spot.name,
                img: spot.img,
                lat: spot.lat,
                lng: spot.lng
            }));

            // Assign string structural array sets onto programmatic target inputs
            document.getElementById('touristSpotsPayload').value = JSON.stringify(payloadData);
        });
    </script>
</body>
</html>