const fs = require('fs');
let path = 'resources/views/admin/packages.blade.php';
let text = fs.readFileSync(path, 'utf8');

text = text.replace(/document\.getElementById\('selectType'\)\.value = packageData\.type \|\| '';/, `document.getElementById('selectType').value = packageData.type || '';\n            document.getElementById('inputPrice').value = packageData.package_price || '';`);

fs.writeFileSync(path, text, 'utf8');
console.log('fixed package form load');
