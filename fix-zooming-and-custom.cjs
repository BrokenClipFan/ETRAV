const fs = require('fs');
let path = 'resources/views/view-package.blade.php';
let text = fs.readFileSync(path, 'utf8');

// 1. Fix the zooming out issue by preventing the pickup location from being added to the map bounds
text = text.replace(
    'bounds.push([p.lat, p.lng]);',
    '// bounds.push([p.lat, p.lng]); // REMOVED: so the map does not zoom out when pickup is set'
);

// 2. Fix the missing isCustomRoute on swap (Sortable onEnd)
const oldOnEnd = `packageData[activePackageId].spots = newSpots;
                                
                                // Redraw map lines without fully rebuilding the form HTML`;
const newOnEnd = `packageData[activePackageId].spots = newSpots;
                                isCustomRoute = true;
                                updateSubmitButtonText();
                                calculateTotal();
                                
                                // Redraw map lines without fully rebuilding the form HTML`;
text = text.replace(oldOnEnd, newOnEnd);

// 3. Just to be perfectly safe on removeSpot (removePlaceFromItinerary)
const oldRemove = `packageData[activePackageId].spots = packageData[activePackageId].spots.filter(spot => spot.id != placeId);
                
                // Redraw map and form`;
const newRemove = `packageData[activePackageId].spots = packageData[activePackageId].spots.filter(spot => spot.id != placeId);
                isCustomRoute = true;
                updateSubmitButtonText();
                
                // Redraw map and form`;
text = text.replace(oldRemove, newRemove);

fs.writeFileSync(path, text, 'utf8');
console.log('done fixing zoom and swap custom');
