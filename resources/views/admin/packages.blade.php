<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Advanced Packages & Spots Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <style>
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
        .package-item-card {
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .package-item-card:hover {
            border-color: #0d6efd !important;
            background-color: #f8fafc !important;
        }
        .spot-badge-item:hover {
            background-color: #fff5f5 !important;
            border-color: #f5c2c2 !important;
        }
        .leaflet-popup-content {
            width: 250px !important;
            margin: 13px;
        }
        .map-itinerary-panel {
            background: white;
            padding: 12px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            max-height: 250px;
            width: 270px;
            overflow-y: auto;
            border: 1px solid #e2e8f0;
            pointer-events: auto;
        }
        .map-itinerary-item {
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .map-itinerary-item:hover {
            background-color: #f1f5f9 !important;
            border-color: #cbd5e1 !important;
        }
        .map-mode-indicator {
            position: absolute;
            top: 15px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }
        @media (max-width: 767.98px) {
            html, body { overflow: auto; height: auto; }
            .main-admin-wrapper { height: auto; }
            .scrollable-panel { height: auto; overflow-y: visible; }
            #adminFullMap { height: 400px; }
        }

        .custom-modern-popup .leaflet-popup-content-wrapper {
            padding: 4px;
            border-radius: 14px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        }
        .custom-modern-popup .leaflet-popup-content {
            margin: 8px !important;
            width: 240px !important;
        }
    </style>
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-md navbar-light bg-white border-bottom py-3" style="height: 65px;">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold text-dark d-flex align-items-center gap-2" href="#">
                <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" style="height: 36px; width: 36px;" class="text-primary">
                    <path d="M24 4C12.95 4 4 12.95 4 24s8.95 20 20 20 20-8.95 20-20S35.05 4 24 4zm2 32h-4v-4h4v4zm0-8h-4V12h4v16z" fill="currentColor"/>
                </svg>
                <span class="fs-5 fw-semibold tracking-wider">Admin Dashboard</span>
            </a>

            <div class="ms-auto d-flex align-items-center gap-4">
                <div class="d-none d-lg-block">
                    <span class="text-muted small fw-medium">
                        <i class="bi bi-mouse-fill text-primary"></i> Left-Click Pins to Attach | Right-Click Map to Create Spot
                    </span>
                </div>

                <div class="position-relative cursor-pointer" role="button" id="notificationDropdown" style="z-index: 1050;">
                    <i class="bi bi-bell text-secondary fs-5 hover-text-dark"></i>
                    <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
                        <span class="visually-hidden">New alerts</span>
                    </span>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid main-admin-wrapper">
        <div class="row h-100 g-0">
            
            <div class="col-12 col-md-3 bg-white border-end h-100 scrollable-panel p-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold text-dark mb-0">Active Packages</h6>
                    <span class="badge bg-dark-subtle text-dark border">{{ $packages->count() }} total</span>
                </div>
                
                <div id="packagesDirectoryContainer">
                    @foreach($packages as $package)
                        <div class="p-3 bg-light rounded-3 mb-2 border border-light-subtle d-flex justify-content-between align-items-start package-item-card" 
                            onclick="loadPackageToForm({{ json_encode($package) }})">
                            <div style="max-width: 70%;">
                                <div class="d-flex align-items-center gap-1 mb-1">
                                    <h6 class="fw-bold mb-0 text-dark small text-truncate">{{ $package->name }}</h6>
                                </div>
                                <span class="text-muted font-monospace d-block" style="font-size: 11px;">Base: ₱{{ $package->package_price }}</span>
                            </div>
                            <div class="btn-group btn-group-sm shadow-sm">
                                <button type="button" class="btn btn-white border text-secondary bg-white" title="Edit" 
                                        onclick="event.stopPropagation(); loadPackageToForm({{ json_encode($package) }})">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button type="button" class="btn btn-white border text-danger bg-white" title="Delete" 
                                        onclick="event.stopPropagation(); confirmDeletePackage({{ $package->id }}, '{{ addslashes($package->name) }}')">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            
            <div class="col-12 col-md-4 bg-white border-end h-100 scrollable-panel p-4">
                <div class="mb-3">
                    <h5 class="fw-bold text-dark mb-1" id="formActionHeader">✨ Create Package</h5>
                    <p class="text-muted small" id="formActionSubtext">Configure parameters and attach global locations below.</p>
                </div>
                
                <form id="packageForm" action="{{ route('admin.store.package') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="package_id" id="formPackageId" value="">
                    <input type="hidden" name="attached_spot_ids" id="attachedSpotsPayload">

                    <div class="row">
                        <div class="col-7 mb-2">
                            <label class="form-label text-muted small fw-bold mb-1">Package Name</label>
                            <input type="text" name="name" id="inputName" class="form-control rounded-3 form-control-sm" placeholder="e.g., South Cebu Tour" required>
                        </div>
                        <div class="col-5 mb-2">
                            <label class="form-label text-muted small fw-bold mb-1">Package Type</label>
                            <select name="type" id="selectType" class="form-select rounded-3 form-control-sm" style="font-size: 13px;" required>
                                <option value="" disabled selected>Select tag...</option>
                                <option value="popular">🔥 Popular</option>
                                <option value="best_combo">⭐ Best Combo</option>
                                <option value="trending">⚡ Trending</option>
                                <option value="budget">💰 Budget Friendly</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-4 mb-2">
                            <label class="form-label text-muted small fw-bold mb-1">Base Price</label>
                            <input type="number" name="package_price" id="inputPackagePrice" class="form-control rounded-3 form-control-sm" min="0" required>
                        </div>
                        <div class="col-4 mb-2">
                            <label class="form-label text-muted small fw-bold mb-1">Pax Limit</label>
                            <input type="number" name="pax" id="inputPax" class="form-control rounded-3 form-control-sm" min="1" required>
                        </div>
                        <div class="col-4 mb-2">
                            <label class="form-label text-muted small fw-bold mb-1">Price / Head</label>
                            <input type="number" name="perhead_price" id="inputPerHeadPrice" class="form-control rounded-3 form-control-sm" min="0" required disabled>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label text-muted small fw-bold mb-1">Feature Banner Image</label>
                        <input type="file" name="image" id="inputImage" class="form-control rounded-3 form-control-sm">
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold mb-1">Description</label>
                        <textarea name="description" id="textareaDescription" class="form-control rounded-3 form-control-sm" rows="2" placeholder="Brief tour overview..."></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-muted small fw-bold d-flex justify-content-between align-items-center mb-2">
                            <span>📍 Attached Itinerary Pipeline</span>
                            <span class="badge bg-primary rounded-pill font-monospace" id="spotCountBadge">0</span>
                        </label>
                        <div id="selectedSpotsContainer" class="d-flex flex-column gap-2 p-2 bg-light rounded-3 border" style="min-height: 120px;">
                            <span class="text-muted small text-center my-auto mx-auto id-empty-msg">Left-click existing map pins to build the itinerary chain loop sequence.</span>
                        </div>
                    </div>

                    <div class="d-flex gap-2 align-items-center mt-3">
                        <button type="button" id="formResetBtn" class="btn btn-light rounded-pill w-50 py-2 border text-secondary" style="font-size: 13px;" onclick="clearPackageForm()">
                            Clear Form
                        </button>
                        
                        <button type="submit" id="formSubmitBtn" class="btn btn-primary rounded-pill w-50 py-2 fw-medium shadow-sm" style="font-size: 13px;">
                            Create Package
                        </button>
                    </div>
                </form>
            </div>

            <div class="col-12 col-md-5 col-lg-5 h-100 position-relative">
                <div id="adminFullMap"></div>
            </div>

        </div>
    </div>

    <form id="standaloneSpotForm" action="{{ route('admin.store.spot') }}" method="POST" enctype="multipart/form-data" class="d-none">
        @csrf
        <input type="text" name="name" id="hiddenSpotName">
        <input type="text" name="latitude" id="hiddenSpotLat">
        <input type="text" name="longitude" id="hiddenSpotLng">
        <input type="number" name="entrance_fee" id="hiddenSpotFee">
        <input type="text" name="description" id="hiddenSpotDesc">
        <input type="file" name="image" id="hiddenSpotFile">
    </form>

    <form id="deletePackageForm" method="POST" class="d-none">
        @csrf
        @method('DELETE')
    </form>

    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 380px;">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-body text-center p-4">
                    <div class="text-danger mb-3">
                        <i class="bi bi-exclamation-octagon-fill" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1" id="deleteConfirmModalLabel">Delete Package</h5>
                    <p class="text-muted small px-2 mb-0">
                        Are you sure you want to permanently delete <strong id="deleteTargetName" class="text-dark"></strong>? This action cannot be undone.
                    </p>
                    
                    <div class="d-flex gap-2 justify-content-center mt-4">
                        <button type="button" class="btn btn-light rounded-pill px-4 border text-secondary w-50" style="font-size: 13px;" data-bs-dismiss="modal">
                            Cancel
                        </button>
                        <button type="button" id="btnConfirmDeleteAction" class="btn btn-danger rounded-pill px-4 fw-medium shadow-sm w-50" style="font-size: 13px;" onclick="executePackageDeletion()">
                            Yes, Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        var adminMap = L.map('adminFullMap').setView([10.3157, 123.8854], 11);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap' }).addTo(adminMap);

        // Data Management Arrays
        let globalDatabaseSpots = @json($allRegisteredSpots ?? []); 
        let activeItinerarySpots = []; 
        
        let mapMarkersInstances = {}; 
        let activeRouteLine = null;
        let temporaryMarker = null;

        // Render Lower Right Itinerary Layout Control Overlay UI elements
        const itineraryControl = L.control({ position: 'bottomright' });
        itineraryControl.onAdd = function (map) {
            const div = L.DomUtil.create('div', 'map-itinerary-panel');
            div.innerHTML = `
                <div class="fw-bold border-bottom pb-1 mb-2 text-dark small"><i class="bi bi-signpost-2-fill text-primary"></i> Live Itinerary (ASC)</div>
                <div id="mapItineraryListContainer" class="d-flex flex-column gap-1.5"><div class="text-muted text-center small py-3">No landmarks attached.</div></div>
            `;
            L.DomEvent.disableScrollPropagation(div);
            L.DomEvent.disableClickPropagation(div);
            return div;
        };
        itineraryControl.addTo(adminMap);

        // Render Master Database Pins onto Layout Viewport using exact DB attributes
        function displayMasterDatabasePins() {
            globalDatabaseSpots.forEach(spot => {
                const lat = parseFloat(spot.latitude);
                const lng = parseFloat(spot.longitude);

                let dbMarker = L.marker([lat, lng]).addTo(adminMap);
                
                // Clean, modern, scannable card design layout matching Bootstrap UI standard
                let popupHtml = `
                    <div class="card border-0 bg-transparent" style="width: 240px;">
                        <img src="${spot.image_path || 'https://via.placeholder.com/150'}" 
                            class="card-img-top rounded-3 border bg-light shadow-sm" 
                            style="height: 110px; object-fit: cover; width: 100%;">
                        
                        <div class="card-body p-0 pt-2.5">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <h6 class="fw-bold text-dark mb-0 lh-sm text-truncate" style="max-width: 160px;">${spot.name}</h6>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-0.5 rounded-pill font-monospace" style="font-size: 11px;">
                                    ₱${spot.price || 0}
                                </span>
                            </div>
                            
                            <p class="text-muted mb-3 small" style="font-size: 11.5px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.4;">
                                ${spot.description && spot.description.trim() !== "" ? spot.description : 'No description available for this place.'}
                            </p>
                            
                            <button type="button" 
                                    class="btn btn-primary btn-sm rounded-pill w-100 fw-medium d-flex align-items-center justify-content-center gap-1.5 shadow-sm py-1.5" 
                                    style="font-size: 12px; transition: transform 0.1s ease;"
                                    onclick="attachPinToItinerary(${spot.id})">
                                <i class="bi bi-plus-circle-fill" style="font-size: 11px;"></i>
                                Attach to Package
                            </button>
                        </div>
                    </div>
                `;
                
                // Bind popup with customized styling overrides
                dbMarker.bindPopup(popupHtml, {
                    maxWidth: 270,
                    className: 'custom-modern-popup'
                });
                
                mapMarkersInstances[spot.id] = dbMarker;
            });
        }
        displayMasterDatabasePins();

        // GESTURE CHANGE: Map Contextmenu Event Handler (Fires on Right-Click)
        adminMap.on('contextmenu', function(e) {
            const lat = e.latlng.lat;
            const lng = e.latlng.lng;

            if (temporaryMarker) adminMap.removeLayer(temporaryMarker);

            temporaryMarker = L.marker([lat, lng]).addTo(adminMap);

            // Updated Layout: Removed duration, full-width fee, and added bottom padding (pb-2)
            const registrationFormHtml = `
                <div class="p-1 pb-2">
                    <h6 class="fw-bold text-dark mb-2 border-bottom pb-1" style="font-size: 13px;">
                        <i class="bi bi-cloud-plus-fill text-success"></i> Register New Spot
                    </h6>
                    <div class="mb-1.5">
                        <label class="form-label text-muted mb-0" style="font-size: 10px;">Spot Name</label>
                        <input type="text" id="newSpotName" class="form-control form-control-sm rounded-2 py-0.5" placeholder="e.g., Oslob Whale Sharks" style="font-size: 11px;">
                    </div>
                    <div class="mb-1.5">
                        <label class="form-label text-muted mb-0" style="font-size: 10px;">Entrance Fee (₱)</label>
                        <input type="number" id="newSpotFee" class="form-control form-control-sm rounded-2 py-0.5" placeholder="0" style="font-size: 11px;" min="0">
                    </div>
                    <div class="mb-1.5">
                        <label class="form-label text-muted mb-0" style="font-size: 10px;">Brief Description</label>
                        <textarea id="newSpotDesc" class="form-control form-control-sm rounded-2 py-0.5" rows="2" placeholder="Describe the location..." style="font-size: 11px;"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted mb-0" style="font-size: 10px;">Display Thumbnail Image</label>
                        <input type="file" id="newSpotFile" class="form-control form-control-sm rounded-2 py-0.5" accept="image/*" style="font-size: 11px;">
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary btn-xs text-white rounded-2 w-50 py-1" style="font-size: 11px;" onclick="cancelPopupMarker()">Cancel</button>
                        <button type="button" class="btn btn-success btn-xs rounded-2 w-50 py-1" style="font-size: 11px;" onclick="submitNewSpotToDatabase(${lat}, ${lng})">Save to DB</button>
                    </div>
                </div>
            `;
            temporaryMarker.bindPopup(registrationFormHtml, { closeButton: false }).openPopup();
        });

        function cancelPopupMarker() {
            if (temporaryMarker) {
                adminMap.removeLayer(temporaryMarker);
                temporaryMarker = null;
            }
        }

        function submitNewSpotToDatabase(lat, lng) {
            const name = document.getElementById('newSpotName').value;
            const fee = document.getElementById('newSpotFee').value;
            const desc = document.getElementById('newSpotDesc').value;
            const fileInput = document.getElementById('newSpotFile');

            if (!name || name.trim() === "") {
                alert("Please name your registered location point.");
                return;
            }

            document.getElementById('hiddenSpotName').value = name;
            document.getElementById('hiddenSpotLat').value = lat;
            document.getElementById('hiddenSpotLng').value = lng;
            document.getElementById('hiddenSpotFee').value = fee || 0;
            document.getElementById('hiddenSpotDesc').value = desc;
            
            if (fileInput.files.length > 0) {
                document.getElementById('hiddenSpotFile').files = fileInput.files;
            }

            document.getElementById('standaloneSpotForm').submit();
        }

        // Connects existing master items down smoothly inside package itineraries arrays logic
        function attachPinToItinerary(spotId) {
            const sourceMasterItem = globalDatabaseSpots.find(s => s.id === spotId);
            if (!sourceMasterItem) return;

            if (activeItinerarySpots.some(item => item.id === spotId)) {
                alert("This location is already attached inside this dynamic package.");
                return;
            }

            activeItinerarySpots.push({
                id: sourceMasterItem.id,
                name: sourceMasterItem.name,
                image_url: sourceMasterItem.image_path, 
                latitude: parseFloat(sourceMasterItem.latitude),
                longitude: parseFloat(sourceMasterItem.longitude),
                position: activeItinerarySpots.length + 1
            });

            if(mapMarkersInstances[spotId]) mapMarkersInstances[spotId].closePopup();
            renderItineraryViews();
        }

        // Render functions 
        function renderItineraryViews() {
            activeItinerarySpots.sort((a, b) => a.position - b.position);

            const formContainer = document.getElementById('selectedSpotsContainer');
            document.getElementById('spotCountBadge').innerText = activeItinerarySpots.length;

            if (activeItinerarySpots.length === 0) {
                formContainer.innerHTML = '<span class="text-muted small text-center my-auto mx-auto id-empty-msg">Left-click existing map pins to build the itinerary chain loop sequence.</span>';
            } else {
                formContainer.innerHTML = '';
                activeItinerarySpots.forEach((spot, idx) => {
                    const row = document.createElement('div');
                    row.className = "d-flex justify-content-between align-items-center bg-white p-2 border rounded-3 spot-badge-item shadow-sm";
                    row.innerHTML = `
                        <div class="d-flex align-items-center gap-2" style="max-width: 80%;">
                            <span class="badge bg-dark rounded-circle d-flex align-items-center justify-content-center" style="width:18px; height:18px; font-size:10px;">${spot.position}</span>
                            <img src="${spot.image_url || 'https://via.placeholder.com/150'}" class="rounded" style="width: 28px; height: 28px; object-fit: cover;">
                            <span class="small fw-semibold text-dark text-truncate" style="font-size:12px;">${spot.name}</span>
                        </div>
                        <button type="button" class="btn p-0 border-0 text-danger" onclick="removeSpotFromItinerary(${idx})"><i class="bi bi-trash"></i></button>
                    `;
                    formContainer.appendChild(row);
                });
            }

            const mapListContainer = document.getElementById('mapItineraryListContainer');
            if (mapListContainer) {
                if (activeItinerarySpots.length === 0) {
                    mapListContainer.innerHTML = '<div class="text-muted text-center small py-3">No landmarks attached.</div>';
                } else {
                    mapListContainer.innerHTML = '';
                    activeItinerarySpots.forEach(spot => {
                        const row = document.createElement('div');
                        row.className = "d-flex align-items-center gap-2 p-1.5 bg-light rounded-2 border map-itinerary-item";
                        row.onclick = function() {
                            adminMap.setView([spot.latitude, spot.longitude], 14, { animate: true, duration: 1 });
                            if (mapMarkersInstances[spot.id]) mapMarkersInstances[spot.id].openPopup();
                        };
                        row.innerHTML = `
                            <span class="badge bg-primary rounded-circle d-flex align-items-center justify-content-center border-0" style="width:16px; height:16px; font-size:9px;">${spot.position}</span>
                            <img src="${spot.image_url || 'https://via.placeholder.com/150'}" class="rounded border bg-white" style="width: 22px; height: 22px; object-fit: cover;">
                            <div class="small fw-medium text-dark text-truncate" style="font-size:11px; max-width:170px;">${spot.name}</div>
                            <i class="bi bi-search text-muted ms-auto animate-focus" style="font-size:10px;"></i>
                        `;
                        mapListContainer.appendChild(row);
                    });
                }
            }

            if (activeRouteLine) adminMap.removeLayer(activeRouteLine);
            if (activeItinerarySpots.length >= 2) {
                let pathCoordinates = activeItinerarySpots.map(s => [s.latitude, s.longitude]);
                activeRouteLine = L.polyline(pathCoordinates, {
                    color: '#0d6efd', weight: 4, opacity: 0.8, dashArray: '5, 10', lineJoin: 'round'
                }).addTo(adminMap);
            }
        }

        function removeSpotFromItinerary(index) {
            activeItinerarySpots.splice(index, 1);
            activeItinerarySpots.forEach((spot, idx) => { spot.position = idx + 1; });
            renderItineraryViews();
        }

        // LOAD STRATEGY PIPELINE: Hydrates forms fields on historical package click inputs selection
        function loadPackageToForm(packageData) {
            activeItinerarySpots = [];

            // Update form headers to Edit Mode
            document.getElementById('formActionHeader').innerText = "🛠️ Edit Package";
            document.getElementById('formActionSubtext').innerText = "Modifying parameters for: " + packageData.name;
            
            // Populate form hidden ID and standard fields
            document.getElementById('formPackageId').value = packageData.id;
            document.getElementById('inputName').value = packageData.name;
            document.getElementById('selectType').value = packageData.type || '';
            document.getElementById('inputPackagePrice').value = packageData.package_price;
            document.getElementById('inputPerHeadPrice').value = packageData.perhead_price;
            document.getElementById('inputPax').value = packageData.pax || 1;
            document.getElementById('textareaDescription').value = packageData.description || '';

            // DYNAMIC BUTTON CHANGE: Transform button state to Update Mode
            const submitBtn = document.getElementById('formSubmitBtn');
            submitBtn.innerText = "Save Edited Package";
            submitBtn.className = "btn btn-success rounded-pill w-50 py-2 fw-medium shadow-sm"; 
            
            const resetBtn = document.getElementById('formResetBtn');
            resetBtn.innerText = "Create New"; 
            resetBtn.className = "btn btn-outline-primary rounded-pill w-50 py-2";

            // Process and map attached places
            if (packageData.places && packageData.places.length > 0) {
                let bounds = [];
                let sortedPlaces = packageData.places.sort((a, b) => {
                    let posA = (a.pivot && a.pivot.position) ? parseInt(a.pivot.position) : 0;
                    let posB = (b.pivot && b.pivot.position) ? parseInt(b.pivot.position) : 0;
                    return posA - posB;
                });
                
                sortedPlaces.forEach((place, index) => {
                    activeItinerarySpots.push({
                        id: place.id, 
                        name: place.name,
                        image_url: place.image_path,
                        latitude: parseFloat(place.latitude),
                        longitude: parseFloat(place.longitude),
                        position: index + 1
                    });
                    
                    if (!isNaN(place.latitude) && !isNaN(place.longitude)) {
                        bounds.push([parseFloat(place.latitude), parseFloat(place.longitude)]);
                    }
                });

                renderItineraryViews();
                if (bounds.length > 0) {
                    adminMap.fitBounds(bounds, { padding: [50, 50] });
                }
            } else {
                renderItineraryViews();
            }
        }

        // FIXED: Completely resets form states and fields back to Create state
        function clearPackageForm() {
            const formElement = document.getElementById('packageForm');
            if (formElement) {
                formElement.reset();
            }
            
            // Clear hidden inputs tracking state
            document.getElementById('formPackageId').value = "";
            document.getElementById('attachedSpotsPayload').value = "";
            
            // Wipe out dynamic HTTP Method overrides
            const methodOverride = document.getElementById('formMethodOverride');
            if (methodOverride) {
                methodOverride.remove();
            }

            // Reset itinerary tracking collection
            activeItinerarySpots = [];
            renderItineraryViews();

            // Reset Form Headers
            document.getElementById('formActionHeader').innerText = "✨ Create Package";
            document.getElementById('formActionSubtext').innerText = "Configure parameters and attach global locations below.";

            // Reset button display configurations back to default Create state
            const submitBtn = document.getElementById('formSubmitBtn');
            submitBtn.innerText = "Create Package";
            submitBtn.className = "btn btn-primary rounded-pill w-50 py-2 fw-medium shadow-sm"; 
            
            const resetBtn = document.getElementById('formResetBtn');
            resetBtn.innerText = "Clear Form";
            resetBtn.className = "btn btn-light rounded-pill w-50 py-2 border text-secondary";
        }

        // DYNAMIC SUBMIT LISTENER: Formulates itinerary payloads and handles routing actions
        document.getElementById('packageForm').addEventListener('submit', function(e) {
            const packageId = document.getElementById('formPackageId').value;
            const formElement = this;

            // 1. Map itinerary parameters to hidden payload tracker
            const attachedPayloadData = activeItinerarySpots.map(spot => ({
                id: spot.id,
                position: spot.position
            }));
            document.getElementById('attachedSpotsPayload').value = JSON.stringify(attachedPayloadData);

            // 2. Adjust action endpoint dynamically based on edit state
            if (packageId && packageId.trim() !== "") {
                // UPDATE PACKAGE DIRECTIVE
                formElement.action = `{{ route('admin.package.update', ':id') }}`.replace(':id', packageId);

                let methodInput = document.getElementById('formMethodOverride');
                if (!methodInput) {
                    methodInput = document.createElement('input');
                    methodInput.setAttribute('type', 'hidden');
                    methodInput.setAttribute('name', '_method');
                    methodInput.setAttribute('id', 'formMethodOverride');
                    formElement.appendChild(methodInput);
                }
                methodInput.value = "PUT";
                
            } else {
                // CREATE NEW PACKAGE DIRECTIVE
                formElement.action = "{{ route('admin.store.package') }}"; 
                
                const methodInput = document.getElementById('formMethodOverride');
                if (methodInput) {
                    methodInput.remove();
                }
            }
        });
        
        // Variable to hold the ID of the package slated for deletion
        let packageIdToDelete = null;

        // 1. Triggered when the card's trash can button is clicked (opens the styled modal)
        function confirmDeletePackage(packageId, packageName) {
            packageIdToDelete = packageId;
            
            // Inject the name dynamically inside the modal warning description
            document.getElementById('deleteTargetName').innerText = packageName;
            
            // Show the modal programmatically using Bootstrap's JS API
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
            deleteModal.show();
        }

        // 2. Triggered when the user clicks "Yes, Delete" inside our custom modal
        function executePackageDeletion() {
            if (!packageIdToDelete) return;

            const deleteForm = document.getElementById('deletePackageForm');
            
            // Bind the Laravel destroy route using your dynamic pattern
            deleteForm.action = `{{ route('admin.package.destroy', ':id') }}`.replace(':id', packageIdToDelete);
            deleteForm.submit();
        }

        document.getElementById('inputPax').addEventListener('input', (e) => updatePriceHead());
        document.getElementById('inputPackagePrice').addEventListener('input', (e) => updatePriceHead());
        function updatePriceHead() {
            const pax = document.getElementById('inputPax').value || 0;
            const basePrice = document.getElementById('inputPackagePrice').value || 0;
            const perHeadInput = document.getElementById('inputPerHeadPrice');
            
            if (pax > 0) {
                perHeadInput.value = (basePrice / pax).toFixed(2); // Rounds to 2 decimal places
            } else {
                perHeadInput.value = '';
            }
        }
    </script>
    @include('layouts.notification')
</body>
</html>