
        document.addEventListener("DOMContentLoaded", function() {
            const map = L.map('map').setView([10.3157, 123.8854], 10);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            const bounds = [];
            window.mapMarkers = {}; // Object to store markers by index

            // 1. Pickup Location
            const pickupLat = {{ $booking->latitude ?? 'null' }};
            const pickupLng = {{ $booking->longitude ?? 'null' }};
            if(pickupLat && pickupLng) {
                const pickupIcon = L.divIcon({
                    html: `<div class="custom-category-pin" style="background-color: #ffc107; color: #000;">0</div>`,
                    className: 'custom-pin-container',
                    iconSize: [36, 36],
                    iconAnchor: [18, 18],
                });

                const pickupMarker = L.marker([pickupLat, pickupLng], {icon: pickupIcon, zIndexOffset: 1000})
                 .addTo(map)
                 .bindTooltip("Pickup Location", { direction: 'top', className: 'custom-pickup-label fw-bold', offset: [0, -15] });
                 
                window.mapMarkers[0] = pickupMarker;
                bounds.push([pickupLat, pickupLng]);
            }

            // 2. Itinerary Locations
            @php
                $itineraryData = $booking->itinerary->map(function($i) {
                    return [
                        'lat' => $i->place ? $i->place->latitude : $i->custom_latitude,
                        'lng' => $i->place ? $i->place->longitude : $i->custom_longitude,
                        'name' => $i->place ? $i->place->name : ($i->custom_name ?? 'Custom Stop'),
                        'category' => $i->place ? 'default' : ($i->custom_category ?? 'custom')
                    ];
                });
            @endphp
            const itinerary = @json($itineraryData);

            const categoryColors = {
                'swimming': { color: '#0dcaf0', icon: 'bi-water' },
                'mountain': { color: '#198754', icon: 'bi-tree-fill' },
                'restaurant': { color: '#ffc107', icon: 'bi-cup-hot-fill' },
                'terminal': { color: '#6f42c1', icon: 'bi-bus-front-fill' },
                'water falls': { color: '#0d6efd', icon: 'bi-tsunami' },
                'custom': { color: '#dc3545', icon: 'bi-pin-map-fill' },
                'default': { color: '#0d6efd', icon: 'bi-geo-alt-fill' }
            };

            const routePoints = [];
            if(pickupLat && pickupLng) {
                routePoints.push([pickupLat, pickupLng]);
            }

            itinerary.forEach((spot, index) => {
                if(spot.lat && spot.lng) {
                    const style = categoryColors[spot.category] || categoryColors['default'];
                    
                    const icon = L.divIcon({
                        html: `<div class="custom-category-pin" style="background-color: ${style.color}; color: white;">${index + 1}</div>`,
                        className: 'custom-pin-container',
                        iconSize: [36, 36],
                        iconAnchor: [18, 18],
                    });

                    const marker = L.marker([spot.lat, spot.lng], {icon: icon})
                     .addTo(map)
                     .bindTooltip(`${spot.name}`, { direction: 'top', className: 'fw-bold', offset: [0, -15] });
                     
                    window.mapMarkers[index + 1] = marker;
                    bounds.push([spot.lat, spot.lng]);
                    routePoints.push([spot.lat, spot.lng]);
                }
            });

            // Draw Route Line
            
            // Initialize Hover Events
            setTimeout(() => {\n                map.invalidateSize();
                document.querySelectorAll('.waypoint-item').forEach(el => {
                    const idx = el.getAttribute('data-index');
                    el.addEventListener('mouseenter', () => {
                        const marker = window.mapMarkers[idx];
                        if(marker) {
                            marker.openTooltip();
                            const iconEl = marker.getElement().querySelector('.custom-category-pin');
                            if(iconEl) iconEl.classList.add('pin-bounce');
                        }
                    });
                    el.addEventListener('mouseleave', () => {
                        const marker = window.mapMarkers[idx];
                        if(marker) {
                            marker.closeTooltip();
                            const iconEl = marker.getElement().querySelector('.custom-category-pin');
                            if(iconEl) iconEl.classList.remove('pin-bounce');
                        }
                    });
                });
            }, 500);

            // Fix map sizing in hidden tab
            document.getElementById('map-tab').addEventListener('shown.bs.tab', function () {
                map.invalidateSize();
                if (bounds.length > 0) map.fitBounds(bounds, { padding: [30, 30] });
            });

            if(routePoints.length > 1) {
                L.polyline(routePoints, {
                    color: '#0d6efd',
                    weight: 3,
                    dashArray: '10, 10',
                    opacity: 0.7
                }).addTo(map);
            }

            if(bounds.length > 0) {
                map.fitBounds(bounds, { padding: [50, 50] });
            }
        });
    