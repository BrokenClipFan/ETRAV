const fs = require('fs');
let path = 'resources/views/welcome.blade.php';
let text = fs.readFileSync(path, 'utf8');

text = text.replace(/let rangeText = \`Est: &#8369;\$\{pMin\} - &#8369;\$\{pMax\}\`;/, 'let rangeText = `&#8369;${pMin} - &#8369;${pMax}`;');

fs.writeFileSync(path, text, 'utf8');
console.log('done fixing js text');
