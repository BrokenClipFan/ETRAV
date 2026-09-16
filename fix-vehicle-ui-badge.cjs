const fs = require('fs');
let path = 'resources/views/view-package.blade.php';
let text = fs.readFileSync(path, 'utf8');

const regexPrice = /<span class="text-primary fw-bold">.*?\{\{ number_format\(\$vehicle->base_price[\s\S]*?<\/span>/;
text = text.replace(regexPrice, '<span class="badge bg-danger d-none pax-warning-text" style="font-size: 10px;">Pax not supported</span>');

const regexSelectedPrice = /document\.getElementById\('selectedVehicleDetails'\)\.innerText = `\$\{vehicle\.capacity\} Pax \|.*?`;/;
text = text.replace(regexSelectedPrice, "document.getElementById('selectedVehicleDetails').innerText = `${vehicle.capacity} Pax Capacity`;");

fs.writeFileSync(path, text, 'utf8');
console.log('done fixing vehicle UI badge');
