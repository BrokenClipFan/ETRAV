const fs = require('fs');
let path = 'resources/views/welcome.blade.php';
let text = fs.readFileSync(path, 'utf8');

const regex = /if \(typeof packageData !== 'undefined'[\s\S]*?\} else \{\s*\}\s*if\(estDisplay\) \{/g;
text = text.replace(regex, 'if(estDisplay) {');

fs.writeFileSync(path, text, 'utf8');
console.log('done removing unused js');
