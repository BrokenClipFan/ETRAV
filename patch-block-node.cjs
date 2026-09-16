const { execSync } = require('child_process');
const fs = require('fs');

let orig = execSync('git show HEAD:resources/views/view-package.blade.php', { encoding: 'utf8' });
let startMatch = orig.indexOf("        function addCustomStop(category = 'custom') {");
let endMatch = orig.indexOf('            calculateTotal();\n        }', startMatch);
let origBlock = orig.substring(startMatch, endMatch + '            calculateTotal();\n        }'.length);

let current = fs.readFileSync('resources/views/view-package.blade.php', 'utf8');

// I will just replace the messed up block.
let currentStart = current.indexOf("        function addCustomStop(category = 'custom') {");
let currentEnd = current.indexOf('            calculateTotal();\n        }', currentStart);
if (currentStart !== -1 && currentEnd !== -1) {
    let currentBlock = current.substring(currentStart, currentEnd + '            calculateTotal();\n        }'.length);
    
    // Inject confirmCustomAction into origBlock
    origBlock = origBlock.replace(
        /function addCustomStop\(category = 'custom'\) \{/,
        `function addCustomStop(category = 'custom') {
            confirmCustomAction(() => {`
    );
    
    origBlock = origBlock.replace(
        /setTimeout\(\(\) => toast\.remove\(\), 4000\);\s*\}/,
        `setTimeout(() => toast.remove(), 4000);\n            });\n        }`
    );
    
    current = current.replace(currentBlock, origBlock);
    fs.writeFileSync('resources/views/view-package.blade.php', current, 'utf8');
    console.log('Patched correctly');
} else {
    console.log('Could not find block in current file!');
}
