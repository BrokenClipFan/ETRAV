const fs = require('fs');
let path = 'resources/views/view-package.blade.php';
let text = fs.readFileSync(path, 'utf8');

// 1. Pricing Summary
const regexSummary = /<div class="bg-light p-3 rounded-3 mb-4 border shadow-sm" style="font-size: 14px;">[\s\S]*?<div class="d-flex justify-content-between align-items-center bg-warning-subtle[\s\S]*?<\/div>\s*<\/div>/;
const newHtml = `<div class="bg-light p-3 rounded-3 mb-4 border shadow-sm" style="font-size: 14px;">
    <div class="d-flex justify-content-between mb-2 text-dark">
        <span><i class="bi bi-info-circle me-1 text-primary"></i> Tour Type:</span>
        <span class="fw-medium" id="breakdownBase">Private Tour</span>
    </div>
    <div class="d-flex justify-content-between mb-2 text-dark mt-2">
        <span><i class="bi bi-car-front-fill me-1 text-primary"></i> Selected Vehicle:</span>
        <span class="fw-medium" id="breakdownVehicle">None</span>
    </div>
    <div class="d-flex justify-content-between mb-2 text-dark mt-2">
        <span><i class="bi bi-people-fill me-1 text-primary"></i> Number of People:</span>
        <span class="fw-medium" id="breakdownHeads">1 head</span>
    </div>
    <div class="d-flex justify-content-between mb-2 text-primary fw-bold bg-primary-subtle p-2 rounded-2" style="font-size: 13px;">
        <span><i class="bi bi-tag-fill me-1"></i> Package Price:</span>
        <span id="breakdownPackagePrice">&#8369;0.00</span>
    </div>
    <div class="d-flex justify-content-between mb-3 text-secondary fw-medium px-2" style="font-size: 13px;">
        <span><i class="bi bi-person-bounding-box me-1"></i> Cost Per Person:</span>
        <span id="breakdownPerPerson">&#8369;0.00 / person</span>
    </div>
    
    <div class="d-flex justify-content-between border-top pt-3 fw-bold text-dark fs-5 mb-2">
        <span>Grand Total:</span>
        <span class="text-success" id="modalTotalPrice">&#8369;0.00</span>
    </div>
    <div id="depositContainer" class="d-flex justify-content-between align-items-center bg-warning-subtle p-2 rounded-2 border border-warning-subtle">
        <div class="text-dark fw-bold" style="font-size: 13px;">
            <i class="bi bi-cash-coin me-1 fs-6"></i> 25% Deposit (Paid After Approval):
        </div>
        <span class="fs-5 fw-black text-dark" id="modalDownpaymentPrice">&#8369;0.00</span>
    </div>
</div>`;
text = text.replace(regexSummary, newHtml);

// 2. JS calculateTotal fixes (Heads, Vehicle, slots, per person)
text = text.replace(/document\.getElementById\('breakdownBase'\)\.innerText = isJoinerAllowed \?[\s\S]*?`\$\{headsCount\} people`;/m, 
`let openSlotsStr = '';
        if (isJoinerAllowed) {
            const vId = document.getElementById('vehicleSelect').value;
            const vCap = (vId && vehiclesData[vId]) ? vehiclesData[vId].capacity : 0;
            if (vCap > 0) {
                const remaining = vCap - headsCount;
                openSlotsStr = remaining > 0 ? \` (\$\{remaining\} slots available)\` : \` (Full)\`;
            }
        }

        document.getElementById('breakdownBase').innerText = isJoinerAllowed ?
              'Joiner / Open Group' + openSlotsStr :
              'Private / Exclusive Group';

        document.getElementById('breakdownHeads').innerText = \`\$\{headsCount\} people\`;
        const vehicleName = document.getElementById('selectedVehicleName').innerText;
        document.getElementById('breakdownVehicle').innerText = vehicleName;`);


