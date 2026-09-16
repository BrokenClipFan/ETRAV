const fs = require('fs');

let orig = fs.readFileSync('original_block.txt', 'utf8');
// orig starts at function addCustomStop. We need up to the end of initializeForm, which is before calculateTotal(); }

let endMatch = orig.indexOf('            calculateTotal();\n        }');
let origBlock = orig.substring(0, endMatch + '            calculateTotal();\n        }'.length);

// Wait, the original was `function addCustomStop` to `calculateTotal(); }`
// The current mangled file has `function addCustomStop` to `calculateTotal(); }` but mangled.

let current = fs.readFileSync('resources/views/view-package.blade.php', 'utf8');
let currentRegex = /function addCustomStop\(category = 'custom'\) \{[\s\S]*?calculateTotal\(\);\s*\}/m;

// Before we replace, let's inject `confirmCustomAction` back into `addCustomStop` in the origBlock
origBlock = origBlock.replace(
    /function addCustomStop\(category = 'custom'\) \{/,
    `function addCustomStop(category = 'custom') {
            confirmCustomAction(() => {`
);

// Close confirmCustomAction at the end of addCustomStop (before focusOnPackageRoute)
// In original, it was:
//            initializeForm();
//            focusOnPackageRoute(activePackageId);
//            
//            // Show alert instruction once
//            const toast = document.createElement('div');
//            ...
//            setTimeout(() => toast.remove(), 4000);
//        }

origBlock = origBlock.replace(
    /setTimeout\(\(\) => toast\.remove\(\), 4000\);\s*\}/,
    `setTimeout(() => toast.remove(), 4000);\n            });\n        }`
);

current = current.replace(currentRegex, origBlock);
fs.writeFileSync('resources/views/view-package.blade.php', current, 'utf8');
console.log('Patched');
