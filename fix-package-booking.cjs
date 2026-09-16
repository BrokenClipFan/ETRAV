const fs = require('fs');
let path = 'resources/views/view-package.blade.php';
let text = fs.readFileSync(path, 'utf8');

// Insert HTML breakdown for Package Price
let oldHtml = `<span class="ps-2">Trip Fare:</span>`;
let newHtml = `<span class="ps-2">Package Price:</span>
                                    <span class="fw-medium text-dark" id="breakdownPackagePrice">&#8369;0.00</span>
                                </div>
                                <div class="d-flex justify-content-between mt-1 text-muted">
                                    <span class="ps-2">Trip Fare:</span>`;
text = text.replace(oldHtml, newHtml);

// Fix calculateTotal JS
let oldJs = `const totalBasePrice = vehiclePrice;`;
let newJs = `const targetedPackageData = packageData["{{ $package->id ?? 0 }}"];
            const pkgPrice = targetedPackageData ? parseFloat(targetedPackageData.package_price) || 0 : 0;
            const totalBasePrice = vehiclePrice + pkgPrice;
            document.getElementById('breakdownPackagePrice').innerText = formatCurrency(pkgPrice);`;
text = text.replace(oldJs, newJs);

fs.writeFileSync(path, text, 'utf8');
console.log('done fixing view-package');