text = text.replace(/const depositContainer = document\.getElementById\('depositContainer'\);\s*if \(isCustomRoute\) \{/m, 
`const depositContainer = document.getElementById('depositContainer');
        
        let perPersonPrice = headsCount > 0 ? (totalToPay / headsCount) : 0;
        if (isJoinerAllowed) {
            const vId = document.getElementById('vehicleSelect').value;
            const vCap = (vId && vehiclesData[vId]) ? vehiclesData[vId].capacity : 1;
            perPersonPrice = vCap > 0 ? (totalToPay / vCap) : 0;
        }

        if (isCustomRoute) {
            document.getElementById('breakdownPerPerson').innerHTML = '<span class="badge bg-warning text-dark">To Be Quoted</span>';`);

text = text.replace(/document\.getElementById\('breakdownPackagePrice'\)\.innerHTML = '&#8369;' \+ pkgPrice\.toLocaleString\('en-US', \{ minimumFractionDigits: 2, maximumFractionDigits: 2 \}\);/m, 
`document.getElementById('breakdownPackagePrice').innerHTML = '&#8369;' + pkgPrice.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            document.getElementById('breakdownPerPerson').innerHTML = '&#8369;' + perPersonPrice.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' / person';`);


// 3. The Modal HTML
const modalHtml = `
<!-- Custom Route Warning Modal -->
<div class="modal fade" id="customRouteWarningModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-bottom-0 bg-warning-subtle text-warning-emphasis rounded-top-4 p-4 pb-3">
                <h5 class="modal-title fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i> Custom Package Notice</h5>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="mb-4">
                    <i class="bi bi-chat-quote text-warning" style="font-size: 4rem;"></i>
                </div>
                <h5 class="fw-bold text-dark mb-3">You are modifying a fixed package!</h5>
                <p class="text-muted mb-0">
                    By adding, removing, or changing spots, your itinerary will be converted into a <strong>Custom Package</strong>.
                </p>
                <p class="text-muted mt-2">
                    The fixed package price will be removed, and an admin will review your requested route to provide a custom price quote before you can pay the deposit.
                </p>
            </div>
            <div class="modal-footer border-top-0 p-4 pt-0">
                <button type="button" class="btn btn-warning w-100 rounded-pill fw-bold" id="understandWarningBtn" disabled>
                    I Understand (5s)
                </button>
            </div>
        </div>
    </div>
</div>
`;
if (!text.includes('id="customRouteWarningModal"')) {
    text = text.replace('</body>', modalHtml + '\n</body>');
}

// 4. Modal logic variables
const jsSetup = `
        let warningModalInstance = null;
        let pendingCustomAction = null;
        let customWarningShown = false;

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
            btn.innerText = \`I Understand (\$\{countdown\}s)\`;
            
            warningModalInstance.show();
            
            const timer = setInterval(() => {
                countdown--;
                if (countdown > 0) {
                    btn.innerText = \`I Understand (\$\{countdown\}s)\`;
                } else {
                    clearInterval(timer);
                    btn.disabled = false;
                    btn.innerText = 'I Understand, Proceed';
                }
            }, 1000);
        }
`;
text = text.replace('let isCustomRoute = false;', 'let isCustomRoute = false;\n' + jsSetup);

// 5. Wrap addPlaceToItinerary accurately
text = text.replace(/function addPlaceToItinerary\(placeId\) \{[\s\S]*?focusOnPackageRoute\(activePackageId\);\n\s*\}/m, 
`function addPlaceToItinerary(placeId) {
            confirmCustomAction(() => {
                const activePackageId = "{{ $package->id ?? 0 }}";
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
        }`);

// 6. Wrap removePlaceFromItinerary accurately
text = text.replace(/function removePlaceFromItinerary\(placeId\) \{[\s\S]*?focusOnPackageRoute\(activePackageId\);\n\s*\}/m, 
`function removePlaceFromItinerary(placeId) {
            confirmCustomAction(() => {
                const activePackageId = "{{ $package->id ?? 0 }}";
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
        }`);

// 7. Wrap addCustomStop accurately (without breaking anything)
text = text.replace(/function addCustomStop\(category = 'custom'\) \{[\s\S]*?setTimeout\(\(\) => toast\.remove\(\), 4000\);\n\s*\}/m, 
`function addCustomStop(category = 'custom') {
            confirmCustomAction(() => {
                const activePackageId = "{{ $package->id ?? 0 }}";
                if (!packageData[activePackageId]) return;
                
                const center = map.getCenter();
                const customId = 'custom_' + Date.now();
                let catName = category.charAt(0).toUpperCase() + category.slice(1);
                if(category === 'water falls') catName = 'Water Falls';
                if(category === 'custom') catName = 'Custom';
                
                const newSpot = {
                    id: customId,
                    name: \`\$\{catName\} Stop (Drag to adjust)\`,
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
                
                const toast = document.createElement('div');
                toast.className = 'alert alert-info position-absolute shadow-sm';
                toast.style.cssText = 'top: 20px; left: 50%; transform: translateX(-50%); z-index: 9999;';
                toast.innerHTML = '<i class="bi bi-info-circle-fill me-2"></i><strong>Custom Pin Added!</strong> Drag the red pin on the map to your desired location.';
                document.querySelector('.map-container').appendChild(toast);
                setTimeout(() => toast.remove(), 4000);
            });
        }`);

fs.writeFileSync(path, text, 'utf8');
console.log('Master patch complete!');
