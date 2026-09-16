const fs = require('fs');
let html = fs.readFileSync('resources/views/admin/booking-details.blade.php', 'utf8');

// 1. Emphasizing Status
const statusMatch = html.match(/<div class="col-6">\s*<span class="text-muted small d-block">Status<\/span>\s*@if\(\$booking->status === 'pending'\)[\s\S]*?@endif\s*<\/div>/);
if(statusMatch) {
    let statusHtml = statusMatch[0].replace(/<div class="col-6">\s*<span class="text-muted small d-block">Status<\/span>/, '').replace(/<\/div>$/, '');
    statusHtml = statusHtml.replace(/<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill">\{\{ ucfirst\(\$booking->status\) \}\}<\/span>/, 
    `@if($booking->status === 'pending_price')
        <span class="badge bg-warning text-dark border border-warning rounded-pill px-2 shadow-sm"><i class="bi bi-tag-fill me-1"></i> Needs Custom Quote</span>
    @else
        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill">{{ ucfirst($booking->status) }}</span>
    @endif`);
    
    html = html.replace(/<h4 class="fw-bold text-dark mb-1">Booking #BKG-\{\{ \$booking->id \}\}<\/h4>/, 
        `<h4 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2 flex-wrap">
            Booking #BKG-{{ $booking->id }}
            ${statusHtml}
        </h4>`);
    html = html.replace(statusMatch[0], '');
}

// 2. Grouping Actions & Sticky Action Bar
const actionsRegex = /<div class="no-print d-flex gap-2">[\s\S]*?<\/div>\s*<\/div>/;
const actionsMatch = html.match(actionsRegex);
if(actionsMatch) {
    let actionsHtml = actionsMatch[0];
    
    const setPriceForm = `@if($booking->status === 'pending_price')
                    <form action="{{ route('admin.booking.set-price', $booking->id) }}" method="POST" class="mt-3">
                        @csrf
                        <label class="form-label small fw-bold text-dark mb-1">Send Custom Quote</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">?</span>
                            <input type="number" name="quoted_price" class="form-control" placeholder="Enter full trip price" required min="0">
                            <button class="btn btn-warning fw-medium text-dark px-4" type="submit"><i class="bi bi-send-fill me-1"></i> Send</button>
                        </div>
                    </form>
                @endif`;
                
    const coreActions = `@if($booking->status === 'pending_price' || $booking->status === 'pending' || $booking->status === 'pending_downpayment')
                        <button type="button" class="btn btn-outline-danger fw-medium shadow-sm w-100 mb-2" data-bs-toggle="modal" data-bs-target="#denyModal">
                            <i class="bi bi-x-circle me-1"></i> Deny Booking
                        </button>
                    @endif
                    @if($booking->status === 'pending')
                    <form action="{{ route('admin.booking.update', $booking->id) }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-primary fw-medium shadow-sm w-100"><i class="bi bi-check2-circle me-1"></i> Approve Booking</button>
                    </form>
                    @elseif($booking->status === 'approved' || $booking->status === 'confirmed')
                    <form action="{{ route('admin.booking.update.complete', $booking->id) }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-success fw-medium shadow-sm w-100"><i class="bi bi-patch-check me-1"></i> Mark Completed</button>
                    </form>
                    @endif`;
                    
    html = html.replace(actionsHtml, `<div class="no-print d-none d-xl-flex gap-2">
                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-4 fw-medium" onclick="window.print()">
                    <i class="bi bi-printer me-1"></i> Print
                </button>
            </div>
        </div>`);
            
    const financialsEndRegex = /<span class="text-primary">?\{\{ number_format\(\$booking->total_price - \$booking->amount_paid, 2\) \}\}<\/span>\s*<\/div>\s*<\/div>/;
    html = html.replace(financialsEndRegex, (match) => {
        return match.replace(/<\/div>\s*<\/div>$/, `</div>
            <div class="mt-3 pt-3 border-top d-none d-xl-block">
                ${setPriceForm}
                <div class="mt-3">
                    ${coreActions}
                </div>
            </div>
        </div>`);
    });
    
    const stickyFooter = `
    <!-- Mobile Sticky Footer Actions -->
    <div class="fixed-bottom d-xl-none bg-white p-3 shadow-lg border-top" style="z-index: 1050;">
        ${setPriceForm}
        <div class="mt-3 d-flex gap-2">
            ${coreActions.replace(/w-100/g, 'flex-fill')}
        </div>
    </div>
    <div class="d-xl-none" style="height: ${html.includes('pending_price') ? '160px' : '80px'};"></div>
    `;
    html = html.replace('</body>', `${stickyFooter}\n</body>`);
}

// 3. Mobile Stacking & Toggling
const tabsHtml = `
        <!-- Mobile Tabs -->
        <ul class="nav nav-pills nav-fill mb-4 d-xl-none bg-white p-1 rounded-3 shadow-sm border" id="mobileTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-medium rounded-2" id="details-tab" data-bs-toggle="pill" data-bs-target="#details-pane" type="button" role="tab">Details</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-medium rounded-2" id="map-tab" data-bs-toggle="pill" data-bs-target="#map-pane" type="button" role="tab">Map View</button>
            </li>
        </ul>
        
        <div class="tab-content" id="mobileTabsContent">
`;

