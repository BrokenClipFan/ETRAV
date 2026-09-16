const fs = require('fs');
let path = 'resources/views/view-package.blade.php';
let text = fs.readFileSync(path, 'utf8');

// In removePlaceFromItinerary
text = text.replace(
    '// Redraw map and form',
    'isCustomRoute = true;\n                updateSubmitButtonText();\n                // Redraw map and form'
);

// In Sortable onEnd
let oldOnEnd = `packageData[activePackageId].spots = newSpots;
                                
                                // Re-render the map lines`;
let newOnEnd = `packageData[activePackageId].spots = newSpots;
                                isCustomRoute = true;
                                updateSubmitButtonText();
                                calculateTotal(); // Calculate to update price to 0 since it's now custom
                                
                                // Re-render the map lines`;
text = text.replace(oldOnEnd, newOnEnd);

fs.writeFileSync(path, text, 'utf8');
console.log('done fixing custom route flag in drag and remove');
