const fs = require('fs');
let path = 'resources/views/view-package.blade.php';
let text = fs.readFileSync(path, 'utf8');

// 1. Replace "Distance-Based Pricing"
text = text.replace(
    /<span class="fw-semibold text-dark">Distance-Based Pricing<\/span>/g,
    '<span class="fw-semibold text-dark">Package Price: &#8369;{{ number_format($package->package_price ?? 0, 2) }}</span>'
);

// 2. Add data-capacity to vehicle rows
text = text.replace(
    /<div class="card border rounded-4 vehicle-item-row overflow-hidden"/g,
    '<div class="card border rounded-4 vehicle-item-row overflow-hidden" data-capacity="{{ $vehicle->capacity }}"'
);

// 3. Swap "Number of Heads" and "Select Vehicle"
const vehicleHtmlMatch = text.match(/<!-- Select Vehicle -->[\s\S]*?@enderror\s*<\/div>/);
const headsHtmlMatch = text.match(/<!-- Number of Heads -->[\s\S]*?@enderror\s*<\/div>/);

if (vehicleHtmlMatch && headsHtmlMatch) {
    const vHtml = vehicleHtmlMatch[0];
    const hHtml = headsHtmlMatch[0];
    
    // We will do a manual replace by cutting them out and placing them in correct order
    // But since they are right next to each other, let's just replace the combined string.
    const combined = vHtml + '\\s*' + hHtml.replace(/[-/\\^$*+?.()|[\]{}]/g, '\\$&'); // wait, safer to just replace them by placeholders
    text = text.replace(vHtml, '%%%VEHICLE%%%');
    text = text.replace(hHtml, '%%%HEADS%%%');
    
    text = text.replace('%%%VEHICLE%%%', hHtml);
    text = text.replace('%%%HEADS%%%', vHtml);
}

// 4. Remove the price details card (trip distance, trip fare, package price, etc)
// It starts from `<div class="bg-light p-3 rounded-3 mb-4 border shadow-sm" style="font-size: 14px;">`
const priceDetailsRegex = /<div class="bg-light p-3 rounded-3 mb-4 border shadow-sm" style="font-size: 14px;">[\s\S]*?<!-- JOINER \/ OPEN GROUP OPTION -->/m;

const newPriceDetails = `<div class="bg-light p-3 rounded-3 mb-4 border shadow-sm" style="font-size: 14px;">
    <div class="d-flex justify-content-between mb-2 text-dark">
        <span><i class="bi bi-info-circle me-1 text-primary"></i> Tour Type:</span>
        <span class="fw-medium" id="breakdownBase">Private Tour</span>
    </div>
    <div class="d-flex justify-content-between mb-2 text-dark mt-2">
        <span><i class="bi bi-people-fill me-1 text-primary"></i> Number of People:</span>
        <span class="fw-medium" id="breakdownHeads">1 head</span>
    </div>
    <div class="d-flex justify-content-between mb-3 text-primary fw-bold bg-primary-subtle p-2 rounded-2" style="font-size: 13px;">
        <span><i class="bi bi-tag-fill me-1"></i> Package Price:</span>
        <span id="breakdownPackagePrice">&#8369;0.00</span>
    </div>
    
    <div class="d-flex justify-content-between border-top pt-3 fw-bold text-dark fs-5 mb-2">
        <span>Grand Total:</span>
        <span class="text-success" id="modalTotalPrice">&#8369;0.00</span>
    </div>
    <div class="d-flex justify-content-between align-items-center bg-warning-subtle p-2 rounded-2 border border-warning-subtle">
        <div class="text-dark fw-bold" style="font-size: 13px;">
            <i class="bi bi-cash-coin me-1 fs-6"></i> 25% Deposit (Paid After Approval):
        </div>
        <span class="fs-5 fw-black text-dark" id="modalDownpaymentPrice">&#8369;0.00</span>
    </div>
</div>

<!-- JOINER / OPEN GROUP OPTION -->`;

text = text.replace(priceDetailsRegex, newPriceDetails);

// Now update JavaScript
// We need to modify filterVehicles() to also filter by capacity
let filterVehiclesOld = `const matchesBrand = activeBrandFilter === '' || name.includes(activeBrandFilter);`;
let filterVehiclesNew = `const matchesBrand = activeBrandFilter === '' || name.includes(activeBrandFilter);
                const headsVal = parseInt(document.getElementById('numberHeads').value) || 1;
                const vehicleCapacity = parseInt(row.getAttribute('data-capacity')) || 0;
                
                if (vehicleCapacity < headsVal) {
                    row.style.opacity = '0.5';
                    row.style.pointerEvents = 'none';
                    // We don't hide it, just disable it
                } else {
                    row.style.opacity = '1';
                    row.style.pointerEvents = 'auto';
                }`;
text = text.replace(filterVehiclesOld, filterVehiclesNew);

// We need to make sure filterVehicles is called oninput of numberHeads
text = text.replace('oninput="calculateTotal()"', 'oninput="calculateTotal(); filterVehicles();"');

fs.writeFileSync(path, text, 'utf8');
console.log('done fixing UI');