html = html.replace(/<div class="row g-4">/, `${tabsHtml}\n        <div class="row g-4">`);
html = html.replace(/<div class="col-12 col-xl-4">/, `<div class="col-12 col-xl-4 tab-pane fade show active" id="details-pane" role="tabpanel" tabindex="0">`);
html = html.replace(/<div class="col-12 col-xl-8">/, `<div class="col-12 col-xl-8 tab-pane fade" id="map-pane" role="tabpanel" tabindex="0">`);
html = html.replace(/<!-- Deny Modal -->/, `</div>\n\n    <!-- Deny Modal -->`);

html = html.replace('</style>', `
        @media (min-width: 1200px) {
            .tab-content > .tab-pane { display: block !important; opacity: 1 !important; visibility: visible !important; }
        }
    </style>`);


// 4. Numbered Waypoints & Hover States
html = html.replace(/<div class="timeline-dot fw-bold text-primary mb-2">/, 
    `<div class="timeline-dot fw-bold text-primary mb-2 waypoint-item" data-index="0" style="cursor:pointer; transition: 0.2s;">`);
html = html.replace(/<div class="timeline-dot fw-medium text-dark">/g, 
    `<div class="timeline-dot fw-medium text-dark waypoint-item" data-index="{{ $loop->iteration }}" style="cursor:pointer; transition: 0.2s;">`);

html = html.replace(/\.timeline-dot::before \{[\s\S]*?\}/, `.timeline-dot::before {
            content: attr(data-index); position: absolute; left: 0; top: 0px; width: 22px; height: 22px;
            background-color: #0d6efd; border-radius: 50%; z-index: 2; color: white; font-size: 11px;
            display: flex; align-items: center; justify-content: center; font-weight: bold;
        }
        .waypoint-item:hover { background-color: #f8f9fa; border-radius: 4px; padding-right: 10px; margin-left: -5px; padding-left: 35px; }
        .waypoint-item:hover::before { transform: scale(1.1); box-shadow: 0 0 10px rgba(13,110,253,0.5); }
        .timeline-dot[data-index="0"]::before { background-color: #ffc107; color: #000; }
        .timeline-dot:not(:last-child)::after { left: 10px; top: 22px; }
        .custom-category-pin { font-weight: bold; font-size: 14px; transition: 0.3s; }
        .pin-bounce { transform: scale(1.3) translateY(-8px) !important; z-index: 9999 !important; box-shadow: 0 10px 15px rgba(0,0,0,0.3); }
        `);

html = html.replace('padding-left: 20px', 'padding-left: 30px');
html = html.replace('left: 4px; top: 14px', 'left: 10px; top: 22px');

// JS replacements
const jsMarkerReplace = `const bounds = [];
            window.mapMarkers = {}; // Object to store markers by index

            // 1. Pickup Location
            const pickupLat = {{ $booking->latitude ?? 'null' }};
            const pickupLng = {{ $booking->longitude ?? 'null' }};
            if(pickupLat && pickupLng) {
                const pickupIcon = L.divIcon({
                    html: \`<div class="custom-category-pin" style="background-color: #ffc107; color: #000;">0</div>\`,
                    className: 'custom-pin-container',
                    iconSize: [36, 36],
                    iconAnchor: [18, 18],
                });

                const pickupMarker = L.marker([pickupLat, pickupLng], {icon: pickupIcon, zIndexOffset: 1000})
                 .addTo(map)
                 .bindTooltip("Pickup Location", { direction: 'top', className: 'custom-pickup-label fw-bold', offset: [0, -15] });
                 
                window.mapMarkers[0] = pickupMarker;
                bounds.push([pickupLat, pickupLng]);
            }`;
html = html.replace(/const bounds = \[\];[\s\S]*?bounds\.push\(\[pickupLat, pickupLng\]\);\s*\}/, jsMarkerReplace);

html = html.replace(/html: \`<div class="custom-category-pin" style="background-color: \$\{style.color\};"><i class="bi \$\{style.icon\}"><\/i><\/div>[\s\S]*?<\/div>\`,/,
`html: \`<div class="custom-category-pin" style="background-color: \$\{style.color\}; color: white;">\$\{index + 1\}</div>\`,`
);

html = html.replace(/L\.marker\(\[spot\.lat, spot\.lng\], \{icon: icon\}\)[\s\S]*?bounds\.push\(\[spot\.lat, spot\.lng\]\);/,
`const marker = L.marker([spot.lat, spot.lng], {icon: icon})
                     .addTo(map)
                     .bindTooltip(\`\${spot.name}\`, { direction: 'top', className: 'fw-bold', offset: [0, -15] });
                     
                    window.mapMarkers[index + 1] = marker;
                    bounds.push([spot.lat, spot.lng]);`
);


const initHoverScript = `
            // Initialize Hover Events
            setTimeout(() => {
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
`;
html = html.replace(/if\(routePoints\.length > 1\) \{/, `${initHoverScript}\n            if(routePoints.length > 1) {`);

fs.writeFileSync('resources/views/admin/booking-details.blade.php', html, 'utf8');
console.log('Done!');
