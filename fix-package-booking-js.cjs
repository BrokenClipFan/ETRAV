const fs = require('fs');
let path = 'resources/views/view-package.blade.php';
let text = fs.readFileSync(path, 'utf8');

// Replace the inside of calculateTotal()
const calcRegex = /function calculateTotal\(\) \{[\s\S]*?function searchMapPlace\(\) \{/m;

const newCalc = `function calculateTotal() {
    const headsInput = document.getElementById('numberHeads');
    let headsCount = parseInt(headsInput.value) || 1;
    const isJoinerAllowed = document.getElementById('allowJoinersCheck').checked;
    
    // Check if Custom Tour
    const targetedPackageData = packageData["{{ $package->id ?? 0 }}"];
    const pkgPrice = targetedPackageData ? parseFloat(targetedPackageData.package_price) || 0 : 0;
    
    // Check if it's currently marked as custom route
    // If it is, the price becomes 0, Admin sets it
    const finalPrice = isCustomRoute ? 0 : pkgPrice;
    
    let totalToPay = finalPrice;
    let downpaymentRequired = totalToPay * 0.25;
    
    document.getElementById('breakdownBase').innerText = isJoinerAllowed ?
        \`Joiner / Open Group (\${headsCount} slots)\` :
        'Private / Exclusive Group';

    document.getElementById('breakdownHeads').innerText = \`\${headsCount} people\`;
    
    document.getElementById('breakdownPackagePrice').innerHTML = '&#8369;' + pkgPrice.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    
    document.getElementById('modalTotalPrice').innerHTML = '&#8369;' + totalToPay.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('modalDownpaymentPrice').innerHTML = '&#8369;' + downpaymentRequired.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function searchMapPlace() {`;

text = text.replace(calcRegex, newCalc);

fs.writeFileSync(path, text, 'utf8');
console.log('done fixing calculateTotal');
