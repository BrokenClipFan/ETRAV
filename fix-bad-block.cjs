const { execSync } = require('child_process');
const fs = require('fs');

let orig = execSync('git show HEAD:resources/views/view-package.blade.php', { encoding: 'utf8' });
let startMatch = orig.indexOf("        function addCustomStop(category = 'custom') {");
let endMatch = orig.indexOf('            calculateTotal();\n        }', startMatch);
let goodBlock = orig.substring(startMatch, endMatch + '            calculateTotal();\n        }'.length);

goodBlock = goodBlock.replace(
    /function addCustomStop\(category = 'custom'\) \{/,
    `function addCustomStop(category = 'custom') {
            confirmCustomAction(() => {`
);
goodBlock = goodBlock.replace(
    /setTimeout\(\(\) => toast\.remove\(\), 4000\);\s*\}/,
    `setTimeout(() => toast.remove(), 4000);\n            });\n        }`
);

// We need to find the bad block in current file. It starts at `function addCustomStop` and ends at `calculateTotal();\n        }`
let current = fs.readFileSync('resources/views/view-package.blade.php', 'utf8');
let badStart = current.indexOf("function addCustomStop(category = 'custom') {");
let badEnd = current.indexOf('calculateTotal();\n        }', badStart);

if (badStart !== -1 && badEnd !== -1) {
    let before = current.substring(0, badStart);
    // Find the proper indentation of the end
    let after = current.substring(badEnd + 'calculateTotal();\n        }'.length);
    fs.writeFileSync('resources/views/view-package.blade.php', before + goodBlock + after, 'utf8');
    console.log('Fixed bad block!');
}
