<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Booking Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        body { background-color: #f4f6f9; font-family: system-ui, sans-serif; }
        .map-container { border-radius: 1rem; overflow: hidden; height: 600px; }
        #map { width: 100%; height: 100%; z-index: 1; }
        
        .timeline-dot { position: relative; padding-left: 20px; padding-bottom: 15px; }
        .timeline-dot::before {
            content: ''; position: absolute; left: 0; top: 4px; width: 10px; height: 10px;
            background-color: #0d6efd; border-radius: 50%; z-index: 2;
        }
        .timeline-dot:not(:last-child)::after {
            content: ''; position: absolute; left: 4px; top: 14px; width: 2px; height: calc(100% - 10px);
            background-color: #e2e8f0; z-index: 1;
        }

        /* Custom Dynamic Category Map Marker Pins */
        .custom-category-pin {
            display: flex; align-items: center; justify-content: center;
            width: 36px; height: 36px; border-radius: 50%; color: white;
            font-size: 16px; box-shadow: 0 4px 10px rgba(0,0,0,0.3); border: 2px solid white;
        }
        .leaflet-tooltip.custom-pickup-label {
            background: rgba(255, 193, 7, 0.95) !important; color: #212529 !important; border: 1px solid #ffc107 !important;
        }

        @media print {
            .no-print { display: none !important; }
            .bg-white { border: none !important; box-shadow: none !important; padding: 0 !important; }
            body { background: #fff !important; }
            .col-xl-5, .col-xl-7 { width: 100% !important; margin-bottom: 20px !important; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        @include('admin.layouts.nav')
    </div>

    <div class="container-fluid p-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <a href="{{ route('admin.bookings') }}" class="btn btn-sm btn-light border text-muted mb-2 no-print"><i class="bi bi-arrow-left me-1"></i>Back to Bookings</a>
                <h4 class="fw-bold text-dark mb-1">Booking #BKG-{{ $booking->id }}</h4>
                <p class="text-muted small mb-0">{{ $booking->package->name ?? 'Custom Itinerary' }}</p>
            </div>
            <div class="no-print d-flex gap-2">
                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-4 fw-medium" onclick="window.print()">
                    <i class="bi bi-printer me-1"></i> Print
                </button>
                @if($booking->status === 'pending')
                                        <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-4 fw-medium shadow-sm" data-bs-toggle="modal" data-bs-target="#denyModal">
                        <i class="bi bi-x-circle me-1"></i> Deny Booking
                    </button>
                    <form action="{{ route('admin.booking.update', $booking->id) }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 fw-medium shadow-sm"><i class="bi bi-check2-circle me-1"></i> Approve Booking</button>
                    </form>
                @elseif($booking->status === 'approved' || $booking->status === 'confirmed')
                    <form action="{{ route('admin.booking.update.complete', $booking->id) }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm rounded-pill px-4 fw-medium shadow-sm"><i class="bi bi-patch-check me-1"></i> Mark Completed</button>
                    </form>
                @endif
            </div>
        </div>

        <div class="row g-4">
            <!-- Left Panel: Details -->
            <div class="col-12 col-xl-4">
                <div class="bg-white rounded-4 shadow-sm border p-4 mb-4">
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-person-badge me-2 text-primary"></i>Passenger Details</h6>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <span class="text-muted small d-block">Lead Client</span>
                            <strong class="text-dark">{{ $booking->user->name ?? 'N/A' }}</strong>
                        </div>
                        <div class="col-6">
                            <span class="text-muted small d-block">Contact</span>
                            <strong class="text-dark small">{{ $booking->user->email ?? 'N/A' }}<br>{{ $booking->user->phone ?? '' }}</strong>
                        </div>
                        <div class="col-6">
                            <span class="text-muted small d-block">Group Size</span>
                            <strong class="text-dark">{{ $booking->pax }} Passengers</strong>
                        </div>
                        <div class="col-6">
                            <span class="text-muted small d-block">Status</span>
                            @if($booking->status === 'pending')
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill">Pending Approval</span>
                            @elseif($booking->status === 'approved')
                                <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill">Awaiting Payment</span>
                            @elseif($booking->status === 'confirmed')
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Paid / Confirmed</span>
                            @elseif($booking->status === 'denied')
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill">Cancelled / Denied</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill">{{ ucfirst($booking->status) }}</span>
                            @endif
                        </div>
                        <div class="col-12 border-top pt-2 mt-2">
                            <span class="text-muted small d-block">Vehicle Assigned</span>
                            <strong class="text-dark"><i class="bi bi-car-front-fill me-1"></i> {{ $booking->vehicle ? $booking->vehicle->brand . ' ' . $booking->vehicle->model : 'N/A' }} ({{ $booking->vehicle->plate_number ?? 'No Plate' }})</strong>
                        </div>
                        <div class="col-12">
                            <span class="text-muted small d-block">Target Schedule</span>
                            <strong class="text-dark"><i class="bi bi-calendar-event me-1"></i> {{ date('l, M d, Y', strtotime($booking->pickup_datetime)) }}</strong>
                        </div>
                    </div>

                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3 mt-4"><i class="bi bi-receipt me-2 text-primary"></i>Financials</h6>
                    <div class="d-flex justify-content-between small text-secondary mb-1">
                        <span>Distance-Based Fare ({{ number_format($booking->distance / 1000, 1) }} km):</span>
                        <span>₱{{ number_format($booking->total_price, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between small text-secondary mb-2 border-bottom pb-2">
                        <span>Cost per Passenger:</span>
                        <span>₱{{ number_format($booking->head_price, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between text-dark fw-medium mb-1" style="font-size: 13px;">
                        <span>Total Gross Cost:</span>
                        <span>₱{{ number_format($booking->total_price, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between text-dark fw-medium mb-2 border-bottom pb-2" style="font-size: 13px;">
                        <span>Amount Paid:</span>
                        <span class="{{ $booking->amount_paid > 0 ? 'text-success' : 'text-danger' }}">₱{{ number_format($booking->amount_paid, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between fw-bold text-dark fs-6">
                        <span>Remaining Balance:</span>
                        <span class="text-primary">₱{{ number_format($booking->total_price - $booking->amount_paid, 2) }}</span>
                    </div>
                </div>

                <div class="bg-white rounded-4 shadow-sm border p-4">
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-signpost-split me-2 text-primary"></i>Itinerary Route</h6>
                    
                    <div class="timeline-dot fw-bold text-primary mb-2">
                        <div>{{ $booking->pickup_place_name }}</div>
                        <small class="text-muted fw-normal">Pickup Location (Start)</small>
                    </div>

                    @forelse($booking->itinerary as $item)
                        @php
                            $name = $item->place ? $item->place->name : ($item->custom_name ?? 'Custom Stop');
                            $desc = $item->place ? ($item->place->description ?? 'Included destination.') : ($item->custom_category ? ucfirst($item->custom_category) . ' Stop' : 'Custom destination.');
                        @endphp
                        <div class="timeline-dot fw-medium text-dark">
                            <div>{{ $name }}</div>
                            <small class="text-muted fw-normal">{{ Str::limit($desc, 60) }}</small>
                        </div>
                    @empty
                        <div class="text-muted small italic">No locations bound to this trip.</div>
                    @endforelse
                </div>
            </div>

            <!-- Right Panel: Live Map -->
            <div class="col-12 col-xl-8">
                <div class="bg-white rounded-4 shadow-sm border p-3 h-100">
                    <div class="map-container position-relative h-100" style="min-height: 600px;">
                        <div id="map"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const map = L.map('map').setView([10.3157, 123.8854], 10);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            const bounds = [];

            // 1. Pickup Location
            const pickupLat = {{ $booking->latitude ?? 'null' }};
            const pickupLng = {{ $booking->longitude ?? 'null' }};
            if(pickupLat && pickupLng) {
                const pickupIcon = L.divIcon({
                    html: `<div class="custom-category-pin" style="background-color: #ffc107; color: #000;"><i class="bi bi-person-raised-hand"></i></div>`,
                    className: 'custom-pin-container',
                    iconSize: [36, 36],
                    iconAnchor: [18, 18],
                });

                L.marker([pickupLat, pickupLng], {icon: pickupIcon, zIndexOffset: 1000})
                 .addTo(map)
                 .bindTooltip("Pickup Location", { permanent: true, direction: 'top', className: 'custom-pickup-label', offset: [0, -15] });
                 
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
                        html: `<div class="custom-category-pin" style="background-color: ${style.color};"><i class="bi ${style.icon}"></i></div>
                               <div style="position:absolute; top:-8px; right:-8px; background:white; color:black; border-radius:50%; width:18px; height:18px; font-size:10px; font-weight:bold; display:flex; align-items:center; justify-content:center; box-shadow:0 2px 4px rgba(0,0,0,0.2);">${index + 1}</div>`,
                        className: 'custom-pin-container',
                        iconSize: [36, 36],
                        iconAnchor: [18, 18],
                    });

                    L.marker([spot.lat, spot.lng], {icon: icon})
                     .addTo(map)
                     .bindPopup(`<b>${spot.name}</b><br>Stop #${index + 1}`);
                     
                    bounds.push([spot.lat, spot.lng]);
                    routePoints.push([spot.lat, spot.lng]);
                }
            });

            // Draw Route Line
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
    </script>

    <!-- Deny Modal -->
    <div class="modal fade" id="denyModal" tabindex="-1" aria-labelledby="denyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <form action="{{ route('admin.booking.deny', $booking->id) }}" method="POST">
                    @csrf
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold text-danger" id="denyModalLabel"><i class="bi bi-exclamation-triangle-fill me-2"></i>Deny Booking</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4 pt-3">
                        <p class="text-muted small mb-3">You are about to cancel this booking and free up the vehicle. Please provide a reason to the user.</p>
                        <div class="mb-3">
                            <label for="admin_message" class="form-label fw-bold text-dark small">Reason / Message</label>
                            <textarea class="form-control rounded-3" id="admin_message" name="admin_message" rows="3" placeholder="e.g., We are fully booked for this vehicle today." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">Deny Booking</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
