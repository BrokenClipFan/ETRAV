const fs = require('fs');
let path = 'resources/views/view-package.blade.php';
let text = fs.readFileSync(path, 'utf8');

const searchStr = /if \(isCustomRoute\) \{/m;
const replaceStr = `
        const targetedPackageData = packageData["{{ $package->id ?? 0 }}"];
        const pkgPrice = targetedPackageData ? parseFloat(targetedPackageData.package_price) || 0 : 0;
        if (isCustomRoute) {`;

if (searchStr.test(text)) {
    text = text.replace(searchStr, replaceStr);
    fs.writeFileSync(path, text, 'utf8');
    console.log('Fixed pkgPrice definition');
} else {
    console.log('regex not found');
}
