const fs = require('fs');
let path = 'resources/views/view-package.blade.php';
let text = fs.readFileSync(path, 'utf8');

text = text.replace(/const distanceKm = \(totalRouteDistance \/ 1000\)\.toFixed\(1\);\s*document\.getElementById\('breakdownDistance'\)\.innerText = `\$\{distanceKm\} km`;/g, 
"// total distance handled implicitly\n            const distanceKm = (totalRouteDistance / 1000).toFixed(1);");

text = text.replace(/document\.getElementById\('breakdownVehicleFare'\)\.innerText = formatCurrency\(vehiclePrice\);\s*if \(additionalIntervals > 0\) \{[\s\S]*?\} else \{[\s\S]*?\}/g,
"");

fs.writeFileSync(path, text, 'utf8');
console.log('Fixed calculateTotal null references');
