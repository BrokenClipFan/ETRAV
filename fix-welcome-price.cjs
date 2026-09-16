const fs = require('fs');
let path = 'resources/views/welcome.blade.php';
let text = fs.readFileSync(path, 'utf8');

let oldLoop = `                let sortedVehicles = [...vehiclesList].sort((a, b) => parseFloat(a.base_price) - parseFloat(b.base_price));
                let minVehicle = sortedVehicles[0];
                let maxVehicle = sortedVehicles[sortedVehicles.length - 1];
                let minPrice = parseFloat(minVehicle.base_price).toLocaleString();
                let maxPrice = parseFloat(maxVehicle.base_price).toLocaleString();
                
                let rangeText = \`Est: &#8369;\${minPrice} - &#8369;\${maxPrice}\`;
                
                document.querySelectorAll('.package-card').forEach(card => {
                    const pkgId = card.getAttribute('data-package-id');`;

let newLoop = `                let sortedVehicles = [...vehiclesList].sort((a, b) => parseFloat(a.base_price) - parseFloat(b.base_price));
                let minVehicle = sortedVehicles[0];
                let maxVehicle = sortedVehicles[sortedVehicles.length - 1];
                
                document.querySelectorAll('.package-card').forEach(card => {
                    const pkgId = card.getAttribute('data-package-id');
                    const pkgPrice = (packageData[pkgId] && packageData[pkgId].package_price) ? parseFloat(packageData[pkgId].package_price) : 0;
                    
                    let pMin = (parseFloat(minVehicle.base_price) + pkgPrice).toLocaleString('en-US', {minimumFractionDigits: 0});
                    let pMax = (parseFloat(maxVehicle.base_price) + pkgPrice).toLocaleString('en-US', {minimumFractionDigits: 0});
                    let rangeText = \`Est: &#8369;\${pMin} - &#8369;\${pMax}\`;`;

text = text.replace(oldLoop, newLoop);

fs.writeFileSync(path, text, 'utf8');
console.log('done fixing welcome price');
