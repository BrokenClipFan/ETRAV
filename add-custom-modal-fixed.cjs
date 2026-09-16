const fs = require('fs');
let path = 'resources/views/view-package.blade.php';
let text = fs.readFileSync(path, 'utf8');

// 1. Add Warning Modal HTML
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

// 2. Add isCustomRoute and Warning modal logic variables
const jsSetup = `
        let isCustomRoute = false;
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
if (!text.includes('function confirmCustomAction')) {
    text = text.replace('let activeBasePrice = 0;', jsSetup + '\n        let activeBasePrice = 0;');
}

// 3. Wrap addPlaceToItinerary accurately
const addPlaceRegex = /function addPlaceToItinerary\(placeId\) \{[\s\S]*?focusOnPackageRoute\(activePackageId\);\s*\}/m;
const newAddPlace = `function addPlaceToItinerary(placeId) {
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
        }`;
if (!text.includes('confirmCustomAction(() => {') || !text.includes('function addPlaceToItinerary')) {
    // wait, we replaced it if not found? we just replace
}
text = text.replace(addPlaceRegex, newAddPlace);

// 4. Wrap removePlaceFromItinerary accurately
const removePlaceRegex = /function removePlaceFromItinerary\(placeId\) \{[\s\S]*?focusOnPackageRoute\(activePackageId\);\s*\}/m;
const newRemovePlace = `function removePlaceFromItinerary(placeId) {
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
        }`;
text = text.replace(removePlaceRegex, newRemovePlace);

// 5. Wrap addCustomStop accurately (without breaking anything)
const addCustomRegex = /function addCustomStop\(category = 'custom'\) \{[\s\S]*?setTimeout\(\(\) => toast\.remove\(\), 4000\);\s*\}/m;
const newAddCustom = `function addCustomStop(category = 'custom') {
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
                
                // Show alert instruction once
                const toast = document.createElement('div');
                toast.className = 'alert alert-info position-absolute shadow-sm';
                toast.style.cssText = 'top: 20px; left: 50%; transform: translateX(-50%); z-index: 9999;';
                toast.innerHTML = '<i class="bi bi-info-circle-fill me-2"></i><strong>Custom Pin Added!</strong> Drag the red pin on the map to your desired location.';
                document.querySelector('.map-container').appendChild(toast);
                setTimeout(() => toast.remove(), 4000);
            });
        }`;
text = text.replace(addCustomRegex, newAddCustom);

fs.writeFileSync(path, text, 'utf8');
console.log('done fixing custom modal logic cleanly');
