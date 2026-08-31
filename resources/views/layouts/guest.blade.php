<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>ETRAV - Authentication</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

    <style>
        body {
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.85) 0%, rgba(30, 41, 59, 0.8) 100%), 
                        url('https://images.unsplash.com/photo-1507525428034-b723cf961d3e?q=80&w=1920&auto=format&fit=crop') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            margin: 0;
            padding: 0;
        }
        .auth-card {
            border: none;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
            max-width: 960px;
            width: 100%;
        }
        .auth-side-banner {
            position: relative;
            min-height: 580px;
            overflow: hidden;
            padding: 0;
        }
        /* Leaflet Container Layer Configuration */
        #bannerMap {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
        }
        /* FIXED: Gradient Overlay is now transparent at the top, fading to blue only at the bottom */
        .map-overlay-tint {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to bottom, rgba(13, 110, 253, 0) 40%, rgba(11, 60, 140, 0.85) 100%);
            z-index: 2;
            pointer-events: none; 
        }
        /* Content Panel Layer */
        .banner-text-content {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 3.5rem 2.5rem;
            color: white;
            z-index: 3;
            pointer-events: none;
        }
        .auth-form-side {
            min-height: 580px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            z-index: 4;
        }
        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
        }
        .btn-auth {
            padding: 0.65rem 1.5rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        .btn-auth:hover {
            transform: translateY(-1px);
        }

        /* Custom SVG Pulse Pin Styling */
        .custom-pin {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .pin-wrapper {
            position: relative;
            animation: mapPulse 2s infinite ease-in-out;
            will-change: transform;
        }
        @keyframes mapPulse {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-6px); }
        }
    </style>
</head>
<body>
    <div class="container p-3 d-flex justify-content-center">
        <div class="card auth-card bg-white">
            <div class="row g-0">
                
                <!-- LEFT SIDE: Leaflet Interactive Map Viewport -->
                <div class="col-lg-5 auth-side-banner d-none d-lg-block">
                    <!-- Leaflet Container -->
                    <div id="bannerMap"></div>
                    <!-- Aesthetic Filter Cover (Fixed) -->
                    <div class="map-overlay-tint"></div>
                    
                    <!-- Text Info Overlay -->
                    <div class="banner-text-content">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-airplane-fill fs-3 text-white"></i>
                            <h2 class="fw-bold tracking-wider m-0">ETRAV</h2>
                        </div>
                        <p class="text-white-50 small mb-0">Manage custom spots, optimize active routes on the mapping engine, and deploy dynamic packages seamlessly.</p>
                    </div>
                </div>

                <!-- RIGHT SIDE: Fixed Form Side Content Shell -->
                <div class="col-lg-7 p-4 p-sm-5 auth-form-side bg-white">
                    @include('layouts.notification')
                    @yield('content')
                </div>

            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JavaScript Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Leaflet JS Component -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Initialize Leaflet Map locked directly onto Cebu Coordinates
            const cebuLat = 10.3157;
            const cebuLng = 123.8854;

            const map = L.map('bannerMap', {
                center: [cebuLat, cebuLng],
                zoom: 10,
                dragging: false,         // Disables Draggability 
                zoomControl: false,      // Hides Zoom Buttons
                scrollWheelZoom: false,  // Disables Scroll Wheel Zoom
                doubleClickZoom: false,  // Disables Double Click Zoom
                boxZoom: false,
                keyboard: false,
                touchZoom: false
            });

            // UPDATED TILE LAYER: Using Carto Voyager for a clean, bright, professional map look
            L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            // Helper function to generate premium Bootstrap colored markers
            function createColorIcon(colorClass, bootstrapIcon) {
                let colorHex = "#0d6efd"; // Default blue
                if (colorClass === 'red') colorHex = "#dc3545";
                if (colorClass === 'yellow') colorHex = "#ffc107";
                if (colorClass === 'teal') colorHex = "#0dcaf0";

                return L.divIcon({
                    className: 'custom-pin',
                    html: `
                        <div class="pin-wrapper" style="color: ${colorHex}; filter: drop-shadow(0 3px 5px rgba(0,0,0,0.5)); font-size: 28px;">
                            <i class="bi ${bootstrapIcon}"></i>
                        </div>
                    `,
                    iconSize: [30, 30],
                    iconAnchor: [15, 30]
                });
            }

            // Mock Data array representing Package locations across Cebu
            const packagesData = [
                { lat: 10.3157, lng: 123.8854, color: 'red', icon: 'bi-geo-alt-fill' },    // Metro Cebu Package
                { lat: 10.7984, lng: 124.0174, color: 'yellow', icon: 'bi-geo-alt-fill' }, // North Cebu / Danao Spot
                { lat: 9.9542, lng: 123.4024, color: 'teal', icon: 'bi-geo-alt-fill' }     // South Cebu / Moalboal Spot
            ];

            // Render markers dynamically on top of the Leaflet display map
            packagesData.forEach(function(pkg) {
                L.marker([pkg.lat, pkg.lng], {
                    icon: createColorIcon(pkg.color, pkg.icon)
                }).addTo(map);
            });
        });
    </script>
</body>
</html>