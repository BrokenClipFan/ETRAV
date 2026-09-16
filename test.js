
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

        
        let isCustomRoute = false;
        let warningModalInstance = null;
        let pendingCustomAction = null;
        let customWarningShown = false;

        function updateSubmitButtonText() {
            const btn = document.querySelector('button[onclick="submitBookingRequest()"]');
            if (btn) {
                if (isCustomRoute) {
                    btn.innerHTML = '<i class="bi bi-calendar-check"></i> Request Custom Quote';
                    btn.classList.replace('btn-primary', 'btn-warning');
                    btn.classList.add('text-dark');
                } else {
                    btn.innerHTML = '<i class="bi bi-calendar-check"></i> Submit Booking Request';
                    btn.classList.replace('btn-warning', 'btn-primary');
                    btn.classList.remove('text-dark');
                }
            }
        }


        function confirmCustomAction(actionCallback) {
            if (isCustomRoute || customWarningShown) {
                actionCallback();
                return;
            }
            
            pendingCustomAction = actionCallback;
            
            if (!warningModalInstance) {
                warningModalInstance = new bootstrap.Modal(document.getElementById('customRouteWarningModal'), {
                    backdrop: 'static',
                    keyboard: false
                });
                
                document.getElementById('understandWarningBtn').addEventListener('click', () => {
                    customWarningShown = true;
                    warningModalInstance.hide();
                    if (pendingCustomAction) {
                        pendingCustomAction();
                        pendingCustomAction = null;
                    }
                });
            }
            
            const btn = document.getElementById('understandWarningBtn');
            btn.disabled = true;
            let countdown = 5;
            btn.innerText = `I Understand (${countdown}s)`;
            
            warningModalInstance.show();
            
            const timer = setInterval(() => {
                countdown--;
                if (countdown > 0) {
                    btn.innerText = `I Understand (${countdown}s)`;
                } else {
                    clearInterval(timer);
                    btn.disabled = false;
                    btn.innerText = 'I Understand, Proceed';
                }
            }, 1000);
        }

        let activeBasePrice = 0;
        let activePaxLimit = 10;
        let totalRouteDistance = 0;

        const packageData = {
            "0": {
                id: 0,
                name: "",
                package_price: 0,
                max_pax: 0,
                spots: [
                                                                        {
                                id: 0,
                                name: "",
                                category: "",
                                description: "",
                                
                                duration: "",
                                image: "",
                                lat: 0,
                                lng: 0
                            },
                        
                    
                ]
            }
        };

        const allPlacesData = {};
                                    allPlacesData[0] = {
                    id: 0,
                    name: "",
                    category: "",
                    description: "",
                    duration: "1h 0m",
                    image: "",
                    lat: 0,
                    lng: 0
                };
            
        

        const vehiclesData = {};
                    vehiclesData[0] = {
                id: 0,
                name: "",
                base_price: 0,
                interval_rate: 0,
                pricing_distance: 0,
                capacity: 0
            };
        

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
            confirmCustomAction(() => {
                const activePackageId = "0";
                const place = allPlacesData[placeId];
                if (place && packageData[activePackageId]) {
                    // Add to the package data
                    packageData[activePackageId].spots.push(place);
                    
                    isCustomRoute = true;
                    updateSubmitButtonText();
                    // Redraw map and form
                    initializeForm();
                    focusOnPackageRoute(activePackageId);
                }
            });
        }
        
        function removePlaceFromItinerary(placeId) {
            confirmCustomAction(() => {
                const activePackageId = "0";
                if (packageData[activePackageId]) {
                    // Remove from the package data
                    packageData[activePackageId].spots = packageData[activePackageId].spots.filter(spot => spot.id != placeId);
                    isCustomRoute = true;
                    updateSubmitButtonText();
                    
                    // Redraw map and form
                    initializeForm();
                    focusOnPackageRoute(activePackageId);
                }
            });
        }
        
        function renameCustomStop(spotId) {
            const activePackageId = "0";
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
            confirmCustomAction(() => {
                const activePackageId = "0";
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
                isCustomRoute = true;
                updateSubmitButtonText();
                
                initializeForm();
                focusOnPackageRoute(activePackageId);
                
                // Show alert instruction once
                const toast = document.createElement('div');
                toast.className = 'alert alert-info position-absolute shadow-sm';
                toast.style.cssText = 'top: 20px; left: 50%; transform: translateX(-50%); z-index: 9999;';
                toast.innerHTML = '<i class="bi bi-info-circle-fill me-2"></i><strong>Custom Pin Added!</strong> Drag the red pin on the map to your desired location.';
                document.querySelector('.map-container').appendChild(toast);
                setTimeout(() => toast.remove(), 4000);
            });
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
                                focusOnPackageRoute("0");
                            }).catch(() => {
                                initializeForm();
                                focusOnPackageRoute("0");
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

                // Calculate distance
                totalRouteDistance = 0;
                for (let i = 0; i < routeCoordinates.length - 1; i++) {
                    const p1 = L.latLng(routeCoordinates[i][0], routeCoordinates[i][1]);
                    const p2 = L.latLng(routeCoordinates[i+1][0], routeCoordinates[i+1][1]);
                    totalRouteDistance += p1.distanceTo(p2);
                }
            } else {
                totalRouteDistance = 0;
            }
            
            // Recalculate price if distance influences it
            calculateTotal();

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

            // Add backdrop overlay
            let backdrop = document.getElementById('mapFocusBackdrop');
            if (!backdrop) {
                backdrop = document.createElement('div');
                backdrop.id = 'mapFocusBackdrop';
                backdrop.style.cssText = 'position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0, 0, 0, 0.75); z-index: 1040; transition: opacity 0.3s;';
                document.body.appendChild(backdrop);
            }
            backdrop.style.display = 'block';

            // Elevate map container wrapper above the backdrop
            const mapCol = document.querySelector('.map-container').parentElement;
            mapCol.style.position = 'relative';
            mapCol.style.zIndex = '1050';
            
            // Add a subtle glow/shadow to the map wrapper
            mapCol.classList.add('shadow-lg');
            mapCol.style.boxShadow = '0 0 40px rgba(0,0,0,0.5)';
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

                // Hide backdrop and reset map container
                let backdrop = document.getElementById('mapFocusBackdrop');
                if (backdrop) backdrop.style.display = 'none';

                const mapCol = document.querySelector('.map-container').parentElement;
                mapCol.style.zIndex = '';
                mapCol.classList.remove('shadow-lg');
                mapCol.style.boxShadow = '';

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
            focusOnPackageRoute("0");
        }

        function initializeForm() {
            // Package base price is now deprecated; we rely purely on Vehicle Price
            activeBasePrice = 0;
            
            // Build draggable spots
            const itineraryContainer = document.getElementById('modalItineraryContainer');
            itineraryContainer.innerHTML = '';
            const standardSpotsInfo = document.getElementById('standardSpotsInfo');

            // Find this package's data in the pre-loaded packageData object
            const targetedPackageData = packageData["0"];
            if (targetedPackageData && targetedPackageData.spots && targetedPackageData.spots.length > 0) {
                standardSpotsInfo.classList.remove('d-none');
                targetedPackageData.spots.forEach((spot, index) => {
                    const badge = document.createElement('div');
                    badge.className = "d-flex align-items-center bg-light border rounded-3 p-2 small fw-medium draggable-spot-item";
                    badge.style.cursor = "grab";
                    let h = 1; let m = 0;
                    const durMatch = String(spot.duration || '').match(/(\d+)h\s*(\d+)m/);
                    if (durMatch) {
                        h = parseInt(durMatch[1]);
                        m = parseInt(durMatch[2]);
                    }
                    
                    badge.innerHTML = `
                        <div class="d-flex align-items-center flex-grow-1 overflow-hidden me-2">
                            <i class="bi bi-grip-vertical text-muted me-1"></i>
                            <span class="text-primary fw-bold me-2">${index + 1}.</span> 
                            <span class="text-truncate" style="font-size: 13px;" title="${spot.name}">${spot.name}</span>
                        </div>
                        
                        <div class="d-flex align-items-center gap-2 flex-shrink-0">
                            ${spot.isCustom ? `
                            <!-- Edit Button -->
                            <button type="button" class="btn btn-sm text-primary p-0 m-0 border-0 bg-transparent" onclick="renameCustomStop('${spot.id}')" title="Rename stop">
                                <i class="bi bi-pencil-square fs-6"></i>
                            </button>
                            ` : ''}

                            <!-- Duration inputs -->
                            <div class="d-flex align-items-center bg-white border rounded-1 overflow-hidden shadow-sm">
                                <input type="number" class="form-control form-control-sm border-0 text-center px-1 py-0 shadow-none text-dark" style="width: 44px; font-size: 12px; height: 26px;" min="0" value="${h}" onchange="updateSpotDuration('${spot.id}', 'hours', this.value)">
                                <span class="bg-light text-muted px-1 border-start border-end" style="font-size: 10px; line-height: 26px;">h</span>
                                <input type="number" class="form-control form-control-sm border-0 text-center px-1 py-0 shadow-none text-dark" style="width: 44px; font-size: 12px; height: 26px;" min="0" max="59" value="${m}" onchange="updateSpotDuration('${spot.id}', 'minutes', this.value)">
                                <span class="bg-light text-muted px-1 border-start" style="font-size: 10px; line-height: 26px;">m</span>
                            </div>

                            <!-- Actions -->
                            <button type="button" class="btn btn-sm text-danger p-0 m-0 border-0 bg-transparent" onclick="removePlaceFromItinerary('${spot.id}')" title="Remove spot">
                                <i class="bi bi-x-circle-fill fs-6"></i>
                            </button>
                        </div>
                    `;
                    // Add hidden input so form submission retains the custom order
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'spots_order[]';
                    input.value = spot.id;
                    badge.appendChild(input);

                    const durInput = document.createElement('input');
                    durInput.type = 'hidden';
                    durInput.name = `spots_duration[${spot.id}]`;
                    durInput.value = spot.duration;
                    badge.appendChild(durInput);

                    if (spot.isCustom) {
                        const nameInput = document.createElement('input');
                        nameInput.type = 'hidden';
                        nameInput.name = `custom_spots_name[${spot.id}]`;
                        nameInput.value = spot.name;
                        badge.appendChild(nameInput);

                        const latInput = document.createElement('input');
                        latInput.type = 'hidden';
                        latInput.name = `custom_spots_lat[${spot.id}]`;
                        latInput.value = spot.lat;
                        badge.appendChild(latInput);

                        const lngInput = document.createElement('input');
                        lngInput.type = 'hidden';
                        lngInput.name = `custom_spots_lng[${spot.id}]`;
                        lngInput.value = spot.lng;
                        badge.appendChild(lngInput);

                        const catInput = document.createElement('input');
                        catInput.type = 'hidden';
                        catInput.name = `custom_spots_category[${spot.id}]`;
                        catInput.value = spot.category;
                        badge.appendChild(catInput);
                    }

                    itineraryContainer.appendChild(badge);
                });
                
                // Initialize SortableJS
                if (typeof Sortable !== 'undefined') {
                    if (sortableItineraryInstance) {
                        sortableItineraryInstance.destroy();
                    }
                    sortableItineraryInstance = new Sortable(itineraryContainer, {
                        animation: 0,
                        ghostClass: 'sortable-ghost',
                        forceFallback: true,
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
                            const activePackageId = "0";
                            if (packageData[activePackageId]) {
                                const oldSpots = [...packageData[activePackageId].spots];
                                const newSpots = [];
                                newOrderIds.forEach(id => {
                                    const spot = oldSpots.find(s => s.id == id);
                                    if (spot) newSpots.push(spot);
                                });
                                packageData[activePackageId].spots = newSpots;
                                
                                // Redraw map lines without fully rebuilding the form HTML
                                setTimeout(() => {
                                    focusOnPackageRoute(activePackageId);
                                }, 10);
                            }
                        }
                    });
                }
            } else {
                standardSpotsInfo.classList.add('d-none');
            }

            calculateTotal();
        }

        function updateSpotDuration(spotId, type, value) {
            const activePackageId = "0";
            if (!packageData[activePackageId]) return;
            const spot = packageData[activePackageId].spots.find(s => String(s.id) === String(spotId));
            if (!spot) return;

            let h = 1; let m = 0;
            const durMatch = String(spot.duration || '').match(/(\d+)h\s*(\d+)m/);
            if (durMatch) {
                h = parseInt(durMatch[1]);
                m = parseInt(durMatch[2]);
            }

            if (type === 'hours') h = parseInt(value) || 0;
            if (type === 'minutes') m = parseInt(value) || 0;

            spot.duration = `${h}h ${m}m`;
            focusOnPackageRoute(activePackageId);
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
            let currentPricingDistance = 5000;
            let baseVehiclePrice = 0;
            let intervalRate = 0;
            let distanceMultiplier = 1;
            let additionalIntervals = 0;

            if (vehicleSelect && vehicleSelect.value && vehiclesData[vehicleSelect.value]) {
                const selectedVehicle = vehiclesData[vehicleSelect.value];
                baseVehiclePrice = parseFloat(selectedVehicle.base_price) || 0;
                intervalRate = parseFloat(selectedVehicle.interval_rate) || 0;
                currentPricingDistance = parseFloat(selectedVehicle.pricing_distance) || 5000;
                
                // Calculate multiplier based on distance
                if (totalRouteDistance > 0) {
                    distanceMultiplier = Math.ceil(totalRouteDistance / currentPricingDistance);
                    if (distanceMultiplier < 1) distanceMultiplier = 1; // Minimum 1 interval
                }
                
                additionalIntervals = distanceMultiplier > 0 ? distanceMultiplier - 1 : 0;
                vehiclePrice = baseVehiclePrice + (intervalRate * additionalIntervals);
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
            
            const totalBasePrice = vehiclePrice;
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
            
            // total distance handled implicitly
            const distanceKm = (totalRouteDistance / 1000).toFixed(1);
            document.getElementById('totalDistanceInput').value = totalRouteDistance;

            

            
        let openSlotsStr = '';
        if (isJoinerAllowed) {
            const vId = document.getElementById('vehicleSelect').value;
            const vCap = (vId && vehiclesData[vId]) ? vehiclesData[vId].capacity : 0;
            if (vCap > 0) {
                const remaining = vCap - headsCount;
                openSlotsStr = remaining > 0 ? ` (${remaining} slots available)` : ` (Full)`;
            }
        }

        document.getElementById('breakdownBase').innerText = isJoinerAllowed ?
              'Joiner / Open Group' + openSlotsStr :
              'Private / Exclusive Group';

        document.getElementById('breakdownHeads').innerText = `${headsCount} people`;
        const vehicleName = document.getElementById('selectedVehicleName').innerText;
        document.getElementById('breakdownVehicle').innerText = vehicleName;
        
        let perPersonPrice = headsCount > 0 ? (totalToPay / headsCount) : 0;
        if (isJoinerAllowed) {
            const vId = document.getElementById('vehicleSelect').value;
            const vCap = (vId && vehiclesData[vId]) ? vehiclesData[vId].capacity : 1;
            perPersonPrice = vCap > 0 ? (totalToPay / vCap) : 0;
        }
        
        const depositContainer = document.getElementById('depositContainer');
        
        const targetedPackageData = packageData["0"];
        const pkgPrice = targetedPackageData ? parseFloat(targetedPackageData.package_price) || 0 : 0;
        if (isCustomRoute) {
            document.getElementById('breakdownPackagePrice').innerHTML = '<span class="badge bg-warning text-dark">To Be Quoted</span>';
            document.getElementById('breakdownPerPerson').innerHTML = '<span class="badge bg-warning text-dark">To Be Quoted</span>';
            document.getElementById('modalTotalPrice').innerHTML = '<span class="text-warning">To Be Quoted</span>';
            if (depositContainer) depositContainer.classList.add('d-none');
        } else {
            document.getElementById('breakdownPackagePrice').innerHTML = '&#8369;' + pkgPrice.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            document.getElementById('breakdownPerPerson').innerHTML = '&#8369;' + perPersonPrice.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' / person';
            document.getElementById('modalTotalPrice').innerHTML = '&#8369;' + totalToPay.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            document.getElementById('modalDownpaymentPrice').innerHTML = '&#8369;' + downpaymentRequired.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            if (depositContainer) depositContainer.classList.remove('d-none');
        }

        }

        function searchMapPlace() {
            const query = document.getElementById('mapSearchInput').value.trim();
            if (!query) return;
            
            // Force the search to prioritize Cebu, Philippines
            let finalQuery = query;
            if (!finalQuery.toLowerCase().includes('cebu')) {
                finalQuery += ', Cebu';
            }
            if (!finalQuery.toLowerCase().includes('philippines')) {
                finalQuery += ', Philippines';
            }
            
            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(finalQuery)}`)
                .then(res => res.json())
                .then(data => {
                    if (data && data.length > 0) {
                        const lat = parseFloat(data[0].lat);
                        const lon = parseFloat(data[0].lon);
                        map.flyTo([lat, lon], 14, { animate: true, duration: 1.5 });
                    } else {
                        alert("Place not found. Try being more specific (e.g., adding city or country).");
                    }
                })
                .catch(err => {
                    console.error("Geocoding error", err);
                    alert("Failed to search place due to a network error.");
                });
        }

                function submitBookingRequest() {
            const form = document.getElementById('bookingForm');
            
            // Check native HTML5 validation (will highlight the required checkbox)
            if (!form.reportValidity()) {
                return;
            }

            const pickupLat = document.getElementById('pickupLatitude').value;
            if (!pickupLat) {
                alert("Please click 'Set' and select a Pickup Location on the map first.");
                return;
            }

            const vehicleId = document.getElementById('vehicleSelect').value;
            if (!vehicleId) {
                alert("Please select a Transport Vehicle for your trip.");
                return;
            }

            document.getElementById('bookingForm').submit();
        }

        window.onload = function() {
            // Automatically map out the active package route
            const activePackageId = 0;
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

                    document.addEventListener("DOMContentLoaded", function() {
                const failedPackageId = "0";
                const targetPackage = packageData[failedPackageId] || {
                    id: 0,
                    name: "Custom Tour Package",
                    package_price: 3500,
                    max_pax: 10,
                    spots: []
                };

                openBookingModal(targetPackage);
            });
        
    