const fs = require('fs');
let path = 'resources/views/view-package.blade.php';
let text = fs.readFileSync(path, 'utf8');

// We need to add an id to the deposit container so we can hide it.
// The container starts with: <div class="d-flex justify-content-between align-items-center bg-warning-subtle
const oldHtml = `<div class="d-flex justify-content-between align-items-center bg-warning-subtle p-2 rounded-2 border border-warning-subtle">
        <div class="text-dark fw-bold" style="font-size: 13px;">
            <i class="bi bi-cash-coin me-1 fs-6"></i> 25% Deposit (Paid After Approval):
        </div>
        <span class="fs-5 fw-black text-dark" id="modalDownpaymentPrice">&#8369;0.00</span>
    </div>`;
const newHtml = `<div id="depositContainer" class="d-flex justify-content-between align-items-center bg-warning-subtle p-2 rounded-2 border border-warning-subtle">
        <div class="text-dark fw-bold" style="font-size: 13px;">
            <i class="bi bi-cash-coin me-1 fs-6"></i> 25% Deposit (Paid After Approval):
        </div>
        <span class="fs-5 fw-black text-dark" id="modalDownpaymentPrice">&#8369;0.00</span>
    </div>`;
text = text.replace(oldHtml, newHtml);

// Now update calculateTotal to hide/show and format "To Be Quoted"
const oldCalc = `document.getElementById('breakdownPackagePrice').innerHTML = '&#8369;' + pkgPrice.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    
    document.getElementById('modalTotalPrice').innerHTML = '&#8369;' + totalToPay.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('modalDownpaymentPrice').innerHTML = '&#8369;' + downpaymentRequired.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });`;

const newCalc = `
    const depositContainer = document.getElementById('depositContainer');
    
    if (isCustomRoute) {
        document.getElementById('breakdownPackagePrice').innerHTML = '<span class="badge bg-warning text-dark">To Be Quoted</span>';
        document.getElementById('modalTotalPrice').innerHTML = '<span class="fs-6 text-warning fw-bold">To Be Quoted</span>';
        if (depositContainer) depositContainer.classList.add('d-none');
        if (depositContainer) depositContainer.classList.remove('d-flex');
    } else {
        document.getElementById('breakdownPackagePrice').innerHTML = '&#8369;' + pkgPrice.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('modalTotalPrice').innerHTML = '&#8369;' + totalToPay.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('modalDownpaymentPrice').innerHTML = '&#8369;' + downpaymentRequired.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        if (depositContainer) depositContainer.classList.remove('d-none');
        if (depositContainer) depositContainer.classList.add('d-flex');
    }`;
text = text.replace(oldCalc, newCalc);

fs.writeFileSync(path, text, 'utf8');
console.log('done fixing summary');
