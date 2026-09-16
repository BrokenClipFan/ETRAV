const fs = require('fs');
let path = 'resources/views/view-package.blade.php';
let text = fs.readFileSync(path, 'utf8');

// 1. Remove price from the HTML
const priceHtml = `<span class="text-primary fw-bold">&#8369;{{ number_format($vehicle->base_price ?? 0, 0) }}</span>`;
text = text.replace(priceHtml, '<span class="badge bg-danger d-none pax-warning-text" style="font-size: 10px;">Pax not supported</span>');

// 2. Remove price from the JS text
const jsTextOld = "document.getElementById('selectedVehicleDetails').innerText = `${vehicle.capacity} Pax | &#8369;${vehicle.base_price}`;";
const jsTextNew = "document.getElementById('selectedVehicleDetails').innerText = `${vehicle.capacity} Pax Capacity`;";
text = text.replace(jsTextOld, jsTextNew);

// 3. Update filterVehicles JS for sorting and showing the "Pax not supported"
const filterOld = `if (vehicleCapacity < headsVal) {
                    row.style.opacity = '0.5';
                    row.style.pointerEvents = 'none';
                    // We don't hide it, just disable it
                } else {
                    row.style.opacity = '1';
                    row.style.pointerEvents = 'auto';
                }`;
const filterNew = `if (vehicleCapacity < headsVal) {
                    row.style.opacity = '0.5';
                    row.style.pointerEvents = 'none';
                    row.style.order = '1';
                    const warning = row.querySelector('.pax-warning-text');
                    if (warning) warning.classList.remove('d-none');
                } else {
                    row.style.opacity = '1';
                    row.style.pointerEvents = 'auto';
                    row.style.order = '0';
                    const warning = row.querySelector('.pax-warning-text');
                    if (warning) warning.classList.add('d-none');
                }`;
text = text.replace(filterOld, filterNew);

fs.writeFileSync(path, text, 'utf8');
console.log('done fixing vehicle UI');
