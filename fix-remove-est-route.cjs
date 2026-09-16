const fs = require('fs');
let path = 'resources/views/welcome.blade.php';
let text = fs.readFileSync(path, 'utf8');

// Remove the HTML badge
let oldBadge = `<span class="border rounded px-2 py-1 text-dark pkg-distance-display" style="font-size: 11px; font-weight: 600; letter-spacing: 0.5px; border-color: #6c757d !important;">
                                        EST. ROUTE
                                    </span>`;
text = text.replace(oldBadge, '');

// Remove the JS assignment
text = text.replace(/if\(distDisplay\) distDisplay\.innerHTML = `Est\. Route: \$\{km\.toFixed\(1\)\} km`;/g, '');
text = text.replace(/if\(distDisplay\) distDisplay\.innerHTML = `Route dist err`;/g, '');
text = text.replace(/if\(distDisplay\) distDisplay\.innerHTML = `Single Destination`;/g, '');

fs.writeFileSync(path, text, 'utf8');
console.log('done removing est route');
