const fs = require('fs');
let path = 'resources/views/view-package.blade.php';
let text = fs.readFileSync(path, 'utf8');

text = text.replace(/focusOnPackageRoute\(activePackageId\);\n\s*\}\n\s*\}\);\n\s*\}\);\n\s*\}/g,
        `focusOnPackageRoute(activePackageId);\n                }\n            });\n        }`);

text = text.replace(/focusOnPackageRoute\(activePackageId\);\n\s*\}\n\s*\}\);\n\s*\}/g,
        `focusOnPackageRoute(activePackageId);\n                }\n            });\n        }`);

text = text.replace(/setTimeout\(\(\) => toast\.remove\(\), 4000\);\n\s*\}\);\n\s*\}\);\n\s*\}/g,
        `setTimeout(() => toast.remove(), 4000);\n            });\n        }`);

text = text.replace(/setTimeout\(\(\) => toast\.remove\(\), 4000\);\n\s*\}\);\n\s*\}/g,
        `setTimeout(() => toast.remove(), 4000);\n            });\n        }`);

fs.writeFileSync(path, text, 'utf8');
console.log('done brute force bracket fix');
