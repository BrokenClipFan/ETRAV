const fs = require('fs');
let path = 'resources/views/view-package.blade.php';
let text = fs.readFileSync(path, 'utf8');

let oldJs = `document.getElementById('breakdownPackagePrice').innerText = formatCurrency(pkgPrice);`;
let newJs = `document.getElementById('breakdownPackagePrice').innerHTML = '&#8369;' + pkgPrice.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });`;
text = text.replace(oldJs, newJs);

fs.writeFileSync(path, text, 'utf8');
console.log('done fixing formatCurrency');
