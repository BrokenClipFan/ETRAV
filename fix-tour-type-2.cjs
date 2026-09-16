const fs = require('fs');
let path = 'resources/views/view-package.blade.php';
let text = fs.readFileSync(path, 'utf8');

const regex = /document\.getElementById\('breakdownBase'\)\.innerText = isJoinerAllowed \?\s*`Joiner \/ Open Group \(\$\{headsCount\} slots\)` :\s*'Private \/ Exclusive Group';/;

const newJs = `document.getElementById('breakdownBase').innerText = isJoinerAllowed ?
          'Joiner / Open Group' :
          'Private / Exclusive Group';`;

if (regex.test(text)) {
    text = text.replace(regex, newJs);
    fs.writeFileSync(path, text, 'utf8');
    console.log('done fixing tour type 2');
} else {
    console.log('regex not found');
}
