const fs = require('fs');
let path = 'resources/views/view-package.blade.php';
let text = fs.readFileSync(path, 'utf8');

const regex = /<div class="bg-light p-3 rounded-3 mb-4 border shadow-sm" style="font-size: 14px;">[\s\S]*?<div class="d-flex justify-content-between align-items-center bg-warning-subtle[\s\S]*?<\/div>\s*<\/div>/;

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
    <div class="d-flex justify-content-between mb-3 text-primary fw-bold bg-primary-subtle p-2 rounded-2" style="font-size: 13px;">
        <span><i class="bi bi-tag-fill me-1"></i> Package Price:</span>
        <span id="breakdownPackagePrice">&#8369;0.00</span>
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

if (regex.test(text)) {
    text = text.replace(regex, newHtml);
    console.log('replaced html');
} else {
    console.log('html not found');
}

// Add breakdownVehicle update to JS
const oldJs = `document.getElementById('breakdownHeads').innerText = \`\$\{headsCount\} people\`;`;
const newJs = `document.getElementById('breakdownHeads').innerText = \`\$\{headsCount\} people\`;
    const vehicleName = document.getElementById('selectedVehicleName').innerText;
    document.getElementById('breakdownVehicle').innerText = vehicleName;`;

text = text.replace(oldJs, newJs);

fs.writeFileSync(path, text, 'utf8');
console.log('done fixing pricing summary');
