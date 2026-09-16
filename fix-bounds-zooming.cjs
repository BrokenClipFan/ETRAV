const fs = require('fs');
let path = 'resources/views/view-package.blade.php';
let text = fs.readFileSync(path, 'utf8');

// The problematic line is in the altMarker loop:
// bounds.push([place.lat, place.lng]);
// Let's remove it so that gray alternative pins don't widen the zoom bounds.

const oldStr = `currentMarkers.push(altMarker);
                    bounds.push([place.lat, place.lng]);`;
const newStr = `currentMarkers.push(altMarker);
                    // bounds.push([place.lat, place.lng]); // REMOVED: do not zoom out to include unselected alternative pins`;

text = text.replace(oldStr, newStr);

fs.writeFileSync(path, text, 'utf8');
console.log('done fixing bounds for gray pins');
