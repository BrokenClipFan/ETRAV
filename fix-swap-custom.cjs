const fs = require('fs');
let path = 'resources/views/view-package.blade.php';
let text = fs.readFileSync(path, 'utf8');

// The code to replace:
/*
packageData[activePackageId].spots = newSpots;
                                isCustomRoute = true;
                                updateSubmitButtonText();
                                calculateTotal();
*/

const oldOnEnd = `packageData[activePackageId].spots = newSpots;
                                isCustomRoute = true;
                                updateSubmitButtonText();
                                calculateTotal();`;

const newOnEnd = `packageData[activePackageId].spots = newSpots;
                                // We don't mark it as custom route just for swapping the order
                                // isCustomRoute = true;
                                // updateSubmitButtonText();
                                // calculateTotal();`;

text = text.replace(oldOnEnd, newOnEnd);
fs.writeFileSync(path, text, 'utf8');
console.log('done fixing swap custom flag');
