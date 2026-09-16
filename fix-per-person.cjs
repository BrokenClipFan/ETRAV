const fs = require('fs');
let path = 'resources/views/view-package.blade.php';
let text = fs.readFileSync(path, 'utf8');

const oldHtml = `<div class="d-flex justify-content-between mb-3 text-primary fw-bold bg-primary-subtle p-2 rounded-2" style="font-size: 13px;">
        <span><i class="bi bi-tag-fill me-1"></i> Package Price:</span>
        <span id="breakdownPackagePrice">&#8369;0.00</span>
    </div>
    
    <div class="d-flex justify-content-between border-top pt-3 fw-bold text-dark fs-5 mb-2">`;

const newHtml = `<div class="d-flex justify-content-between mb-2 text-primary fw-bold bg-primary-subtle p-2 rounded-2" style="font-size: 13px;">
        <span><i class="bi bi-tag-fill me-1"></i> Package Price:</span>
        <span id="breakdownPackagePrice">&#8369;0.00</span>
    </div>
    <div class="d-flex justify-content-between mb-3 text-secondary fw-medium px-2" style="font-size: 13px;">
        <span><i class="bi bi-person-bounding-box me-1"></i> Cost Per Person:</span>
        <span id="breakdownPerPerson">&#8369;0.00 / person</span>
    </div>
    
    <div class="d-flex justify-content-between border-top pt-3 fw-bold text-dark fs-5 mb-2">`;

text = text.replace(oldHtml, newHtml);

// I also need to update calculateTotal to calculate and format it
const oldJs = `if (isCustomRoute) {
          document.getElementById('breakdownPackagePrice').innerHTML = '<span class="badge bg-warning text-dark">To Be Quoted</span>';`;

const newJs = `
      // Calculate per person
      let perPersonPrice = headsCount > 0 ? (totalToPay / headsCount) : 0;
      if (isJoinerAllowed) {
          const vId = document.getElementById('vehicleSelect').value;
          const vCap = (vId && vehiclesData[vId]) ? vehiclesData[vId].capacity : 1;
          perPersonPrice = vCap > 0 ? (totalToPay / vCap) : 0;
      }
      
      if (isCustomRoute) {
          document.getElementById('breakdownPackagePrice').innerHTML = '<span class="badge bg-warning text-dark">To Be Quoted</span>';
          document.getElementById('breakdownPerPerson').innerHTML = '<span class="badge bg-warning text-dark">To Be Quoted</span>';`;

text = text.replace(oldJs, newJs);

const oldJs2 = `document.getElementById('breakdownPackagePrice').innerHTML = '&#8369;' + pkgPrice.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('modalTotalPrice').innerHTML = '&#8369;' + totalToPay.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });`;

const newJs2 = `document.getElementById('breakdownPackagePrice').innerHTML = '&#8369;' + pkgPrice.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('breakdownPerPerson').innerHTML = '&#8369;' + perPersonPrice.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' / person';
        document.getElementById('modalTotalPrice').innerHTML = '&#8369;' + totalToPay.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });`;

text = text.replace(oldJs2, newJs2);

fs.writeFileSync(path, text, 'utf8');
console.log('done fixing cost per person');
