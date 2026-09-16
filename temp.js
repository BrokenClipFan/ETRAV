
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
            packageData[1] = {
                id: 1,
                name: 1,
                package_price: 1,
                max_pax: 1,
                spots: [
                    @foreach ($package->places as $place)
                        {
                            id: 1,
                            name: 1,
                            category: 1,
                            description: 1,
                            
                            duration: 1,
                            image: 1,
                            lat: 1,
                            lng: 1
                        },
                    }
                ]
            };
        }
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
            vehiclesData[1] = {
                id: 1,
                name: 1,
                base_price: 1,
                capacity: 1
            };
        }

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
                const failedPackageId = "1";
                const targetPackage = packageData[failedPackageId] || {
                    id: 0,
                    name: "Custom Tour Package",
                    package_price: 3500,
                    max_pax: 10,
                    spots: []
                };

                openBookingModal(targetPackage);
            });
        }
    
        const vehiclesList = [];

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
                    
                    if (typeof packageData !== 'undefined' && packageData[pkgId] && packageData[pkgId].spots && packageData[pkgId].spots.length > 1) {
                        try {
                            let totalDist = 0;
                            const spots = packageData[pkgId].spots;
                            for(let i=0; i<spots.length-1; i++) {
                                const p1 = L.latLng(spots[i].lat, spots[i].lng);
                                const p2 = L.latLng(spots[i+1].lat, spots[i+1].lng);
                                totalDist += p1.distanceTo(p2);
                            }
                            let km = (totalDist * 1.3) / 1000;
                            
                        } catch (e) {
                            
                        }
                    } else {
                        
                    }
                    
                    if(estDisplay) {
                        estDisplay.innerHTML = priceText;
                    }
                });
            }
        });
    